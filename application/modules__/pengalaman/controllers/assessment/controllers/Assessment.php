<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assessment extends CI_Controller {

	public $id_pengadaan;
	public $tabNav;

	public function __construct(){
		parent::__construct();

		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}

		$this->load->model('assessment_model','am');
				
	}

	public function index()
	{	
		$this->load->library('form');
		$search = $this->input->get('q');
		$page = '';
		$post = $this->input->post();

		$per_page=10;

		$sort = $this->utility->generateSort(array('ms_procurement.name', 'pemenang', 'point', 'tr_assessment.category'));
		
		$data['pengadaan_list']=$this->am->get_pengadaan_list($search, $sort, $page, $per_page,TRUE);

		$data['filter_list'] = $this->filter->group_filter_post($this->get_field_pe());

		$data['pagination'] = $this->utility->generate_page('assessment',$sort, $per_page, $this->am->get_pengadaan_list($search, $sort, '','',FALSE));
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('assessment/content',$data,TRUE);
		$layout['script']= $this->load->view('assessment/content_js',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function view_vendor($id_pengadaan){
		$post = $this->input->post();

		$per_page=10;

		$data['sort'] = $this->utility->generateSort(array('peserta_name','point'));
	
		$data['list'] = $this->am->get_assessment_vendor_list($id_pengadaan,NULL, $data['sort'], '','',FALSE);
		
		// $data['filter_list'] = $this->form->group_filter_post($this->get_field());
		$page = 'peserta';
		$data['pagination'] = $this->utility->generate_page('assessment/content_vendor/'.$id_pengadaan, NULL,  $this->am->get_assessment_vendor_list($id_pengadaan,NULL, $data['sort'], '','',FALSE));
		
		$data['id']			= $id_pengadaan;
		$layout['content']	= $this->load->view('assessment/content_vendor',$data,TRUE);
		$layout['script']	= $this->load->view('assessment/content_js',$data,TRUE);
		$layout['script']	= $this->load->view('assessment/form_filter',$data,TRUE);
		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}


	public function history_nilai($id)
	{	
		$post = $this->input->post();

		$per_page=10;

		$data['sort'] 		= $this->utility->generateSort(array('date','point'));
		$data['list']		= $this->am->get_assessment_vendor($id);

		// $data['filter_list'] = $this->form->group_filter_post($this->get_field());
		$page = 'peserta';
		$data['pagination'] = $this->utility->generate_page('assessment/history_nilai/'.$id, NULL,  $this->am->get_assessment_vendor($id,NULL, $data['sort'], '','',FALSE));
		
		$data['id']	= $id;
		$layout['content']	= $this->load->view('assessment/history_nilai',$data,TRUE);


		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_history($id){
		// $this->db->delete($id)
	}


	public function form_assessment($id,$id_vendor){
		$this->load->model('blacklist/blacklist_model','blm');
		$data = $this->am->get_pengadaan($id);

		$data['assessment_question'] = $this->am->get_question_assessment();
		$data['tabNav'] = $this->tabNav;
		$data['data_assessment'] = $this->am->get_assessment($id,$id_vendor);
		$data['data_approve'] = $this->am->get_approve($id,$id_vendor);
		// print_r($data);die;

		if($this->input->post('simpan')){

			unset($_POST['simpan']);

			$_POST['id_vendor'] 		= $id_vendor;
			$_POST['id_procurement'] 	= $id;
			$_POST['date']				= date('Y-m-d H:i:s');
			// print_r($this->input->post());
			$assessment = $this->am->save_assessment($id,$this->input->post());

			switch ($this->session->userdata('admin')['id_role']) {
				case 9:
					$text 				= 'Data telah terkirim ke User HSE';
					$email['to']		= $this->am->get_mail(2);
					$email['subject']	= "Penilaian (".$data['name'].") - Sistem Aplikasi Kelogistikan PT Nusantara Regas";
					$email['msg']		= '
						Admin User telah memberikan penilaian pada <b>'.$data['name'].'</b> dalam Sistem Aplikasi Kelogistikan PT Nusantara Regas.<br/>
						Untuk melengkapi proses penilaian, harap login ke sistem dan mengakses menu Assessment untuk melengkapi data - data penilaian pada pengadaan <b>'.$data['name'].'</b> di aplikasi.<br/><br/><br/>
						Terima kasih.<br/>
						PT Nusantara Regas';
					redirect('assessment/print_bast/'.$id.'/'.$id_vendor);
					break;
				
				case 2:
					$text 				= 'Data telah terkirim ke User Logistik';
					$email['to']		= $this->am->get_mail(3);
					$email['subject']	= "Penilaian (".$data['name'].") - Sistem Aplikasi Kelogistikan PT Nusantara Regas";
					$email['msg']		= '
						Admin HSE telah memberikan penilaian pada <b>'.$data['name'].'</b> dalam Sistem Aplikasi Kelogistikan PT Nusantara Regas.<br/>
						
						Untuk melengkapi proses penilaian, harap login ke sistem dan mengakses menu Assessment untuk melengkapi data - data penilaian pada pengadaan <b>'.$data['name'].'</b> di aplikasi.<br/><br/><br/>
						Terima kasih.<br/>
						PT Nusantara Regas';
					break;

				case 3:
					$text 			= 'Data telah tersimpan';
					break;
			}

			if($assessment){

				foreach ($email['to'] as $keyTo => $valueTo) {
					# code...
					// echo $valueTo['email']." - ".$email['msg'];
					$this->utility->mail($valueTo['email'], $email['msg'], $email['subject']);

				}


				if($this->session->userdata('admin')['id_role']==3){
					$poin = $this->am->check_poin($id,$id_vendor);
					
					$cb = $this->blm->check_blacklist($poin,$id_vendor,site_url('assessment/'));

					if($this->am->check_approve($id,$id_vendor)>0){
						$this->am->set_assessment($id_vendor,$poin,$cb['id_blacklist'],$id);
						if($cb['id_blacklist']==1||$cb['id_blacklist']==2){
								if($this->session->userdata('admin')['id_role']==3){
									$this->session->set_userdata('blacklist',$cb);
									
									$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Penyedia Barang/Jasa masuk dalam Daftar '.(($cb['id_blacklist']==1)?'Merah':'Hitam').'</p>');
									redirect('blacklist/tambah/');
								}
							
						}else{
							$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">'.$text.'</p>');
							redirect(site_url('assessment'));
						}
						
					}else{
						$this->session->set_flashdata('msgSuccess','<p class="errorMsg">Penilaian harus disetujui User HSE</p>');
						redirect(current_url());
					}
				}else{

					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">'.$text.'</p>');
					redirect(site_url('assessment'));
				}
			}
			

		}

		$data['role'] 		= $this->session->userdata('admin');
		$layout['content']	= $this->load->view('assessment/form_assessment',$data,TRUE);
		$layout['script']	= $this->load->view('assessment/form_assessment_js',$data,TRUE);
		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function print_bast($id, $id_vendor){
		$data['role'] 		= $this->session->userdata('admin');
		$data['default']	= $this->am->get_default_template()->row_array();
		$data['content']	= $this->am->get_data_bast($id, $id_vendor)->row_array();
		$pengadaan	= $this->am->get_bast_fill($id)->row();
		
		$data['text']		= $data['default']['text'];
		$data['text'] = str_replace(
								array(
									'{pekerjaan}',
									'{penyedia}',
									'{alamat_penyedia}',
									'{no_kontrak}',
									'{tanggal_kontrak}',
									'{besaran_kontrak}',
									'{besaran_terbilang}',
									'{hari}',
									'{tanggal}',
									'{bulan}',
									'{tahun}'
								),
								array(
									$pengadaan->pekerjaan,
									$pengadaan->legal_name.' '.$pengadaan->penyedia,
									$pengadaan->alamat_penyedia,
									$pengadaan->no_kontrak,
									default_date($pengadaan->tanggal_kontrak),
									number_format($pengadaan->besaran),
									'<b>'.terbilang($pengadaan->besaran).'</b>',
									get_hari(date('Y-m-d')),
									date('j'),
									get_month(date('Y-m-d')),
									date('Y')

								),
								$data['text']
							);
		if(isset($data['content']['value'])){
			$data['text'] = $data['content']['value'];
		}
		
		if($this->input->post('simpan')){
			if($this->am->insert_bast($id, $id_vendor)){
				redirect(site_url('assessment/export_bast/'.$id.'/'.$id_vendor));
			}
		}
		$layout['content']	= $this->load->view('assessment/print_bast',$data,TRUE);
		$layout['script']	= $this->load->view('assessment/print_bast_js',$data,TRUE);
		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function export_bast($id,$id_vendor){
		$data = $this->am->get_data_bast($id,$id_vendor)->row_array();
  		header("Cache-Control: ");// leave blank to avoid IE errors
		header("Pragma: ");// leave blank to avoid IE errors
		header("Content-type: application/octet-stream");
  		// header("Content-type: application/vnd.ms-word");
		header("Content-Disposition: attachment; Filename=SaveAsWordDoc.doc");

		echo '<html xmlns:v="urn:schemas-microsoft-com:vml"
				xmlns:o="urn:schemas-microsoft-com:office:office"
				xmlns:w="urn:schemas-microsoft-com:office:word"
				xmlns="http://www.w3.org/TR/REC-html40">';
		echo '<head>
				<meta http-equiv=Content-Type content="text/html; charset=utf-8">
				<meta name=ProgId content=Word.Document>
				<meta name=Generator content="Microsoft Word 9">
				<meta name=Originator content="Microsoft Word 9">
				<!--[if !mso]>
				<style>
				v\:* {behavior:url(#default#VML);}
				o\:* {behavior:url(#default#VML);}
				w\:* {behavior:url(#default#VML);}
				.shape {behavior:url(#default#VML);}
				</style>
				<![endif]-->
				<title>title</title>
				<!--[if gte mso 9]><xml>
				 <w:WordDocument>
				  <w:View>Print</w:View>
				  <w:DoNotHyphenateCaps/>
				  <w:PunctuationKerning/>
				  <w:DrawingGridHorizontalSpacing>9.35 pt</w:DrawingGridHorizontalSpacing>
				  <w:DrawingGridVerticalSpacing>9.35 pt</w:DrawingGridVerticalSpacing>
				 </w:WordDocument>
				</xml><![endif]-->
				<style>
				</head><body>';	

		echo $data['value'].'</body></html>';


		die();
	}
	public function print_assessment($id,$id_vendor){
		$data = $this->am->get_pengadaan($id);
		// $data_kontrak = $this->am->get_kontrak($id);

		$data['assessment_question'] 	= $this->am->get_question_assessment();
		$data['data_assessment'] 		= $this->am->get_assessment($id,$id_vendor);
		$data['vendor'] 				= $this->am->get_vendor_report($id_vendor);
		$data['nilai_assessment'] 		= $this->am->get_assessment($id,$id_vendor);
		
		

		$layout['content']= $this->load->view('assessment/print_assessment',$data,TRUE);
		$this->load->view('template_print',$layout);
	}


	public function get_field_pe(){
		return array(
			array(
				'label'	=>	'Pengadaan',
				'filter'=>	array(
								array('table'=>'ms_procurement|name' ,'type'=>'text','label'=> 'Nama Pengadaan'),
								array('table'=>'ms_vendor|name' ,'type'=>'text','label'=> 'Nama Pemenang'),
								array('table'=>'ms_procurement_bsb|id_bidang|get_bidang' ,'type'=>'dropdown','label'=> 'Bidang'),
							)
			),
			array(
				'label'	=>	'Kontrak',
				'filter'=>	array(
								array('table'=>'ms_contract|contract_date' 	,'type'=>'date','label'=> 'Tanggal Kontrak'),
								array('table'=>'ms_contract|no_contract','type'=>'text','label'=> 'No. Kontrak'),
								array('table'=>'ms_contract|no_sppbj','type'=>'text','label'=> 'SPPBJ'),
								array('table'=>'ms_contract|sppbj_date','type'=>'date','label'=> 'Tanggal SPPBJ'),
								array('table'=>'ms_contract|no_spmk','type'=>'text','label'=> 'SPMK'),
								array('table'=>'ms_contract|spmk_date','type'=>'date','label'=> 'Tanggal SPMK'),
								array('table'=>'ms_contract|contract_price','type'=>'number_range','label'=> 'Nilai Kontrak (Rp)'),
							)
			),
			array(
				'label'	=>	'Assessment',
				'filter'=>	array(
								array('table'=>'tr_assessment|point','type'=>'number_range','label'=> 'Skor Assessment'),
								array('table'=>'tr_assessment_point|category|get_warna','type'=>'dropdown','label'=> 'Warna'),
							)
			),
		);
	}
}