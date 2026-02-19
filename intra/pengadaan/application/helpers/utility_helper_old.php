<?php
function default_date($date){
	$month = array(
				1	=> 	'Januari',
				2	=>	'Februari',
				3	=>	'Maret',
				4	=>	'April',
				5	=>	'Mei',
				6	=>	'Juni',
				7	=>	'Juli',
				8	=>	'Agustus',
				9	=>	'September',
				10	=>	'Oktober',
				11	=>	'November',
				12	=> 	'Desember');

	return date('d',strtotime($date)) .' '. $month[date('n',strtotime($date))] .' '.date('Y',strtotime($date));
}
function get_range_date($date1,$date2){
	return ceil((strtotime($date1) - strtotime($date2))/86400)+1;
}