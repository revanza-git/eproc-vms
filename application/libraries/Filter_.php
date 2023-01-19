<?php 
class Filter{
	private $CI;

	public function __construct(){
		$this->CI =& get_instance(); 

		$this->CI->load->library('upload');
	}
	

	public function group_filter_post($field=null){

		if($field === null){
			$field = $this->get_field();
		}
		// $filter = $this->CI->session->userdata('filter')[$this->CI->uri->uri_string()];
		$filter = $this->CI->input->post('filter');

		$html = '<div class="filter">
				<div class="filterHeader">
				<a class="filterBtn"><i class="fa fa-filter"></i>&nbsp;Filter&nbsp;<i class="fa fa-angle-down"></i></a>

				</div>
				<div class="groupFilterArea clearfix">
					<div class="filterForm">';
					
					
					foreach($field as $row){
						$displayActive = (count($row['filter'])>0) ? 'style="display: block;"' : '';
						// $displayActive = count($row['filter'])>0;
						$html .= '<div class="groupForm">
									<div class="groupFormHeader">
										<label class="title">'.$row['label'].'</label><i class="fa fa-sort-desc"></i>
									</div>
									<div class="groupFormContent" '.$displayActive.'>';
									/* New Filter Input*/

									foreach($row['filter'] as $key => $value){

										$field = explode('|',$value['table']);

										switch ($value['type']) {
											case 'text':
												$html .= '<div class="groupFieldBlock"><label class="title">'.$value['label'].'</label>';

												$html .= '<div class="groupFieldInput" name="filter['.$value['table'].'][]">'.
														 '<input type="text" class="hiddenFilter">'.
														 '<div class="groupFieldWrap">';

												/*Make iteration for input value from posted value*/
												foreach($filter[$value['table']] as $key => $row){
													$html .= '<input type="text" name="filter['.$value['table'].'][]" value="'.$row.'" class="'.$value['class'].'">';
												}
												
												$html .= '<a href="#" class="addFilterGroup" ><i class="fa fa-plus"></i></a>&nbsp;<a href="#" class="removeFilterGroup" ><i class="fa fa-minus"></i></a></div></div>';
												$html .= '</div>';
												break;

											case 'dropdown':
												$html .= '<div class="groupFieldBlock"><label class="title">'.$value['label'].'</label>';
												$data_dropdown = $this->$field[2]();
												$html .= '<div class="groupFieldInput" name="filter['.$value['table'].'][]" >'.
														 form_dropdown(NULL, $data_dropdown, NULL,' class="hiddenFilter"').
														 '<div class="groupFieldWrap">';

												/*Make iteration for input value from posted value*/
												foreach($filter[$value['table']] as $key => $row){
													$html .= form_dropdown('filter['.$value['table'].'][]', $data_dropdown, $row,' class="'.$value['class'].'"');
												}

												$html .= '<a href="#" class="addFilterGroup" ><i class="fa fa-plus"></i></a>&nbsp;<a href="#" class="removeFilterGroup" ><i class="fa fa-minus"></i></a></div></div>';
												$html .= '</div>';
												break;

											case 'date_range':
												// echo $filter[$value['label']];
												$html .= '<div class="groupFieldInput" name="filter['.$value['table'].']"><div class="groupFieldWrap">';
												$html .= '<div class="groupFieldBlock hiddenFilter"><label class="title">'.$value['label'][0].'</label>';
												$html .= $this->CI->form->calendar_filter(array(), false);
												$html .= '<label>'.$value['label'][1].'</label>';
												$html .= $this->CI->form->calendar_filter(array(), false);
												$html .= '</div>';
												$html .= '<div class="dateWrap clearfix">';
												
												foreach($filter[$value['table']]['start_date'] as $key => $row){

													$html .= '<div class="groupFieldBlock ">';

														$html .= '<label>'.$value['label'][0].'</label>';
														$html .= $this->CI->form->calendar_filter(array('name'=>'filter['.$value['table'].'][start_date]['.$key.']','value'=>$filter[$value['table']]['start_date'][$key]), false);
														$html .= '<label>'.$value['label'][1].'</label>';
														$html .= $this->CI->form->calendar_filter(array('name'=>'filter['.$value['table'].'][end_date]['.$key.']','value'=>$filter[$value['table']]['end_date'][$key]), false);
														
													$html .= '</div>';
												}

												$html.='</div>';
												$html .= '<a href="#" class="addFilterGroupDate" ><i class="fa fa-plus"></i></a>&nbsp;<a href="#" class="removeFilterGroupDate" ><i class="fa fa-minus"></i></a></div>';
												$html .= '</div>';
												break;

											
											case 'single_date':
												$html .= '<div class="groupFieldBlock"><label class="title">'.$value['label'].'</label>';

												$html .= '<div class="groupFieldInput" name="filter['.$value['table'].'][]">'.
														 '<input type="text" class="hiddenFilter">'.
														 '<div class="groupFieldWrap">';

												/*Make iteration for input value from posted value*/
												foreach($filter[$value['table']] as $key => $row){
													$html .= $this->CI->form->calendar_filter(array('name'=>'filter['.$value['table'].'][start_date]['.$key.']','value'=>$filter[$value['table']]['start_date'][$key]), false);
												}
												
												$html .= '<a href="#" class="addFilterGroup" ><i class="fa fa-plus"></i></a>&nbsp;<a href="#" class="removeFilterGroup" ><i class="fa fa-minus"></i></a></div></div>';
												$html .= '</div>';
												break;
											case 'number_range':
												// echo $filter[$value['label']];
												$html .= '<div class="groupFieldBlock "><label class="title">'.$value['label'].'</label><div class="groupFieldInput" name="filter['.$value['table'].']"><div class="groupFieldWrap">';
												$html .= '<div class="groupFieldBlock hiddenFilter"><label class="title">Nilai Minimum</label>';
												$html .= '<div class="dekodr-range-number">'.$this->CI->form->number(array('value'=>0), false).'</div>';
												$html .= '<label>Nilai Maximum</label>';
												$html .= '<div class="dekodr-range-number">'.$this->CI->form->number(array('value'=>0), false).'</div>';
												$html .= '</div>';
												$html .= '<div class="dateWrap clearfix">';
												
												foreach($filter[$value['table']]['start_value'] as $key => $row){

													$html .= '<div class="groupFieldBlock ">';

														$html .= '<label>Nilai Minimum</label>';

														$html .= '<div class="dekodr-range-number">'.$this->CI->form->number(array('name'=>'filter['.$value['table'].'][start_value]['.$key.']','value'=>$filter[$value['table']]['start_value'][$key]), false).'</div>';
														$html .= '<label>Nilai Maximum</label>';
														$html .= '<div class="dekodr-range-number">'.$this->CI->form->number(array('name'=>'filter['.$value['table'].'][end_value]['.$key.']','value'=>$filter[$value['table']]['end_value'][$key]), false).'</div>';
														
													$html .= '</div>';
												}

												$html.='</div>';
												$html .= '<a href="#" class="addFilterNumberRange" ><i class="fa fa-plus"></i></a>&nbsp;<a href="#" class="removeFilterNumberRange" ><i class="fa fa-minus"></i></a></div>';
												$html .= '</div></div>';
												break;
											case 'date':
												// echo $filter[$value['label']];
												$html .= '<div class="groupFieldBlock "><label class="title">'.$value['label'].'</label><div class="groupFieldInput" name="filter['.$value['table'].']"><div class="groupFieldWrap">';
												$html .= '<div class="groupFieldBlock hiddenFilter"><label>Dari Tanggal</label>';
												$html .= $this->CI->form->calendar_filter(array(), false);
												$html .= '<label>Sampai</label>';
												$html .= $this->CI->form->calendar_filter(array(), false);
												$html .= '</div>';
												$html .= '<div class="dateWrap clearfix">';
												
												foreach($filter[$value['table']]['start_date'] as $key => $row){

													$html .= '<div class="groupFieldBlock ">';

														$html .= '<label>Dari Tanggal</label>';
														$html .= $this->CI->form->calendar_filter(array('name'=>'filter['.$value['table'].'][start_date]['.$key.']','value'=>$filter[$value['table']]['start_date'][$key]), false);
														$html .= '<label>Sampai</label>';
														$html .= $this->CI->form->calendar_filter(array('name'=>'filter['.$value['table'].'][end_date]['.$key.']','value'=>$filter[$value['table']]['end_date'][$key]), false);
														
													$html .= '</div>';
												}

												$html.='</div>';
												$html .= '<a href="#" class="addFilterGroupDate" ><i class="fa fa-plus"></i></a>&nbsp;<a href="#" class="removeFilterGroupDate" ><i class="fa fa-minus"></i></a></div>';
												$html .= '</div></div>';
												break;
											case 'lifetime_date':
												$html .= '<div class="groupFieldBlock" ><label class="title">'.$value['label'].'</label><div class="groupFieldInput" name="filter['.$value['table'].']"><div class="groupFieldWrap">';
												$html .= '<div class="groupFieldBlock hiddenFilter"><label>Dari Tanggal</label>';
												$html .= $this->CI->form->lifetime_calendar(array(), false);
												$html .= '<label>Sampai</label>';
												$html .= $this->CI->form->lifetime_calendar(array(), false);
												$html .= '</div>';
												$html .= '<div class="dateWrap clearfix">';
												
												foreach($filter[$value['table']]['start_date'] as $key => $row){

													$html .= '<div class="groupFieldBlock ">';

														$html .= '<label>Dari Tanggal</label>';
														$html .= $this->CI->form->lifetime_calendar(array('name'=>'filter['.$value['table'].'][start_date]['.$key.']','value'=>$filter[$value['table']]['start_date'][$key]), false);
														$html .= '<label>Sampai</label>';
														$html .= $this->CI->form->lifetime_calendar(array('name'=>'filter['.$value['table'].'][end_date]['.$key.']','value'=>$filter[$value['table']]['end_date'][$key]), false);
														
													$html .= '</div>';
												}

												$html.='</div>';
												$html .= '<a href="#" class="addFilterGroupDate" ><i class="fa fa-plus"></i></a>&nbsp;<a href="#" class="removeFilterGroupDate" ><i class="fa fa-minus"></i></a></div>';
												$html .= '</div></div>';
												break;
										}

									}

									
							$html .='</div>';
						$html .= '</div>';
					}

		$html .='</div><button type="submit" class="btnBlue"><i class="fa fa-filter"></i>&nbsp;Filter</button></div></div>';

		return $html;
	}

	public function generate_query($instance,$filter){
		
		/*CLEAN ALL ARRAY*/
		$clean_filter = array();

		
		$post_filter = $this->CI->input->post('filter');
		$sess_filter = $this->CI->session->userdata('filter')[$this->CI->uri->uri_string()];

		foreach($post_filter as $key => $val_arr){
			if(isset($val_arr[0])){
				if(count($val_arr[0])==1 && $val_arr[0]=='' ) 
					unset($post_filter[$key]);
			}
		}
		
		foreach($sess_filter as $key => $val_arr){
			// if(isset($val_arr[0])){
				if(!isset($post_filter[$key])) 
					unset($sess_filter[$key]);
			// }
		}

		if(empty($filter_session[$this->CI->uri->uri_string()]) || !isset($filter_session[$this->CI->uri->uri_string()])){
			$filter_session = array();
		}
		$filter_session[$this->CI->uri->uri_string()] = (!empty($post_filter)) ? $post_filter : ((!empty($sess_filter))? $sess_filter : array());
		$this->CI->session->set_userdata('filter',$filter_session);
		$filter_data = $this->CI->session->userdata('filter')[$this->CI->uri->uri_string()];
		


		if($filter_data){
			
			
			/*END CLEAN ALL ARRAY*/
			$field_array = $this->field_array();
			$join = array();
			$date_array = array();

			foreach($filter_data as $key => $row){
				$str = '';
				$field = explode('|',$key);					

				// $field[0] = str_replace('-', '', subject)
				/*Cek apabila berbentuk tanggal*/
				if(isset($row['start_date'])||isset($row['end_date'])){
					$total_row = 0;	
					$str .= '(';

					foreach($row['start_date'] as $k => $value){
					// 	/*Generate Query*/
						
						if(isset($field_array[$field[0]])){
							$join[$field[0]] = $field_array[$field[0]];
						}

						$pre = ($total_row==0) ? '' : ' OR ';

						$str .= $pre.' ( `'.$field[0].'`.`'.$field[1].'` >= "'.$row['start_date'][$k].'" AND `'.$field[0].'`.`'.$field[1].'` <= "'.$row['end_date'][$k].'")';
						
						$total_row ++;

						
					}

					$str .=')';
					$instance->where($str);
					
				}elseif(isset($row['start_value'])||isset($row['end_value'])){
					$total_row = 0;	
					$str .= '(';

					foreach($row['start_value'] as $k => $value){
					// 	/*Generate Query*/
						
						if(isset($field_array[$field[0]])){
							$join[$field[0]] = $field_array[$field[0]];
						}

						$pre = ($total_row==0) ? '' : ' OR ';

						$str .= $pre.' ( `'.$field[0].'`.`'.$field[1].'` >= "'.$row['start_value'][$k].'" AND `'.$field[0].'`.`'.$field[1].'` <= "'.$row['end_value'][$k].'")';
						
						$total_row ++;

						
					}

					$str .=')';
					$instance->where($str);

				}else{
					
					$total_row = 0;	 
					$str .= '(';


					// if($field[0]=='ms_agen_produk')
					// 	$join['ms_agen msa'] = $field_array['ms_agen'];
					foreach($row as $k => $value){
						
						if($value!=''){

							if(isset($field_array[$field[0]])){
								$join[$field[0]] = $field_array[$field[0]];
							}

								$pre = ($total_row==0) ? '' : ' OR ';
								$str .= $pre.' '.$field[0].'.'.$field[1].' LIKE "%'.$value.'%"';
								
								
								
								
				
						}
						$total_row ++;
					}
					if(isset($field[3])){
						$join['ms_ijin_usaha miu'] = $field_array['miu'];
						$instance->where('miu.type',$field[3]);
					}
					
					$str .= ')';
					$instance->where($str,NULL,TRUE);
				}
				
			}

			foreach($join as $field => $join_str){
				$instance->join($field, $join_str);
			}
		}

		return $instance;

	}

	public function field_array(){
		$arr = array(
			// 'tb_legal'=>'tb_legal.id=ms_vendor_admistrasi.id_legal',
			// 'ms_akta'=>'ms_akta.id_vendor=ms_vendor.id',
			// 'ms_pengurus'=>'ms_pengurus.id_vendor=ms_vendor.id',
			'ms_pemilik'=>'ms_pemilik.id_vendor=ms_vendor.id',
			// 'ms_situ msst'=>'msst.id_vendor=ms_vendor.id',
			'miu'=>'miu.id_vendor=ms_vendor.id',
			'ms_iu_bsb'=>'ms_iu_bsb.id_vendor=ms_vendor.id',
			//'ms_csms'=>'ms_csms.id_vendor=ms_vendor.id',
			// 'tb_bidang'=>'tb_bidang.id_dpt_type=ms_ijin_usaha.id_dpt_type',
			'tr_dpt'=>'tr_dpt.id_vendor=ms_vendor.id',
			// 'ms_agen'=>'ms_agen.id_vendor=ms_vendor.id',
			'ms_agen_produk'=>'ms_agen_produk.id_agen=ms_agen.id',
			// 'ms_pengalaman'=>'ms_pengalaman.id_vendor=ms_vendor.id',
			// 'ms_pengalaman'=>'ms_pengalaman.id_vendor=ms_vendor.id',
			'tr_assessment tra'=>'tra.id_vendor=ms_vendor.id',
			// 'tr_assessment_point'=>'tr_assessment_point.id_vendor=ms_vendor.id'
		);
		return $arr;
	}


	function get_legal(){
		$arr = $this->CI->db->select('id,name')->where('del',0)->get('tb_legal')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}

	function status_dpt(){
		$data[0] = 'Menunggu Verifikasi';
		$data[1] = 'Menunggu Di Angkat';
		return $data;
	}
	function get_dpt_type(){
		$arr = $this->CI->db->select('id,name')->get('tb_dpt_type')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}
	function get_izin_type(){
		return array(	'siup'=>'SIUP',
						'ijin_lain'=>'Surat Izin Usaha Lainnya',
						'asosiasi'=>'Sertifikat Asosiasi/Lainnya',
						'siujk'=>'SIUJK',
						'sbu'=>'SBU');
	}
	function get_bidang(){
		$arr = $this->CI->db->select('id,name')->where('del',0)->get('tb_bidang')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}

	function get_sub_bidang(){
		$arr = $this->CI->db->select('id,name')->where('del',0)->get('tb_sub_bidang')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['name'];
		}
		return $result;
	}
	function get_k3_limit(){
		$arr = $this->CI->db->select('id,value')->where('del',0)->get('tb_csms_limit')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['value'];
		}
		return $result;
	}
	function get_warna(){
		$arr = $this->CI->db->select('id,value')->where('del',0)->get('tb_blacklist_limit')->result_array();
		$result = array();
		foreach($arr as $key => $row){
			$result[$row['id']] = $row['value'];
		}
		return $result;
	}
	function get_is_vms(){
		return array(1=>'VMS',0=>'Non-VMS');
	}
	function get_kualifikasi(){
		return array(
			'kecil' => 'Kecil',
			'menengah' => 'Menengah',
			'besar' => 'Besar'
			);
	}
	function get_category_katalog(){
		return array(
			'barang' => 'Barang',
			'jasa' => 'Jasa',
			);
	}
	public function get_field($array = array()){
		$a =  array(
			array(
				'label'	=>	'Administrasi',
				'filter'=>	array(
								array('table'=>'ms_vendor|name' ,'type'=>'text','label'=> 'Nama Penyedia Barang / Jasa'),
								array('table'=>'ms_vendor_admistrasi|id|get_legal' ,'type'=>'dropdown','label'=> 'Badan Hukum'),
								array('table'=>'ms_vendor_admistrasi|vendor_website' ,'type'=>'text','label'=> 'Website'),
								array('table'=>'ms_vendor_admistrasi|npwp_code' ,'type'=>'text','label'=> 'NPWP','class'=>'npwp_code'),
								array('table'=>'ms_vendor_admistrasi|nppkp_code' ,'type'=>'text','label'=> 'NPPKP'),
								array('table'=>'ms_vendor_admistrasi|vendor_address' ,'type'=>'text','label'=> 'Alamat'),
								array('table'=>'ms_vendor_admistrasi|vendor_city' ,'type'=>'text','label'=> 'Kota'),
								array('table'=>'ms_vendor_admistrasi|vendor_email' ,'type'=>'text','label'=> 'Email'),
							)
			),
			array(
				'label'	=>	'Akta Perusahaan',
				'filter'=>	array(
								array('table'=>'ms_akta|no' ,'type'=>'text','label'=> 'No Akta')
							)
			),
			array(
				'label'	=>	'Pengurus',
				'filter'=>	array(
								array('table'=>'ms_pengurus|name' 	,'type'=>'text','label'=> 'Nama'),
								array('table'=>'ms_pengurus|no' 	,'type'=>'text','label'=> 'No. KITAS')
							)
			),
			array(
				'label'	=>	'Kepemilikan Modal',
				'filter'=>	array(
								array('table'=>'ms_pemilik|name' 	,'type'=>'text','label'=> 'Nama')
							)
			),
			array(
				'label'	=>	'SITU/SKPD',
				'filter'=>	array(
								array('table'=>'ms_situ|no' 	,'type'=>'text','label'=> 'No'),
								array('table'=>'ms_situ|address' 	,'type'=>'text','label'=> 'Alamat')
							)
			),
			array(
				'label'	=>	'Bidang / Sub Bidang',
				'filter'=>	array(
								array('table'=>'ms_iu_bsb|id_bidang|get_bidang|siup' ,'type'=>'dropdown','label'=> 'Bidang'),
								array('table'=>'ms_iu_bsb|id_sub_bidang|get_sub_bidang|siup' ,'type'=>'dropdown','label'=> 'Sub Bidang')
							)
			),
			array(
				'label'	=>	'Tanggal Diangkat DPT',
				'filter'=>	array(
								array('table'=>'tr_dpt|start_date' 	,'type'=>'date_range','label'=> array('Dari tanggal :',' Hingga Tanggal : '))
							)
			),
			array(
				'label'	=>	'CSMS',
				'filter'=>	array(
								array('table'=>'ms_csms|id_csms_limit|get_k3_limit','type'=>'dropdown','label'=> 'Kriteria CSMS'),
							)
			),
			array(
				'label'	=>	'Hasil Assessment',
				'filter'=>	array(
								array('table'=>'tr_assessment_point|category|get_warna','type'=>'dropdown','label'=> 'Warna'),
							)
			),
			array(
				'label'	=>	'TDP',
				'filter'=>	array(
								array('table'=>'tr_tdp|no' 	,'type'=>'text','label'=> 'No'),
							)
			),
			array(
				'label'	=>	'SIUP',
				'filter'=>	array(
								array('table'=>'ms_ijin_usaha|no||siup' 	,'type'=>'text','label'=> 'No'),
								array('table'=>'ms_ijin_usaha|qualification||siup' 	,'type'=>'text','label'=> 'Kualifikasi'),
								array('table'=>'ms_iu_bsb|id_bidang|get_bidang|siup' ,'type'=>'dropdown','label'=> 'Bidang'),
								array('table'=>'ms_iu_bsb|id_sub_bidang|get_sub_bidang|siup' ,'type'=>'dropdown','label'=> 'Sub Bidang')
							)
			),
			array(
				'label'	=>	'Surat Ijin Usaha Lainnya',
				'filter'=>	array(
								array('table'=>'ms_ijin_usaha|no||ijin_lain' 	,'type'=>'text','label'=> 'No'),
								array('table'=>'ms_ijin_usaha|qualification|get_kualifikasi|ijin_lain' 	,'type'=>'text','label'=> 'Kualifikasi'),
								array('table'=>'ms_ijin_usaha|authorize_by||ijin_lain' 	,'type'=>'text','label'=> 'Lembaga Penerbit'),
								array('table'=>'ms_iu_bsb|id_bidang|get_bidang|ijin_lain' ,'type'=>'dropdown','label'=> 'Bidang'),
								array('table'=>'ms_iu_bsb|id_sub_bidang|get_sub_bidang|ijin_lain' ,'type'=>'dropdown','label'=> 'Sub Bidang')
							)
			),
			array(
				'label'	=>	'Sertifikat Asosiasi/Lainnya',
				'filter'=>	array(
								array('table'=>'ms_ijin_usaha|authorize_by||asosiasi' 	,'type'=>'text','label'=> 'Lembaga Penerbit'),
								array('table'=>'ms_ijin_usaha|no||asosiasi' 	,'type'=>'text','label'=> 'no'),
								array('table'=>'ms_iu_bsb|id_bidang|get_bidang|ijin_lain' ,'type'=>'dropdown','label'=> 'Bidang'),
								array('table'=>'ms_iu_bsb|id_sub_bidang|get_sub_bidang|ijin_lain' ,'type'=>'dropdown','label'=> 'Sub Bidang')
							)
			),

			array(
				'label'	=>	'SIUJK',
				'filter'=>	array(
								
								array('table'=>'ms_ijin_usaha|qualification|get_kualifikasi|siujk' 	,'type'=>'text','label'=> 'Kualifikasi'),
								array('table'=>'ms_ijin_usaha|authorize_by||siujk' 	,'type'=>'text','label'=> 'Lembaga Penerbit'),
								array('table'=>'ms_iu_bsb|id_bidang|get_bidang|ijin_lain' ,'type'=>'dropdown','label'=> 'Bidang'),
								array('table'=>'ms_iu_bsb|id_sub_bidang|get_sub_bidang|ijin_lain' ,'type'=>'dropdown','label'=> 'Sub Bidang')
							)
			),
			array(
				'label'	=>	'Keagenan',
				'filter'=>	array(
								
								array('table'=>'ms_agen|type' ,'type'=>'text','label'=> 'Kualifikasi'),
								array('table'=>'ms_agen_produk|produk' 	,'type'=>'text','label'=> 'Nama Produk'),
								array('table'=>'ms_agen_produk|merk' 	,'type'=>'text','label'=> 'Nama Merk'),
							)
			),
			array(
				'label'	=>	'Pengalaman',
				'filter'=>	array(
								array('table'=>'ms_pengalaman|job_name' 	,'type'=>'text','label'=> 'Nama Paket Pengadaan'),
								array('table'=>'ms_pengalaman|job_location' 	,'type'=>'text','label'=> 'Lokasi'),
							)
						)
		);
		if(!empty($array)){
			$a[] = $array;
		}
		return $a;
	}
}