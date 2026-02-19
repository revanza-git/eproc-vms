<?php defined('BASEPATH') or exit('No direct script access allowed');

class Feedback_model extends CI_Model
{

    function get_feedback_list($search = '', $sort = '', $page = '', $per_page = '', $is_page = FALSE, $filter = array())
    {
        $admin = $this->session->userdata('admin');
        $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        // print_r($admin);
        $this->db->select('tr_feedback.id, ms_procurement.name, ms_vendor.name pemenang');
        $this->db->where('ms_procurement.del', 0);
        $this->db->where('ms_procurement.id_mekanisme!=', 1);
        $this->db->join('ms_vendor', 'ms_vendor.id=tr_feedback.id_vendor', 'LEFT');
        $this->db->join('ms_procurement', 'ms_procurement.id=tr_feedback.id_vendor', 'LEFT');

        if ($this->input->get('sort') && $this->input->get('by')) {
            $this->db->order_by($this->input->get('by'), $this->input->get('sort'));
        }
        if ($is_page) {
            $cur_page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 1;
            $this->db->limit($per_page, $per_page * ($cur_page - 1));
        }

        $a = $this->filter->generate_query($this->db->group_by('tr_feedback.id'), $filter);

        $query = $a->get('tr_feedback');
        // echo $this->db->last_query();die;
        return $query->result_array();
    }

    public function get_feedback($id)
    {
        $a = $this->db->where('id', $id)->get('tr_feedback');
        return $a->row_array();
    }

    public function save_reply($id, $data)
    {
        $a = $this->db->where('id', $id)->update('tr_feedback', $data);
        return $a;
    }

    public function get_vendor_email($id)
    {
        $a = $this->db->select('ms_vendor_admistrasi.vendor_email email')
            ->join('ms_vendor', 'ms_vendor.id=tr_feedback.id_vendor', 'LEFT')
            ->join('ms_vendor_admistrasi', 'ms_vendor_admistrasi.id_vendor=ms_vendor.id', 'LEFT')
            ->where('tr_feedback.id', $id)
            ->get('tr_feedback');
        return $a->row_array();
    }
}
