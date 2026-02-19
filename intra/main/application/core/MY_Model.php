<?php
class MY_Model extends CI_model{
	public $table;
	function getData($form){
		$quote_identifier = function($identifier) {
			$identifier = (string) $identifier;
			$parts = explode('.', $identifier);
			$quoted = array();
			foreach ($parts as $part) {
				if ($part === '' || !preg_match('/^[A-Za-z0-9_]+$/', $part)) {
					return null;
				}
				$quoted[] = '`' . $part . '`';
			}
			return implode('.', $quoted);
		};
		$table = $quote_identifier($this->table);
		if ($table === null) {
			throw new InvalidArgumentException('Invalid table name');
		}

		$query = "SELECT * FROM ".$table." WHERE del = 0";
		if($this->input->post('filter')){
			$query .= $this->filter($this->input->post('filter'), false);
		}
		return $query;
	}

	function insert($data){
		$data['del'] = 0;
		foreach ($data as $key => $value) {

			if(is_array($value)){
				foreach ($value as $keys => $values) {
					if($values=='') unset($value[$keys]);
				}

				$data[$key] = implode(',',$value);

			}
		}
		$a =  $this->db->insert($this->table, $data);

		return $a;
	}

	function update($id, $data){
		// unset($data['pengadaan']);
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $keys => $values) {
					if ($values === '') {
						unset($value[$keys]);
					}
				}
				$data[$key] = implode(',', $value);
			}
		}
		$a = $this->db->
				where('id', $id)->
				update($this->table, $data);

		return $a;
	}
	function delete($id){

		return $this->db->where('id', $id)
					->update($this->table, array(
											'del' => 1,
											'edit_stamp' => timestamp()
											)
					);
	}
	function remove($id){
		return $this->db->where('id', $id)
						->delete($this->table);
	}

	function filter($form, $filter=array(), $prefix=true){
		$query = '';
		if($prefix) {
			$query .= ' WHERE';
		}else{
			$query .= ' AND ';
		}
		$i = 0;
		$_filter = $this->keyForm($form['filter']);
		$quote_identifier = function($identifier) {
			$identifier = str_replace('|', '.', (string) $identifier);
			$parts = explode('.', $identifier);
			$quoted = array();
			foreach ($parts as $part) {
				if ($part === '' || !preg_match('/^[A-Za-z0-9_]+$/', $part)) {
					return null;
				}
				$quoted[] = '`' . $part . '`';
			}
			return implode('.', $quoted);
		};
		
		foreach ($filter as $key => $value) {
			if (!isset($_filter[$key])) {
				continue;
			}

			$query .= '(';
			$j = 0;
			$_key = $quote_identifier($key);
			if ($_key === null) {
				continue;
			}
			foreach($value as $keyField => $valueField){
				$type = $_filter[$key];
				$empty = false;
				$res = '';

				switch ($type) {

					case 'date_range':
						if($valueField[$key.'_start']==''&&$valueField[$key.'_end']!=''){
							$end = date('Y-m-d', strtotime($valueField[$key.'_end']));
							$res .= '(' . $_key . ' < ' . $this->db->escape($end) . ')';
						}else if($valueField[$key.'_start']!=''&&$valueField[$key.'_end']==''){
							$start = date('Y-m-d', strtotime($valueField[$key.'_start']));
							$res .= '(' . $_key . ' > ' . $this->db->escape($start) . ')';
						}else if($valueField[$key.'_start']!=''&&$valueField[$key.'_end']!=''){
							$start = date('Y-m-d', strtotime($valueField[$key.'_start']));
							$end = date('Y-m-d', strtotime($valueField[$key.'_end']));
							$res .= '(' . $_key . ' BETWEEN ' . $this->db->escape($start) . ' AND ' . $this->db->escape($end) . ')';
						}else{
							$empty = true;
						}
						break;
					case 'date_range_lifetime':
						if(is_array($valueField)){
							if($valueField[$key.'_start']==''&&$valueField[$key.'_end']!=''){
								$end = date('Y-m-d', strtotime($valueField[$key.'_end']));
								$res .= '(' . $_key . ' < ' . $this->db->escape($end) . ' AND ' . $_key . ' != ' . $this->db->escape('lifetime') . ')';
							}else if($valueField[$key.'_start']!=''&&$valueField[$key.'_end']==''){
								$start = date('Y-m-d', strtotime($valueField[$key.'_start']));
								$res .= '(' . $_key . ' > ' . $this->db->escape($start) . ' AND ' . $_key . ' != ' . $this->db->escape('lifetime') . ')';
							}else if($valueField[$key.'_start']!=''&&$valueField[$key.'_end']!=''){
								$start = date('Y-m-d', strtotime($valueField[$key.'_start']));
								$end = date('Y-m-d', strtotime($valueField[$key.'_end']));
								$res .= '(' . $_key . ' BETWEEN ' . $this->db->escape($start) . ' AND ' . $this->db->escape($end) . ' AND ' . $_key . ' != ' . $this->db->escape('lifetime') . ')';
							}else{
								$empty = true;
							}
						}else{
							$res .= '(' . $_key . ' = ' . $this->db->escape('lifetime') . ')';
						}
						break;
					case 'dropdown':
						if($valueField==''){
							$empty = true;
						}else{
							$res .= '(' . $_key . ' = ' . $this->db->escape($valueField) . ')';
						}
						
						break;
					case 'text':
					default:
						if($valueField==''){
							$empty = true;
						}else{
							$pattern = '%' . $this->db->escape_like_str($valueField) . '%';
							$res .= '(' . $_key . ' LIKE ' . $this->db->escape($pattern) . " ESCAPE '!')";
						}
						break;
				}
				if($keyField>0 && !$empty) $query .= ' OR ';
				$query.=$res;

				$j++;
				// echo count($value);
				
			}
			$query .= ')';

			$i++;
			// echo count($filter);
			// echo count($filter).$i;
			if(count($filter)>$i) $query .= ' AND ';
		}
		return $query;
	}

	function keyForm($form){
		$return = array();
		foreach ($form as $key => $value) {
			if($value['type']=='date_range'||$value['type']=='date_range_lifetime'){
				$f = 'name';
			}else{
				$f = 'field';
			}

			$return[$value[$f]] = $value['type'];
		}
		return $return;
	}

	public function activity_log($id_user,$activity,$iden)
	{
		$arr = array(
			'id_user' 		=> $id_user,
			'activity'		=> $activity,
			'activity_date' => date('Y-m-d H:i:s'),
			'iden'			=> $iden
		);

		return $this->db->insert('tr_log_activity',$arr);
	}

	public function insertHistoryPengadaan($id_pengadaan,$type,$data)
	{
		unset($data['entry_stamp']);
		$data['id_pengadaan'] 	= $id_pengadaan;
		$data['status']			= $type;
		$data['entry_stamp']	= date('Y-m-d H:i:s');
		return $this->db->insert('tr_history_pengadaan', $data);
	}

}
