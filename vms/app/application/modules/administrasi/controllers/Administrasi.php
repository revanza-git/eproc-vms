<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Administrasi extends CI_Controller {

	public function __construct(){
		parent::__construct();
        date_default_timezone_set('Asia/Bangkok');
		if(!$this->session->userdata('user')){
			redirect(site_url());
		}
		$this->load->model('vendor/vendor_model','vm');
		$this->load->model('administrasi_model', 'am');
		$this->load->library('encrypt');
		$this->load->library('utility');
		$this->load->library('form_validation');
		require_once(BASEPATH."plugins/dompdf2/dompdf_config.inc.php");
	}

    public function time_zone_test()
    {
        echo 'Default Timezone: ' . date('d-m-Y H:i:s') . '</br>';
        date_default_timezone_set('Asia/Jakarta');
        echo 'Indonesian Timezone: ' . date('d-m-Y H:i:s');
    }

	public function index()
	{	
		$data = $this->session->userdata('user');
		if($this->vm->check_pic($data['id_user'])==0){
			redirect(site_url('dashboard/pernyataan'));
		}
		$user = $this->session->userdata('user');
		$data = $this->vm->get_data($user['id_user']);

		$layout['content']= $this->load->view('view',$data,TRUE);

		$item['header'] 	= $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] 	= $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function edit(){
		$user = $this->session->userdata('user');
		$data = $this->vm->get_data($user['id_user']);
		$data['sbu'] = $this->vm->get_sbu();
		$data['legal'] = $this->vm->get_legal();
		$_POST = $this->securities->clean_input($_POST,'save');
		$user = $this->session->userdata('user');
		$vld = 	array(
			array(
				'field'=>'id_legal',
				'label'=>'Badan hukum',
				'rules'=>'required'
				),
			array(
				'field'=>'name',
				'label'=>'Nama Badan Usaha',
				'rules'=>''
				),
			array(
				'field'=>'npwp_code',
				'label'=>'NPWP',
				'rules'=>'required|is_valid_npwp'
				),
			/*array(
				'field'=>'npwp_date',
				'label'=>'Tanggal Pengukuhan',
				'rules'=>'required'
				),*/
			
			array(
				'field'=>'nppkp_code',
				'label'=>'SPPKP',
				'rules'=>'required'
				),
			/*array(
				'field'=>'nppkp_date',
				'label'=>'Tanggal Pengukuhan',
				'rules'=>'required'
				),*/
			
			array(
				'field'=>'vendor_office_status',
				'label'=>'Status',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_address',
				'label'=>'Alamat',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_country',
				'label'=>'Negara',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_province',
				'label'=>'Provinsi',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_city',
				'label'=>'Kota',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_postal',
				'label'=>'Kode Pos',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_fax',
				'label'=>'Fax',
				'rules'=>''
				),
			array(
				'field'=>'vendor_phone',
				'label'=>'No. Telp',
				'rules'=>'required'
				),
			array(
				'field'=>'vendor_email',
				'label'=>'Email',
				'rules'=>'required|valid_email'
				),
			array(
				'field'=>'vendor_website',
				'label'=>'Website',
				'rules'=>''
				),
			/*array(				
				'field'=>'npwp_file',
				'label'=>'NPWP',
				'rules'=>'callback_do_upload[npwp_file]'
				),
			array(				
				'field'=>'nppkp_file',
				'label'=>'NPWP',
				'rules'=>'callback_do_upload[nppkp_file]'
				)*/
			);
		
		if(!empty($_FILES['npwp_file']['name'])||!isset($data['npwp_file'])){
			$vld[] = array(
				'field'=>'npwp_file',
				'label'=>'NPWP',
				'rules'=>'callback_do_upload[npwp_file]'
				);
		}

		if(!empty($_FILES['nppkp_file']['name'])||!isset($data['nppkp_file'])){
			$vld[] = array(
				'field'=>'nppkp_file',
				'label'=>'NPPKP',
				'rules'=>'callback_do_upload[nppkp_file]'
				);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);
			
			$result = $this->vm->edit_data($this->input->post(),$user['id_user']);
			
			if($result){
				
				$this->dpt->non_iu_change($user['id_user']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('administrasi'));

			}
			
		}
		$layout['content']= $this->load->view('edit',$data,TRUE);

		$user = $this->session->userdata('user');
		$item['header'] = $this->load->view('dashboard/header',$user,TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function hapus($id){
		$user = $this->session->userdata('user');
		// Ambil data administrasi/vendor untuk mengetahui file lampiran
		$adm_data = $this->vm->get_data($user['id_user']);

		if($this->am->delete($id)){
			// Hapus file fisik terkait jika ada
			$attachments = array(
				'npwp_file'  => 'npwp_file',
				'nppkp_file' => 'nppkp_file'
			);

			foreach($attachments as $field => $dir){
				if(!empty($adm_data[$field])){
					$base = realpath(FCPATH.'../lampiran');
					if($base === FALSE){
						$base = FCPATH.'lampiran';
					}
					$path = rtrim($base,'/\\').'/'.$dir.'/'.$adm_data[$field];
					if(file_exists($path)){
						@unlink($path);
					}
				}
			}

			$this->dpt->non_iu_change($user['id_user']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data beserta lampirannya!</p>');
			redirect(site_url('administrasi'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('administrasi'));
		}
	}
	public function do_upload($field, $db_name = ''){	
		
		// Generate unique file name
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);

		// Resolve base lampiran directory (prefer one level above FCPATH)
		$base_lampiran_path = realpath(FCPATH.'../lampiran');
		if($base_lampiran_path === FALSE){
			$base_lampiran_path = FCPATH.'lampiran';
		}
		$base_lampiran_path = rtrim($base_lampiran_path, '/\\');

		// Ensure sub-directory exists
		$config['upload_path'] = $base_lampiran_path.'/'.$db_name.'/';
		if(!is_dir($config['upload_path'])){
			mkdir($config['upload_path'], 0755, true);
		}

		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif';
		$config['max_size']      = 0; // unlimited, rely on php.ini limits

		$this->load->library('upload');
		$this->upload->initialize($config);

		if(!$this->upload->do_upload($db_name)){
			$_POST[$db_name] = $file_name;
			$this->form_validation->set_message('do_upload', $this->upload->display_errors('',''));
			return false;
		}else{
			$_POST[$db_name] = $file_name;
			return true;
		}
	}

	public function download_surat_pernyataan()
	{
		$user = $this->session->userdata('user');
		$pic = $this->am->get_pic($user['id_user']);
		
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
					<br>
					<br>
					<p>" . $pic['pic_name'] . "</p>
					<p>" . $pic['pic_position'] . "</p>
					<br>
					<p><b>Dicetak dengan sistem aplikasi kelogistikan PT Nusantara Regas, Dokumen ini resmi tanpa stempel dan/atau tanda tangan pejabat.</b></p>
				</body>
			</html>";
		
		$dompdf = new DOMPDF();  
		$dompdf->load_html($a);  
		$dompdf->set_paper('A4','potrait'); 
		$dompdf->render();
									
		$dompdf->stream("Surat Pernyataan.pdf",array('Attachment' => 1));
	}
}
