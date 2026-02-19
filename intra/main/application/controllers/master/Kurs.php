<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kurs extends MY_Controller {

	public $form;
	public $modelAlias 	= 'km';
	public $alias 		= 'tb_legal';
	public $module 		= 'kurs';

	public function __construct(){
		parent::__construct();		
		$this->load->model('master/Kurs_model','km');


		$this->form = array(
				'form' => array(
					array(
						'field'	=> 	'name',
						'type'	=>	'text',
						'label'	=>	'Nama Mata Uang',
						'rules' => 	'required',
					),array(
						'field'	=> 	'symbol',
						'type'	=>	'text',
						'label'	=>	'Simbol',
						'rules' => 	'required',
					)
				),

				'successAlert'=>'Berhasil mengubah data!',
				'filter'=>array(
					array(
						'type'	=>	'text',
						'label'	=>	'Nama Mata Uang',
						'field' =>  'name'
					),
					array(
						'type'	=>	'text',
						'label'	=>	'Symbol',
						'field' =>  'symbol'
					),
					
				)
			);
		$this->insertUrl = site_url('master/kurs/save/');
		$this->updateUrl = 'master/kurs/update';
		$this->deleteUrl = 'master/kurs/delete/';
		$this->getData = $this->km->getData($this->form);
		
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

	public function index($id = null){
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('kurs'),
			'title' => 'Mata Uang'
		));
		
		$this->header = 'Mata Uang';
		$this->content = $this->load->view('master/kurs/list',null, TRUE);
		$this->script = $this->load->view('master/kurs/list_js', null, TRUE);
		parent::index($id);
	}
	
}
