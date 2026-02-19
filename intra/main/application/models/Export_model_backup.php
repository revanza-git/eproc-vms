<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Export_model extends MY_Model{
	public $fppbj   = 'ms_fppbj';
	public $fkpbj   = 'ms_fkpbj';
    public $fp3     = 'ms_fp3';
    public $method  = 'tb_proc_method';
    
	function __construct(){
		parent::__construct();

	}
    
    
    function fppbj($id=''){
        if(empty($id) || !is_numeric($id)){
            return array();
        }
        $query = "SELECT * FROM ms_fppbj WHERE id = ".(int)$id;
        return $this->db->query($query)->row_array();
    }

    function fkpbj($id_fppbj=''){
        $data = $this->db->where('id_fppbj', $id_fppbj)->get($fkpbj);

        return $data->row_array();
    }

    public function get_analisa($id_fppbj){
        $data = $this->db->where('id_fppbj', $id_fppbj)->get('tr_analisa_risiko');
        $data = $data->row_array();
        $data['dpt_list_'] = json_decode($data['dpt_list']);

        // print_r($data);
        if ($data['dpt_list_'] !== null) {
            # code...
            unset($data['dpt_list']);
            foreach ($data['dpt_list_']->dpt as $id_dpt) {
                // echo $id_dpt;
                $data['dpt_list'][] .= $this->get_dpt($id_dpt);
            }
        }
        $data['usulan'] = $data['dpt_list_']->usulan;
        // print_r($data);die;
        return $data;

    }

    public function get_dpt($id_dpt)
    {
        $data = $this->db->where('id', $id_dpt)->get('ms_vendor')->row_array();
        return $data['name'];
    }

    public function get_sistem_kontrak($id_fppbj,$table)
    {
        // echo $id_fppbj;echo $table;
        if ($table == 'ms_fppbj') {
            $data = $this->db->where('id', $id_fppbj)->get('ms_fppbj');
        } else {
            $data = $this->db->where('id_fppbj', $id_fppbj)->get($table);
        }
        
        $data = $data->row_array();
        // print_r($data);die;
        $data['sistem_kontrak'] = json_decode($data['sistem_kontrak']);

        if ($data['sistem_kontrak'] !== null) {
            foreach ($data['sistem_kontrak'] as $id_fppbj) {
                $data['sistem_kontrak_'][] .= $id_fppbj;
            }
        }
        // print_r($data);
        return $data;
    }
    
    function rekap_perencanaan_($year = null){
        $data = $this->db->select('ms_fppbj.id id_fppbj, year_anggaran, nama_pengadaan, tb_division.name divisi, ms_fppbj.id, idr_anggaran, jenis_pengadaan, tb_proc_method.name metode_pengadaan, desc, jwp, jwpp')
                    ->where('year_anggaran', $year)
                    ->where('is_approved', 3)
                    ->where('is_planning', 1)
                    ->where('is_status', 0)
                    ->where('lampiran_persetujuan IS NOT NULL')
                    ->join('tb_division', 'tb_division.id = ms_fppbj.id_division')
                    ->join('tb_proc_method', 'tb_proc_method.id = ms_fppbj.metode_pengadaan')
                    ->order_by('id_kadiv DESC, divisi')
                    ->get('ms_fppbj')->result_array();
                    // print_r($data);die;
        foreach ($data as $key => $value) {
            $id_[] = $value['id_fppbj'];
        }
        $id_ = json_encode($id_);
        $this->session->set_userdata('export_id', $id_);
        return $data;
    }

    function rekap_perencanaan($year = null){
         // $kadiv = $this->db->select('name kadiv, id')->get('tb_kadiv')->result_array();
        // print_r($kadiv);
        // foreach ($kadiv as $key => $kadiv_) {
        $division = $this->db    ->select('tb_kadiv.name id_kadiv, tb_division.name, tb_division.id')
                                 ->join('tb_kadiv','tb_division.id_kadiv=tb_kadiv.id','LEFT')
                                 ->order_by('id_kadiv','ASC')
                                 ->get('tb_division')
                                 ->result_array();
            // $division = "SELECT tb_kadiv.name kadiv, tb_division.name divisi,tb_division.id FROM tb_kadiv INNER JOIN tb_division ON tb_division.id_kadiv=tb_kadiv.id ORDER BY id_kadiv";
            // $division = $this->db->query($division)->result_array();
            // print_r($kadiv_);die;
            // print_r($division);
            foreach ($division as $key_ => $division_) {
                // print_r($division_);die;
                $data[$division_['id_kadiv']][$division_['name']] = $this->db->query("SELECT   tb_division.name AS division,
                        ms_fppbj.id id_fppbj,
                        year_anggaran,
                        nama_pengadaan,
                        tb_division.name divisi,
                        ms_fppbj.id,
                        ms_fppbj.idr_anggaran,
                        ms_fppbj.jenis_pengadaan,
                        tb_proc_method.name metode_pengadaan,
                        ms_fppbj.desc,
                        ms_fppbj.jwp,
                        ms_fppbj.jwp_start,
                        ms_fppbj.jwp_end,
                        ms_fppbj.jwpp,
                        ms_fppbj.jwpp_start,
                        ms_fppbj.jwpp_end
                        FROM ms_fppbj 
                        LEFT JOIN tb_division ON tb_division.id = ms_fppbj.id_division
                        LEFT JOIN tb_proc_method ON tb_proc_method.id = ms_fppbj.metode_pengadaan
                        WHERE 
                        ms_fppbj.is_planning = 1 
                        AND ms_fppbj.is_status = 0 
                        AND ms_fppbj.is_reject = 0 
                        AND ms_fppbj.del = 0 
                        AND ms_fppbj.is_approved_hse < 2
                        AND ((id_division =".$division_['id']." AND ms_fppbj.year_anggaran = ".$year." AND ms_fppbj.is_approved = 3 AND (ms_fppbj.idr_anggaran <= 100000000 OR (ms_fppbj.idr_anggaran > 100000000 AND ms_fppbj.metode_pengadaan = 3))))
                        OR  (id_division =".$division_['id']." AND ms_fppbj.year_anggaran = ".$year."
                        AND ms_fppbj.is_approved = 4 AND ms_fppbj.idr_anggaran > 100000000)
                        ORDER BY id_kadiv DESC, divisi")->result_array();
            }
        // }
//         print_r($data);
// die;
            
         // print_r($data);die;
        foreach ($data as $key => $value) {
            $id_[] = $value['id_fppbj'];
        }
        $id_ = json_encode($id_);
        $this->session->set_userdata('export_id', $id_);

        // print_r($data);die;
        return $data;
    }
	
    function rekap_department($year = null){
         $divisi = $this->db->get('tb_division')->result_array();
        foreach ($divisi as $key => $value) {
            $query = "SELECT
                            tb_division.id, tb_division.name divisi_name,
                            count(metode_pengadaan) as total_metod,
                            count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                            count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                            count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                            count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                            count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                            
                        FROM ms_fppbj
                        LEFT JOIN tb_division ON ms_fppbj.id_division=tb_division.id 
                        WHERE (ms_fppbj.year_anggaran = $year AND ms_fppbj.del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3)) OR  (ms_fppbj.year_anggaran = $year AND ms_fppbj.del = 0 AND is_approved = 4 AND idr_anggaran > 100000000))
                        GROUP BY tb_division.id
                        order by tb_division.id ASC";
            $query = $this->db->query($query)->result_array();

        }
        // print_r($query);die;
        return $query;
        // $kadiv = $this->db  ->select('tb_kadiv.name kadiv, tb_kadiv.id')
        //                     ->get('tb_kadiv')
        //                     ->result_array();
        // // print_r($kadiv);die;
        // foreach ($divisi as $key => $value) {
        //     // print_r($value);die;
        //     $data[$key]['id_kadiv'] = $value['id'];
        //     $data[$key]['kadiv']    = $value['kadiv'];

        //     $getIdDivision = $this->db->query('SELECT id id_division FROM tb_division WHERE id_kadiv ='.$value['id'])->result_array();
        //     // print_r($getIdDivision);die;
        //     $id_divisi_ = '';
        //     $pelelangan = '';
        //     $pemilihan_langsung ='';
        //     $swakelola ='';
        //     $penunjukan_langsung ='';
        //     $pengadaan_langsung ='';
        //     // $met_ = '';
        //     $query = '';
        //     foreach ($getIdDivision as $key_div => $value_div) {
        //          // $id_divisi_ .= $value_div['id_division'].',';
        //         // $met_ .= ',';
        //         $query .= 'tb_division.name division, id id_division,
        //                         (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id AND is_status = 0 AND is_reject = 0 AND del = 0 AND is_approved_hse < 2 AND ((id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 1 AND del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3)))) OR  (id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 1 AND del = 0 AND is_approved = 4 AND idr_anggaran > 100000000)) as pelelangan,

        //                         (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id  AND is_status = 0 AND is_reject = 0 AND del = 0 AND is_approved_hse < 2 AND ((id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 2 AND del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3)))) OR  (id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 2 AND del = 0 AND is_approved = 4 AND idr_anggaran > 100000000)) as pemilihan_langsung,

        //                         (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id  AND is_status = 0 AND is_reject = 0 AND del = 0 AND is_approved_hse < 2 AND ((id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 3 AND del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3)))) OR  (id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 3 AND del = 0 AND is_approved = 4 AND idr_anggaran > 100000000)) as swakelola,

        //                         (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id  AND is_status = 0 AND is_reject = 0 AND del = 0 AND is_approved_hse < 2 AND ((id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 4 AND del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3)))) OR  (id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 4 AND del = 0 AND is_approved = 4 AND idr_anggaran > 100000000)) as penunjukan_langsung,

        //                         (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id  AND is_status = 0 AND is_reject = 0 AND del = 0 AND is_approved_hse < 2 AND ((id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 5 AND del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3)))) OR  (id_division ='.$value_div['id_division'].'  AND metode_pengadaan = 5 AND del = 0 AND is_approved = 4 AND idr_anggaran > 100000000)) as pengadaan_langsung,';
        //                       ;
        //     }
            // $id_divisi = substr($id_divisi_,substr($id_divisi_),-1);
            // print_r($id_divisi);die;
            // $met = substr($met_,substr($met_),-1);
            // echo $query;die;
            // $division = $this->db->select($query)
            //             ->where('id_kadiv', $value['id'])
            //             ->get('tb_division')
            //             ->result_array();
                // echo($division);die;
       
            // print_r($division);die;
            // $data[$key]['detail'] = $division;
            // print_r($data[$key]['detail'][0]);die;
        //}
        // print_r($data);die;
        // return $data;
        return $data;
    }

     function filter_rekap_department($year = null){
        $kadiv = $this->db->select('name kadiv, id')->get('tb_kadiv')->result_array();

        foreach ($kadiv as $key => $value) {
            $data[$key]['id_kadiv'] = $value['id'];
            $data[$key]['kadiv']    = $value['kadiv'];
            
            $division = $this->db
                            ->select('tb_division.name division, id id_division,
                                (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id AND lampiran_persetujuan IS NOT NULL AND is_approved = 3 AND is_status = 0 AND metode_pengadaan = 1) as pelelangan,
                                (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id AND lampiran_persetujuan IS NOT NULL AND is_approved = 3 AND is_status = 0 AND metode_pengadaan = 2) as pemilihan_langsung,
                                (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id AND lampiran_persetujuan IS NOT NULL AND is_approved = 3 AND is_status = 0 AND metode_pengadaan = 3) as swakelola,
                                (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id AND lampiran_persetujuan IS NOT NULL AND is_approved = 3 AND is_status = 0 AND metode_pengadaan = 4) as penunjukan_langsung,
                                (SELECT count(id) FROM ms_fppbj where ms_fppbj.id = tb_division.id AND lampiran_persetujuan IS NOT NULL AND is_approved = 3 AND is_status = 0 AND metode_pengadaan = 5) as pengadaan_langsung')
                            ->where('id_kadiv', $value['id'])
                            ->get('tb_division')
                            ->result_array();

            $data[$key]['detail'] = $division;
        }
        
        return $data;
    }

    function get_exportFP3($id_fppbj=''){
        $data = $this->db->select('ms_fppbj.nama_pengadaan nama_pengadaan_fppbj, ms_fp3.nama_pengadaan, tb_proc_method.name metode_pengadaan, ms_fp3.jadwal_pengadaan, ms_fp3.idr_anggaran, ms_fp3.desc, ms_fp3.status, tb_division.name nama_divisi')
                        ->where('ms_fp3.id_fppbj', $id_fppbj)
                        ->join('ms_fppbj', 'ms_fppbj.id = ms_fp3.id_fppbj', 'LEFT')
                        ->join('tb_proc_method', 'tb_proc_method.id = ms_fp3.metode_pengadaan', 'LEFT')
                        ->join('tb_division', 'tb_division.id = ms_fppbj.id_division', 'LEFT')
                        ->group_by('ms_fp3.id')
                        ->get('ms_fp3');
                        
                        
        // print_r($data->result_array());die;
        return $data->result_array();
    }

    function get_exportUser(){
        $data = $this->db->select('ms_user.*, tb_division.name division')
                    ->where('ms_user.del', 0)
                    ->join('tb_division', 'tb_division.id = ms_user.id_division')
                    ->get('ms_user')->result_array();

        return $data;
    }

    public function getDivision($year)
    {
        // echo $year;
        if($year > 0){
            $query = "  SELECT  tb_division.name AS division,
                                nama_pengadaan AS name,
                                year_anggaran AS year,
                                ms_fppbj.id,
                                ms_fppbj.id_division
                        FROM ".$this->fppbj."
                        LEFT JOIN tb_division ON tb_division.id = ms_fppbj.id_division
                        WHERE    ms_fppbj.is_status = 0 
                        AND ms_fppbj.is_reject = 0
                        AND ms_fppbj.is_approved_hse < 2
                        AND ((ms_fppbj.year_anggaran = ".$year."
                        AND ms_fppbj.del = 0 AND ms_fppbj.is_approved = 3 AND (ms_fppbj.idr_anggaran <= 100000000 OR (ms_fppbj.idr_anggaran > 100000000 AND ms_fppbj.metode_pengadaan = 3))))
                        OR  ( ms_fppbj.year_anggaran = ".$year."
                        AND ms_fppbj.del = 0 AND ms_fppbj.is_approved = 4 AND ms_fppbj.idr_anggaran > 100000000)";
            $query .= " GROUP BY ms_fppbj.id_division";
        }else{
            $query = "  SELECT  nama_pengadaan AS name,
                                count(*) AS total,
                                year_anggaran AS year,
                                ms_fppbj.id,
                                ms_fppbj.id_division
                        FROM ".$this->fppbj."
                        WHERE  ms_fppbj.is_status = 0 
                        AND ms_fppbj.is_reject = 0
                        AND ms_fppbj.is_approved_hse < 2
                        AND (( ms_fppbj.del = 0 AND ms_fppbj.is_approved = 3 AND (ms_fppbj.idr_anggaran <= 100000000 OR (ms_fppbj.idr_anggaran > 100000000 AND ms_fppbj.metode_pengadaan = 3))))
                        OR  ( ms_fppbj.del = 0 AND ms_fppbj.is_approved = 4 AND ms_fppbj.idr_anggaran > 100000000)";
            $query .= " GROUP BY year";
        }
        $query = $this->db->query($query)->result_array();
        $data = array();
        foreach ($query as $key => $value) {
            $data[$value['id_division']] = $value['division'];
        }
        return $data;
    }
	
	public function getDataRekapFilter($year,$post)
    {
        $id_division_ = "";
        foreach ($post['id_division'] as $key => $value) {
            $id_division_ .= $value.',';
        }
        $id_division = substr($id_division_,substr($id_division_),-1);

        if (count($post['id_division']) > 0) {
            $where_id_div = " ms_fppbj.id_division IN(".$id_division.") AND";
        } else {
            $where_id_div = '';
        }

        if ($post['start'] != '' && $post['end'] != '') {
            $where_date = " jwpp_start BETWEEN ".$post['start']." AND jwpp_end BETWEEN ".$post['end']." AND ";
        } else{
            $where_date = '';
        }

        $query = "SELECT  tb_division.name AS division,
                                nama_pengadaan AS name,
                                year_anggaran AS year,
                                ms_fppbj.id
                        FROM ".$this->fppbj."
                        LEFT JOIN tb_division ON tb_division.id = ms_fppbj.id_division
                        WHERE  ms_fppbj.is_status = 0 
                        AND ms_fppbj.is_reject = 0
                        AND ms_fppbj.is_approved_hse < 2
                        AND ((ms_fppbj.year_anggaran = ".$year." AND ".$where_id_div." ".$where_date." ms_fppbj.del = 0 AND ms_fppbj.is_approved = 3 AND (ms_fppbj.idr_anggaran <= 100000000 OR (ms_fppbj.idr_anggaran > 100000000 AND ms_fppbj.metode_pengadaan = 3))))
                        OR  (ms_fppbj.year_anggaran = ".$year." AND ".$where_id_div." ".$where_date." ms_fppbj.del = 0 AND ms_fppbj.is_approved = 4 AND ms_fppbj.idr_anggaran > 100000000)";
        $query .= " ORDER BY ms_fppbj.id_division";
        // echo $query;die;

        return $this->db->query($query)->result_array();
    }
}