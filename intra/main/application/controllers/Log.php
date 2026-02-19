<?php
/**
 * 
 */
class Log extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('log_model','lm');

		$this->getData = $this->lm->getData(null);
	}

	public function index($id = null){
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('kurs'),
			'title' => 'Riwayat Aktivitas'
		));
		
		$this->header = 'Riwayat Aktivitas';
		$this->content = $this->load->view('log/list',null, TRUE);
		$this->script = $this->load->view('log/list_js', null, TRUE);
		parent::index($id);
	}
}