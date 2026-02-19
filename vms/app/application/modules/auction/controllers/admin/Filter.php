<?php
/*
 * Filter class, part of jc-table framework
 * @author :alexandroputra
 * 
 * WARNING !! Do Not Change a Thing !
 * 
 * */

class Filter extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->library('form');
	}
	
	function form_default(){
		$id_type = explode("/", $_POST['id_type']);
		
		$id = $id_type[2];
		$type = $id_type[1];
				
		if($type == "text"){
			$return = 'Ketikkan filter anda &nbsp; <input type="text" size="20" value="" id="text-'.$id.'"/>';
		}
		
		if($type == "kualifikasi"){
			$return = 
			'<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td valign="top" width="100" align="right">Pilih Filter</td>
					<td valign="top" width="20" align="center">:</td>
					<td valign="top">
						<select id="text-'.$id.'">
							<option value="kecil">Kecil</option>
							<option value="non-kecil">non-Kecil</option>
						</select>
					</td>
				</tr>
			 </table>';
			
			$type = "text";
		}
		
		if($type == "group_bsb"){
			$return = 
			'<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td valign="top" width="100" align="right">Pilih Filter</td>
					<td valign="top" width="20" align="center">:</td>
					<td valign="top">
						<select id="text-'.$id.'">
							<option value="Barang">Barang</option>
							<option value="Jasa Pekerjaan Konstruksi">Jasa Pekerjaan Konstruksi</option>
							<option value="Jasa Konsultan Perencana">Jasa Konsultan Perencana/Pengawas Konstruksi</option>
							<option value="Jasa Konsultan non-Konstruksi">Jasa Konsultan non-Konstruksi</option>
							<option value="Jasa Lainnya">Jasa Lainnya</option>
						</select>
					</td>
				</tr>
			 </table>';
			
			$data['width'] = 500;
			$type = "text";
		}
		
		else if($type == "bsb"){
			$setting = array(
				'db' => 'tb_bidang',
				'id' => 'id',
				'value' => 'name'
			);
			$return = $this->form->drop_down_db('bsb-id_bidang', '', $setting, 'onchange="fill_sub_bidang(\'bsb-id_sub_bidang\', this.value)"');	
			$data['width'] = 600;
		}
		
		$data['filter_content'] = $return;
		$data['type'] = $type;
		$data['name'] = $id;
		$data['form_id'] = "filter";
		
		$data['content'] = "jc-table/filter/jqFilter-constructor";
		
		$this->load->view("jc-table/form/jc-form", $data);
	}
}