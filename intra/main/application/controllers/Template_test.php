<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Template_test extends MY_Controller {

	function index($param) {
		$this->load->view('template/'.$param);
	}
}