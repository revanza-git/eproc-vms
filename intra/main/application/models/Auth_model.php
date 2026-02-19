<?php

class Auth_model extends CI_model
{
    public $eproc_db;
    function __construct()
    {
        parent::__construct();
        $this->eproc_db = $this->load->database('eproc', true);
    }

    public function get_user($id_user)
    {
        $data = $this->eproc_db->where('id', $id_user)->get('ms_admin')->row_array();

        $division = $this->db->where('id', $data['id_division'])->get('tb_division')->row_array();
        $role = $this->db->where('id', $data['id_role_app2'])->get('tb_role')->row_array();

        $set_session = array(
            'name'          =>  $data['name'],
            'division'      =>  $division['name'],
            'id_user'       =>  $data['id'],
            'id_role'       =>  $data['id_role_app2'],
            'id_division'   =>  $data['id_division'],
            'email'         =>  $data['email'],
            'role_name'     =>  $role['name'],
            'photo_profile' =>  $data['photo_profile']
        );
        $this->session->set_userdata('admin', $set_session);
        return true;
    }
}
