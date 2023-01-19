<?php
function default_date($date){

  $month = array(
        1 =>  'Januari',
        2 =>  'Februari',
        3 =>  'Maret',
        4 =>  'April',
        5 =>  'Mei',
        6 =>  'Juni',
        7 =>  'Juli',
        8 =>  'Agustus',
        9 =>  'September',
        10  =>  'Oktober',
        11  =>  'November',
        12  =>  'Desember');

  return date('d',strtotime($date)) .' '. $month[date('n',strtotime($date))] .' '.date('Y',strtotime($date));

}
function special_date($date){

	$month = array(
				'01'	=> 	'Januari',
				'02'	=>	'Februari',
				'03'	=>	'Maret',
				'04'	=>	'April',
				'05'	=>	'Mei',
				'06'	=>	'Juni',
				'07'	=>	'Juli',
				'08'	=>	'Agustus',
				'09'	=>	'September',
				'10'	=>	'Oktober',
				'11'	=>	'November',
				'12'	=> 	'Desember');
  $_date = explode('-',$date);
  return $_date[2].' '.$month[$_date[1]].' '.$_date[0];
	// return date('d',strtotime($date)) .' '. $month[date('n',strtotime($date))] .' '.date('Y',strtotime($date));
// 
}
function get_hari($date){
  $day = array(
        1 =>  'Senin',
        2 =>  'Selasa',
        3 =>  'Rabu',
        4 =>  'Kamis',
        5 =>  'Jumat',
        6 =>  'Sabtu',
        7 =>  'Minggu');

  return $day[date('N',strtotime($date))];
}
function get_month($date){
  $month = array(
        1 =>  'Januari',
        2 =>  'Februari',
        3 =>  'Maret',
        4 =>  'April',
        5 =>  'Mei',
        6 =>  'Juni',
        7 =>  'Juli',
        8 =>  'Agustus',
        9 =>  'September',
        10  =>  'Oktober',
        11  =>  'November',
        12  =>  'Desember');
  return $month[date('n',strtotime($date))];
}
function get_range_date($date1,$date2){
	return ceil((strtotime($date1) - strtotime($date2))/86400)+1;
}
function terbilang($x)
{
  $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
  if ($x < 12)
    return " " . $abil[$x];
  elseif ($x < 20)
    return terbilang($x - 10) . "belas";
  elseif ($x < 100)
    return terbilang($x / 10) . " puluh" . terbilang($x % 10);
  elseif ($x < 200)
    return " seratus" . terbilang($x - 100);
  elseif ($x < 1000)
    return terbilang($x / 100) . " ratus" . terbilang($x % 100);
  elseif ($x < 2000)
    return " seribu" . terbilang($x - 1000);
  elseif ($x < 1000000)
    return terbilang($x / 1000) . " ribu" . terbilang($x % 1000);
  elseif ($x < 1000000000)
    return terbilang($x / 1000000) . " juta" . terbilang($x % 1000000);
}
