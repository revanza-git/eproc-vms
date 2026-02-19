<?php defined('BASEPATH') or exit('No direct script access allowed');

class Fp3 extends MY_Controller
{

	public $form;
	public $modelAlias 	= 'fp3';
	public $alias 		= 'ms_fp3';
	public $module 		= 'kurs';

	public $current_year = 2023;
	
	public function __construct()
	{	
		parent::__construct();
		// Debug logging removed to prevent log clutter
		
		$this->load->model('Fp3_model', 'fp3');
		$this->load->model('Fppbj_model', 'fm');
		$this->load->model('Main_model', 'mm');
		$this->load->model('pengadaan_model', 'pm');

		$this->load->library('pdf');
		include_once APPPATH . 'third_party/dompdf2/dompdf_config.inc.php';


		$this->form = array(
			'form' => array(
				array(
					'field'	=> 	'status',
					'type'	=>	'fp3',
					'label'	=>	'FP3',
				),
				array(
					'field'	=> 	'id_fppbj',
					'type'	=>	'dropdown',
					'label'	=>	'Nama Pengadaan (Lama)',
					'source' =>  $this->fp3->getFppbj(),
					'rules' =>	'required'
				),
				array(
					'field'	=> 	'nama_pengadaan',
					'type'	=>	'text',
					'label'	=>	'Nama Pengadaan (Baru)',
				),
				array(
					'field'	=> 	'no_pr_lama',
					'type'	=>	'text',
					'label'	=>	'Nomor PR (Lama)',
				),
				array(
					'field'	=> 	'no_pr',
					'type'	=>	'text',
					'label'	=>	'Nomor PR (Baru)',
				),
				array(
					'field'	=> 	'metode_pengadaan_lama',
					'type'	=>	'text',
					'label'	=>	'Metode Pengadaan (Lama)'
				), array(
					'field'	=> 	'metode_pengadaan',
					'type'	=>	'dropdown',
					'label'	=>	'Metode Pengadaan (Baru)',
					'source' =>	$this->mm->getProcMethod(),
				),
				// array(
				// 	'field'	=> 	'idr_anggaran_lama',
				// 	'type'	=>	'currency',
				// 	'label'	=>	'Anggaran (Lama)',
				// ),
				// array(
				// 	'field'	=> 	'idr_anggaran',
				// 	'type'	=>	'currency',
				// 	'label'	=>	'Anggaran (Baru)',
				// ),
				array(
					'field'	=> 	array('jwpp_start_lama', 'jwpp_end_lama'),
					'type'	=>	'date_range',
					'label'	=>	'Masa Penyelesaian Pekerjaan (Lama)',
				),
				array(
					'field'	=> 	array('jwpp_start', 'jwpp_end'),
					'type'	=>	'date_range',
					'label'	=>	'Masa Penyelesaian Pekerjaan (Baru)',
				), array(
					'field'	=> 	'desc_lama',
					'type'	=>	'textarea',
					'label'	=>	'Keterangan (Lama)',
				), array(
					'field'	=> 	'desc',
					'type'	=>	'textarea',
					'label'	=>	'Keterangan (Baru)',
				), array(
					'field' => 'kak_lampiran_lama',
					'type'  => 'file',
					'label' => 'KAK Lampiran (Lama)',
					'upload_path' => base_url('assets/lampiran/kak_lampiran/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*'
				), array(
					'field' => 'kak_lampiran',
					'type'  => 'file',
					'label' => 'KAK Lampiran (Baru)',
					'upload_path' => base_url('assets/lampiran/kak_lampiran/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*'
				),
				array(
					'field' => 'pr_lampiran_lama',
					'type'  => 'file',
					'label' => 'PR Lampiran (Lama)',
					'upload_path' => base_url('assets/lampiran/pr_lampiran/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*'
				), array(
					'field' => 'pr_lampiran',
					'type'  => 'file',
					'label' => 'PR Lampiran (Baru)',
					'upload_path' => base_url('assets/lampiran/pr_lampiran/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*'
				), array(
					'field'	=> 	'desc_batal',
					'type'	=>	'textarea',
					'label'	=>	'Justifikasi Batal',
				), array(
					'field'	=> 	'fp3_type',
					'type'	=>	'hidden',
				)
			),

			'successAlert' => 'Berhasil mengubah data!',
			'filter' => array(
				array(
					'field'	=> 	'a|status',
					'type'	=>	'text',
					'label'	=>	'Status'
				), array(
					'field'	=> 	'a|id_fppbj',
					'type'	=>	'dropdown',
					'label'	=>	'Nama Pengadaan B/J',
					'source' =>  $this->fp3->getFppbj(),
					'rules' => 	'required',
				), array(
					'field'	=> 	'a|nama_pengadaan',
					'type'	=>	'text',
					'label'	=>	'Nama Pengadaan',
					'rules' => 	'required',
				), array(
					'field'	=> 	'a|metode_pengadaan',
					'type'	=>	'text',
					'label'	=>	'Metode Pengadaan',
					'rules' => 	'required',
				), array(
					'field'	=> 	'a|jadwal_pengadaan',
					'type'	=>	'dateTime',
					'label'	=>	'Masa Penyelesaian Pekerjaan',
					'rules' => 	'required',
				)

			)
		);
		$this->insertUrl = site_url('fp3/save/');
		$this->updateUrl = 'fp3/update/';
		$this->deleteUrl = 'fp3/delete/';
		$this->approveURL = 'fp3/approve/';
		$this->getData = $this->fp3->getData($this->form);
		
		// Filter form elements to only include valid validation rules
		$validation_rules = array();
		foreach ($this->form['form'] as $element) {
			if (isset($element['field']) && isset($element['rules'])) {
				$validation_rules[] = $element;
			}
		}
		if (!empty($validation_rules)) {
			$this->form_validation->set_rules($validation_rules);
		}
	}

	public function fp3ByYear($year)
	{
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('pengadaan/fp3'),
			'title' => 'FP3 ' . $year
		));

		$data['year'] = $year;
		$data['id'] = $year; // Pass year as id for the JavaScript view

		$this->header = 'FP3 ' . $year;
		$this->content = $this->load->view('fp3/list_year', $data, TRUE);
		$this->script = $this->load->view('fp3/list_year_js', $data, TRUE);
		parent::index();
	}

	public function getData($id_division = "", $id_fppbj = "", $year = "")
	{
		$config['query'] = $this->fp3->getData($id_division, $id_fppbj, $year);
		$return = $this->tablegenerator->initialize($config);
		echo json_encode($return);
	}

	public function getDataFP3ByYear($year)
	{
		// echo "string".$year;
		$config['query'] = $this->pm->getDataFP3ByYear($year);
		$return = $this->tablegenerator->initialize($config);
		// print_r($return);
		echo json_encode($return);
	}

	public function insert($year=null)
	{
		if (isset($year)) {
            $this->form = array(
                'form' => array(
                    array(
                        'field'	=> 	'status',
                        'type'	=>	'fp3',
                        'label'	=>	'FP3',
                    ),
                    array(
                        'field'	=> 	'id_fppbj',
                        'type'	=>	'dropdown',
                        'label'	=>	'Nama Pengadaan (Lama)',
                        'source' =>  $this->fp3->getFppbj("", $year),
                        'rules' =>	'required'
                    ),
                    array(
                        'field'	=> 	'nama_pengadaan',
                        'type'	=>	'text',
                        'label'	=>	'Nama Pengadaan (Baru)',
                    ),
                    array(
                        'field'	=> 	'no_pr_lama',
                        'type'	=>	'text',
                        'label'	=>	'Nomor PR (Lama)',
                    ),
                    array(
                        'field'	=> 	'no_pr',
                        'type'	=>	'text',
                        'label'	=>	'Nomor PR (Baru)',
                    ),
                    array(
                        'field'	=> 	'metode_pengadaan_lama',
                        'type'	=>	'text',
                        'label'	=>	'Metode Pengadaan (Lama)'
                    ), array(
                        'field'	=> 	'metode_pengadaan',
                        'type'	=>	'dropdown',
                        'label'	=>	'Metode Pengadaan (Baru)',
                        'source' =>	$this->mm->getProcMethod(),
                    ),
                    // array(
                    // 	'field'	=> 	'idr_anggaran_lama',
                    // 	'type'	=>	'currency',
                    // 	'label'	=>	'Anggaran (Lama)',
                    // ),
                    // array(
                    // 	'field'	=> 	'idr_anggaran',
                    // 	'type'	=>	'currency',
                    // 	'label'	=>	'Anggaran (Baru)',
                    // ),
                    array(
                        'field'	=> 	array('jwpp_start_lama', 'jwpp_end_lama'),
                        'type'	=>	'date_range',
                        'label'	=>	'Masa Penyelesaian Pekerjaan (Lama)',
                    ),
                    array(
                        'field'	=> 	array('jwpp_start', 'jwpp_end'),
                        'type'	=>	'date_range',
                        'label'	=>	'Masa Penyelesaian Pekerjaan (Baru)',
                    ), array(
                        'field'	=> 	'desc_lama',
                        'type'	=>	'textarea',
                        'label'	=>	'Keterangan (Lama)',
                    ), array(
                        'field'	=> 	'desc',
                        'type'	=>	'textarea',
                        'label'	=>	'Keterangan (Baru)',
                    ), array(
                        'field' => 'kak_lampiran_lama',
                        'type'  => 'file',
                        'label' => 'KAK Lampiran (Lama)',
                        'upload_path' => base_url('assets/lampiran/kak_lampiran/'),
                        'upload_url' => site_url('fp3/upload_lampiran'),
                        'allowed_types' => '*'
                    ), array(
                        'field' => 'kak_lampiran',
                        'type'  => 'file',
                        'label' => 'KAK Lampiran (Baru)',
                        'upload_path' => base_url('assets/lampiran/kak_lampiran/'),
                        'upload_url' => site_url('fp3/upload_lampiran'),
                        'allowed_types' => '*'
                    ),
                    array(
                        'field' => 'pr_lampiran_lama',
                        'type'  => 'file',
                        'label' => 'PR Lampiran (Lama)',
                        'upload_path' => base_url('assets/lampiran/pr_lampiran/'),
                        'upload_url' => site_url('fp3/upload_lampiran'),
                        'allowed_types' => '*'
                    ), array(
                        'field' => 'pr_lampiran',
                        'type'  => 'file',
                        'label' => 'PR Lampiran (Baru)',
                        'upload_path' => base_url('assets/lampiran/pr_lampiran/'),
                        'upload_url' => site_url('fp3/upload_lampiran'),
                        'allowed_types' => '*'
                    ), array(
                        'field'	=> 	'desc_batal',
                        'type'	=>	'textarea',
                        'label'	=>	'Justifikasi Batal',
                    ), array(
                        'field'	=> 	'fp3_type',
                        'type'	=>	'hidden',
                    )
                ),

                'successAlert' => 'Berhasil mengubah data!',
                'filter' => array(
                    array(
                        'field'	=> 	'a|status',
                        'type'	=>	'text',
                        'label'	=>	'Status'
                    ), array(
                        'field'	=> 	'a|id_fppbj',
                        'type'	=>	'dropdown',
                        'label'	=>	'Nama Pengadaan B/J',
                        'source' =>  $this->fp3->getFppbj(),
                        'rules' => 	'required',
                    ), array(
                        'field'	=> 	'a|nama_pengadaan',
                        'type'	=>	'text',
                        'label'	=>	'Nama Pengadaan',
                        'rules' => 	'required',
                    ), array(
                        'field'	=> 	'a|metode_pengadaan',
                        'type'	=>	'text',
                        'label'	=>	'Metode Pengadaan',
                        'rules' => 	'required',
                    ), array(
                        'field'	=> 	'a|jadwal_pengadaan',
                        'type'	=>	'dateTime',
                        'label'	=>	'Masa Penyelesaian Pekerjaan',
                        'rules' => 	'required',
                    )

                )
            );
        } else {
            $this->form = $this->form;
        }

		foreach ($this->form['form'] as $key => $element) {
			// For insert operation, we don't populate from existing data
			// Only set readonly properties for 'lama' (old) fields
			if ($this->form['form'][$key]['field'] == array('jwpp_start_lama', 'jwpp_end_lama')) {
				$this->form['form'][$key]['readonly'] = true;
			}
			if ($this->form['form'][$key]['field'] == 'no_pr_lama') {
				$this->form['form'][$key]['readonly'] = true;
			}
			if ($this->form['form'][$key]['field'] == 'metode_pengadaan_lama') {
				$this->form['form'][$key]['readonly'] = true;
			}
			if ($this->form['form'][$key]['field'] == 'idr_anggaran_lama') {
				$this->form['form'][$key]['readonly'] = true;
			}
			if ($this->form['form'][$key]['field'] == 'desc_lama') {
				$this->form['form'][$key]['readonly'] = true;
			}
			if ($this->form['form'][$key]['field'] == 'kak_lampiran_lama') {
				$this->form['form'][$key]['readonly'] = true;
			}
			if ($this->form['form'][$key]['field'] == 'pr_lampiran_lama') {
				$this->form['form'][$key]['readonly'] = true;
			}
		}

		$this->form['url'] = $this->insertUrl;
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Simpan',
			),
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}

	public function get_data_fppbj($id = "")
	{
		echo json_encode($this->fp3->get_data_fppbj($id));
	}

	public function approve($id, $param_)
	{
		$post = $this->input->post();

		$id_pic = isset($post['id_pic']) ? $post['id_pic'] : null;
		$table = "ms_fp3";
		$fp3 = $this->fp3->selectData($id);
		$fppbj = $this->fm->selectData($fp3['id_fppbj']);

		$pejabat_pengadaan_id = isset($post['pejabat_pengadaan_id']) ? $post['pejabat_pengadaan_id'] : null;
		$param_ = array('is_status' => 1, 'is_approved' => $param_, 'pejabat_pengadaan_id' => $pejabat_pengadaan_id);
		$data 	= $this->mm->approve($table, $id, $param_);

		$division = $this->get_email_division($this->session->userdata('admin')['id_division']);

		$to = '';
		foreach ($division as $key => $value) {
			$to .= ' ' . $value['email'];
		}

		$subject = 'FP3 telah disetujui';
		$message = $fppbj['nama_pengadaan'] . 'telah di approve oleh ' . $this->session->userdata('admin')['name'];

		$activity = $this->session->userdata('admin')['name'] . " menyetujui pengadaan : " . $fppbj['nama_pengadaan'];

		$fp3['id_pengadaan'] = $fp3['id_fppbj'];
		$fp3['approved_by']  = $this->session->userdata('admin')['id_user'];
		$tgl_approval = isset($post['tgl_approval']) ? $post['tgl_approval'] : date('Y-m-d');
		$fp3['pejabat_pengadaan_id'] = isset($post['pejabat_pengadaan_id']) ? $post['pejabat_pengadaan_id'] : null;
		$fp3['date']	= $tgl_approval . ' ' . date('H:i:s');

		unset($fp3['id_fppbj']);
		unset($fp3['nama_lama']);
		unset($fp3['metode_lama']);
		unset($fp3['jwpp_start_lama']);
		unset($fp3['jwpp_end_lama']);
		unset($fp3['jadwal_pengadaan']);
		unset($fp3['metode_name']);
		unset($fp3['desc_lama']);
		unset($fp3['kak_lama']);
		unset($fp3['no_pr_lama']);
		unset($fp3['pr_lama']);
		unset($fp3['id']);
		unset($fp3['pic_name']);

		$this->insertHistoryPengadaan($fppbj['id'], 'approval', $fp3);

		$this->activity_log($this->session->userdata('admin')['id_user'], $activity, $id);
		$this->send_mail($to, $subject, $message, $link);

		echo json_encode(array('status' => 'success'));
	}

	public function getSingleData($id = null)
	{
		$this->form = array(
			'form' => array(
				array(
					'field'	=> 	'status',
					'type'	=>	'fp3',
					'label'	=>	'FP3',
				), array(
					'field'	=> 	'nama_lama',
					'type'	=>	'text',
					'label'	=>	'Nama Pengadaan',
					'rules' => 	'',
				), array(
					'field'	=> 	'nama_pengadaan',
					'type'	=>	'text',
					'label'	=>	'Nama Pengadaan (Baru)',
				), array(
					'field'	=> 	'no_pr_lama',
					'type'	=>	'text',
					'label'	=>	'No PR (Lama)',
					'rules' => 	'',
				), array(
					'field'	=> 	'no_pr',
					'type'	=>	'text',
					'label'	=>	'No PR (Baru)',
				),
				array(
					'field'	=> 	'metode_lama',
					'type'	=>	'dropdown',
					'label'	=>	'Metode Pengadaan (Lama)',
					'source' =>	$this->mm->getProcMethod(),
				),
				array(
					'field'	=> 	'metode_pengadaan',
					'type'	=>	'dropdown',
					'label'	=>	'Metode Pengadaan (Baru)',
					'source' =>	$this->mm->getProcMethod(),
				),
				array(
					'field'	=> 	array('jwpp_start_lama', 'jwpp_end_lama'),
					'type'	=>	'date_range',
					'label'	=>	'Masa Penyelesaian Pekerjaan (Lama)',
				),
				array(
					'field'	=> 	array('jwpp_start', 'jwpp_end'),
					'type'	=>	'date_range',
					'label'	=>	'Masa Penyelesaian Pekerjaan (Baru)',
				), array(
					'field'	=> 	'desc_lama',
					'type'	=>	'textarea',
					'label'	=>	'Keterangan (Lama)',
				), array(
					'field'	=> 	'desc',
					'type'	=>	'textarea',
					'label'	=>	'Keterangan (Baru)',
				), array(
					'field' => 'kak_lama',
					'type'  => 'file',
					'label' => 'KAK Lampiran (Lama)',
					'upload_path' => base_url('assets/lampiran/fp3/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*',
					'rules' => 'required',
				), array(
					'field' => 'kak_lampiran',
					'type'  => 'file',
					'label' => 'KAK Lampiran (Baru)',
					'upload_path' => base_url('assets/lampiran/fp3/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*',
					'rules' => 'required',
				), array(
					'field' => 'pr_lama',
					'type'  => 'file',
					'label' => 'PR Lampiran (Lama)',
					'upload_path' => base_url('assets/lampiran/fp3/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*',
					'rules' => 'required',
				), array(
					'field' => 'pr_lampiran',
					'type'  => 'file',
					'label' => 'PR Lampiran (Baru)',
					'upload_path' => base_url('assets/lampiran/fp3/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*',
					'rules' => 'required',
				),
				array(
					'field'	=> 	'desc_batal',
					'type'	=>	'textarea',
					'label'	=>	'Keterangan Batal',
				),
				array(
					'field'	=> 	'tgl_approval',
					'type'	=>	'hidden',
					'label'	=>	''
				),
				array(
					'field'	=> 	'pic_name',
					'type'	=>	'text',
					'label'	=>	'PIC ',
				),
				array(
					'field' => 'pejabat_pengadaan_id',
					'type'	=> 'hidden',
					'label'	=> '',
				),
			)
		);
		$admin = $this->session->userdata('admin');

		$dataFP3 = $this->fp3->selectData($id);
		// $param_  = ($admin['id_role'] == 4) ? ($param_=1) : (($admin['id_role'] == 3) ? ($param_=2) : (($admin['id_role'] == 2) ? ($param_=3) : ''));
		// Initialize param_ with default value
		$param_ = 0;
		
		if ($admin['id_role'] == 4) {
			$param_ = 1;
		} elseif ($admin['id_role'] == 3) {
			$param_ = 2;
		} elseif ($admin['id_role'] == 2) {
			$param_ = 3;
		} elseif ($admin['id_role'] == 7 || $admin['id_role'] == 8 || $admin['id_role'] == 9) {
			$param_ = 4;
		}

		$this->form['url'] 		= site_url($this->approveURL . $id . '/' . $param_);
		$this->form['reject'] 	= site_url('fp3/reject/' . $id . '/' . $param_);
		if ($admin['id_role'] == 2 || $admin['id_role'] == 3 || $admin['id_role'] == 4 || $admin['id_role'] == 7 || $admin['id_role'] == 8 || $admin['id_role'] == 9) {
			$btn_setuju = array(
				array(
					'type' 	=> 'submit',
					// 'link'	=> $this->form['url'],
					'label' => '<i style="line-height:25px;" class="fas fa-thumbs-up"></i>&nbsp;Setujui Data'
				)
			);
			$btn_reject = array(
				array(
					'type' 	=> 'reject',
					'label' => '<i style="line-height:25px;" class="fas fa-thumbs-down"></i>&nbsp;Revisi Data'
				)
			);
			$btn_cancel = array(
				array(
					'type' => 'cancel',
					'label' => 'Tutup'
				)
			);
			
			//log_message('error', $admin['id_role']);
			console.log($dataFP3);

			if ($dataFP3['is_approved'] == 0 && $dataFP3['is_reject'] == 0 && $dataFP3["is_cancelled"] == 0 && $admin['id_role'] == 4) {
				$this->form['form'][16]['type'] = 'date';
				$this->form['form'][16]['label'] = 'Tanggal Approval';
				$this->form['form'][16]['value'] = date('Y-m-d');
				$this->form['button'] = array_merge($btn_setuju, $btn_reject, $btn_cancel);
			} else if ($dataFP3['is_approved'] == 1 && $dataFP3['is_reject'] == 0 && $dataFP3["is_cancelled"] == 0 && $admin['id_role'] == 3) {
				$this->form['form'][16]['type'] = 'date';
				$this->form['form'][16]['label'] = 'Tanggal Approval';
				$this->form['form'][16]['value'] = date('Y-m-d');
				$this->form['form'][18]['type'] = 'dropdown';
				$this->form['form'][18]['label'] = 'Pilih Pejabat Pengadaan';
				$this->form['form'][18]['source'] = $this->pm->pejabatPengadaan();	
				$this->form['button'] = array_merge($btn_setuju, $btn_reject, $btn_cancel);
			} else if ($dataFP3['is_approved'] == 2 && $dataFP3['is_reject'] == 0 && $dataFP3["is_cancelled"] == 0 && $admin['id_role'] == 2) {
				$this->form['form'][16]['type'] = 'date';
				$this->form['form'][16]['label'] = 'Tanggal Approval';
				$this->form['form'][16]['value'] = date('Y-m-d');
				$this->form['button'] = array_merge($btn_setuju, $btn_reject, $btn_cancel);
			} else if ($dataFP3['is_approved'] == 3 && $dataFP3['is_reject'] == 0 && $dataFP3["is_cancelled"] == 0 && $admin['id_role'] == 7) {
				// && ($dataFP3['metode_name'] == 'Penunjukan Langsung' || $dataFP3['metode_name'] == 'Pemilihan Langsung' || $dataFP3['metode_name'] == 'Pelelangan' || $dataFP3['metode_name'] == 'Pengadaan Langsung')))
				$this->form['form'][16]['type'] = 'date';
				$this->form['form'][16]['label'] = 'Tanggal Approval';
				$this->form['form'][16]['value'] = date('Y-m-d');
				$this->form['button'] = array_merge($btn_setuju, $btn_reject, $btn_cancel);
			} else if ($dataFP3['is_approved'] == 3 && $dataFP3['is_reject'] == 0 && $dataFP3["is_cancelled"] == 0 && $admin['id_role'] == 8 && ($dataFP3['idr_anggaran'] > '1000000000' && $dataFP3['idr_anggaran'] <= '10000000000') && ($dataFP3['metode_name'] == 'Penunjukan Langsung' || $dataFP3['metode_name'] == 'Pemilihan Langsung' || $dataFP3['metode_name'] == 'Pelelangan' || $dataFP3['metode_name'] == 'Pengadaan Langsung')) {
				$this->form['form'][16]['type'] = 'date';
				$this->form['form'][16]['label'] = 'Tanggal Approval';
				$this->form['form'][16]['value'] = date('Y-m-d');
				$this->form['button'] = array_merge($btn_setuju, $btn_reject, $btn_cancel);
			} else if ($dataFP3['is_approved'] == 3 && $dataFP3['is_reject'] == 0 && $dataFP3["is_cancelled"] == 0 && $admin['id_role'] == 9 && $dataFP3['idr_anggaran'] >= '10000000000' && ($dataFP3['metode_name'] == 'Penunjukan Langsung' || $dataFP3['metode_name'] == 'Pemilihan Langsung' || $dataFP3['metode_name'] == 'Pelelangan' || $dataFP3['metode_name'] == 'Pengadaan Langsung')) {
				$this->form['form'][16]['type'] = 'date';
				$this->form['form'][16]['label'] = 'Tanggal Approval';
				$this->form['form'][16]['value'] = date('Y-m-d');
				$this->form['button'] = array_merge($btn_setuju, $btn_reject, $btn_cancel);
			} else {
				$this->form['button'] = $btn_cancel;
			}
		} else {
			$push = array(
				array(
					'type' => 'cancel',
					'label' => 'Tutup'
				)
			);
			$this->form['button'] = $push;
		}
		$modelAlias = $this->modelAlias;
		$getData   = $this->$modelAlias->selectData($id);
		foreach ($this->form['form'] as $key => $value) {
			if ($key != 16 && $key != 18) {	
				$this->form['form'][$key]['readonly'] = true;
			}
			
			// Handle array fields properly
			if (is_array($value['field'])) {
				// Skip array fields for value assignment
				continue;
			}
			
			$getData[$value['field']] = isset($getData[$value['field']]) && $getData[$value['field']] ? $getData[$value['field']] : "-";
			$this->form['form'][$key]['value'] = $getData[$value['field']];
			$this->form['form'][16]['value'] = date('Y-m-d');

			if ($value['type'] == 'date_range' && is_array($value['field'])) {
				$_value = array();
				foreach ($value['field'] as $keyField => $rowField) {
					$_value[] = isset($getData[$rowField]) ? $getData[$rowField] : '';
				}
				$this->form['form'][$key]['value'] = $_value;
			}
			if ($value['type'] == 'dateperiod') {
				$dateperiod = json_decode($getData[$value['field']]);
				$this->form['form'][$key]['value'] = date('d M Y', strtotime($dateperiod->start)) . " sampai " . date('d M Y', strtotime($dateperiod->end));
			}
			if ($value['type'] == 'money') {
				$this->form['form'][$key]['value'] = number_format($getData[$value['field']]);
			}
			if ($value['type'] == 'currency') {
				$this->form['form'][$key]['value'] = number_format($getData[$value['field']], 2);
			}
			if ($value['type'] == 'money_asing') {
				$this->form['form'][$key]['value'][] = $getData[$value['field'][0]];
				$this->form['form'][$key]['value'][] = number_format($getData[$value['field'][1]]);
			}
		}
		echo json_encode($this->form);
	}

	public function index($id_division = "", $id_fppbj = "", $year = "")
	{
		// print_r("tes");
		// die();
		$division = $this->mm->getDiv_($id_division);
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('fp3'),
			'title' => 'FP3'
		));

		$data['id_division']	= $id_division;
		$data['id_fppbj']		= $id_fppbj;
		$data['year']		 	= $year;
		$data['id']				= $id_division; // Pass id_division as id for the JavaScript view

		$this->header = 'FP3 ' . $division['name'];
		$this->content = $this->load->view('fp3/list', $data, TRUE);
		$this->script = $this->load->view('fp3/list_js', $data, TRUE);
		parent::index();
	}

	public function save($data = null)
	{
		$modelAlias = $this->modelAlias;
		$save = $this->input->post();
		$fppbj = $this->$modelAlias->get_data_fppbj($save['id_fppbj']);

		if ($save['fp3_type'] == 'ubah') {
			if ($save['jwpp_start']) {
				$metods = ($save['metode_pengadaan']) ? $save['metode_pengadaan'] : $fppbj['metode_pengadaan'];
				if (!$this->check_avail_date($save['jwpp_start'], $metods)) {
					$form = [
						'jwpp_start' => 'Tanggal tidak sesuai'
					];
					echo json_encode(array('status' => 'error', 'form' => $form));
					die;
				}
			}
			if ($save['jwpp_end']) {
				if (!$this->check_end_date($save['jwpp_start'], $save['jwpp_end'])) {
					$form = [
						'jwpp_start' => 'Tanggal akhir tidak boleh kurang dari tanggal mulai'
					];
					echo json_encode(array('status' => 'error', 'form' => $form));
					die;
				}
			}
		}
		
		if ($this->validation()) {
			// print_r($save);die;
			$save['status'] 	 = 'ubah';
			$save['entry_stamp'] = timestamp();
			$save['idr_anggaran'] = str_replace(',', '', $save['idr_anggaran']);
			$save['is_status']	 = 1;
			unset($save['fp3']);

			if ($this->$modelAlias->edit_to_fp3($save)) {
				$by_division = $this->get_division($this->session->userdata('admin')['id_division']);
				$division = $this->get_email_division($this->session->userdata('admin')['id_division']);

				$to_ = '';
				foreach ($division as $key => $value) {
					$to_ .= $value['email'] . ' ,';
				}
				$to = substr($to_, substr($to_), -2);
				$subject = 'FP3 baru telah dibuat.';
				$message = $save['nama_pengadaan'] . ' telah di buat oleh ' . $by_division['name'];

				$activity = $this->session->userdata('admin')['id_division'] . " membuat FP3 dengan nama : " . $save['nama_pengadaan'];

				$this->activity_log($this->session->userdata('admin')['id_user'], $activity, $save['id_fppbj']);

				$this->send_mail($to, $subject, $message, $link);
				$data_note = array(
					'id_user' => $this->session->userdata('admin')['id_division'],
					'id_fppbj' => $save['id_fppbj'],
					'value' => 'FP3 dengan nama pengadaan ' . $save['nama_pengadaan'] . ' telah di buat oleh ' . $by_division['name'],
					'entry_stamp' => date('Y-m-d H:i:s'),
					'is_active' => 1
				);
				$this->db->insert('tr_note', $data_note);
				$this->session->set_flashdata('msg', $this->successMessage);
				$this->deleteTemp($save);
				// echo json_encode(array('status'=>'success'));
			}
		}
	}

	public function aktifkan($id)
	{
		if ($this->fp3->updateStatus($id, 1)) {
			$return['status'] = 'success';
		} else {
			$return['status'] = 'error';
		}
		echo json_encode($return);
	}

	public function updateAktifkan($id)
	{
		$this->formDelete['url'] = site_url('fp3/aktifkan/' . $id);
		$this->formDelete['button'] = array(
			array(
				'type' => 'delete',
				'label' => 'Aktifkan'
			),
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->formDelete);
	}

	public function batalkan($id)
	{
		if ($this->fp3->updateStatus($id, 2)) {
			$return['status'] = 'success';
		} else {
			$return['status'] = 'error';
		}
		echo json_encode($return);
	}

	public function updateBatalkan($id)
	{
		$this->formDelete['url'] = site_url('fp3/batalkan/' . $id);
		$this->formDelete['button'] = array(
			array(
				'type' => 'delete',
				'label' => 'Batalkan'
			), array(
				'type' => 'cancel',
				'label' => 'Cancel'
			)
		);
		echo json_encode($this->formDelete);
	}

	public function approve_($id, $param_)
	{
		$table = "ms_fp3";
		// print_r($table);
		$param_ = array('is_status' => 1, 'is_approved' => $param_);
		$data 	= $this->mm->approve($table, $id, $param_);
		redirect($_SERVER['HTTP_REFERER']);
		return $data;
	}

	public function edit($id = null)
	{

		$this->form = array(
			'form' => array(
				array(
					'field'	=> 	'fp3',
					'type'	=>	'fp3',
					'label'	=>	'FP3',
				),
				array(
					'field'	=> 	'nama_lama',
					'type'	=>	'text',
					'label'	=>	'Nama Pengadaan',
				),
				array(
					'field'	=> 	'nama_pengadaan',
					'type'	=>	'text',
					'label'	=>	'Nama Pengadaan (Baru)',
				), array(
					'field'	=> 	'metode_pengadaan',
					'type'	=>	'dropdown',
					'label'	=>	'Metode Pengadaan (Baru)',
					'source' =>	$this->mm->getProcMethod(),
				), array(
					'field'	=> 	array('jwpp_start', 'jwpp_end'),
					'type'	=>	'date_range',
					'label'	=>	'Masa Penyelesaian Pekerjaan (Baru)',
				), array(
					'field'	=> 	'desc',
					'type'	=>	'textarea',
					'label'	=>	'Keterangan',
				), array(
					'field' => 'kak_lampiran',
					'type'  => 'file',
					'label' => 'KAK Lampiran',
					'upload_path' => base_url('assets/lampiran/kak_lampiran/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*',
					'rules' => 'required',
				), array(
					'field' => 'pr_lampiran',
					'type'  => 'file',
					'label' => 'PR Lampiran',
					'upload_path' => base_url('assets/lampiran/pr_lampiran/'),
					'upload_url' => site_url('fp3/upload_lampiran'),
					'allowed_types' => '*',
					'rules' => 'required',
				), array(
					'field'	=> 	'desc_batal',
					'type'	=>	'textarea',
					'label'	=>	'Keterangan Batal',
				), array(
					'field'	=> 	'status',
					'type'	=>	'hidden',
				),
			),
		);

		$modelAlias = $this->modelAlias;
		$data = $this->$modelAlias->selectData($id);
		
		foreach ($this->form['form'] as $key => $element) {
			$this->form['form'][$key]['value'] = $data[$element['field']];
			if ($this->form['form'][$key]['field'] == 'nama_lama') {
				$this->form['form'][$key]['readonly'] = true;
			}
			if ($this->form['form'][$key]['type'] == 'dateperiod') {
				$dateperiod = json_decode($getData[$value['field']]);
				$this->form['form'][$key]['value'] = date('d M Y', strtotime($dateperiod->start)) . " sampai " . date('d M Y', strtotime($dateperiod->end));
			}
			if ($this->form['form'][$key]['type'] == 'date_range') {
				$_value = array();

				foreach ($this->form['form'][$key]['field'] as $keys => $values) {
					$_value[] = $data[$values];
				}
				$this->form['form'][$key]['value'] = $_value;
			}
		}

		$this->form['url'] = site_url($this->updateUrl . '/' . $id);
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Ubah'
			),
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}

	//dicek approve sudah sampai terakhir belum, jika belum maka tidak boleh edit
	public function update($id)
	{
		$modelAlias = $this->modelAlias;
		
		// if ($this->validation()) {
		$save = $this->input->post();
		$lastData = $this->$modelAlias->selectData($id);

		$activity = $this->session->userdata('admin')['id_division'] . " mengubah data : " . $save['nama_pengadaan'];

		$this->activity_log($this->session->userdata('admin')['id_user'], $activity, $id);

		if ($lastData['is_reject'] == 1) {
			$save['is_reject'] = 0;
			$save['is_approved'] = $lastData['is_approved'] - 1;

			$up = array(
				'is_reject' 	=> 0,
				'is_approved'	=> $lastData['is_approved'] - 1
			);

			$this->db->where('id', $lastData['id_fppbj'])->where('del', 0)->update('ms_fppbj', $up);
		} else {
			$save['is_approved'] = 0;
			$save['is_reject'] 	 = 0;

			$up = array(
				'is_reject' 	=> 0,
				'is_approved'	=> 0
			);

			$this->db->where('id', $lastData['id_fppbj'])->where('del', 0)->update('ms_fppbj', $up);
		}

		if ($this->$modelAlias->update($id, $save)) {
			$save['is_status'] = 1;
			$this->insertHistoryPengadaan($lastData['id_fppbj'], 'perubahan', $save);
			$this->session->set_userdata('alert', $this->form['successAlert']);
			$this->deleteTemp($save, $lastData);
			echo json_encode(array('status' => 'success'));
		}
		// }
	}

	public function form_download_fp3($id)
	{
		$this->form = array(
			'form' => array(
				array(
					'field' => 'to',
					'type' => 'text',
					'label' => 'Kepada',
				),
				array(
					'field' => 'pb',
					'type' => 'text',
					'label' => 'Pusat Biaya',
				),
				array(
					'field' => 'no',
					'type' => 'text',
					'label' => 'Nomor',
				),
				array(
					'field' => 'date',
					'type' => 'date',
					'label' => 'Tanggal',
				),
				array(
					'field' => 'kadep_',
					'type' => 'text',
					'label' => 'Kolom TTD - Dept/Div',
				),
				array(
					'field' => 'kadep',
					'type' => 'text',
					'label' => 'Kolom TTD - Nama (min. setingkat Ka. Dept)',
				),
				array(
					'field' => 'kadiv_',
					'type' => 'text',
					'label' => 'Kolom TTD - Div/Dirut',
				),
				array(
					'field' => 'kadiv',
					'type' => 'text',
					'label' => 'Kolom TTD - Nama (min. setingkat Ka. Divisi atau Direktur Utama
					untuk fungsi leher)',
				)
			)
		);

		$this->form['url'] = site_url('export/fp3/' . $id);
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Download',
			),
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}

	public function reject($id, $param_)
	{
		$post = $this->input->post();
		$id_pic = $post['id_pic'];
		$table = "ms_fp3";
		$fp3 = $this->fp3->selectData($id);
		$fppbj = $this->fm->selectData($fp3['id_fppbj']);

		$save = $this->input->post();
		$save_keterangan['id_fppbj'] 	= $fp3['id_fppbj'];
		$save_keterangan['id_user'] 	= $fppbj['id_division'];
		$save_keterangan['is_active']   = 1;
		$save_keterangan['value']	    = $save['keterangan'];
		$save_keterangan['entry_stamp'] = date('Y-m-d H:i:s');
		$save_keterangan['type']	    = 'reject';

		$this->fm->insert_keterangan($save_keterangan);
		$param_ = array('is_status' => 1, 'is_reject' => 1, 'is_approved' => $param_);
		$data 	= $this->mm->approve($table, $id, $param_);

		$division = $this->get_email_division($this->session->userdata('admin')['id_division']);

		$to = '';
		foreach ($division as $key => $value) {
			$to .= ' ' . $value['email'];
		}

		$subject = $fppbj['nama_pengadaan'] . ' di revisi oleh ' . $this->session->userdata('admin')['name'];
		$message = $save['keterangan'];

		$activity = $this->session->userdata('admin')['name'] . " menolak pengadaan : " . $fppbj['nama_pengadaan'];

		$fp3['approved_by']  = $this->session->userdata('admin')['id_user'];
		$tgl_approval = $post['tgl_approval'];
		$fp3['date']	= $tgl_approval . ' ' . date('H:i:s');
		$fp3['desc_reject']  = $save['keterangan'];

		unset($fp3['id_fppbj']);
		unset($fp3['nama_lama']);
		unset($fp3['metode_lama']);
		unset($fp3['jwpp_start_lama']);
		unset($fp3['jwpp_end_lama']);
		unset($fp3['jadwal_pengadaan']);
		unset($fp3['metode_name']);
		unset($fp3['desc_lama']);
		unset($fp3['kak_lama']);
		unset($fp3['no_pr_lama']);
		unset($fp3['pr_lama']);
		unset($fp3['id']);
		unset($fp3['pic_name']);

		$this->insertHistoryPengadaan($fppbj['id'], 'reject', $fp3);

		$this->activity_log($this->session->userdata('admin')['id_user'], $activity, $id);
		$this->send_mail($to, $subject, $message, $link);

		redirect($_SERVER['HTTP_REFERER']);
		return $data;
	}
}
