<?php
class Form{
	
	private $CI;

	public function __construct(){
		$this->CI =& get_instance(); 

		$this->CI->load->library('upload');
	}
	

	function get_kurs($name,$data){
		$arr = $this->CI->db->select('id,symbol')->where('del',0)->get('tb_kurs')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['symbol'];
		}
		return form_dropdown($name, $result, $data,'');
	}
	function get_foreign_kurs($name,$data){
		$arr = $this->CI->db->select('id,symbol')->where('id!=',1)->where('del',0)->get('tb_kurs')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['symbol'];
		}
		return form_dropdown($name, $result, $data,'');
	}

	protected function build_str_query($query = ''){
		$arr = preg_match_all('/{(.*?)}/', $query, $display);

		$return = $query;
		$return_arr['var'] = $display[1];
		
		$initial = false;
		$x = 0;
		foreach($display[0] as $haystack){
			if(!$initial){ 
				$initial = true; 
				if((strpos($string, $haystack) + 1)) $return_arr['start_from'] = "var"; else $return_arr['start_from'] = "cons";
			}

			$return = str_replace($haystack, "|", $return);
			$x++;
		}
		
		$return = explode("|", $return);
		$y = 0;
		foreach($return as $_return){
			if($_return) $return_arr['cons'][] = $_return;
			$y++;
		}	
		
		$start = $return_arr['start_from'];
		if($start == "var") $end = "cons"; else $end = "var";

		$i = 0;
		$to_return = array();
		foreach($return_arr[$start] as $_start){
			array_push($to_return, array('type' => $start, 'data' => $_start));
			if($return_arr[$end][$i]) array_push($to_return, array('type' => $end, 'data' => $return_arr[$end][$i]));
			$i++;
		}
		
		return $to_return;
	}

	public function text_box($param = array()){
		$return = '<input type="text"';
		foreach($param as $index => $_param){
			$return .= ' '.$index.'="'.$_param.'"';
		}
		$return .= '/>';

		return $return;
	}
	public function number($param = array()){
		$return = '<input type="number"';
		foreach($param as $index => $_param){
			$return .= ' '.$index.'="'.$_param.'"';
		}
		$return .= '/>';

		return $return;
	}

	public function decimal($param = array()){
		$return = '<input clas="dekodr-decimal" type="text"';
		foreach($param as $index => $_param){
			$return .= ' '.$index.'="'.$_param.'"';
		}
		$return .= '/>';

		return $return;
	}

	public function password($param = array()){
		$return = '<input type="password"';
		foreach($param as $index => $_param){
			$return .= ' '.$index.'="'.$_param.'"';
		}
		$return .= '/>';

		return $return;

	}

	public function hidden($param = array()){
		$return = '<input type="hidden"';
		foreach($param as $index => $_param){
			$return .= ' '.$index.'="'.$_param.'"';
		}
		$return .= '/>';

		return $return;

	}

	public function button($param = array()){
		$return = '<input type="button"';
		foreach($param as $index => $_param){
			$return .= ' '.$index.'="'.$_param.'"';
		}
		$return .= '/>';

		return $return;

	}

	public function text_area($param = array()){
		$value = $param['value'];
		unset($param['value']);

		$return .= '<textarea';
		foreach($param as $index => $_param){
			$return .= ' '.$index.'="'.$_param.'"';
		}
		$return .= '>';
		$return .= $value;
		$return .= '</textarea>';

		return $return;
	}

	public function drop_down($param = array(), $value = array()){
		$selected = $param['value'];
		unset($param['value']);

		$return = '<select';
		foreach($param as $index => $_param)
			$return .= ' '.$index.'="'.$_param.'"';
		
		$return .= '>';

		$return .= '<option value=""> - pilih - </option>';

		foreach($value as $index => $_value){
			$return .= '<option value="'.$index.'"';
			if($selected == $index) $return .= ' selected="selected"';
			$return .= '>'.$_value.'</option>';
		}

		$return .= '</select>';

		return $return;
	}

	public function drop_down_db($param = array(), $setting = array()){
		$string = $this->build_str_query($setting['label']);
		$value = array();
		$table = isset($setting['table']) ? (string) $setting['table'] : '';
		if ($table === '' || !preg_match('/^[A-Za-z0-9_]+$/', $table)) {
			return $this->drop_down($param, array());
		}

		$this->CI->db->from($table);
		if (!empty($setting['where'])) {
			if (is_array($setting['where'])) {
				$this->CI->db->where($setting['where']);
			} else {
				$where = (string) $setting['where'];
				if (preg_match('/(;|--|\\/\\*|\\*\\/|\\bunion\\b|\\bdrop\\b|\\binsert\\b|\\bupdate\\b|\\bdelete\\b)/i', $where)) {
					return $this->drop_down($param, array());
				}
				$this->CI->db->where($where, null, false);
			}
		}

		$sql = $this->CI->db->get();
		
		foreach($sql->result() as $data){
			
			$label = "";

			foreach($string as $_string){

				if($_string['type'] == "var") $label .= $data->$_string['data'];
				else $label .= $_string['data'];
			}
			$array[$data->$setting['value']] = $label;		
		}

		return $this->drop_down($param, $array);
	}

	public function calendar($param = array(), $disable_day = false){
		$return = '';
		$class = 'dekodr-calendar';
		if(!$param['value']) $param['value'] = date("Y-m-d");


		for($i=1;$i<=31;$i++){ $x = $i; if($i < 10) $x = "0".$i; $day[$x] = $x; }
		$month = array(
			'01' => 'Januari',
			'02' => 'Febuari',
			'03' => 'Maret',
			'04' => 'April',
			'05' => 'Mei',
			'06' => 'Juni',
			'07' => 'Juli',
			'08' => 'Agustus',
			'09' => 'September',
			'10' => 'Oktober',
			'11' => 'November',
			'12' => 'Desember'
		);
		for($i=2032;$i>=1900;$i--) $year[$i] = $i;
	
		$return .= '<div class="dekodr-calendar" style="display : inline-block">';
		
		if(!$disable_day)
			$return .= $this->drop_down(array('class' => 'dekodr-calendar-day', 'id'=>$param['name'].'_date-date',	'value' => date("d", strtotime($param['value'])),'onClick'=>'changeCal_date(\''.$param['name'].'\')'), $day);
		else		
			$return .= $this->hidden(array('class' => 'dekodr-calendar-day', 'value' => '01'));

		$return .= $this->drop_down(array('class' => 'dekodr-calendar-month', 'id'=>$param['name'].'_date-month',	'value' => date("m", strtotime($param['value'])),'onClick'=>'changeCal_date(\''.$param['name'].'\')'), $month);
		$return .= $this->drop_down(array('class' => 'dekodr-calendar-year', 'id'=>$param['name'].'_date-year', 'value' => date("Y", strtotime($param['value'])),'onClick'=>'changeCal_date(\''.$param['name'].'\')'), $year);		
		
		$return .= $this->hidden(array('name' => $param['name'], 'class' => 'dekodr-calendar-hidden', 'value' => $param['value'],'id'=>$param['name']));

		$return .= '</div>';


		return $return;
	}

	public function timestamp($param = array(), $disable_day = false){
		$return = '';
		$class = 'dekodr-calendar';
		if(!$param['value']) $param['value'] = date("Y-m-d H:i:s");


		for($i=1;$i<=31;$i++){ $x = $i; if($i < 10) $x = "0".$i; $day[$x] = $x; }
		$month = array(
			'01' => 'Januari',
			'02' => 'Febuari',
			'03' => 'Maret',
			'04' => 'April',
			'05' => 'Mei',
			'06' => 'Juni',
			'07' => 'Juli',
			'08' => 'Agustus',
			'09' => 'September',
			'10' => 'Oktober',
			'11' => 'November',
			'12' => 'Desember'
		);
		for($i=2032;$i>=1900;$i--) $year[$i] = $i;
	
		$return .= '<div class="dekodr-calendar" style="display : inline-block">';
		
		if(!$disable_day)
			$return .= $this->drop_down(array('class' => 'dekodr-calendar-day', 'id'=>$param['name'].'_date-date',	'value' => date("d", strtotime($param['value'])),'onClick'=>'changeCal_time(\''.$param['name'].'\')'), $day);
		else		
			$return .= $this->hidden(array('class' => 'dekodr-calendar-day', 'value' => '01'));

		$return .= $this->drop_down(array('class' => 'dekodr-calendar-month', 'id'=>$param['name'].'_date-month',	'value' => date("m", strtotime($param['value'])),'onClick'=>'changeCal_time(\''.$param['name'].'\')'), $month);
		$return .= $this->drop_down(array('class' => 'dekodr-calendar-year', 'id'=>$param['name'].'_date-year', 'value' => date("Y", strtotime($param['value'])),'onClick'=>'changeCal_time(\''.$param['name'].'\')'), $year);		
		$return .= ' ';
		$return .= $this->number(array('class' => 'dekodr-calendar-hour', 'id'=>$param['name'].'_date-hour', 'max'=>'24', 'min'=>'0', 'value' => date("H", strtotime($param['value'])),'onChange'=>'changeCal_time(\''.$param['name'].'\')','style'=>'width:30px'), $hour);	
		$return .= ' : ';	
		$return .= $this->number(array('class' => 'dekodr-calendar-minute', 'id'=>$param['name'].'_date-minute', 'max'=>'60', 'min'=>'0', 'value' => date("i", strtotime($param['value'])),'onChange'=>'changeCal_time(\''.$param['name'].'\')','style'=>'width:30px'), $minute);		
		$return .= ' : ';	
		$return .= $this->number(array('class' => 'dekodr-calendar-second', 'id'=>$param['name'].'_date-second',  'max'=>'60', 'min'=>'0', 'value' => date("s", strtotime($param['value'])),'onChange'=>'changeCal_time(\''.$param['name'].'\')','style'=>'width:30px'), $second);		
		
		$return .= $this->hidden(array('name' => $param['name'], 'class' => 'dekodr-calendar-hidden', 'value' => $param['value'],'id'=>$param['name']));

		$return .= '</div>';


		return $return;
	}

	public function calendar_filter($param = array(), $disable_day = false){
		$return = '';
		$class = 'dekodr-calendar';
		if(!$param['value']) $param['value'] = date("Y-m-d");


		for($i=1;$i<=31;$i++){ $x = $i; if($i < 10) $x = "0".$i; $day[$x] = $x; }
		$month = array(
			'01' => 'Januari',
			'02' => 'Febuari',
			'03' => 'Maret',
			'04' => 'April',
			'05' => 'Mei',
			'06' => 'Juni',
			'07' => 'Juli',
			'08' => 'Agustus',
			'09' => 'September',
			'10' => 'Oktober',
			'11' => 'November',
			'12' => 'Desember'
		);
		for($i=2032;$i>=1900;$i--) $year[$i] = $i;
	
		$return .= '<div class="dekodr-calendar" style="display : inline-block">';
		
		if(!$disable_day)
			$return .= $this->drop_down(array('class' => 'dekodr-calendar-day', 'id'=>$param['name'].'_date-date',	'value' => date("d", strtotime($param['value'])),'onClick'=>'changeCal_date_filter($(this))'), $day);
		else		
			$return .= $this->hidden(array('class' => 'dekodr-calendar-day', 'value' => '01'));

		$return .= $this->drop_down(array('class' => 'dekodr-calendar-month', 'id'=>$param['name'].'_date-month',	'value' => date("m", strtotime($param['value'])),'onClick'=>'changeCal_date_filter($(this))'), $month);
		$return .= $this->drop_down(array('class' => 'dekodr-calendar-year', 'id'=>$param['name'].'_date-year', 'value' => date("Y", strtotime($param['value'])),'onClick'=>'changeCal_date_filter($(this))'), $year);		
		
		$return .= $this->hidden(array('name' => $param['name'], 'class' => 'dekodr-calendar-hidden', 'value' => $param['value'],'id'=>$param['name']));

		$return .= '</div>';


		return $return;
	}

	public function lifetime_calendar($param = array()){
		$return = '<div class="dekodr-calendar-lifetime" style="display : inline-block">';
			$return .= $this->calendar($param);
			$return .= '<div class="dekodr-calendar-checkbox">';
				$checked = ($param['value']=='lifetime') ? 'checked' : '';
				$text = (isset($param['text_str'])) ? $param['text_str'] : 'Selama Perusahaan Berdiri';
				$return .= '<div><label><input type="checkbox" '.$checked.' name="'.$param['name'].'" value="lifetime" class="dekodr-calendar-checkbox-input"/>'.$text.'</label></div>';
			$return .= '</div>';
		$return .= '</div>';
		
		return $return;
	}

	public function file($param = array()){
		$path = $param['path'];
		unset($param['path']);
		$pathKey = $param['name'].'-path';
		$pathValue = preg_replace('/[^a-zA-Z0-9_\\-]/', '', (string) $param['name']);

		if(!$param['value']){
			$return = '<input type="file"';
			foreach($param as $index => $_param){
				$return .= ' '.$index.'="'.$_param.'"';
			}
			$return .= '/>';
			$return .= ((!ISSET($param['caption'])) ?'<p><i>Format data harus PDF, JPEG, JPG, PNG, GIF ,ZIP atau RAR.  Max 2 MB (2048 KB)</i></p>' : $param['caption']);
			$return .= form_error($param['name']);
			$return .= $this->hidden(array('name' => $pathKey, 'value' => $pathValue));
		}
		else{
			
					$return .= '<p><a target="_blank" href="'.base_url('lampiran/'.$param['name'].'/'.$param['value']).'" target="_blank">Lampiran</a><p>';
					$return .= '<input type="file"';
					unset($param['value']);
					foreach($param as $index => $_param){
						$return .= ' '.$index.'="'.$_param.'"';
					}
					$return .= '/>';
					$return .= ((!ISSET($param['caption'])) ?'<p><i>Format data harus PDF, JPEG, JPG, PNG, GIF ,ZIP atau RAR.  Max 2 MB (2048 KB)</i></p>' : $param['caption']);
					$return .= form_error($param['name']);
					$return .= $this->hidden(array('name' => $pathKey, 'value' => $pathValue));
				
		}

		return $return; 
	}

	public function upload($name = ''){
		$pathKey = $name.'-path';
		$path = isset($_POST[$pathKey]) ? (string) $_POST[$pathKey] : '';
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('/[^a-zA-Z0-9_\\-\\/]/', '', $path);
		$path = trim($path, '/');
		$path = preg_replace('#/+#', '/', $path);
		$path = str_replace('..', '', $path);
		if ($path === '') {
			return array('status' => 'error', 'message' => 'Invalid upload path');
		}

		$expectedPrefix = preg_replace('/[^a-zA-Z0-9_\\-]/', '', (string) $name);
		if ($expectedPrefix === '' || ($path !== $expectedPrefix && strpos($path, $expectedPrefix . '/') !== 0)) {
			return array('status' => 'error', 'message' => 'Invalid upload path');
		}

		$baseDir = realpath('./lampiran');
		if ($baseDir === false) {
			return array('status' => 'error', 'message' => 'Upload base directory not found');
		}
		$targetDir = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
		if (strpos($targetDir, $baseDir) !== 0) {
			return array('status' => 'error', 'message' => 'Invalid upload path');
		}
		if (!is_dir($targetDir) && !mkdir($targetDir, 0750, true)) {
			return array('status' => 'error', 'message' => 'Failed to create upload directory');
		}
		$resolvedTargetDir = realpath($targetDir);
		if ($resolvedTargetDir === false || strpos($resolvedTargetDir, $baseDir) !== 0) {
			return array('status' => 'error', 'message' => 'Invalid upload path');
		}

		$config['upload_path']		= $resolvedTargetDir;
		$config['allowed_types']	= 'pdf|jpeg|jpg|png|gif|zip|rar';
		$config['max_size']			= 2000;
		$config['file_name'] 		= $file_name = date("YmdHis").rand(1, 9); 

		$this->CI->upload->initialize($config);

        if(!$this->CI->upload->do_upload($name))
			return array('status' => 'error', 'message' => $this->CI->upload->display_errors());
		else{
			$_POST[$name] = $file_name.$this->CI->upload->data('file_ext');
			return array('status' => 'success', 'message' => 'upload success');
		}
	}

	public function input($rule = array()){
		if($_FILES){
			foreach($_FILES as $index => $data){
				if(!$err = $this->upload($index)) return $err;
			}
		}

		$return = new stdClass();

		foreach($_POST as $index => $data){

			if(strpos($index, "-old")){
				if(!$_FILES[$index])
					$index = str_replace("-old", "", $index);
			}

			$search = array(
			    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
			    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
			);
			$_data = preg_replace($search, '', $data);
			
			$return->{$index} = is_string($data) ? $_data : $data;
		}

		return $return;
	}

	public function get_temp_data($field_name = ''){
		$form = $this->CI->session->userdata('form');
		return (set_value($field_name)) ? set_value($field_name):((isset($form[$field_name]))?$form[$field_name]:'') ;
	}
	
}
