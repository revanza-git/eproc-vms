<?php 
/**
 * 
 */
class Log_model extends MY_Model
{
	public $table = 'tr_log_activity';

	public function getData($form = null)
	{
		$query = " SELECT activity, activity_date FROM ".$this->table;

		return $query;
	}
}