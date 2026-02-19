<?php

class Login_model extends CI_model
{

    public $eproc_db;
    function __construct()
    {
        parent::__construct();
        $this->eproc_db = $this->load->database('eproc', true);
    }

    public function cek_login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $sql = "SELECT * FROM ms_login WHERE username = ? AND password = ?";
        $_sql = $this->eproc_db->query($sql, array($username, $password));
        $sql = $_sql->row_array();

        $ct_sql = '';
        if ($_sql->num_rows() > 0) {
            if ($sql['type'] == "user") {
                $ct_sql = "SELECT * FROM ms_vendor WHERE id=? AND is_active =? AND del = 0";
                $ct_sql = $this->eproc_db->query($ct_sql, array($sql['id_user'], 1));

                if (count($ct_sql->result_array()) > 0) {
                    $data = $ct_sql->row_array();
                    $res = array(
                        'id_user' => $data['id'],
                        'type' => 'user',
                        'app' => 'vms'
                    );
                    return $res;
                } else {
                    return false;
                }
            } else if ($sql['type'] == "admin" && $sql['type_app'] == 1) {

                $ct_sql = "SELECT *,ms_admin.id id, ms_admin.name name, tb_role.name role_name FROM ms_admin JOIN tb_role ON ms_admin.id_role = tb_role.id WHERE ms_admin.id=? AND ms_admin.del=?";

                $ct_sql = $this->eproc_db->query($ct_sql, array($sql['id_user'], 0));

                if (count($ct_sql->result_array()) > 0) {
                    $data = $ct_sql->row_array();

                    $res = array(
                        'id_user' => $data['id'],
                        'type' => 'admin',
                        'app' => 'vms'
                    );

                    return $res;
                } else {
                    return false;
                }
            } else if ($sql['type'] == "admin" && $sql['type_app'] == 2) {
                $ct_sql = " SELECT 
								a.id
							FROM
								ms_admin a 
							WHERE
								a.del = ? AND a.id = ?
								";

                $ct_sql = $this->eproc_db->query($ct_sql, array(0, $sql['id_user']));

                if (count($ct_sql->result_array()) > 0) {

                    $data = $ct_sql->row_array();

                    $res = array(
                        'id_user' => $data['id'],
                        'type' => 'admin',
                        'app' => 'eproc'
                    );

                    return $res;
                } else {
                    return false;
                }
            }
        } else {

            return false;
        }
    }
}
