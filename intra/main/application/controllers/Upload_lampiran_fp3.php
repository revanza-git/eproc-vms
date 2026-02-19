<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 */
class Upload_lampiran_fp3 extends MY_Controller
{
	public $modelAlias = 'pm';
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('Pemaketan_model','pm');
		$this->form = array(
			'form'=>array(
				array(
					'field' => 'lampiran_persetujuan',
					'type'  => 'file',
					'label' => 'Upload Lampiran Persetujuan',
					'upload_path'=> base_url('assets/lampiran/fppbj/'),
					'upload_url'=> site_url('upload_lampiran_persetujuan/upload_lampiran'),
					'allowed_types'=> 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png',
					'rules' => 'required' 
				)
			)
		);
	}

	public function form_lampiran_persetujuan($id)
	{
		$modelAlias = $this->modelAlias;
		$data = $this->$modelAlias->selectData($id);

		foreach($this->form['form'] as $key => $element) {
			$this->form['form'][$key]['value'] = $data[$element['field']];
		}

		$this->form['url'] = site_url('upload_lampiran_persetujuan/upload_lampiran_persetujuan/'.$id);
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Simpan',
			) ,
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}

	public function upload_lampiran_persetujuan($id){
		$modelAlias = $this->modelAlias;
		if ($this->validation()) {
			$save = $this->input->post();			
			$lastData = $this->$modelAlias->selectData($id);
			if ($this->$modelAlias->upload_lampiran_persetujuan($id, $save)) {
				$this->session->set_flashdata('msg', $this->successMessage);
	 			$this->deleteTemp($save);
			}
		}
	}

	public function lewat_fp3()
	{
		$get_jwpp = $this->getReport();
		$page = '<table border=1>
			<thead>
				<tr>
					<th>No</th>
					<th>Divisi</th>
					<th>Nama Pengadaan</th>
				</tr>
			</thead>
			<tbody>';

		$no = 1;	
		foreach ($get_jwpp as $key => $value) {
			$page .= '<tr>
				<td>'.$no.'</td>
				<td>'.$value['divisi'].'</td>
				<td>'.$value['nama_pengadaan'].'</td>
			</tr>';
			$no++;
		}
		$page .='</tbody>
		</table>';
		// header("Content-type: application/vnd.ms-excel");
		// header("Content-Disposition: attachment; filename=List pengadaan yg sdh lewat (FP3).xls");
		echo $page;
	}

	public function get_jwpp($value='')
	{
		$query = $this->db 	->select('a.id,a.jwpp_start,a.is_status,b.name metode_pengadaan')
							->from('ms_fppbj a')
							->join('tb_proc_method b','b.id=a.metode_pengadaan','LEFT')
							->where('a.del',0)
							->get();
		return $query;
	}

	public function insert_tr_email_blast($value='')
	{
		$getFppbj = $this->get_jwpp();

		foreach ($getFppbj->result_array() as $key => $value) {
			
			$metode_day	= 0;
			$metode = trim($value['metode_pengadaan']);
			if ($metode == "Pelelangan") {
				$metode_day = 60; //60 hari
			}else if ($metode == "Pengadaan Langsung") {
				$metode_day = 10;// 10 hari
			}else if ($metode == "Pemilihan Langsung"){
				$metode_day = 45; //45 hari
			}else if ($metode == "Swakelola"){
				$metode_day = 0;
			}else if ($metode == "Penunjukan Langsung") {
				$metode_day = 20;// 20 hari
			}else{
				// $metode_day = 1;
			}
			$yellow = $value['jwpp_start'];
	        // echo $value['metode_pengadaan'].'<br>';
	        $start_yellow 	= $metode_day+14;
	        $end_yellow 	= $metode_day+1;
			$yellow__ 		= date('Y-m-d', strtotime($yellow.'-'.$start_yellow.' days'));
			$yellow___ 		= date('Y-m-d', strtotime($yellow.'-'.$end_yellow.' days'));
			
			$prevDate 		= date('Y-m-d', strtotime($yellow__.'-14 days'));

			//$this->date_periode($value['id'],$prevDate,$yellow__,1);
			$this->date_periode($value['id'],$yellow__,$yellow___,2);
		}
	}

	public function date_periode($id,$begin,$end,$type)
	{
		$begin = new DateTime($begin);
		$end = new DateTime($end);

		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);
		
		foreach ($period as $dt) {
			// echo $dt->format("Y-m-d").'<br>';die;
			$data = array(
				'id_pengadaan'	=> $id,
				'date_alert'	=> $dt->format("Y-m-d"),
				'type'			=> $type
			);
			$this->db->insert('tr_email_blast',$data);
		}
	}

	public function getReport($metode="")
	{
		$query = " 	SELECT 
							b.name divisi,
					        a.nama_pengadaan,
					        a.jwpp_start
					FROM 
							ms_fppbj a
					INNER JOIN
							tb_division b ON b.id=a.id_division
					WHERE
							a.del = 0 and DATE_ADD(a.jwpp_start, INTERVAL -12 DAY) < CURRENT_DATE() ";
		$query = $this->db->query($query)->result_array();
		return $query;
	}

	public function getWarningFkpbj()
	{
		$query = " 	SELECT 
							b.name divisi,
					        a.nama_pengadaan,
					        a.jwpp_start
					FROM 
							ms_fppbj a
					INNER JOIN
							tb_division b ON b.id=a.id_division
					WHERE
							a.del = 0 and DATE_ADD(a.jwpp_start, INTERVAL -12 DAY) < CURRENT_DATE() ";
		$query = $this->db->query($query)->result_array();
		return $query;
	}
}
