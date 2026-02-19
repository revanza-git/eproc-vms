<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Timeline extends MY_Controller {

	public $form;
	public $modelAlias 	= 'fm';
	public $alias 		= 'ms_fppbj';
	public $module 		= 'kurs';

	public function __construct(){
		parent::__construct();		
		$this->load->model('Fppbj_model','fm');
		$this->load->model('Pemaketan_model','pm');
	}

	public function pdf_preview($filename="test"){
		$path = './assets/lampiran/'.$filename.'.pdf';

		header('Content-Type: application/pdf');
		header('Content-Disposition: inline; filename='.$path);
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');

		readfile($path);
	}

	public function view($type="fppbj", $id=""){

		$get_divisi = $this->fm->selectData($id);

		// print_r($get_divisi);die;

		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('pemaketan'),
			'title' => 'Pemaketan Pengadaan'
		));
		$this->breadcrumb->addlevel(2, array(
			'url' => site_url('division'),
			'title' => 'Timeline'
		));

			$type = strtoupper($type);
			$data['proc']	=	$this->pm->getProc($id);
			$data['id'] = $id;
			
			$this->header = 'Timeline '.$type.' - '.$get_divisi['division'];
			$this->content = $this->load->view('timeline/proc',$data, TRUE);
			$this->script  = $this->load->view('timeline/proc_js',$data, TRUE);
			parent::index();

	}
	
}
