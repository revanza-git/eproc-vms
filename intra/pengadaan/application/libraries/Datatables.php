<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datatables
{
	protected $ci;

	protected $select = '*';
	protected $select_escape = null;
	protected $from = null;
	protected $joins = array();
	protected $wheres = array();
	protected $order_bys = array();

	public function __construct()
	{
		$this->ci =& get_instance();
	}

	public function select($select = '*', $escape = null)
	{
		$this->select = $select;
		$this->select_escape = $escape;
		return $this;
	}

	public function from($from)
	{
		$this->from = $from;
		return $this;
	}

	public function join($table, $cond, $type = '')
	{
		$this->joins[] = array($table, $cond, $type);
		return $this;
	}

	public function where($key, $value = null, $escape = true)
	{
		$this->wheres[] = array($key, $value, $escape);
		return $this;
	}

	public function order_by($orderby, $direction = '', $escape = null)
	{
		$this->order_bys[] = array($orderby, $direction, $escape);
		return $this;
	}

	public function generate()
	{
		$draw = (int) $this->ci->input->post_get('draw');
		$start = (int) $this->ci->input->post_get('start');
		$length = $this->ci->input->post_get('length');
		$length = ($length === null) ? -1 : (int) $length;
		$search_value = (string) $this->ci->input->post_get('search[value]');

		$records_total = $this->count_all();
		$records_filtered = $this->count_filtered($search_value);
		$data = $this->get_data($search_value, $start, $length);

		return json_encode(array(
			'draw' => $draw,
			'recordsTotal' => $records_total,
			'recordsFiltered' => $records_filtered,
			'data' => $data,
		));
	}

	protected function count_all()
	{
		$this->apply_base_query(true);
		$query = $this->ci->db->get();
		$row = $query->row_array();
		$this->ci->db->reset_query();
		return isset($row['count']) ? (int) $row['count'] : 0;
	}

	protected function count_filtered($search_value)
	{
		$this->apply_base_query(true);
		$this->apply_search($search_value);
		$query = $this->ci->db->get();
		$row = $query->row_array();
		$this->ci->db->reset_query();
		return isset($row['count']) ? (int) $row['count'] : 0;
	}

	protected function get_data($search_value, $start, $length)
	{
		$this->apply_base_query(false);
		$this->apply_search($search_value);
		$this->apply_ordering();

		if ($length > 0)
		{
			$this->ci->db->limit($length, max(0, $start));
		}

		$query = $this->ci->db->get();
		$result = $query->result_array();
		$this->ci->db->reset_query();
		return $result;
	}

	protected function apply_base_query($count_only)
	{
		$this->ci->db->reset_query();

		if ($count_only)
		{
			$this->ci->db->select('COUNT(*) AS count', false);
		}
		else
		{
			$this->ci->db->select($this->select, $this->select_escape);
		}

		if ($this->from !== null)
		{
			$this->ci->db->from($this->from);
		}

		for ($i = 0, $c = count($this->joins); $i < $c; $i++)
		{
			$join = $this->joins[$i];
			$this->ci->db->join($join[0], $join[1], $join[2]);
		}

		for ($i = 0, $c = count($this->wheres); $i < $c; $i++)
		{
			$where = $this->wheres[$i];
			if ($where[1] === null)
			{
				$this->ci->db->where($where[0], null, $where[2]);
			}
			else
			{
				$this->ci->db->where($where[0], $where[1], $where[2]);
			}
		}
	}

	protected function apply_search($search_value)
	{
		$search_value = trim($search_value);
		if ($search_value === '')
		{
			return;
		}

		$columns = $this->extract_select_columns();
		if (empty($columns))
		{
			return;
		}

		$this->ci->db->group_start();
		for ($i = 0, $c = count($columns); $i < $c; $i++)
		{
			$this->ci->db->or_like($columns[$i], $search_value);
		}
		$this->ci->db->group_end();
	}

	protected function apply_ordering()
	{
		$idx = $this->ci->input->post_get('order[0][column]');
		$dir = strtolower((string) $this->ci->input->post_get('order[0][dir]'));
		$dir = ($dir === 'desc') ? 'DESC' : 'ASC';

		$columns = $this->extract_select_columns();

		if ($idx !== null && $idx !== '' && is_numeric($idx))
		{
			$idx = (int) $idx;
			if (isset($columns[$idx]))
			{
				$this->ci->db->order_by($columns[$idx], $dir, false);
				return;
			}
		}

		for ($i = 0, $c = count($this->order_bys); $i < $c; $i++)
		{
			$order = $this->order_bys[$i];
			$this->ci->db->order_by($order[0], $order[1], $order[2]);
		}
	}

	protected function extract_select_columns()
	{
		$select = (string) $this->select;
		if ($select === '' || $select === '*')
		{
			return array();
		}

		$parts = explode(',', $select);
		$columns = array();

		for ($i = 0, $c = count($parts); $i < $c; $i++)
		{
			$expr = trim($parts[$i]);
			if ($expr === '')
			{
				continue;
			}

			$expr = preg_replace('/\\s+AS\\s+.+$/i', '', $expr);
			$expr = preg_replace('/\\s+.+$/', '', $expr);
			$expr = trim($expr);

			if ($expr !== '')
			{
				$columns[] = $expr;
			}
		}

		return $columns;
	}
}
