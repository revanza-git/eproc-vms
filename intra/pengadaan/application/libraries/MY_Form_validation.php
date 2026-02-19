<?php

	class MY_Form_Validation extends CI_Form_Validation{

		function __construct($config){
			parent:: __construct($config);
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

		public function is_valid_npwp($field, $opt){
			$npwp = preg_replace('/[^0-9]/', '', $field);
			
			if($npwp==''){
				$this->CI->form_validation->set_message('is_valid_npwp', 'NPWP tidak boleh kosong');
				return false;
			}

			return true;
		}

		/*public function do_upload($field){	
			
			$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
			// echo 'a';
			// $config['upload_path'] = './lampiran/'.$db_name.'/';
			// $config['allowed_types'] = 'pdf|jpeg|jpg|png|gif';
			// $config['max_size'] = '2096';
			
			// $this->CI->load->library('upload');
			// $this->CI->upload->initialize($config);
			
			// if ( ! $this->CI->upload->do_upload($db_name)){
				// $_POST[$db_name] = $file_name;
				// $this->CI->form_validation->set_message('do_upload', $this->upload->display_errors('',''));
				$this->CI->form_validation->set_message('do_upload', $file_name);
				// return false;
			//}else{
			// 	$_POST[$db_name] = $file_name; 
				return true;
			// }
		}*/
		
	}