<?php
class Jctable{
	
	private $CI;
	
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();	
	}
	
	function generate_filter($setting = array()){
		$data['setting'] 		= $setting['param']; 
		$data['table_setting'] 	= $setting['setting']; 
		

		if(isset($setting['setting']['custom_view'])) $this->CI->load->view($setting['setting']['custom_view'], $data);

		$this->CI->load->view('jc-table/table/jc-table-filter', $data);
	}
	
	function generate_table($settings = array()){
		$table_param 	= $settings['param'];
		$table_setting 	= $settings['setting'];
		
		$data['table_setting'] = $table_setting;
		
		$model = explode("/", $table_setting['model']);
		$model = $model[count($model) - 1];
		
		$physic_addr = $table_setting['model'];
				
		$this->CI->load->model($table_setting['model']);
		$this->CI->load->library('pagination');
		
		$query = $this->CI->$model->get_data_tb();

		$paging['limit']	= $_POST['jc-table-page-limit'];
		$paging['page']		= $_POST['jc-table-page-num'];
		$ord['name']		= $_POST['jc-table-ord-name'];
		$ord['dir']			= $_POST['jc-table-ord-dir'];
		
		//popping the array 4 times, to remove the page and ord variable. we no longer need it
		for($i=0;$i<4;$i++) array_pop($_POST);
		
		$setting = $_POST;
		$data['filter'] = $setting;
		
		//cek whenether there's any filter applied. it wil changed the label in the bottom-most row on the table.
		//i encourage you to check the different between filtered and unfiltered table
		if($setting) $data['filtered'] = true;

		//get grand total deal (without paging)
		$grand_total = $this->query($query, array(), $table_param);	
		$grand_total = $grand_total->num_rows();
					
		//get total deal on current page (paging included)
		$total = $this->query($query, $setting, $table_param);	
		$total = $config['total_rows'] = $total->num_rows();
				
		//determine the paging value. the default is 25. i suggest you NOT TO CHANGE the default value
		if(!$paging['limit']) $paging['limit'] = 25;
		if(!$paging['page'])  $paging['page'] = 0;
		
		//determine value of LIMIT on sql
		$config['per_page'] = $param['limit'] = $paging['limit']; 
		$config['cur_page'] = $param['start'] = $paging['page']; 
			
		//paginating..
		$this->CI->pagination->initialize($config);
		$data['pagination'] = $this->CI->pagination->create_links();
					
		//get the main data source. you may check the model anyway.
		$data['query'] = $this->query($query, $setting, $table_param, $param, $ord);
		
		//set session for filter, _param variable and ording. this is on purpose on report generating
		//so why i have to put this on session ? because to generate report we have to open another controller
		//and.. here is the best i know to send these array type variable from this page to that frame.
		
		$_SESSION['temp_filter'] = $setting;
		$_SESSION['temp_setting'] = $table_param;
		$_SESSION['temp_ord'] = $ord;
		
		//set the _param values. this used to filter generating
		$data['setting'] = $table_param;
		$data['page_limit'] = $this->drop_down_paging('jc-table-page-limit', $param['limit']); //, 'page_limit(this.value)');
		
		//just some ording setting
		$data['ord_name'] = $ord['name'];
		$data['ord_dir'] = $ord['dir'];
		
		//filtered total and grand total number to display
		$data['filtered_total'] = $total;
		$data['grand_total'] = $grand_total;
				
		//to make a helper of data paging. see the bottom-left, beside the dropdown of data number. yap.. here they 
		//are been setted

		$data['row_start'] = $paging['page'] + 1;
		$data['row_end'] = $data['row_start'] + $data['query']->num_rows() - 1;
		$data['indicator'] = 'deal';
		
		//count on mean using index of the field that will be counted on the bottom-most row of data
		$data['count_on'] = 9;
		$data['count_label'] = "Amount";
		
		//setting filter, URL is link for the first field
		$data['filter'] = $setting;
		//$data['url'] = base_url()."index.php/deals/deal_detail";
		
		$data['custom_setting'] = $settings['custom_display'];
		
		$this->CI->load->view('jc-table/table/jc-table-table', $data);
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
	
	function query($query = '', $filter = array(), $setting = array(), $paging = array(), $ord = array()){
			
		$sql = $query['sql'];
		$param = $query['param'];
		
		for($i=0;$i<count($filter);$i++){
			$_ftr = key($filter);
			 
			$name  = $setting[$_ftr]['field'];
			$type  = $setting[$_ftr]['type'];
			$value = $filter[$_ftr];

			$sql .= $this->generate_query_filter($name, $value, $type);
			next($filter);
		}
			
		if($ord['name'])
			$sql .= " ORDER BY ".$ord['name']." ".$ord['dir'];
			
		if($paging and $paging['limit'] != 'all'){
			if(!$paging['start']) $paging['start'] = 0;
			if(!$paging['limit']) $paging['limit'] = 25;
			
			$sql .= " LIMIT ".$paging['start'].",".$paging['limit']; 
		}
		
		return $this->CI->db->query($sql, $param);		
	}
	
	function generate_query_filter($name = '', $value = '', $type = '')
	{
		$sql = "";
		
		if($type == "pick_list"){
			$sql .= " AND (";
			$x = 1;
						
			foreach($value as $_ftr){				
				$sql .= " ".$name." = '".$_ftr."' ";
				if($x != count($value)) $sql .= " OR ";
				$x++;
			}
				
			$sql .= ")";
		}
		else if($type ==  "text"){
			$sql .= " AND (";
			$x = 1;
				
			foreach($value as $_ftr){
				$sql .= "LOWER(".$name.") LIKE '%".str_replace(" ", "%", strtolower($_ftr))."%'";
				if($x != count($value)) $sql .= " OR ";
				$x++;
			}
				
			$sql .= ")";
		}
		else if($type == 'number'){
			$sql .= " AND ( ";
			$sql .= $name." >= ".$value['from']." AND ".$name." <= ".$value['to']." ";
			$sql .= " )";
		}
		else if($type == 'date'){
			$sql .= " AND ( ";
				
			if($value['year'])
			$sql .= " YEAR(".$name.") = '".$value['year']."' ";
			
			if($value['year'] and ($value['period'] or $value['month']))
			$sql .= " AND ";
			
			if($value['period']){
				if($value['period'] == "1")
				$sql .= " (MONTH(".$name.") >= '1' AND MONTH(".$name.") <= '3')";
				else if($value['period'] == "2")
				$sql .= " (MONTH(".$name.") >= '4' AND MONTH(".$name.") <= '6')";
				else if($value['period'] == "3")
				$sql .= " (MONTH(".$name.") >= '7' AND MONTH(".$name.") <= '9')";
				else if($value['period'] == "4")
				$sql .= " (MONTH(".$name.") >= '10' AND MONTH(".$name.") <= '12')";
			}
			if($value['month']){
				$sql .= " MONTH(".$name.") = '".$value['month']."'";
			}
			
			if(!$value['from'] and $value['to'])
				$sql .= " ".$name." <= '".$value['to']."'";
			if(!$value['to'] and $value['from'])
				$sql .= " ".$name." <= '".$value['from']."'";
			if($value['from'] and $value['to'])
				$sql .= $name." >= '".$value['from']."' AND ".$name." <= '".$value['to']."'";
				
			$sql .= ")";
		}

		return $sql;
	}
	
}