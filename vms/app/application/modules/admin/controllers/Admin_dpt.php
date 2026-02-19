<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Admin_dpt extends CI_Controller {

	public function __construct(){

		parent::__construct();

		if(!$this->session->userdata('admin')){

			redirect(site_url());

		}

		$this->load->model('user/admin_user_model','aum');

		$this->load->model('vendor/vendor_model','vm');

		

	}

	public function index(){

		$this->load->library('form');

		$this->load->library('datatables');

		$search = $this->input->get('q');

		$page = '';

		

			if(count($_POST['simpan'])){
				// print_r($_POST);
				unset($_POST['simpan']);
				$nomor = $_POST['certificate_no'];
				$id_vendor = $_POST['certificate_id'];

				$update = $this->vm->update_certificate($id_vendor, $nomor);

				if($update){
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah nomor sertifikat!</p>');

					redirect(site_url('admin/admin_dpt/'));
				}

				

			}

			

	
		$per_page=10;



		$sort = $this->utility->generateSort(array('name','category','npwp_code','vendor_address','id','vendor_phone'));

		

		$data['vendor_list']	= $this->vm->get_dpt_list($search, $sort, $page, $per_page,TRUE);
		$data['csms_limit']		= $this->vm->get_csms_limit($search, $sort, $page, $per_page,TRUE);



		$data['filter_list'] = $this->filter->group_filter_post($this->filter->get_field());



		$data['pagination'] = $this->utility->generate_page('admin/admin_dpt',$sort, $per_page, $this->vm->get_dpt_list($search, $sort, '','',FALSE,$this->filter->get_field()));

		$data['sort'] = $sort;

		$layout['content']= $this->load->view('dpt/content_dpt',$data,TRUE);

		$layout['script']= $this->load->view('dpt/content_dpt_js',$data,TRUE);

		// $layout['script']= $this->load->view('dpt/form_filter',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);

		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);

		$this->load->view('template',$item);

	}

	public function export_excel($title="Data Penyedia Barang/Jasa", $data){
		$data = $this->vm->get_dpt_list($search, $sort, $page, $per_page,TRUE);
		$csms_limit = $this->vm->get_csms_limit();
		// print_r($data);die;	
		$table = "<table border=1>";

			$table .= "<tr>";
			$table .= "<td style='background: #f6e58d;'>No</td>";
			$table .= "<td style='background: #f6e58d;'>Nama Badan Usaha</td>";
			$table .= "<td style='background: #f6e58d;'>Nama Penyedia Barang/Jasa</td>";
			$table .= "<td style='background: #f6e58d;'>Kategori CSMS</td>";
			$table .= "<td style='background: #f6e58d;'>Nomor Sertifikat</td>";
			$table .= "<td style='background: #f6e58d;'>NPWP</td>";
			$table .= "<td style='background: #f6e58d;'>Alamat</td>";
			$table .= "<td style='background: #f6e58d;'>Telepon</td>";
			$table .= "</tr>";

		foreach ($data as $key => $value) {
			# code...
			$no = $key + 1;
			$table .= "<tr>";
			$table .= "<td>".$no."</td>";
			$table .= "<td>".$value['legal']."</td>";
			$table .= "<td>".$value['name']."</td>";
            
			    $table_ = "<td>-</td>";
			foreach ($csms_limit as $key_ => $value_) {
                # code...

                if ($value['score'] > $value_['end_score'] && $value['score'] < $value_['start_score']) {
                    # code...
			        $table_ = "<td>".$value_['value']."</td>";
                }
			}
			$table .= $table_;
			$table .= "<td>".$value['certificate_no']."</td>";
			$table .= "<td>".$value['npwp_code']."</td>";
			$table .= "<td>".$value['vendor_address']."</td>";
			$table .= "<td>".$value['vendor_phone']."</td>";
			$table .= "</tr>";
		}
		$table .= "</table>";
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename='.$title.'.xls');



		echo $table;
	}

}