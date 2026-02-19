<?php
class Utility{
	
	protected $CI;
	protected $id_sbu;
	
	function __construct(){
		$this->CI =& get_instance();
		
		$this->id_sbu = $this->get_userdata('id_sbu');
		$this->CI->load->database();
		$this->CI->load->library('pagination');
	}
	
	function get_userdata($key) {
		$this->CI =& get_instance();
		return $this->CI->session->userdata($key);
	}
	
	function generate_page($page ='', $sort = array(),$per_page = 10,$dataSource = NULL){
		/*Config Pagination*/
		
		$config['base_url'] = site_url($page.'?'.$this->generateLink('per_page'));
		$config['total_rows'] = count($dataSource);
		$config['per_page'] = $per_page;
		$config['num_links'] = 2;
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['full_tag_open'] = '<div class="navNumber"><ul class="pagination">';
		$config['full_tag_close'] = '</ul></div>';
		$config['first_link'] = '&laquo';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = '&raquo;';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['next_link'] = '&rsaquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '&lsaquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		$this->CI->pagination->initialize($config);
		return $this->CI->pagination->create_links();
	}
	
	function generateLink($exception = '') {
		$get = $_GET;
		unset($get[$exception]);
		return http_build_query($get);
	}
}
?> 