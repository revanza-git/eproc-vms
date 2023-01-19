<?php
class Hpsoe_master extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('hpsoe/hpsoe_model');
		$this->load->model('hpsoe/hpsoe_group_model');
	}
	
	function index($is_public = false){
		$data['query'] = $this->hpsoe_model->get_variable();
		$data['formula'] = $this->hpsoe_model->get_variable('formula');
		
		$setting = array(
			'db' => 'hpsoe_pgn.ms_group',
			'id' => 'id',
			'value' => 'name',
			'disable_empty' => true
		);
		$data['group'] = $this->form->drop_down_db('group', '', $setting);
		
		$setting = array(
			'db' => 'vm_pgn.tb_kurs',
			'id' => 'symbol',
			'value' => 'symbol'
		);
		$data['kurs'] = $this->form->drop_down_db('kurs', '', $setting);
		
		$function = "
			function searchuOm(data){
				var master = data;
				
				var callback = function(data){
					$('#satuan').val(data.nama);
					$('#komag').val(master.komag);
					console.log(master.komag);
				}
				
				ajaxJsonFeedBack(base_url + 'index.php/utilities/get_uom', 'query=' + data.id, callback);
			}";		
		$data['komag_search'] = $this->utility->komag('komag-search', '', $function);
		
		$data['volume'] = $this->form->money('volume', '', array(10,10));
		$data['komag'] = $this->form->text_box('komag', '', array(15,15));
		$data['satuan'] = $this->form->text_box('satuan', '', array(5,5));
		
		if($is_public) $data['is_public'] = true;
		
		$this->load->view('content/hpsoe/master', $data);
	}
	
	function user_view(){
		$this->load->view('template/main_hpsoe');
	}
	
	function form_formula($id = ''){
		$data['content'] = "form/hpsoe/formula";
		$data['width'] = 700;
		
		$setting = array(
			array('name' => 'Tambah (+)', 'value' => '+'),
			array('name' => 'Kurang (-)', 'value' => '-'),
			array('name' => 'Kali (x)', 'value' => '*'),
			array('name' => 'Bagi (/)', 'value' => '/'),
			array('name' => 'Prosentase (%)', 'value' => '%'),
			array('name' => 'SUM', 'value' => 'sum')
		);
		$data['operation'] = $this->form->drop_down('operation', $setting, 'draw_formula()', '');
		
		$data['name'] = $this->form->text_box('name', '', array(50, 150));
		$data['var_name'] = $this->form->text_box('var_name', '', array(5, 5), 'onkeyup="draw_formula()"');
		$data['remark'] = $this->form->text_area('remark', '', array(50,3));
		
		$data['val_1'] = $this->drop_down_formula('val_1');
		$data['val_2_id'] = $this->drop_down_formula('val_2_id', true);
		$data['val_2_of'] = $this->drop_down_formula('val_2_of', true);
		
		$this->load->view("jc-table/form/jc-form", $data);
	}
	
	function drop_down_formula($name = '', $is_noname = false){
		$return = '<select '; 
		if(!$is_noname) $return .= 'name="'.$name.'" id="'.$name.'"'; else $return .= ' class="hidden-'.$name.'"'; 
		$return .= ' onchange="draw_formula()">';

		$return .= '<option value="">-- pilih --</option>';
		$query = $this->hpsoe_model->get_formula();
		foreach($query->result() as $data)
			$return .= '<option value="'.$data->id.'">'.$data->var_name.'</option>';
		$return .= '</select>';
		
		return $return;
	}
	
	function formula_drawer($is_report = false){
		$data['query'] = $this->hpsoe_model->get_variable('formula', $_POST['group']);
		$data['variable'] = $this->hpsoe_model->get_variable('variable');
		$data['group'] = $this->hpsoe_group_model->get_group();
		
		$data['param'] = $_POST;
		if($is_report) $data['param']['is_report'] = "true"; else $data['param']['is_report'] = "false";
		
		$this->load->view('content/hpsoe/formula-drawer', $data);	
	}
	
	function save(){
		$param = array(
			'name' => $_POST['name'],
			'var_name' => $_POST['var_name'], 
			'type' => 'formula',
			'remark' => $_POST['remark'],
			'val_1' => $_POST['val_1'], 
			'operation' => $_POST['operation'],
			'var_type_2' => $_POST['var_type_2'],
			'val_2_id' => $_POST['val_2_id'],
			'val_2' => $_POST['val_2'],
			'val_2_of' => $_POST['val_2_of']
		);
		$this->hpsoe_model->save($param);
		
		$json = array(
			'status' => 'success',
			'message' => 'formula telah di simpan !'
		);
		die(json_encode($json));
	}
	
	function delete($id = ''){
		$this->hpsoe_model->delete($id);
		
		$json = array(
			'status' => 'success',
			'message' => 'formula telah di ubah !'
		);
		die(json_encode($json));
	}
}