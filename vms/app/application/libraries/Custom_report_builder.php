<?php
class Custom_report_builder{
	
	private $CI;
	
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();

		$this->CI->load->model('reporting/custom_model');
	}
	
	function build_html_constructor($master = array()){
		
		$content = $master['content'];
		$row_max = $master['row_total'];
		$col_max = $master['col_total'];		
		
		$return .= '<tr';
		if($setting['row_count'] > 1) $return .= ' rowspan="'.$setting['row_count'].'"';
		$return .= '/>'; 
		
		foreach($content as $_content){
			foreach($_content as $data){
				
				if(is_array($data)) foreach($data as $_data){ $return .= "<tr><td>".$data."</td></tr>\n"; }
				
				$return .= "<td>".$data."</td>\n"; 
			}
		}
		
		$return .= '</tr>\n'; 
				
		$return = str_replace("<", "&lt;", $return);
		$return = str_replace(">", "&gt;", $return);
		$return = str_replace("\\n", "<br/>", $return);
		
		return $return;
	}
	
	function build_html_situ($id = ''){
		$data = $this->CI->custom_model->get_situ($id);
				
		$row_count = 0;
		$return = array();
		
		foreach($data as $_data){
			$push = array($data->alamat, date("d M Y", strtotime($data->masa_berlaku)));
			array_push($return, $push);
			
			$row_count++;
		}
		
		return array('content' => $return, 'row_count' => $row_count, 'col_count' => 2);
	}	

	function build_html_akta($id = ''){
		$data = $this->CI->custom_model->get_akta($id);
				
		$row_count = 0;
		$return = array();
		
		foreach($data as $_data){
			$push = array($data->no_akta, $data->notaris, date("d M Y", strtotime($data->tanggal)));
			array_push($return, $push);
			
			$row_count++;
		}
		
		return array('content' => $return, 'row_count' => $row_count, 'col_count' => 2);
	}
	
	function build_html_pengurus($id = ''){
		$direktur = $this->CI->custom_model->get_pengurus($id, 'direksi');
		$komisaris = $this->CI->custom_model->get_pengurus($id, 'komisaris');
		
		$cont_dir = '<ol style="margin : 0px">';
		foreach($direktur as $_direktur) $cont_dir .= '<li>'.$_direktur->nama.'</li>';
		$cont_dir .= '</ol>';

		$cont_kom = '<ol style="margin : 0px">';
		foreach($komisaris as $_komisaris) $cont_kom .= '<li>'.$_direktur->nama.'</li>';
		$cont_kom .= '</ol>';
	
		$return = array($cont_dir, $cont_kom);
		
		return array('content' => $return, 'row_count' => 1, 'col_count' => 2);
	}
	
	function build_html_asosiasi($id = ''){
		$data = $this->CI->custom_model->get_asosiasi($id);
				
		$row_count = 0;
		$return = array();
		
		foreach($data as $_data){
			$push = array($data->lembaga_penerbit, date("d M Y", strtotime($data->masa_berlaku)));
			array_push($return, $push);
			
			$row_count++;
		}
		
		return array('content' => $return, 'row_count' => $row_count, 'col_count' => 2);
	}
	
	function build_html_tdp($id = ''){
		$data = $this->CI->custom_model->get_tdp($id);
				
		$row_count = 0;
		$return = array();
		
		foreach($data as $_data){
			$push = array($data->no, date("d M Y", strtotime($data->masa_berlaku)));
			array_push($return, $push);
			
			$row_count++;
		}
		
		return array('content' => $return, 'row_count' => $row_count, 'col_count' => 2);
	}
	
	function build_html_bsb($id = ''){
		$data = $this->CI->custom_model->get_bsb_general($id);
	
		$bidang = '';
		foreach($data as $_data){
			if($bidang != $_data->bidang){
				$return .= '<div>'.$_data->bidang."</div>";
				$return .= $_data->bidang;
			}
			
			$return .= '<div style="margin-left : 10px"> - '.$_data->sub_bidang."</div>";
		}
				
		return array('content' => array($return), 'row_count' => 1, 'col_count' => 1);
	}
		
	function build_html_agen($id = ''){
		$data = $this->CI->custom_model->get_agen($id);
				
		$row_count = 0;
		$return = array();
		
		foreach($data as $_data){
			
			$produk = $this->CI->custom_model->get_produk($data->id);										
			foreach($produk as $_produk)
				$con_pro .= '<div>'.$_produk->produk." - ".$_produk->merk."</div>";
				
			$push = array($data->jenis, date("d M Y", $con_pro, strtotime($data->masa_berlaku)));
			array_push($return, $push);
			
			$row_count++;
		}
		
		return array('content' => $return, 'row_count' => $row_count, 'col_count' => 3);
	}
	
		/*
		
		else if($_header['header'] == "siup" or $_header['header'] == "siujk" or $_header['header'] == "ijin_lain" or $_header['header'] == "sbu"){
			$type = $_header['header'];
			$siup = $this->CI->custom_model->get_siup($data->id, $type);
			
			echo '<table cellspacing="0" cellpadding="0" border="0" width="100%" style="padding : 0px; border : none; border-collapse : collapse; height : 100%">';	
			$count = 0; $total = count($siup);
			foreach($siup as $_siup){
				if($count == ($total - 1) and $total > 1){
					$left = ' style="border-bottom : none; border-left : none; padding : 3px" ';
					$right = ' style="border-bottom : none; border-right : none; padding : 3px" ';
				}
				else if($count == 0 and $total > 1){
					$left = ' style="border-top : none; border-left : none; padding : 3px" ';
					$right = ' style="border-top : none; border-right : none; padding : 3px" ';
				}
				else if($count == 0 and $total == 1){
					$left = ' style="border-bottom : none; border-top : none; border-left : none; padding : 3px" ';
					$right = ' style="border-bottom : none; border-top : none; border-right : none; padding : 3px" ';
				}
					
				echo "<tr>";
					echo '<td width="100" '.$left.'>';	
						if($type != "sbu") echo $_siup->kualifikasi;
						else echo $_siup->anggota_asosiasi;
					echo "</td>";
					echo '<td width="300" '.$left.'>'; 
	
						if($type == "sbu") echo '<table cellspacing="0" cellpadding="0" border="0" width="100%" style="padding : 0px; border : none; border-collapse : collapse; height : 100%">';
	
						$bsb = $this->CI->custom_model->get_bsb($_siup->id, $type);
						
						$bidang = "";
						
						$count_detail = 0; $total_detail = count($bsb); 
						foreach($bsb as $_bsb){
							if($type != "sbu"){
								if($bidang != $_bsb->bidang){
									echo '<div>'.$_bsb->bidang."</div>";
									$bidang = $_bsb->bidang;
								}
								echo '<div style="margin-left : 10px"> - '.$_bsb->sub_bidang."</div>";
							}
							else{
								if($count_detail == ($total_detail - 1) and $total_detail > 1){
									$left_detail = ' style="border-bottom : none; border-left : none; padding : none" ';
									$right_detail = ' style="border-bottom : none; border-right : none; padding : none" ';
								}
								else if($count_detail == 0 and $total_detail > 1){
									$left_detail = ' style="border-top : none; border-left : none; padding : none" ';
									$right_detail = ' style="border-top : none; border-right : none; padding : none" ';
								}
								else if($count_detail == 0 and $total_detail == 1){
									$left_detail = ' style="border-bottom : none; border-top : none; border-left : none; padding : none" ';
									$right_detail = ' style="border-bottom : none; border-top : none; border-right : none; padding : none" ';
								}
									
								
								if($bidang != $_bsb->bidang){
									echo '<tr><td colspan="2" style="border-right : none; border-top : none; border-left : none">'.$_bsb->bidang."</td></tr>";
									$bidang = $_bsb->bidang;
								}
																
								echo '<tr>';
									echo '<td width="250" '.$left_detail.'>'.$_bsb->sub_bidang.'</td>';
									echo '<td width="50" '.$right_detail.'>'.$_bsb->grade.'</td>';
								echo '</tr>';
							}
							
							$count_detail++;
						}
						
						if($type == "sbu") echo "</table>";
						
					echo "</td>";
					echo '<td width="100" '.$right.'>'.date("d M Y", strtotime($_siup->masa_berlaku)).'</td>';
				echo "</tr>";
				
				$count++;
			}
			echo "</table>";
		}
		*/
}