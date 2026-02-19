<?php

class Main_model extends CI_model{

	public $eproc_db;
	public $eproc_gabungan_db;
	function __construct()
	{
		parent::__construct();
		$this->eproc_db = $this->load->database('eproc',true);
		$this->eproc_gabungan_db = $this->load->database('test',true);
	}

	public function cek_login()
	{
		$username = trim((string) $this->input->post('username', true));
		$password = (string) $this->input->post('password');
		
		$del = 0;

		$sql = "SELECT * FROM ms_login WHERE username = ? AND del = ? LIMIT 1";

		$_sql = $this->eproc_db->query($sql, array($username, $del));
	
		$sql = $_sql->row_array();
	
		$ct_sql = '';

		if($_sql->num_rows() > 0){
			$storedPassword = isset($sql['password']) ? (string) $sql['password'] : '';
			$isHash = (bool) preg_match('/^\$(2y|2a|argon2id|argon2i)\$/', $storedPassword);
			$validPassword = false;
			if ($isHash) {
				$validPassword = password_verify($password, $storedPassword);
			} else {
				$validPassword = hash_equals($storedPassword, $password);
			}
			if (!$validPassword) {
				return false;
			}
			if (!$isHash && $storedPassword !== '') {
				$newHash = password_hash($password, PASSWORD_DEFAULT);
				if ($newHash) {
					if (isset($sql['id'])) {
						$this->eproc_db->where('id', $sql['id'])->update('ms_login', array('password' => $newHash));
					} else {
						$this->eproc_db
							->where('username', $username)
							->where('del', $del)
							->update('ms_login', array('password' => $newHash));
					}
				}
			}
	
			if($sql['type'] == "user"){

				$ct_sql = "SELECT * FROM ms_vendor WHERE id=? AND is_active =?";

				$ct_sql = $this->eproc_db->query($ct_sql, array($sql['id_user'],1));
				if(count($ct_sql->result_array() )> 0){
					$data = $ct_sql->row_array();

				$set_session = array(
					'id_user' 		=> 	$data['id'],
					'name'			=>	$data['name'],
					'id_sbu'		=>	$data['id_sbu'],
					'vendor_status'	=>	$data['vendor_status'],
					'is_active'		=>	$data['is_active'],
					'app'			=>	'vms',
					'type'			=> 'user'
				);

				$this->session->set_userdata('user',$set_session);
				return true;
				}
				else{
					return false;
				}
				
			}else if($sql['type'] == "admin" AND $sql['type_app'] == 1){
				$ct_sql = "SELECT *,ms_admin.id id, ms_admin.name name, tb_role.name role_name FROM ms_admin JOIN tb_role ON ms_admin.id_role = tb_role.id WHERE ms_admin.id=? AND ms_admin.del=?";

				$ct_sql = $this->eproc_db->query($ct_sql, array($sql['id_user'],0));
				
				if(count($ct_sql->result_array() )> 0){
					$data = $ct_sql->row_array();

					$set_session = array(
						'id_user' 		=> 	$data['id'],
						'name'			=>	$data['name'],
						'id_sbu'		=>	$data['id_sbu'],
						'id_role'		=>	$data['id_role'],
						'role_name'		=>	$data['role_name'],
						'sbu_name'		=>	$data['sbu_name'],
						'app'			=>	'vms',
						'app_type'		=>	$sql['type_app'],
						'type'			=> 'admin'
					);

					$this->session->set_userdata('admin',$set_session);

					return true;
				}else{
					return false;
				}

			}else if ($sql['type'] == "admin" AND $sql['type_app'] == 2) {
				// echo "Masuk kon 2";die;
				$ct_sql = " SELECT 
								a.name,
								a.email,
								a.id_role,
								a.id_division,
								a.photo_profile,
								a.id,
								b.name division
							FROM
								ms_admin a 
							INNER JOIN
								tb_division b ON b.id=a.id_division
							WHERE
								a.del = ? AND a.id = ?
								";

				$ct_sql = $this->eproc_db->query($ct_sql, array(0,$sql['id_user']));
				
				if(count($ct_sql->result_array() )> 0){

					$data = $ct_sql->row_array();

					$set_session = array(

						'name'			=>	$data['name'],
						'division'		=>	$data['division'],
						'id_user' 		=> 	$data['id'],
						'id_role'		=>	$data['id_role'],
						'id_division'	=>  $data['id_division'],
						'email'			=>  $data['email'],
						'photo_profile' =>  $data['photo_profile'],
						'app_type' 		=>	$sql['type_app']
					);
					$this->session->set_userdata('admin',$set_session);
					$admin = $this->session->userdata('admin');

					$activity = array(
						'id_user'	=>	$admin['id_user'],
						'activity'	=>	$admin['name']." Telah Login",
						'activity_date' => date('Y-m-d H:i:s')
					);

					$this->db->insert('tr_log_activity',$activity);
					return true;
				}else{
					return false;
				}

			}



		}else{

			return false;

		}
	}

	public function to_app($id)
	{
		$query = "	SELECT
						a.*,
						b.name role_name
						-- c.name division
					FROM
						ms_admin a
					JOIN
						tb_role b ON b.id=a.id_role
					-- JOIN
					-- 	tb_division c ON c.id=a.id_division
					WHERE
						a.id = ?
		"; 

		$query = $this->eproc_db->query($query,array($id))->row_array();
		// echo $this->eproc_db->last_query();die;
		return $query;
	}

	public function cek_login_()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$sql = "SELECT * FROM ms_login WHERE username = ? AND password = ?";
		$_sql = $this->eproc_db->query($sql, array($username, $password));
		$sql = $_sql->row_array();

		$ct_sql = '';

		if($_sql->num_rows() > 0){

			if($sql['type'] == "user"){
				$ct_sql = "SELECT * FROM ms_vendor WHERE id=? AND is_active =?";
				$ct_sql = $this->eproc_db->query($ct_sql, array($sql['id_user'],1));
				if(count($ct_sql->result_array() )> 0){
				$data = $ct_sql->row_array();

				$set_session = array(
					'id_user' 		=> 	$data['id'],
					'name'			=>	$data['name'],
					'id_sbu'		=>	$data['id_sbu'],
					'vendor_status'	=>	$data['vendor_status'],
					'is_active'		=>	$data['is_active'],
					'app'			=>	'vms',
					'type'			=> 'user'
				);

				$this->session->set_userdata('user',$set_session);
				return true;
				}
				else{
					return false;
				}
				
			}else if($sql['type'] == "admin" AND $sql['type_app'] == 1){
				$ct_sql = "SELECT *,ms_admin.id id, ms_admin.name name, tb_role.name role_name FROM ms_admin JOIN tb_role ON ms_admin.id_role = tb_role.id WHERE ms_admin.id=? AND ms_admin.del=?";
				$ct_sql = $this->eproc_db->query($ct_sql, array($sql['id_user'],0));
				
				if(count($ct_sql->result_array() )> 0){
					$data = $ct_sql->row_array();

					$set_session = array(
						'id_user' 		=> 	$data['id'],
						'name'			=>	$data['name'],
						'id_sbu'		=>	$data['id_sbu'],
						'id_role'		=>	$data['id_role'],
						'role_name'		=>	$data['role_name'],
						'sbu_name'		=>	$data['sbu_name'],
						'app'			=>	'vms',
						'app_type'		=>	$sql['type_app'],
						'type'			=> 'admin'
					);

					$this->session->set_userdata('admin',$set_session);

					return true;
				}else{
					return false;
				}

			}else if ($sql['type'] == "admin" AND $sql['type_app'] == 2) {
				$ct_sql = " SELECT 
								a.name,
								a.email,
								a.id_role,
								a.id_division,
								a.photo_profile,
								a.id,
								b.name division
							FROM
								ms_admin a 
							INNER JOIN
								tb_division b ON b.id=a.id_division
							WHERE
								a.del = ? AND a.id = ?
								";

				$ct_sql = $this->eproc_db->query($ct_sql, array(0,$sql['id_user']));
				
				if(count($ct_sql->result_array() )> 0){

					$data = $ct_sql->row_array();

					$set_session = array(

						'name'			=>	$data['name'],
						'division'		=>	$data['division'],
						'id_user' 		=> 	$data['id'],
						'id_role'		=>	$data['id_role'],
						'id_division'	=>  $data['id_division'],
						'email'			=>  $data['email'],
						'photo_profile' =>  $data['photo_profile'],
						'app_type' 		=>	$sql['type_app']
					);
					$this->session->set_userdata('admin',$set_session);
					return true;
				}else{
					return false;
				}

			}



		}else{

			return false;

		}
	}

	function getKurs(){
		$return = array();
		$query = "SELECT * FROM tb_kurs WHERE del = 0";
		$query = $this->db->query($query);
		foreach ($query->result_array() as $key => $value) {
			$return[$value['id']] = $value['symbol'];
		}
		return $return;
	}

	public function getRole(){
		$return = array();
		$query = "SELECT * FROM tb_role";
		$query = $this->db->query($query);
		foreach ($query->result_array() as $key => $value) {
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}

	public function getDiv(){
		$return = array();
		$query = "SELECT * FROM tb_division WHERE del = 0";
		$query = $this->db->query($query);
		foreach ($query->result_array() as $key => $value) {
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}
	
	function getProcMethod(){
		$return[''] = 'Pilih Dibawah Ini';
		$query = "SELECT * FROM tb_proc_method WHERE del = 0";
		$query = $this->db->query($query);
		foreach ($query->result_array() as $key => $value) {
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}


	function getFppbj(){
		$return[''] = 'Pilih Dibawah Ini';
		$query = "SELECT * FROM ms_fppbj WHERE del = 0";
		$query = $this->db->query($query);
		foreach ($query->result_array() as $key => $value) {
			$return[$value['id']] = $value['nama_pengadaan'];
		}
		return $return;
	}

	function getDiv_($id=""){
		return  $this->db->where('id', $id)->get('tb_division')->row_array();
	}

	public function update_status($table_= "ms_fppbj", $id, $param_){
		return $this->db->where('id', $id)->update($table_, array('is_status' => $param_));
	}

	public function approve($table, $id, $param_){		
		$data = $this->db->where('id', $id)->get($table)->row_array();

		if ($data['is_status'] == 1) {
			$this->db->where('id', $data['id_fppbj'])->update('ms_fppbj', $param_);
			$query = $this->db->where('id', $id)->update($table, $param_);
		}
		else if ($data['is_status'] == 2) {
			$update = array(
				'is_approved' => $param_['is_approved']
			);
			$this->db->where('id_fppbj', $data['id'])->update('ms_fkpbj', $param_);
			$query = $this->db->where('id', $data['id'])->update('ms_fppbj', $param_);
		} else{
			$this->db->where('id', $id)->update($table, $param_);
			$query = $this->db->where('id', $data['id'])->update('ms_fppbj', $param_);
		}

		return $query;
	}

	public function notification($id_user='', $active = 1){
		$id_user = (int) $id_user;
		$active = (int) $active;
		$note = "SELECT * FROM tr_note WHERE id_user = ? AND is_active = ? AND type IS NULL ORDER BY id DESC";
		$query = $this->db->query($note, array($id_user, $active));

		return $query;
	}

	public function get_fppbj($year){
		$params = array();
		$q = '';
		$yearInt = (int) $year;
		if($year != ''){
			$q = ' AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ')';
		}

		$sql = "SELECT
						*
				  FROM
				  		ms_fppbj
				  WHERE
				  		is_status = 0 AND is_approved = 0 AND del = 0 ".$q;

		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$id_role = (is_array($admin) && isset($admin['id_role']) && $admin['id_role'] !== '') ? (int) $admin['id_role'] : null;

		// Superadmin (role 10) can see all divisions
		if ($id_division !== null && $id_division != 1 && $id_division != 5 && $id_role != 10) {
			$sql .= " AND id_division = ".$id_division;
		}

		$query = $this->db->query($sql, $params);
		return $query;
	}

	public function get_fppbj_selesai($year,$is_perencanaan='1'){
		$yearInt = (int) $year;
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$id_role = (is_array($admin) && isset($admin['id_role']) && $admin['id_role'] !== '') ? (int) $admin['id_role'] : null;

		// Superadmin (role 10) can see all divisions
		if ($id_division !== null && $id_division != 1 && $id_division != 5 && $id_role != 10) {
			$divisi = "id_division = ".$id_division." AND ";
		}else{
			$divisi = '';
		}

		if($year != ''){
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND ';
		} else{
			$q = '';
		}

        if ($is_perencanaan != '1') {
            $sql = "SELECT * from ms_fppbj a where ".$divisi." a.is_perencanaan = 2 AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = a.id AND ya.year_anggaran = ".$yearInt.") and is_approved = 3";
        } else {
            $sql = "SELECT
					*
				FROM
					ms_fppbj
				WHERE 
					is_approved_hse < 2
                        AND (".$divisi." $q is_perencanaan = 1 AND is_status = 0 AND is_reject = 0 AND del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3)))
					OR  (".$divisi." $q is_perencanaan = 1 AND is_status = 0 AND is_reject = 0 AND del = 0 AND is_approved = 4 AND idr_anggaran > 100000000)
					OR  (".$divisi." $q is_perencanaan = 1 AND is_status = 1 AND del = 0)
					OR  (".$divisi." $q is_perencanaan = 1 AND is_status = 2 AND del = 0)
					";
        }

		//print_r($sql);
        
		$query = $this->db->query($sql);
		return $query;
	}

	public function get_total_fppbj_semua($year,$is_perencanaan="1"){
		$admin = $this->session->userdata('admin');
		$id_role = (is_array($admin) && isset($admin['id_role']) && $admin['id_role'] !== '') ? (int) $admin['id_role'] : null;
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if($year != ''){
			$q = ' AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = '.$yearInt.') ';
		} else{
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " AND is_perencanaan = ".$is_perencanaan;
		} else {
			$perencanaan = " AND is_perencanaan = 1";
		}

		$sql = "SELECT
						*
				  FROM
				  		ms_fppbj
				  WHERE 
				  		del = 0".$q.$perencanaan;

		// Superadmin (role 10), division 1, and division 5 can see all divisions
		if ($id_division !== null && $id_division != 1 && $id_division != 5 && $id_role != 10) {
			$sql .= " AND id_division = ".$id_division;
		}
		
		$query = $this->db->query($sql);
		return $query;
	}

	public function total_pending_dir_fkpbj($year = "")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if ($year != '') {
			$q = ' AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fkpbj.id_fppbj AND ya.year_anggaran = ' . $yearInt . ')';
		} else {
			$q = '';
		}

		$sql = "SELECT
						*
				  FROM
				  		ms_fkpbj
				  WHERE 
				  		is_status = 0 
				  		AND is_approved = 3
						AND del = 0
				        AND is_reject = 0
				        $q
				        AND idr_anggaran > 100000000
				        ";

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$sql .= " AND id_division = " . $id_division;
		}

		$query = $this->db->query($sql);
		return $query;
	}

	public function total_pending_dir($year = "", $is_perencanaan = "1")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if ($year != '') {
			$q = ' AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ')';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " AND is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " AND is_perencanaan = 1";
		}

		$sql = "SELECT
						*
				  FROM
				  		ms_fppbj
				  WHERE 
				  		is_status = 0 
				  		AND is_approved = 3
						AND del = 0
				        AND is_reject = 0
				        AND is_writeoff = 0 $q $perencanaan
				        AND idr_anggaran > 100000000
				        ";

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$sql .= " AND id_division = " . $id_division;
		}

		$query = $this->db->query($sql);
		return $query;
	}

	public function get_fppbj_pending($year,$is_perencanaan="1"){
		$admin = $this->session->userdata('admin');
		$id_role = (is_array($admin) && isset($admin['id_role']) && $admin['id_role'] !== '') ? (int) $admin['id_role'] : null;
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$divisi = "id_division = ".$id_division." AND ";
		} else{
			$divisi = " ";
		}
		
		if($year != ''){
			$q = ' AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = '.$yearInt.') ';
		} else{
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " AND is_perencanaan = ".$is_perencanaan;
		} else {
			$perencanaan = " AND is_perencanaan = 1";
		}
		
		$sql = "	SELECT
						*
				  	FROM
				  		ms_fppbj
				  	WHERE 
				  		$divisi del = 0 AND is_approved = 0 AND is_status = 0 AND is_reject = 0 ".$q.$perencanaan;		

		$query = $this->db->query($sql);

		
		return $query;
	}

	function search_data($value){
		$result = array();
		$admin = $this->session->userdata('admin');		
		$query = "	SELECT
		                a.id,
		                a.nama_pengadaan,
		                b.name nama_divisi,
		                a.is_status,
		                a.is_approved,
		                b.id id_division,
		                a.entry_stamp
					FROM ms_fppbj a
					LEFT JOIN tb_division b ON b.id=a.id_division
					WHERE a.del = 0 AND a.nama_pengadaan LIKE ? OR b.name LIKE ?
					LIMIT 5";

	    $query = $this->db->query($query, array('%'.$_POST['search'].'%','%'.$_POST['search'].'%'))->result_array();		
		$result = array();
		foreach($query as $key => $value){
			if ($value['is_status'] == 0) {
				$class = 'fppbj';
			} elseif ($value['is_status'] == 1) {
				$class = 'fp3';
			} else {
				$class = 'fkpbj';
			}
			$result[$value['id']] = 
				'<div class="search-result"><div class="sr-logo '.$class.'">
					<span class="icon"><i class="fas fa-file-alt"></i></span>
				</div>
				<div class="sr-item">
					<div class="sr-name"><span class="sr-no">1.</span><a href="'.base_url('pemaketan/division/'.$value['id_division'].'/'.$value['id'].'/'.date('Y',strtotime($value['entry_stamp']))).'">'.$value['nama_pengadaan'].'</a></div>
					<div class="sr-keterangan">
						<span class="status">Aktif</span>
						<span class="divisi"'.$value['nama_divisi'].'</span>
					</div>
					<div class="sr-icon">
						<span class="icon ar">
							<i class="fas fa-radiation"></i>
						</span>
						<span class="icon sw" style="font-size: 13px">
							<i class="fas fa-luggage-cart"></i>
						</span>
					</div>
					</div>
				</div>';
		}
		
		return $result;
	}

	public function get_fppbj_reject($year,$is_perencanaan="1"){
		$admin = $this->session->userdata('admin');
		$id_role = (is_array($admin) && isset($admin['id_role']) && $admin['id_role'] !== '') ? (int) $admin['id_role'] : null;
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if ($id_division !== null && $id_division!= 1 && $id_division != 5) {
			$divisi = "id_division = ".$id_division." AND ";
		} else{
			$divisi = '';
		}

		if($year != ''){
			$q = ' AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = '.$yearInt.') AND ';
		} else{
			$q = ' AND ';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = ".$is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}
		
		$sql = "SELECT
						*
				  FROM
				  		ms_fppbj
				  WHERE 
				  ".$divisi."
				  		is_writeoff = 0
						AND 
						(is_status = 0 $q $perencanaan AND del = 0 AND is_reject = 1 AND idr_anggaran < 100000000)
                        OR
						(is_status = 0 $q $perencanaan AND del = 0 AND is_reject = 1 AND idr_anggaran > 100000000)";
		
		//print_r($sql);
		
		/*if ($id_role == 2) {
			$sql .= " AND is_approved <= 2"; 
		}*/
		
		$query = $this->db->query($sql);
	
		return $query;
	}

	public function delete($id)
	{
		return $this->db->where('id',$id)->update('tr_note',array('is_active' => 0,'edit_stamp'=>date('Y-m-d H:i:s')));
	}

	public function get_pending_kadiv()
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$divisi = " AND id_division = " . $id_division;
		} else {
			$divisi = '';
		}
		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						is_status = 0 AND is_approved = 0 AND del = 0 $divisi";
		return $this->db->query($query);
	}

	public function get_pending_admin_hsse($year = "", $is_perencanaan = "1")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if ($year != '') {
			$q = ' AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') ';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " AND is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " AND is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
					del = 0 AND	((is_status = 0 AND tipe_pengadaan = 'barang' AND is_approved = 0 AND id_division = 5 AND is_approved_hse = 0) OR (is_status = 0 AND tipe_pengadaan = 'jasa' AND is_approved = 1 AND is_approved_hse = 0)) " . $q . $perencanaan;

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$query .= " AND id_division = " . $id_division;
		}

		return $this->db->query($query);
	}

	public function get_pending_admin_pengendalian($year = "", $is_perencanaan = "1")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if ($year != '') {
			$q = ' AND EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') ';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " AND is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " AND is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						is_status = 0 AND 
						del = 0 AND 
						is_approved = 1 AND 
						(
							(is_approved_hse = 1  AND tipe_pengadaan = 'jasa') OR 
							((is_approved_hse = 0 OR is_approved_hse = 1) AND tipe_pengadaan = 'barang')
						) AND (is_reject = 0) " . $q . $perencanaan;

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$query .= " AND id_division = " . $id_division;
		}

		return $this->db->query($query);
	}

	public function get_pending_kadept_proc($year = "", $is_perencanaan = "1")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$q = "id_division = " . $id_division . " AND";
		} else {
			$q = '';  // Initialize $q to empty string if condition is false
		}

		if ($year != '') {
			$q2 = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q2 = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan . " AND ";
		} else {
			$perencanaan = " is_perencanaan = 1 AND ";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
					-- 	is_writeoff = 0
			        -- AND  
						(del = 0 AND $q $q2 $perencanaan is_status = 0 AND  is_approved = 2 AND is_reject = 0 AND idr_anggaran <= 100000000 AND (metode_pengadaan = 4))
			        OR 
						(del = 0 AND $q $q2 $perencanaan is_status = 0 AND  is_approved = 2 AND is_reject = 0 AND idr_anggaran > 100000000 AND (metode_pengadaan = 4 OR metode_pengadaan = 2 OR metode_pengadaan = 1 OR metode_pengadaan = 5))
					OR
						($q $q2 $perencanaan is_status = 0 AND  is_approved = 2 AND is_reject = 0 AND del = 0 AND idr_anggaran <= 100000000 AND (metode_pengadaan != 4))";
		
		return $this->db->query($query);
	}

	public function get_pending_dirut($year = "", $is_perencanaan = "1")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		// Initialize variables to prevent undefined variable errors
		$q = '';
		$q2 = '';
		$perencanaan = '';

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$q = "id_division = " . $id_division . " AND ";
		}

		if ($year != '') {
			$q2 = 'EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND ';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = "is_perencanaan = " . $is_perencanaan . " ";
		} else {
			$perencanaan = "is_perencanaan = 1 ";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $q2 $perencanaan AND is_status = 0 AND is_approved = 3 AND is_reject = 0 AND is_writeoff = 0 AND idr_anggaran >= 10000000000";
		return $this->db->query($query);
	}

	public function get_pending_dirke($year = "", $is_perencanaan = "1")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		// Initialize variables to prevent undefined variable errors
		$q = '';
		$q2 = '';
		$perencanaan = '';

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$q = "id_division = " . $id_division . " AND ";
		}

		if ($year != '') {
			$q2 = 'EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND ';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = "is_perencanaan = " . $is_perencanaan . " ";
		} else {
			$perencanaan = "is_perencanaan = 1 ";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $q2 $perencanaan AND del = 0 AND is_status = 0 AND is_approved = 3 AND is_reject = 0 AND is_writeoff = 0 AND (idr_anggaran > 1000000000 AND idr_anggaran <= 10000000000)";

		return $this->db->query($query);
	}

	public function get_pending_dirsdm($year = "", $is_perencanaan = "1")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		// Initialize variables to prevent undefined variable errors
		$q = '';
		$q2 = '';
		$perencanaan = '';

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$q = "id_division = " . $id_division . " AND ";
		}

		if ($year != '') {
			$q2 = 'EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND ';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = "is_perencanaan = " . $is_perencanaan . " ";
		} else {
			$perencanaan = "is_perencanaan = 1 ";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $q2 $perencanaan AND
						is_status = 0 AND 
				        is_approved = 3 AND 
				        is_reject = 0 AND 
				        is_writeoff = 0 AND 
				        ((idr_anggaran > 100000000 AND idr_anggaran <= 1000000000)) AND del = 0";

		return $this->db->query($query);
	}

	public function get_pending_dirsdm_fkpbj($year = "")
	{
		$admin = $this->session->userdata('admin');
		$id_division = (is_array($admin) && isset($admin['id_division']) && $admin['id_division'] !== '') ? (int) $admin['id_division'] : null;
		$yearInt = (int) $year;

		if ($id_division !== null && $id_division != 1 && $id_division != 5) {
			$q = "id_division = " . $id_division . " AND";
		}

		if ($year != '') {
			$q2 = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fkpbj.id_fppbj AND ya.year_anggaran = ' . $yearInt . ')';
		} else {
			$q2 = '';
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fkpbj
					WHERE
						$q $q2 AND
						is_status = 0 AND 
				        is_approved = 3 AND 
				        is_reject = 0 AND 
				        ((idr_anggaran > 100000000 AND idr_anggaran <= 1000000000)) AND del = 0";

		return $this->db->query($query);
	}

	public function get_done_dirut($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $perencanaan AND is_status = 0 AND is_approved = 4 AND is_reject = 0 AND is_writeoff = 0 AND idr_anggaran >= 10000000000 AND (metode_pengadaan = 'Penunjukan Langsung' OR metode_pengadaan = 'Pemilihan Langsung' OR metode_pengadaan = 'Pelelangan')";
		return $this->db->query($query);
	}

	public function get_done_dirke($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $perencanaan AND is_status = 0 AND is_approved = 4 AND is_reject = 0 AND is_writeoff = 0 AND (idr_anggaran > 1000000000 AND idr_anggaran <= 10000000000) AND del = 0";
		return $this->db->query($query);
	}

	public function get_done_dirsdm($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
					 $q $perencanaan AND
						is_status = 0 AND 
				        is_approved = 4 AND 
				        is_reject = 0 AND 
				        is_writeoff = 0 AND 
				        ((idr_anggaran > 100000000 AND idr_anggaran <= 1000000000)) AND del = 0";
		return $this->db->query($query);
	}

	public function get_reject_dirut($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $perencanaan AND is_status = 0 AND is_approved = 3 AND is_reject = 1 AND is_writeoff = 0 AND idr_anggaran >= 10000000000 AND (metode_pengadaan = 'Penunjukan Langsung' OR metode_pengadaan = 'Pemilihan Langsung' OR metode_pengadaan = 'Pelelangan')";
		return $this->db->query($query);
	}

	public function get_reject_dirke($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $perencanaan AND is_status = 0 AND is_approved = 3 AND is_reject = 1 AND is_writeoff = 0 AND (idr_anggaran > 1000000000 AND idr_anggaran <= 10000000000)";
		return $this->db->query($query);
	}

	public function get_reject_dirsdm($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
					$q $perencanaan AND
						is_status = 0 AND 
				        is_approved = 3 AND 
				        is_reject = 1 AND 
				        is_writeoff = 0 AND 
				        ((idr_anggaran > 100000000 AND idr_anggaran <= 1000000000)) AND del = 0";
		return $this->db->query($query);
	}

	public function get_fppbj_done_kadept_otorisasi($year)
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}
		$sql = "SELECT
						*
				  FROM
				  		ms_fppbj
				  WHERE 
				  $q
				  		is_status = 0 AND 
				        is_approved = 3 AND 
				        is_reject = 0 AND 
				        is_writeoff = 0 AND 
				        idr_anggaran < 100000000 AND 
				        (metode_pengadaan = 4)";
		$query = $this->db->query($sql);
		return $query;
	}

	public function get_total_fppbj_directure($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						*
					FROM 
						ms_fppbj
					WHERE
					$q $perencanaan AND
						is_status = 0 AND is_approved = 3 AND idr_anggaran > 100000000 AND del = 0 AND (metode_pengadaan = 4 OR 
				        metode_pengadaan = 2 OR 
				        metode_pengadaan = 1) AND is_approved_hse < 2";
		return $this->db->query($query);
	}

	public function get_total_fppbj_dirke($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $perencanaan AND is_status = 0 AND is_approved = 3 AND is_writeoff = 0 AND (idr_anggaran > 1000000000 AND idr_anggaran <= 10000000000) AND del = 0";
		return $this->db->query($query);
	}

	public function get_total_fppbj_dirut($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
						$q $perencanaan AND is_status = 0 AND is_approved = 3 AND is_writeoff = 0 AND idr_anggaran >= 10000000000 AND del = 0";
		return $this->db->query($query);
	}

	public function get_total_fppbj_dirsdm($year = "", $is_perencanaan = "1")
	{
		$yearInt = (int) $year;
		if ($year != '') {
			$q = ' EXISTS (SELECT 1 FROM ms_fppbj_year_anggaran ya WHERE ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ' . $yearInt . ') AND';
		} else {
			$q = '';
		}

		if ($is_perencanaan != '1') {
			$perencanaan = " is_perencanaan = " . $is_perencanaan;
		} else {
			$perencanaan = " is_perencanaan = 1";
		}

		$query = "SELECT 
						* 
					FROM 
						ms_fppbj
					WHERE
					$q $perencanaan AND
						is_status = 0 AND 
				        is_approved = 3 AND 
				        is_writeoff = 0 AND 
				        ((idr_anggaran > 100000000 AND idr_anggaran <= 1000000000)) AND del = 0";
		return $this->db->query($query);
	}
}
