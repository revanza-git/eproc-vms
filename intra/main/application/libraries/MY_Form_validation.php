<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class MY_Form_Validation extends CI_Form_Validation{
		protected $CI;
		function __construct(){
			parent:: __construct();
			$this->CI =& get_instance();
		}

		public function backdate($str){

			$date = strtotime($str);

			if($date < strtotime(date('Y-m-d'))&&$str!='lifetime'){

				$this->CI->form_validation->set_message('backdate', 'Tanggal tidak boleh lampau');
				return false;

			}else{

				return true;

			}

		}
		public function forward_date($str){

			$date = strtotime($str);

			if($date > strtotime(date('Y-m-d'))){
				// echo 'true';
				$this->CI->form_validation->set_message('forward_date', 'Tanggal tidak boleh lebih dari hari ini');
				return false;

			}else{

				return true;

			}

		}
		public function date_range($field, $opt){
			$date1 = strtotime($field);
			$date2 = strtotime($_POST[$opt]);

			if($date1 >= $date2){
				$this->CI->form_validation->set_message('date_range', '%s tidak boleh lebih dari tanggal berakhir');
				return false;
			}else{
				return true;
			}
		}
		public function date_low($field, $opt){
			$date1 = strtotime($field);
			$date2 = strtotime($_POST[$opt]);

			if($date2 > $date1){
				$this->CI->form_validation->set_message('date_low', '%s tidak boleh lebih rendah dari tanggal awal');
				return false;
			}else{
				return true;
			}
		}

		public function date_range_same($field, $opt){
			$date1 = strtotime($field);
			$date2 = strtotime($_POST[$opt]);

			if($date1 > $date2){
				$this->CI->form_validation->set_message('date_range_same', '%s tidak boleh lebih dari tanggal berakhir');
				return false;
			}else{
				return true;
			}
		}

		public function valid_npwp($field, $opt){
			$npwp = preg_replace('/[^0-9]/', '', $field);
			
			if($npwp==''){
				$this->CI->form_validation->set_message('valid_npwp', 'NPWP tidak boleh kosong');
				return false;
			}else{
				if(strlen($npwp)!=15){
					$this->CI->form_validation->set_message('valid_npwp', 'Format NPWP salah');
					return false;
				}else{
					return true;
				}
			}
		}
		
	}