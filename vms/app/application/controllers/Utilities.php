<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Utilities extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_sub_bidang($id_bidang = '', $location = '', $id_location = '') {
        // Set content type to JSON
        $this->output->set_content_type('application/json');
        
        $result = array();
        
        if (!empty($id_bidang)) {
            // Get sub bidang based on bidang ID
            $query = $this->db->select('id, name')
                            ->where('id_bidang', $id_bidang)
                            ->where('del', 0)
                            ->get('tb_sub_bidang');
            
            foreach ($query->result() as $row) {
                $result[] = array(
                    'value' => $row->id,
                    'label' => $row->name
                );
            }
        }
        
        echo json_encode($result);
    }
} 