<?php
class Hpsoe_report extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('hpsoe/hpsoe_report_model');
	}
	
	function index(){
		//if($_POST['is_public']) 

		if($_POST['custom-name-komag']) $_POST['komag-search-nama'] = $_POST['custom-name-komag'];
		$_SESSION['hpsoe_pgn_report_cart'][] = $_POST;
		$data['query'] = $_SESSION['hpsoe_pgn_report_cart'];
		
		$this->load->view('content/hpsoe/report_cart', $data);
	}
	
	function delete($index = ''){
		if($index != 'reset') unset($_SESSION['hpsoe_pgn_report_cart'][$index]);
		else unset($_SESSION['hpsoe_pgn_report_cart']);
	}
	
	function report_view(){
		$data['query'] = $_SESSION['hpsoe_pgn_report_cart'];
		
		$this->load->view('content/hpsoe/report_view', $data);
	}
}