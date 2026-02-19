<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 */
class Upload_lampiran_persetujuan extends MY_Controller{
	public $modelAlias = 'pm';
	
	function __construct(){
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
					'allowed_types'=> '*',
					'rules' => 'required' 
				)
			)
		);
	}

	public function form_lampiran_persetujuan($id){
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
	 			return true;
			}
		}
	}
}