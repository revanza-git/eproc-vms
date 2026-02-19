<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengadaan_model extends CI_Model {

	public $fppbj="ms_fppbj";
	
	function pejabatPengadaan()
	{
		$query = "SELECT id, name FROM ms_user where id_role = 9 or id_role = 8 or id_role = 7 or id_role = 2";

		$data = $this->db->query($query)->result_array();

		$result = array();
		foreach($data as $value)
		{
			if ($value['name'] == "Haryo") {
                $value['name'] = "Kepala Procurement";
            }
			$result[$value['id']] = $value['name'];
		}

		return $result;
	}

	public function getData()
	{
		// Extract individual years from comma-separated year_anggaran values
		$query = "	SELECT 
			count(t.id) AS total,
			SUBSTRING_INDEX(SUBSTRING_INDEX(t.year_anggaran, ',', n.n+1), ',', -1) AS year
			FROM 
				ms_fppbj AS t
			JOIN (
				SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
			) AS n
			ON 
				CHAR_LENGTH(t.year_anggaran) - CHAR_LENGTH(REPLACE(t.year_anggaran, ',', '')) >= n.n
			WHERE
				SUBSTRING_INDEX(SUBSTRING_INDEX(t.year_anggaran, ',', n.n+1), ',', -1) != ''
					AND t.del = 0
			GROUP BY year";

		//var_dump($query);

		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), false);
		}

		return $query;
	}

	public function getDataByYear($year)
	{
		$admin = $this->session->userdata('admin');
		$yearInt = (int) $year;

		$this->db->select('ms_fppbj.nama_pengadaan AS name, COUNT(*) AS total, ya.year_anggaran AS year, ms_fppbj.id', false);
		$this->db->from($this->fppbj);
		$this->db->join('ms_fppbj_year_anggaran ya', 'ya.id_fppbj = ms_fppbj.id', 'inner');
		$this->db->where('ya.year_anggaran', $yearInt);
		$this->db->where('ms_fppbj.del', 0);

		if (!in_array((int) $admin['id_role'], array(7, 8, 9), true) && (int) $admin['id_role'] === 6) {
			$this->db->where('ms_fppbj.id_pic', (int) $admin['id_user']);
		}

		$this->db->group_by('ms_fppbj.id');

		return $this->db->get()->result_array();
	}

	public function getDataFP3()
	{
		$admin = $this->session->userdata('admin');

		// Division access control
		// Superadmin (role 10), division 1, and division 5 can see all divisions
		if ($admin['id_division'] == 1 || $admin['id_division'] == 5 || $admin['id_role'] == 10) {
			$division_filter = "";
		} else {
			$division_filter = " AND b.id_division = " . $admin['id_division'];
		}

		// Split comma-separated years to show individual year folders
		// This will extract each year from comma-separated year_anggaran values
		$query = "	SELECT 
						@row_number:=@row_number+1 AS id,
						COUNT(DISTINCT a.id) AS total,
						SUBSTRING_INDEX(SUBSTRING_INDEX(b.year_anggaran, ',', n.n+1), ',', -1) AS year
					FROM ms_fp3 a 
					INNER JOIN " . $this->fppbj . " b ON a.id_fppbj = b.id
					JOIN (
						SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 
						UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
					) AS n
					JOIN (SELECT @row_number:=0) AS r
					ON CHAR_LENGTH(b.year_anggaran) - CHAR_LENGTH(REPLACE(b.year_anggaran, ',', '')) >= n.n
					WHERE a.del = 0 
						AND b.del = 0
						AND SUBSTRING_INDEX(SUBSTRING_INDEX(b.year_anggaran, ',', n.n+1), ',', -1) != ''
						" . $division_filter . "
					GROUP BY year
					HAVING total > 0
					ORDER BY year DESC";

		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), false);
		}
		
		return $query;
	}

	function getDataFP3ByYear($year){
		$admin = $this->session->userdata('admin');
		$yearInt = (int) $year;

		// Division access control
		// Superadmin (role 10), division 1, and division 5 can see all divisions
		if ($admin['id_division'] == 1 || $admin['id_division'] == 5 || $admin['id_role'] == 10) {
			$division_filter = "";
		} else {
			$division_filter = " AND ms_fppbj.id_division = " . $admin['id_division'];
		}

		$get = "JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ".$yearInt." WHERE 1=1 " . $division_filter . " ";

		$query = "	SELECT  name,
							count(*) AS total,
							ms_fppbj.id,
							tb_division.id id_division
					FROM ms_fp3
					LEFT JOIN ".$this->fppbj." ON ms_fppbj.id = ms_fp3.id_fppbj
					LEFT JOIN tb_division ON ms_fppbj.id_division = tb_division.id 
					 ".$get."";
		if($this->input->post('filter')){
			$query .= $this->filter($form, $this->input->post('filter'), false);
		}
		//echo $query;die;
		$query .= " GROUP BY id_division ";
		
		return $query;
	}
}

/* End of file Pengadaan_model.php */
/* Location: ./application/models/Pengadaan_model.php */
