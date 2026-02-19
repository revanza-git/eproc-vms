<?php 
class dpt extends cron{

	function __construct(){
		parent::__construct();
	}
	function remove_dpt($id){
		$vendor_id = (int) $id;
		$today = date('Y-m-d');
		$timestamp = date('Y-m-d H:i:s');

		$sql = "UPDATE tr_dpt SET end_date = ?, status = 2, edit_stamp = ? WHERE id_vendor = ?";
		$this->execute($sql, "ssi", array($today, $timestamp, $vendor_id));

		$sql1 = "UPDATE ms_vendor SET vendor_status = 1, edit_stamp = ? WHERE id_vendor = ?";
		$this->execute($sql1, "si", array($timestamp, $vendor_id));
	}
}
