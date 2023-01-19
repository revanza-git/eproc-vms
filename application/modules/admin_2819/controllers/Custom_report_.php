<?php defined('BASEPATH') OR exit('No direct script access allowed');



/*

*	@author : alexandroputra

*/



class Custom_report_ extends CI_Controller{

	

	public function __construct(){

		parent::__construct();



		$this->load->model('report/custom_report_model', 'crm');

		$this->load->model('pengadaan/pengadaan_model', 'pm');

	}





	public function get_field_in(){
		return array(
			array(
				'label'	=>	'Pengadaan',
				'filter'=>	array(
								array('table'=>'ms_procurement|name' ,'type'=>'text','label'=> 'Nama Pengadaan'),
								array('table'=>'ms_vendor|name' ,'type'=>'text','label'=> 'Nama Pemenang'),
								array('table'=>'ms_procurement_bsb|id_bidang|get_bidang' ,'type'=>'dropdown','label'=> 'Bidang'),
							)
			),
			array(
				'label'	=>	'Kontrak',
				'filter'=>	array(
								// array('table'=>'ms_vendor mv|mv.name' ,'type'=>'text','label'=> 'Pemenang Kontrak'),
								array('table'=>'ms_contract|contract_date' 	,'type'=>'date','label'=> 'Tanggal Kontrak'),
								array('table'=>'ms_contract|no_contract','type'=>'text','label'=> 'No. Kontrak'),
								array('table'=>'ms_contract|no_sppbj','type'=>'text','label'=> 'No. SPPBJ'),
								array('table'=>'ms_contract|sppbj_date','type'=>'date','label'=> 'Tanggal SPPBJ'),
								array('table'=>'ms_contract|no_spmk','type'=>'text','label'=> 'No. SPMK'),
								array('table'=>'ms_contract|spmk_date','type'=>'date','label'=> 'Tanggal SPMK'),
								array('table'=>'ms_contract|contract_price','type'=>'number_range','label'=> 'Nilai Kontrak (Rp)'),
								array('table'=>'ms_contract|contract_price_kurs','type'=>'number_range','label'=> 'Nilai Kontrak (Kurs)'),
							)
			),
			
		);
	}





	public function index(){

		$search = $this->input->get('q');
		$page = '';
		
		$per_page=8;

		$data['sort'] 			= $this->utility->generateSort(array('ms_procurement.name', 'pemenang', 'proc_date','id','contract_date'));



		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field_in());

		$data['pengadaan']		= $this->crm->get_pengadaan_progress($search, $sort, $page, $per_page,TRUE);



		foreach($data['pengadaan'] as $key => $row){

			$data_kontrak 										= $this->pm->get_kontrak($row['id']);

			$data['pengadaan'][$key]['progress'] 				= $this->pm->get_paket_progress($row['id']);

			$data['pengadaan'][$key]['graph']					= $this->pm->get_graph($data_kontrak['id'],$row['id']); 

			$data['pengadaan'][$key]['graph']['realization']	= $this->pm->get_realization($row['id']);

		

			$last_pengadaan						 				= end($data['pengadaan'][$key]['progress']);

			$pekerjaan						 					= $data['pengadaan'][$key]['graph']['realization'];
			$last_pekerjaan										= 'Dalam masa '.$pekerjaan['step'] .' pada hari ke-'.$pekerjaan['now'];
		

			$data['pengadaan'][$key]['last_progress']			= (isset($pekerjaan)) ? $last_pekerjaan : $last_pengadaan['value'];



		}





		$data['color'] 			= array('#A2A2A2','#CACACA','#DCDCDC');

		$data['color_amandemen']= array('#1281FF','#4197FA','#65ACFC','#8BC0FC','#A7CFFC');



		$data['pagination'] = $this->utility->generate_page('admin/custom_report',$sort, $per_page, $this->crm->get_pengadaan_progress($search, $sort, '','',FALSE));

		

		$layout['content']	= $this->load->view('report/custom-selector', $data, TRUE);

		$layout['script']	= $this->load->view('report/custom-selector_js', $data, TRUE);

		

		$data['header'] 	= $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);

		$data['content'] 	= $this->load->view('admin/dashboard', $layout, TRUE);
		// print_r($data);
		$this->load->view('template', $data);

	}



	public function view(){

		$query = $this->crm->get_master();

		$step = $this->crm->get_pengadaan_step(); 



		foreach($step->result() as $data)

			$proses[] = $data->value;



		$param = array(

			'name' 					=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Nama Paket Pengadaan</td>',

		    'hps_value' 			=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" colspan="2">Nilai HPS</td>',

		    'budget_source' 		=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Sumber Anggaran</td>',

		    'pejabat_pengadaan' 	=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Pejabat Pengadaan</td>',

		    'budget_year' 			=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Tahun Anggaran</td>',

		    'budget_holder_name' 	=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Budget Holder</td>',

		    'budget_spender_name' 	=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Budget Spender</td>',

		    'mekanisme' 			=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Metode Pengadaan</td>',

		    'barang' 				=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Barang/Jasa</td>',

		    'peserta' 				=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Peserta Pengaadan</td>',

		    'pemenang' 				=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Pemenangan Pengadaan</td>',

		    'nilai_kontrak' 		=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" colspan="2">Nilai Kontrak</td>',

		    'efisiensi' 			=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" colspan="2">Efisiensi</td>',

		    'kontrak_period' 		=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">Periode Kontrak</td>',

		    'proses_pengadaan' 		=> '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">'.implode('</td><td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">', $proses).'</td>'

		);





		$return = '<table cellpadding="0" cellspacing="0" border="1">';

			$return .= '<tr>

							<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;" rowspan="2">NO.</td>';



		foreach($_POST['field'] as $index => $header)

			$return .= $param[$index];



			$return .= '</tr>';

			$return .= '<tr>';



		foreach($_POST['field'] as $index => $detail){			

			if($index == 'hps_value' or $index == "nilai_kontrak")

				$return .= '<td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;">Rupiah</td><td style="background: #8db4e2; font-weight: bold; vertical-align: middle; text-align:center;">Kurs</td>';

		}



			$return .= '</tr>';



		$index = 1;

		$nomor = 1;

		foreach($query->result() as $data){		

			$return .= "<tr><td style='vertical-align: middle; text-align:left;'>".$nomor."</td>";

			foreach($_POST['field'] as $index => $detail){

				

				if($index == 'hps_value'){

					$return .= '<td style="vertical-align: middle; text-align:left;">Rp. '.$data->idr_value.'</td>'.

							   '<td style="vertical-align: middle; text-align:left;">'.$data->symbol.' '.$data->kurs_value.'</td>';

				}

				else if($index == 'efisiensi'){

					$return .= '<td style="vertical-align: middle; text-align:left;">'.number_format((($data->idr_value - $data->contract_price) / ($data->idr_value)) * 0.1, 2).'%</td>'.

							   '<td style="vertical-align: middle; text-align:left;">'.number_format((($data->kurs_value - $data->contract_price_kurs) / ($data->kurs_value)) * 0.1, 2).'%</td>';

				}

				else if($index == 'nilai_kontrak'){

					$return .= '<td style="vertical-align: middle; text-align:left;">Rp. '.$data->contract_price.'</td>'.

							   '<td style="vertical-align: middle; text-align:left;">'.$data->symbol_contract.' '.$data->contract_price_kurs.'</td>';

				}

				else if($index == 'kontrak_period'){

					$start_contract = $end_contract = '';



					if($data->start_contract) 	$start_contract = date("d/m/Y", strtotime($data->start_contract));

					if($data->end_contract) 	$end_contract = " - ".date("d/m/Y", strtotime($data->end_contract));



					$return .= '<td style="vertical-align: middle; text-align:left;">'.$start_contract.$end_contract.'</td>';

				}

				else if($index == 'barang'){

					$return .= '<td style="vertical-align: middle; text-align:left;">'.

							   		'<ol>';

							   		

						   	   $sql = $this->crm->get_barang($data->id);

						   	   foreach($sql->result() as $barang)

							   $return .= '<li>'.

							   				'<div>Nama Barang : '.$barang->nama_barang.'</div>'.

							   				'<div>HPS : '.$barang->symbol.' '.$barang->nilai_hps.'</div>'.

				   						  '</li>';



					$return .=		'</ol>'.

							   '</td>';

				}

				else if($index == 'peserta'){

					$return .= '<td style="vertical-align: middle;  text-align:left;">'.

							   		'<ol>';

							   		

						   	   $sql = $this->crm->get_peserta($data->id);

						   	   foreach($sql->result() as $peserta)

							   $return .= '<li>'.$peserta->name.'</li>';



					$return .=		'</ol>'.

							   '</td>';

				}

				else if($index == 'proses_pengadaan'){

					foreach($step->result() as $_step){

						$step_date = $this->crm->get_step_date($data->id, $_step->id);

						if($step_date) $step_date = date("d/m/Y", strtotime($step_date));

						else $step_date = ' - '; 



						$return .= '<td style="vertical-align: middle; text-align:left;">'.$step_date.'</td>';

					}

				}

				else if($index == 'proses_pengadaan'){

					foreach($step->result() as $_step){

						$step_date = $this->crm->get_step_date($data->id, $_step->id);

						if($step_date) $step_date = date("d/m/Y", strtotime($step_date));

						else $step_date = ' - '; 



						$return .= '<td style="vertical-align: middle; text-align:left;">'.$step_date.'</td>';

					}

				}

				else

					$return .= '<td style="vertical-align: middle; text-align:left;">'.$data->{$index}.'</td>';

			

			}

			$sql .= "</tr>";

			

			$index++;

			$nomor++;

		}





			$return .= '</tr>';

		$return .= '</table>';



  		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename=Laporan_Pengadaan.xls');



		echo $return;

	}

}