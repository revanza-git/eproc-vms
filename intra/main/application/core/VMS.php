<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class VMS extends MY_Controller {
    public function __construct(){
        parent::__construct();  
       $this->load->model('Vendor_model','vm'); 
        $user = $this->session->userdata('user');
        if($user){
            $status = $this->vm->isNew($user['id_user']);

        if($status == 1){
            redirect(site_url('vendor/surat_pernyataan'));
        }    
        }
        
    }
    
	public function save($data=null){
        $modelAlias = $this->modelAlias;

        if($this->validation()){
            $user = $this->session->userdata('user');
            $save = $this->input->post();

            $save['id_vendor'] = $user['id_user'];
            $save['entry_stamp'] = timestamp();

            if($this->$modelAlias->insert($save)){
                $id = $this->db->insert_id();
                 $this->dpt->non_iu_change($user['id_user']);
                if(isset($save['expire_date'])&&$save['expire_date']!='lifetime'){
                   
                    $this->dpt->set_email_blast($id, $this->alias, $this->module, $save['expire_date']);
                }
               
                $this->deleteTemp($save);
                return true;
            }
        }
    }

    public function update($id){
        error_reporting(E_ALL);
        $modelAlias = $this->modelAlias;
        if($this->validation()){
             $user = $this->session->userdata('user');
            $save = $this->input->post();
            $lastData = $this->$modelAlias->selectData($id);

            if($this->$modelAlias->update($id, $save)){
                 $this->dpt->non_iu_change($user['id_user']);
                if(isset($save['expire_date'])&&$save['expire_date']!='lifetime'){
                   
                    $this->dpt->edit_email_blast($id, $this->alias, $this->module, $save['expire_date']);
                }
                $this->deleteTemp($save, $lastData);
            }
        }
    }
    public function delete($id){
         $user = $this->session->userdata('user');
        $modelAlias = $this->modelAlias;
        if($this->$modelAlias->delete($id)){
             $this->dpt->non_iu_change($user['id_user']);
            $return['status'] = 'success';  
        }else{
            $return['status'] = 'error';  
        }
        echo json_encode($return);
    }
}