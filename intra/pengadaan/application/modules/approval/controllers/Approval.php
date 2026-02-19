<?php defined('BASEPATH') or exit('No direct script access allowed');

class Approval extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('admin')) {
			redirect(site_url());
		}
		$this->load->model('vendor/vendor_model', 'apprm');
		$this->load->library('data_process');
		$this->load->library('email');
		require_once(BASEPATH . "plugins/dompdf2/dompdf_config.inc.php");
	}

	public function administrasi($id)
	{
		$this->load->model('vendor/vendor_model', 'vm');

		$data = $this->vm->get_data($id);
		if (!is_array($data)) {
			$data = array();
		}
		if (!array_key_exists('data_status', $data)) {
			$data['data_status'] = '';
		}
		$vld = 	array(
			array(
				'field' => 'status',
				'label' => 'Status',
				'rules' => 'required'
			)
		);

		$this->form_validation->set_rules($vld);

		if ($this->input->post('simpan')) {
			if ($this->form_validation->run() == TRUE) {
				$result = $this->data_process->check($id, $this->input->post(), $data['id'], 'ms_vendor_admistrasi');
				if ($result) {
					$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
					redirect(site_url('approval/administrasi/' . $id));
				}
			}
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('administrasi', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function akta($id, $akta_type = 'pendirian')
	{
		$this->load->model('akta/akta_model', 'am');

		$data['akta_list'] = $this->am->get_akta_admin_list($id, $akta_type);

		if ($this->input->post('simpan')) {
			$akta_data = $this->input->post('akta');
			if (is_array($akta_data)) {
				foreach ($akta_data as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_akta');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/akta/' . $id . '/' . $akta_type));
		}
		$data['id_data'] = $id;

		if ($akta_type == 'pendirian') {
			$view = 'akta/akta_pendirian';
		} else {
			$view = 'akta/akta_perubahan';
		}
		$layout['content'] =  $this->load->view($view, $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}
	
	public function situ($id)
	{
		$this->load->model('situ/situ_model', 'sm');

		$data['situ_list'] = $this->sm->get_situ_admin_list($id);

		if ($this->input->post('simpan')) {
			$situ = $this->input->post('situ');
			if (!empty($situ) && is_array($situ)) {
				foreach ($situ as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_situ');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/situ/' . $id));
		}
		$data['id_data'] = $id;

		$layout['content'] =  $this->load->view('situ', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function tdp($id)
	{
		$this->load->model('tdp/tdp_model', 'sm');

		$data['tdp_list'] = $this->sm->get_tdp_admin_list($id);

		if ($this->input->post('simpan')) {
			$tdp_data = $this->input->post('tdp');
			if (is_array($tdp_data)) {
				foreach ($tdp_data as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_tdp');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/tdp/' . $id));
		}
		$data['id_data'] = $id;

		$layout['content'] =  $this->load->view('tdp', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function pengurus($id)
	{
		$this->load->model('pengurus/pengurus_model', 'pm');

		$data['pengurus_list'] = $this->pm->get_pengurus_admin_list($id);

		if ($this->input->post('simpan')) {
			$pengurus_data = $this->input->post('pengurus');
			if (is_array($pengurus_data)) {
				foreach ($pengurus_data as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_pengurus');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/pengurus/' . $id));
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('pengurus', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function pemilik($id)
	{
		$this->load->model('pemilik/pemilik_model', 'pm');

		$data['pemilik_list'] = $this->pm->get_pemilik_admin_list($id);
		$data['ubo_file'] = $this->pm->get_ubo($id);
		//print_r($this->pm->get_ubo($id));die;

		if ($this->input->post('simpan')) {
			$pemilik = $this->input->post('pemilik');
			if (!empty($pemilik) && is_array($pemilik)) {
				foreach ($pemilik as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_pemilik');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/pemilik/' . $id));
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('pemilik', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function badan_usaha($id, $surat = 'siup')
	{
		$this->load->model('izin/izin_model', 'im');
		$this->load->model('approval_model', 'am');

		$data['dt_siu'] = array('siup' => 'SIUP', 'ijin_lain' => 'Surat Izin Usaha Lainnya', 'asosiasi' => 'Sertifikat Asosiasi/Lainnya', 'siujk' => 'SIUJK', 'sbu' => 'SBU');
		$data['dt_dpt'] = $this->am->get_dpt_type();
		$data['surat'] = $surat;

		$table = array(
			'siup' => array(
				'dpt'	=>	'DPT Tipe',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'qualification' => 'Kualifikasi',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			),
			'ijin_lain' => array(
				'dpt'	=>	'DPT Tipe',
				'authorize_by'	=>	'Lembaga Penerbit',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'qualification' => 'Kualifikasi',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			),
			'asosiasi' => array(
				'dpt'	=>	'DPT Tipe',
				'authorize_by'	=>	'Lembaga Penerbit',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			),
			'sbu' => array(
				'dpt'	=>	'DPT Tipe',
				'authorize_by'	=>	'Anggota Asosiasi',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			),
			'siujk' => array(
				'dpt'	=>	'DPT Tipe',
				'authorize_by'	=>	'Lembaga Penerbit',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'qualification' => 'Kualifikasi',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			)
		);

		if ($this->input->post('simpan')) {

			$ijin_usaha = $this->input->post('ijin_usaha');
			if (!empty($ijin_usaha) && is_array($ijin_usaha)) {
				foreach ($ijin_usaha as $key => $value) {
					$a = $this->data_process->check($id, $value, $key, 'ms_ijin_usaha');
					if ($a) {
						$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
					}
				}
			}
			redirect(site_url('approval/badan_usaha/' . $id . '/' . $surat));
		}
		$data['table'] = $table;
		$data['id_data'] = $id;
		$data['izin_list'] = $this->im->get_izin_admin_list($id, $surat);

		$layout['content'] =  $this->load->view('izin', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function bsb($id_data, $id)
	{
		$this->load->model('izin/izin_model', 'im');

		$data['bsb_list'] = $this->im->get_bsb_admin_list($id);
		$data['get_data'] = $this->im->get_data($id);
		$data['dt_siu'] = array('siup' => 'SIUP', 'ijin_lain' => 'Surat Izin Usaha Lainnya', 'asosiasi' => 'Sertifikat Asosiasi/Lainnya', 'siujk' => 'SIUJK', 'sbu' => 'SBU');

		$table = array(
			'siup' => array(
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'qualification' => 'Kualifikasi',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			),
			'ijin_lain' => array(
				'authorize_by'	=>	'Lembaga Penerbit',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'qualification' => 'Kualifikasi',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			),
			'asosiasi' => array(
				'authorize_by'	=>	'Lembaga Penerbit',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			),
			'sbu' => array(
				'authorize_by'	=>	'Anggota Asosiasi',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			),
			'siujk' => array(
				'authorize_by'	=>	'Lembaga Penerbit',
				'no'	=>	'No.',
				'issue_date' => 'Tanggal',
				'qualification' => 'Kualifikasi',
				'expire_date' => 'Masa Berlaku',
				'izin_file' => 'Lampiran',
			)
		);

		if ($this->input->post('simpan')) {
			$bsb = $this->input->post('bsb');
			if (!empty($bsb) && is_array($bsb)) {
				foreach ($bsb as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_iu_bsb');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/bsb/' . $id_data . '/' . $id));
		}
		$data['id_data'] = $id_data;
		$data['table'] = $table;
		$layout['content'] =  $this->load->view('bsb', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function agen($id)
	{
		$this->load->model('agen/agen_model', 'am');

		$data['agen_list'] = $this->am->get_agen_admin_list($id);

		if ($this->input->post('simpan')) {
			$agen = $this->input->post('agen');
			if (!empty($agen) && is_array($agen)) {
				foreach ($agen as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_agen');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/agen/' . $id));
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('agen', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}
	public function produk($id_data, $id)
	{
		$this->load->model('agen/agen_model', 'am');
		$data = $this->am->get_data($id);
		$data['produk_list'] = $this->am->get_produk_admin_list($id_data, $id);


		if ($this->input->post('simpan')) {
			$produk = $this->input->post('produk');
			if (!empty($produk) && is_array($produk)) {
				foreach ($produk as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_agen_produk');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/produk/' . $id_data . '/' . $id));
		}
		$data['id_data'] = $id_data;
		$layout['content'] =  $this->load->view('produk', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function pengalaman($id)
	{
		$this->load->model('pengalaman/pengalaman_model', 'pm');

		$data['pengalaman_list'] = $this->pm->get_pengalaman_admin_list($id);

		if ($this->input->post('simpan')) {
			$pengalaman = $this->input->post('pengalaman');
			if (!empty($pengalaman) && is_array($pengalaman)) {
				foreach ($pengalaman as $key => $value) {
					$this->data_process->check($id, $value, $key, 'ms_pengalaman');
				}
			}
			$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('approval/pengalaman/' . $id));
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('pengalaman', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}
	public function pengalaman_detail($id_data, $id)
	{
		$this->load->model('pengalaman/pengalaman_model', 'pm');

		$data = $this->pm->get_data_full($id_data, $id);

		$data['id_data'] = $id_data;
		$data['id_pengalaman'] = $id;
		$layout['content'] =  $this->load->view('pengalaman_detail', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id_data);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}
	public function k3($id)
	{
		$this->load->model('approval_model', 'appm');
		$this->load->model('k3/k3_model', 'km');
		$data = $this->vm->get_data($id);
		if (!is_array($data)) {
			$data = array();
		}
		$data['get_csms'] = $this->km->get_csms($id);
		if (!is_array($data['get_csms'])) {
			$data['get_csms'] = array();
		}
		if (!array_key_exists('legal_name', $data)) {
			$data['legal_name'] = '';
		}
		if (!array_key_exists('name', $data)) {
			$data['name'] = '';
		}
		$data['id_data'] = $id;

		if (!isset($data['get_csms'])) {
			$data['ms_quest'] = $this->km->get_master_header();
			// $data['quest']=$this->km->get_quest();
			$data['field_quest'] = $this->km->get_field_quest();
			$data['evaluasi_list'] = $this->km->get_evaluasi_list();
			// $data['evaluasi'] = $this->km->get_evaluasi();


			$data['data_k3'] = $this->km->get_k3_data($id);
			// $data['csms_limit']=$this->km->get_csms_limit($id);
			$data['score_k3'] = $this->km->get_poin($id);
		}

		$data['data_poin'] = $this->km->get_poin($id);
		$k3_data = $this->km->get_k3_all_data($id);
		$data['csms_file'] 	= isset($k3_data['csms_file']) ? $k3_data['csms_file'] : array();

		$csms_file_id = isset($data['csms_file']['id']) ? $data['csms_file']['id'] : 0;
		$data['value_k3'] = $this->km->get_penilaian_value($id, $csms_file_id);


		$layout['content'] = $this->load->view('k3', $data, TRUE);
		$vld = 	array(
			array(
				'field' => 'status',
				'label' => 'Status',
				'rules' => 'required'
			)
		);

		$this->form_validation->set_rules($vld);
		if ($this->input->post('simpan')) {
			if ($this->form_validation->run() == TRUE) {
				$_POST['mandatory'] = 1;
				$result = $this->data_process->check($id, $this->input->post(), $id, 'ms_csms', 'id_vendor');

				if ($result) {
					if ($_POST['status'] && $_POST['mandatory']) {
						$array = $this->appm->set_expiry($id);
						$this->dpt->set_email_blast($result, 'ms_csms', 'Lampiran CSMS', $array['expiry_date']);
					}

					$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses menyimpan data!</p>');
					redirect(site_url('approval/k3/' . $id));
				}
			}
		}

		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);



		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}


	public function verification($id)
	{
		$this->load->model('approval_model', 'am');
		$data = $this->vm->get_vendor_name($id);
		if (!is_array($data)) {
			$data = array();
		}
		$admin = $this->session->userdata('admin');

		$data['approval_data'] = $this->am->get_total_data($id);
		$data['spv']	= $this->am->get_spv_mail(8);
		// print_r($data['spv']);die;
		if (!is_array($data['approval_data'])) {
			$data['approval_data'] = array('total' => 0, 0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array());
		}
		if (!isset($data['approval_data']['total']) || (int) $data['approval_data']['total'] <= 0) {
			$data['approval_data']['total'] = 1;
		}
		$data['approval_data'][0] = isset($data['approval_data'][0]) && is_array($data['approval_data'][0]) ? $data['approval_data'][0] : array();
		$data['approval_data'][1] = isset($data['approval_data'][1]) && is_array($data['approval_data'][1]) ? $data['approval_data'][1] : array();
		$data['approval_data'][2] = isset($data['approval_data'][2]) && is_array($data['approval_data'][2]) ? $data['approval_data'][2] : array();
		$data['approval_data'][3] = isset($data['approval_data'][3]) && is_array($data['approval_data'][3]) ? $data['approval_data'][3] : array();
		$data['approval_data'][4] = isset($data['approval_data'][4]) && is_array($data['approval_data'][4]) ? $data['approval_data'][4] : array();
		if (!array_key_exists('need_approve', $data)) {
			$data['need_approve'] = 0;
		}
		if (!array_key_exists('certificate_no', $data)) {
			$data['certificate_no'] = '';
		}
		$data['graphBar'] = array(
			0 =>
			array(
				'val' => (count($data['approval_data'][0])) / $data['approval_data']['total'] * 100,
				'color' => '#f39c12'
			),
			1 =>
			array(
				'val' => (count($data['approval_data'][1]) + count($data['approval_data'][2])) / $data['approval_data']['total'] * 100,
				'color' => '#2cc36b'
			),
			3 => array(
				'val' => (count($data['approval_data'][3]) + count($data['approval_data'][4])) / $data['approval_data']['total'] * 100,
				'color' => '#c0392b'
			),
		);

		$email['dpt'] 		= $this->vm->get_dpt_mail($id);
		if (!is_array($email['dpt']) || empty($email['dpt'])) {
			$email['dpt'] = $this->vm->get_data($id);
			if (!is_array($email['dpt']) || empty($email['dpt'])) {
				$email['dpt'] = array();
			}
			if (!isset($email['dpt']['email']) && isset($email['dpt']['vendor_email'])) {
				$email['dpt']['email'] = $email['dpt']['vendor_email'];
			}
		}
		if (!isset($email['dpt']['legal_name'])) {
			$email['dpt']['legal_name'] = '';
		}
		if (!isset($email['dpt']['name'])) {
			$email['dpt']['name'] = '';
		}
		$email['msg'] 		=
			$email['dpt']['legal_name'] . ". " . $email['dpt']['name'] . " telah masuk kedalam daftar tunggu di Sistem Aplikasi Kelogistikan PT Nusantara Regas.<br/>
						Untuk selanjutnya, silahkan memeriksa kembali kelengkapan data - data calon DPT di aplikasi.<br/><br/>
						Terima kasih.<br/>
						PT Nusantara Regas'";
		$email['subject']	= "Daftar Tunggu DPT - Sistem Aplikasi Kelogistikan PT Nusantara Regas";


		if ($this->input->post('simpan')) {
			/// if ($admin['id_role'] == 1) {
				#email SPV
				foreach ($data['spv'] as $keyspv => $valuespv) {
					$this->utility->mail($valuespv['email'], $email['msg'], $email['subject']);
				}

				if (count($data['approval_data'][0]) > 0 || count($data['approval_data'][3]) > 0) {
					$this->session->set_flashdata('msgError', '<p class="errorMsg">Tidak Bisa diangkat menjadi vendor. Ada beberapa data yang harus diperbaiki</p>');
				} else {
					$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Data terkirim untuk disetujui!</p>');

					$data['approval_data'] = $this->am->angkat_vendor($id);

					redirect(site_url('approval/verification/' . $id));
				}
			/*} else {
				$msg =  $email['dpt']['legal_name'] . ". " . $email['dpt']['name'] . " telah di lakukan pengecekan data di Sistem Aplikasi Kelogistikan PT Nusantara Regas.<br/>
						Untuk selanjutnya, silahkan memeriksa kembali kelengkapan data - data calon DPT di aplikasi.<br/><br/>
						Terima kasih.<br/>
						PT Nusantara Regas'";
				$superadmin = $this->am->get_spv_mail(1);

				foreach ($superadmin as $keyspv => $valuespv) {
					$this->utility->mail($valuespv['email'], $msg, $email['subject']);
				}
				$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Data terkirim untuk dicek oleh superadmin!</p>');

				redirect(site_url('approval/verification/' . $id));
			}*/
		}

		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('verifikasi', $data, TRUE);
		$layout['script'] =  $this->load->view('verifikasi_js', $data, TRUE);
		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$this->load->model('approval_model', 'appm');

		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);

		$this->load->view('template', $item);
	}

	public function approve($id)
	{
		// echo "string kesini";die;
		$this->load->model('approval_model', 'am');
		$this->load->model('vendor/vendor_model', 'vm');

		// $res = $this->am->approve($id);
		$email['dpt'] 		= $this->vm->get_dpt_mail($id);
		$email['adm'] 		= $this->vm->get_adm_mail($this->session->userdata('admin')['id_role']);
		$email['msg'] 		= "
								Kepada <i>" . $email['dpt']['legal_name'] . ". " . $email['dpt']['name'] . ".</i>
									<br/><br/>
								Dengan ini kami sampaikan bahwa perusahaan saudara telah diangkat menjadi DPT(Daftar Penyedia Barang/Jasa Terdaftar) di PT Nusantara Regas.
									<br/><br/><br/>
								Terima kasih,
									<br/>
								PT Nusantara Regas.";
		$email['subject']	= "Pemberitahuan Pengangkatan DPT - Sistem Aplikasi Kelogistikan PT Nusantara Regas";
		$email['to'] = array();
		$email['to'][] = $email['dpt']['email'];
		foreach ($email['adm'] as $keyadm => $valueadm) {
			$email['to'][] = $valueadm['email'];
		}

		$this->am->approve($id);
		foreach ($email['to'] as $keyto => $valueto) {
			$this->utility->mail($valueto, $email['msg'], $email['subject']);
		}

		$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Vendor telah diangkat menjadi DPT!</p>');
		redirect('admin/admin_vendor/waiting_list/1');
	}

	function mail($to, $message)
	{
		$this->email->clear(TRUE);

		$this->email->from('vms-noreply@nusantararegas.com', 'VMS REGAS');
		$this->email->to('arinal.dzikrul@dekodr.co.id');

		// $this->email->cc('alicia.ordinary@gmail.com'); 
		$this->email->bcc('muarifgustiar@gmail.com');
		$this->email->subject('Sistem Aplikasi Kelogistikan PT Nusantara Regas');

		$this->email->message($message);
		$this->email->send();

		echo $this->email->print_debugger();
	}

	function mail_test()
	{

		$to = base64_encode("arinaldha@gmail.com");
		$sub = base64_encode("Blablab");
		$message = base64_encode("askjdgjasgdjkadsgjkasgfkjadsgjhagfjkagdsk");

		$url = "http://dekodr.co.id/send_mail/send.php";
		$data = http_build_query(array(
			'to' => $to,
			'sub' => $sub,
			'message' => $message
		));

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded",
				'method'  => 'POST',
				'content' => $data,
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result)
			redirect('dashboard');
		else
			echo '<script>alert("Gagal Mengirim Email!");location.reload();</script>';
	}
	
	public function download_surat_pernyataan($id_vendor)
	{
		$this->load->model('administrasi/administrasi_model', 'adm');
		$pic = $this->adm->get_pic($id_vendor);
		
		$a = "<html>
				<head>
					<title></title>
				</head>
				<body>
					<p><b><h4 style='text-align:center;'>SURAT PERNYATAAN</h4></b></p>
					<p><h3>Saya yang bertanda tangan dibawah ini :</h3></p>
					<p>Nama : " . $pic['pic_name'] . "</p>
					<p>Jabatan : " . $pic['pic_position'] . "</p>
					<p>Mewakili " . $pic['legal'] . " " . $pic['vendor'] . "</p>
					<p><b><h3>Dengan ini menyatakan :</h3></b></p>
					<p>
						<ul>
							<li>Saya secara hukum mempunyai kapasitas untuk menandatangani kontrak (sesuai akta pendirian/perubahannya/surat kuasa);</li>
 <li>Saya / Perusahaan mengikuti kegiatan di PT Nusantara Regas berdasarkan prinsip itikad baik dan tidak dalam pengaruh atau mempengaruhi pihak yang berkepentingan.</li>
 <li>Saya atas nama Perusahaan menyatakan tidak akan melakukan tindakan suap (termasuk konflik kepentingan), penipuan ataupun KKN sesuai ketentuan dan perundang-undangan yang berlaku.</li>
							<li>Saya/perusahaan saya tidak sedang dalam pengawasan pengadilan atau tidak sedang dinyatakan pailit atau kegiatan usahanya tidak sedang dihentikan atau tidak sedang menjalani hukuman (sanksi) pidana;</li>
							<li>Saya tidak pernah dihukum berdasarkan putusan pengadilan atas tindakan yang berkaitan dengan kondite profesional saya;</li>
							<li>Perusahaan saya memiliki kinerja baik dan tidak termasuk dalam kelompok yang terkena sanksi atau daftar hitam di PT Nusantara Regas maupun di instansi lainnya, dan tidak dalam sengketa dengan PT Nusantara Regas;</li>
							<li>Informasi/dokumen/formulir yang akan saya sampaikan adalah benar dan dapat dipertanggung jawabkan secara hukum.</li>
							<li>Segala dokumen dan formulir yang disampaikan / isi adalah benar.</li>
							<li>
								Apabila dikemudian hari, ditemui bahwa dokumen dokumen dan formulir yang telah kami berikan tidak benar/palsu, maka kami bersedia dikenakan sanksi sebagai berikut:
								<ul>
									<li>Administrasi tidak diikutsertakan dalam setiap Pengadaan Barang dan Jasa PT Nusantara Regas selama 2 (dua) tahun</li>
									<li>Penawaran kami digugurkan</li>
									<li>Dibatalkan sebagai pemenang pengadaan</li>
									<li>Dituntut ganti rugi atau digugat secara perdata</li>
									<li>Dilaporkan kepada pihak yang berwajib untuk diproses secara pidana.</li>
								</ul>
							</li>
						</ul>
					</p>
					<p>Demikian pernyataan ini dibuat.</p>
					<p>" . $pic['pic_name'] . "</p>
					<p>" . $pic['pic_position'] . "</p>
					<br>
					<p><b>Dicetak dengan sistem aplikasi kelogistikan PT Nusantara Regas, Dokumen ini resmi tanpa stempel dan/atau tanda tangan pejabat.</b></p>
				</body>
			</html>";
		// echo $a;
		$dompdf = new DOMPDF();
		$dompdf->load_html($a);
		$dompdf->set_paper('A4', 'potrait');
		$dompdf->render();

		$dompdf->stream("Surat Pernyataan.pdf", array('Attachment' => 1));
	}

	function mailer_test()
	{
		return $this->utility->mail('arinaldha@gmail.com', 'Testing', 'Testing');
	}
}
