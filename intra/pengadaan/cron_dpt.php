<?php 
class dpt extends cron{

	function __construct(){
		parent::__construct();
	}
	function remove_dpt($id){
		$sql = "UPDATE tr_dpt SET end_date = '".date('Y-m-d')."' , status = 2, edit_stamp = '".date('Y-m-d H:i:s')."' WHERE id_vendor = ".$id;
		$this->query($sql);

		$sql1 = "UPDATE ms_vendor SET vendor_status = 1 , edit_stamp = '".date('Y-m-d H:i:s')."' WHERE id_vendor = ".$id;
		$this->query($sql1);
	}
	function __destruct(){
		mysql_close();
	}
}
