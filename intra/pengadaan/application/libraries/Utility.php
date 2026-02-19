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
	
	function generate_page($page ='', $sort = array(),$per_page = 10,$dataSource = NULL){
		/*Config Pagination*/
		
		
		$config['base_url'] = site_url($page.'?'.$this->generateLink('per_page'));
		// Fix: dataSource should be the total count, not an array to count
		$config['total_rows'] = is_array($dataSource) ? count($dataSource) : (int)$dataSource;
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
	function generateSort($arr = array()){
		$sort = array();
		foreach($arr as $val){
			$sort[$val] = ($this->CI->input->get('by')==$val) ? $this->CI->input->get('sort') : 'asc';
		}
		return $sort;
	}
	function generateLink(){
		$CI =& get_instance();
		$numargs = func_get_args();
		$rawParam = array('per_page','q','sort','by');
		$param = array();
		$arr = array_diff($rawParam, $numargs);

		foreach($arr as $row){
			
			if($CI->input->get($row)) $param[$row] = $CI->input->get($row);
		}
		
		$getLink = http_build_query($param);
		return $getLink;
	}

	function name_generator($name)
	{
		$rand = '';
		$array = explode('.' , $name);
		$length = count($array)-1;	
		
		$tgl = date("d");
		$bln = date("m");
		$thn = date("y");
		
		$jam = date("h");
		$mnt = date("i");
		$dtk = date("s");
		
		for($i=0;$i<3;$i++)
			$rand .= rand(0,9);
			 
		$ext = $array[$length];
		$new = $tgl.$bln.$thn."_".$jam.$mnt.$dtk."_".$rand.".".$ext;
		return $new; 
	}
	
	function password_generator()
	{
		$sessid = '';
		$to_rand = array("T", "v", "q", "L", "u", "2", "3", "g", "m", "M", "O", "t", "N", "i", "9", "h", "8", "k", "K", "W", "I", "V", "1", "J", "p", "H", "y", "R", "6", "f", "U", "b", "4", "d", "s", "7", "z", "S", "P", "n", "Z", "G", "C", "w", "a", "5", "o", "A", "l", "c", "F", "Q", "X", "j", "D", "r", "Y", "x", "e", "B", "0", "E");
					
		for($i=0;$i<10;$i++){
			$angka = rand(0,62);
			$sessid .= $to_rand[$angka]; 
		}
		
		return $sessid;
	}
	
	function session_id()
	{
		$return = '';
		for($i=0;$i<10;$i++){
			$return .= rand(0,9);
		}
		return $return;
	}
	
	function get_userdata($name = ''){

		if($this->CI->session->userdata('user')){
			$sess = $this->CI->session->userdata('user');

			$sql = $this->CI->db->query('SELECT id AS id_user, name, id_sbu, vendor_status, is_active FROM ms_vendor WHERE id = ?', 1);
			
			$data = $sql->row_array(); 

			return $data[$name];
		}
		else if($this->CI->session->userdata('admin'))
			$sess = $this->CI->session->userdata('admin');
			if(!empty($sess)){
				return $sess[$name];
			}
	}

	function get_nama($id = ''){
		$sql = "SELECT nama FROM ms_vendor WHERE id = ?";
		$sql = $this->CI->db->query($sql, $id);
		$sql = $sql->row_array();

		return $sql['nama'];
	}
	
	
	function get_sbu(){
		$sql = "SELECT name FROM tb_sbu WHERE id = ?";
		$sql = $this->CI->db->query($sql, $this->id_sbu);
		$sql = $sql->row_array();

		return $sql['name'];
	}
	
	function bidang_sub_bidang($name = '', $selected = ''){
		$return = null;
		
		$bidang = $this->CI->db->query('SELECT * FROM ms_bidang');
		$bidang = $bidang->result();
		
		$return .= '<script type="text/javascript">';
		$return .= 'function get_sub_type';	
		$return .=
				'$.ajax({
					url : '.base_url().'index.php/utilities/get_sub_bidang/,
					success : function(data){
						toReturn = null;
						for(i=0;i<data.length;i++)
							toReturn += \'<option value="\' + data[i].value + \'">\' + data[i].label + \'</option>\';
						
						$("#'.$name.'").html(toReturn);
					}
				});';
	
		$return .= '</script>';
		
		$return .= '<select name="'.$name.'" id="'.$name.'">';
			
		$return .= '</select>';
	}
	
	function drop_down_vendor($name = '', $id_pengadaan = '', $selected = ''){
		$return = null;
		
		$sql = $this->CI->db->query('SELECT b.id, b.nama, "is_vms" AS source
									FROM ms_peserta_pengadaan a 
									
									LEFT JOIN ms_vendor b ON a.id_vendor = b.id 
									WHERE a.id_pengadaan = "'.$id_pengadaan.'" AND 
									b.id NOT IN (SELECT id_vendor FROM tr_blacklist WHERE id_pengadaan = "'.$id_pengadaan.'") 
									
									UNION ALL 
									
									SELECT id, nama, "non_vms" AS source FROM ms_peserta_non_vms 
									WHERE id_pengadaan = "'.$id_pengadaan.'" AND is_blacklist IS NULL ');
		
		$return .= '<select id="'.$name.'" name="'.$name.'">';
		$return .= '<option value="">-- choose one --</option>';
		foreach($sql->result() as $data){
			$return .= '<option value="'.$data->id.'" alt="'.$data->source.'"';
			if($selected == $data->id)
				$return .= ' selected="selected"';
			$return .= '>'.$data->nama.'</option>';
		}
		$return .= '</select>';
		
		return $return;
	}
	
	function drop_down_sbu($name = '', $selected = ''){
		$sql = "SELECT * FROM tb_sbu WHERE id NOT IN (SELECT id_sbu FROM ms_admin) ";
		if($selected) $sql .= " OR id = '".$selected."'";
		
		$sql = $this->CI->db->query($sql);
		
		$return .= '<select id="'.$name.'" name="'.$name.'">';
			$return .= '<option value="">-- choose one --</option>';
			foreach($sql->result() as $data){
				$return .= '<option value="'.$data->id.'"';
				if($selected == $data->id)
					$return .= ' selected="selected"';
				$return .= '>'.$data->name.'</option>';
			}
		$return .= '</select>';
		
		return $return;
	}
	
	function drop_down_bsb($name = '', $selected = '', $id_user = '', $id_siup = ''){
		$sql = "SELECT a.*, b.name AS bidang_name, c.name AS sub_bidang_name FROM ms_siup_bsb a 
					LEFT JOIN tb_bidang b ON a.id_bidang = b.id
					LEFT JOIN tb_sub_bidang c ON a.id_sub_bidang = c.id 
					
					WHERE id_siup = ? AND a.id NOT IN(SELECT id_bsb FROM ms_katalog WHERE id_siup = ?) ";
		
		$sql = $this->CI->db->query($sql, array($id_siup, $id_siup));
		
		$return .= '<select id="'.$name.'" name="'.$name.'">';
			$return .= '<option value="">-- choose one --</option>';
			foreach($sql->result() as $data)
				$return .= '<option value="'.$data->id.'">'.$data->bidang_name.'/'.$data->sub_bidang_name.'</option>';
		$return .= '</select>';
		
		return $return;
	}
		
	function drop_down_sub_bidang($name = '', $selected = '', $id_bidang = '', $id_vendor = '', $id_location = '', $location = ''){
		$sql = "SELECT a.* FROM tb_sub_bidang a WHERE id_bidang = ? 
					AND 
						(a.id NOT IN (SELECT id_sub_bidang FROM ms_bsb WHERE id_vendor = ? AND id_location = ? AND location = ? AND id_sub_bidang IS NOT NULL)  
						 OR a.id = ?) 
					AND a.id NOT IN (SELECT id_past FROM tb_sub_bidang WHERE id_past IS NOT NULL) ";
					
		$sql = $this->CI->db->query($sql, array($id_bidang, $id_vendor, $id_location, $location, $selected));
		
		$return .= '<select id="'.$name.'" name="'.$name.'">';
			$return .= '<option value="">-- choose one --</option>';
			foreach($sql->result() as $data){
				$return .= '<option value="'.$data->id.'"';
				if($selected == $data->id)
					$return .= ' selected="selected"';
				$return .= '>'.$data->name.'</option>';
			}
		$return .= '</select>';
		
		return $return;
	}
	
	function drop_down_produk($name = '', $selected = '', $id_user = ''){
		$sql = "SELECT * FROM ms_agen_produk WHERE id_vendor = ? AND 
					id NOT IN(SELECT id_produk FROM ms_katalog WHERE id_vendor = ? GROUP BY id_produk)";
		
		$sql = $this->CI->db->query($sql, array($id_user, $id_user));
		
		$return .= '<select id="'.$name.'" name="'.$name.'">';
			$return .= '<option value="">-- choose one --</option>';
			foreach($sql->result() as $data)
				$return .= '<option value="'.$data->id.'">'.$data->produk.'/'.$data->merk.'</option>';
		$return .= '</select>';
		
		return $return;
	}
	
	function drop_down_paging($name = '', $value = '', $java = '')
	{
		$return = "<select name='".$name."' id='".$name."' onchange='".$java."'>";
		
		$array = array(25, 50, 100, 250);
		foreach($array as $data){
			if($value == $data) $sel = " selected='selected' "; else $sel = "";
			 
				$return .= "<option value='".$data."'".$sel.">".$data."</option>";
		}
		
		$return .= "</select>";
		
		return $return;
	}
	
	function drop_down_bsb_katalog($id_vendor = '', $name = ''){
		$sql = "SELECT a.*,
					   b.name AS bidang, 
					   c.name AS sub_bidang  
					   
					   FROM ms_bsb a 
					   LEFT JOIN tb_bidang b ON a.id_bidang = b.id
					   LEFT JOIN tb_sub_bidang c ON a.id_sub_bidang = c.id 
					   
					   WHERE id_vendor = ?";
					
		$sql = $this->CI->db->query($sql, $id_vendor);
		
		$return .= '<select id="'.$name.'" name="'.$name.'" style="font-size : 10px">';
			$return .= '<option value="">-- choose one --</option>';
			foreach($sql->result() as $data){
				if($data->location == "siup") $location = "SIUP";
				if($data->location == "siujk") $location = "SIUJK";
				if($data->location == "sbu") $location = "SBU";
				if($data->location == "ijin_lain") $location = "Surat Izin Usaha Lainnya";
				
				$return .= '<option value="'.$data->id.'">'.$location." - ".$data->bidang.'/'.$data->sub_bidang.'</option>';
			}
		$return .= '</select>';
		
		return $return;
	}
	
	function cek_note($id = '', $address = ''){
		$this->CI->load->model('sticky_note_model');
		
		$address = str_replace("save_edit_", "", $address);
			
		$fill = $this->CI->sticky_note_model->select_data($id, $address);
		if($fill->num_rows()){
			foreach($fill->result() as $data)
				echo '  $("#sticky-note-container-'.$address.'").createSticky({ 
						  "url" : "'.base_url().'index.php/sticky_note/index/'.$data->id.'", 
						  "id" : "'.$data->id.'",
						  "width": "'.$data->box_width.'",
						  "height": "'.$data->box_height.'",
						  "top": "'.$data->box_top.'",
						  "left": "'.$data->box_left.'"
					  });';
		}
	}
	function check_administrasi(){
		$data = $this->CI->session->userdata('user');
		$error = 0;
		$result = $this->CI->db->select('npwp_code, npwp_file, nppkp_code, nppkp_file, vendor_office_status, vendor_address, vendor_country, vendor_province, vendor_city, vendor_phone, vendor_email, vendor_postal')->where('id_vendor',$data['id_user'])->get('ms_vendor_admistrasi')->row_array();
		foreach($result as $key => $value){
			if($value==''){
				$error += 1;
			} 
		}
		return $error;
	}
	function get_bidang(){
		$return = array();
		$result = $this->CI->db->select('name')->get('tb_bidang')->result_array();
		$return[''] =  '--Pilih Data--';
		foreach($result as $key => $value){
			$return[$value['name']] = $value['name'];
		}
		return $return;
	}
	function get_bidang_list(){
		$return = array();
		$result = $this->CI->db->select('id, name')->get('tb_bidang')->result_array();
		$return[''] =  '--Pilih Data--';
		foreach($result as $key => $value){
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}
	function get_sub_bidang_list($id){
		$return = array();
		$result = $this->CI->db->select('id, name')->where('id_bidang',$id)->get('tb_sub_bidang')->result_array();
		$return[''] =  '--Pilih Data--';
		foreach($result as $key => $value){
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}
	function tabNav($data,$current){
		$html = '<div class="tabNav" id="tabNav">
					<ul>';
			foreach($data as $key => $row){
				$html .= 	'<li class="'.(($current==$key)?'active':'').'">
								<a href="'.$row['url'].'" >'.$row['label'].'</a>
							</li><!--
							-->';
			}
		$html .='</ul></div>';
		return $html;		
	}

	function generate_radio_k3($id,$evaluasi_list,$val, $is_null = FALSE,$act='edit'){
		$letters = array('a','b','c','d');
		$html = '';
		if($is_null==FALSE){
			
			
			foreach($letters as $letter){
				$checked = ($act=='edit') ? (($val==$evaluasi_list[$id]['point_'.$letter]) ? 'checked' : '') : '';
				$html .= '<td class="radioQuest"><label><input class="radiocsms" type="radio" '.$checked.' name="evaluasi['.$evaluasi_list[$id]['id'].']" value="'. $evaluasi_list[$id]['point_'.$letter].'">'. $evaluasi_list[$id]['point_'.$letter].'</label></td>';
			}
			return $html;
		}else{
			foreach($letters as $letter){
				$html .= '<td class="radioQuest"></td>';
			}
			return $html;
		}
	}

	function generate_checked_k3($id, $evaluasi_list,$value,$is_null = FALSE){
		$letters = array('point_a','point_b','point_c','point_d');
		$html = '';
		$sub_total = 0;
		if($is_null==FALSE){
			foreach($letters as $letter){
				if($evaluasi_list[$id][$letter]==$value){
					$html .='<td class="radioQuest"><i class="fa fa-check">&nbsp;'.$value.'</i></td>';
				}else{
					$html .='<td class="radioQuest">'.$evaluasi_list[$id][$letter].'</td>';
				}
			}
			$html .= '<td class="radioQuest">'.$value.'</td><td class="radioQuest"></td><td class="radioQuest"></td>';
		}else{
			foreach($letters as $letter){
				$html .= '<td class="radioQuest"></td>';
			}
			$html .= '<td class="radioQuest"></td><td class="radioQuest"></td><td class="radioQuest"></td>';
		}

		
		return $html;
	}
	function mail($to,$message,$subject){
		// Initialize CodeIgniter instance
		$this->CI =& get_instance();
		
		// Load email library and use the configuration from config/email.php
		$this->CI->load->library('email');
		
		// Clear any previous email data
		$this->CI->email->clear(TRUE);

		// Set email parameters
		$this->CI->email->from(env('EMAIL_VMS_FROM_ADDRESS', 'nusantararegas.smtp@gmail.com'), env('EMAIL_VMS_FROM_NAME', 'VMS REGAS'));
		$this->CI->email->to($to); 
		$this->CI->email->bcc(env('EMAIL_TEST_ADDRESS', 'muarifgustiar@gmail.com'));
		$this->CI->email->subject($subject);
		$this->CI->email->message($message);

		// Attempt to send email
		if (!$this->CI->email->send()) {
			// Log error if email fails to send
			log_message('error', 'Failed to send email to: ' . $to . ' - Subject: ' . $subject);
			// Optionally show user-friendly message without breaking page flow
			// echo '<script>alert("Email gagal dikirim. Silakan coba lagi.");
		} else {
			log_message('info', 'Email sent successfully to: ' . $to);
		}

		// Uncomment for debugging email issues
		// echo $this->CI->email->print_debugger();
	}
}