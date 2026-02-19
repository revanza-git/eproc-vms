<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Export_model extends MY_Model{
    public $fppbj   = 'ms_fppbj';
    public $fkpbj   = 'ms_fkpbj';
    public $fp3     = 'ms_fp3';
    public $method  = 'tb_proc_method';
    public $eproc_db;
    
    function __construct(){
        parent::__construct();
        $this->eproc_db = $this->load->database('eproc',true);
    }
    
    public function getPicById($id_pic)
    {
        $query = $this->eproc_db->where('del', 0)->where('id',$id_pic)->get('ms_admin');
        return $query->row_array();
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
        $row = $this->db->where('id_fppbj', $id_fppbj)->get('tr_analisa_risiko')->row_array();
        if (!is_array($row)) {
            $row = array();
        }

        $row['dpt_list'] = array();
        $row['usulan'] = '';
        $row['dpt_list_'] = null;

        if (isset($row['dpt_list']) && is_string($row['dpt_list']) && $row['dpt_list'] !== '') {
            $row['dpt_list_'] = json_decode($row['dpt_list']);
        } elseif (isset($row['dpt_list']) && !is_string($row['dpt_list'])) {
            $row['dpt_list_'] = $row['dpt_list'];
        }

        if (is_object($row['dpt_list_'])) {
            if (isset($row['dpt_list_']->dpt) && is_array($row['dpt_list_']->dpt)) {
                foreach ($row['dpt_list_']->dpt as $id_dpt) {
                    $name = $this->get_dpt($id_dpt);
                    if ($name !== null && $name !== '') {
                        $row['dpt_list'][] = $name;
                    }
                }
            }
            if (isset($row['dpt_list_']->usulan) && $row['dpt_list_']->usulan !== null) {
                $row['usulan'] = $row['dpt_list_']->usulan;
            }
        } elseif (is_array($row['dpt_list_'])) {
            foreach ($row['dpt_list_'] as $id_dpt) {
                $name = $this->get_dpt($id_dpt);
                if ($name !== null && $name !== '') {
                    $row['dpt_list'][] = $name;
                }
            }
        }

        return $row;
    }

    public function get_dpt($id_dpt)
    {
        $data = $this->eproc_db->where('id', $id_dpt)->get('ms_vendor')->row_array();
        return (is_array($data) && isset($data['name'])) ? $data['name'] : null;
    }

    public function get_sistem_kontrak($id_fppbj,$table)
    {
        if ($table == 'ms_fppbj') {
            $data = $this->db->where('id', $id_fppbj)->get('ms_fppbj');
        } else {
            $data = $this->db->where('id_fppbj', $id_fppbj)->get($table);
        }
        
        $data = $data->row_array();

        $data['sistem_kontrak'] = json_decode($data['sistem_kontrak']);
        $data['sistem_kontrak_'] = []; // Initialize array to prevent undefined index

        if (!empty($data['sistem_kontrak'])) {
            foreach ($data['sistem_kontrak'] as $id_fppbj) {
                $data['sistem_kontrak_'][] = $id_fppbj;
            }
        }

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

        foreach ($data as $key => $value) {
            $id_[] = $value['id_fppbj'];
        }
        $id_ = json_encode($id_);
        $this->session->set_userdata('export_id', $id_);

        return $data;
    }

    function rekap_perencanaan($year = null)
    {
        $division = $this->db->select('tb_kadiv.name id_kadiv, tb_division.name, tb_division.id')
            ->join('tb_kadiv', 'tb_division.id_kadiv=tb_kadiv.id', 'LEFT')
            ->order_by('id_kadiv', 'DESC')
            ->get('tb_division')
            ->result_array();
     
        foreach ($division as $key_ => $division_) {
            $data[$division_['id_kadiv']][$division_['name']] = $this->db->query("SELECT   tb_division.name AS division,
                        ms_fppbj.id id_fppbj,
                        year_anggaran,
                        nama_pengadaan,
                        tb_division.name divisi,
                        ms_fppbj.id,
                        ms_fppbj.idr_anggaran,
                        ms_fppbj.usd_anggaran,
                        ms_fppbj.jenis_pengadaan,
                        tb_proc_method.name metode_pengadaan,
                        ms_fppbj.desc,
                        ms_fppbj.jwp,
                        ms_fppbj.jwp_start,
                        ms_fppbj.jwp_end,
                        ms_fppbj.jwpp,
                        ms_fppbj.jwpp_start,
                        ms_fppbj.jwpp_end,
                        ms_fppbj.is_status,
                        ms_fppbj.id_division,
                        ms_fppbj.id_pic,
                        ms_fppbj.is_perencanaan,
                        ms_fppbj.is_reject,
                        ms_fppbj.is_approved,
                        ms_fppbj.metode_pengadaan metode
                        FROM ms_fppbj 
                        LEFT JOIN tb_division ON tb_division.id = ms_fppbj.id_division
                        LEFT JOIN tb_proc_method ON tb_proc_method.id = ms_fppbj.metode_pengadaan
                        WHERE 
                        ms_fppbj.is_status = 0
                        AND ms_fppbj.is_reject = 0  
                        AND ms_fppbj.is_approved_hse < 2
                        AND 
                        (
                            (
                                id_division = " . $division_['id'] . " AND 
                                ms_fppbj.del = 0 AND 
                                ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND 
                                ms_fppbj.is_approved = 3 AND 
                                (ms_fppbj.idr_anggaran <= 100000000 OR 
                                (
                                    ms_fppbj.idr_anggaran > 100000000 AND 
                                    ms_fppbj.metode_pengadaan = 3
                                )
                            )
                        )
                        )
                        OR  
                        (
                            id_division = " . $division_['id'] . " AND 
                            ms_fppbj.del = 0 AND 
                            ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND 
                            ms_fppbj.is_approved = 4 AND 
                            ms_fppbj.idr_anggaran > 100000000
                        )
                        OR 
                        (
                            id_division = " . $division_['id'] . " AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND is_status = 2
                        )
                        OR 
                        (
                            id_division = " . $division_['id'] . " AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND is_status = 1
                        )
                        ORDER BY id_kadiv DESC, divisi")->result_array();
        }

        $id_ = array(); // Initialize as empty array
        foreach ($data as $key => $value) {
            if (isset($value['id_fppbj'])) {
                $id_[] = $value['id_fppbj'];
            }
        }
        $id_ = json_encode($id_);
        $this->session->set_userdata('export_id', $id_);

        return $data;
    }

    function rekap_department_fkpbj($year = null,$id_division){
        $query = "SELECT
                        ms_fppbj.id,
                        tb_division.id id_division, 
                        tb_division.name divisi_name,
                        c.name metode_pengadaan_name,
                        count(metode_pengadaan) as total_metod,
                        count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                        count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                        count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                        count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                        count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                        
                    FROM ms_fppbj
                    LEFT JOIN tb_division ON ms_fppbj.id_division=tb_division.id
                    LEFT JOIN tb_proc_method c ON ms_fppbj.metode_pengadaan=c.id 
                    WHERE ms_fppbj.is_status = 2 AND ms_fppbj.year_anggaran = $year AND ms_fppbj.del = 0 AND ms_fppbj.id_division = $id_division
                    GROUP BY tb_division.id
                    order by tb_division.id DESC";
        $query = $this->db->query($query)->result_array();
        
        if (count($query) > 0) {
            return $query;    
        } else {
            $table = array(
                0 => array(
                    'metode_1' => 0,
                    'metode_2' => 0,
                    'metode_3' => 0,
                    'metode_4' => 0,
                    'metode_5' => 0
                )
            );

            return $table;
        }
        
    }

    function count_rekap_department_fkpbj_telat($year = null,$id_division,$metode_pengadaan){
        $metode = trim($metode_pengadaan);
        if ($metode == "Pelelangan") {
            $metode_day = 60; //60 hari
        }else if ($metode == "Pengadaan Langsung") {
            $metode_day = 10;// 10 hari
        }else if ($metode == "Pemilihan Langsung"){
            $metode_day = 45; //45 hari
        }else if ($metode == "Swakelola"){
            $metode_day = 0;
        }else if ($metode == "Penunjukan Langsung") {
            $metode_day = 20;// 20 hari
        }else{
            //$metode_day = 1;
        }
        $start_yellow = $metode_day + 14 * -1;
        $end_yellow = $metode_day + 1 ;

            $query = "SELECT
                            a.id,
                            tb_division.id, tb_division.name divisi_name,
                            count(metode_pengadaan) as total_metod,
                            count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                            count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                            count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                            count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                            count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                            
                        FROM ms_fkpbj a
                        LEFT JOIN tb_division ON a.id_division=tb_division.id
                        WHERE a.entry_stamp LIKE '%".$year."%' AND a.id_division = ? AND a.del = 0 AND DATE(a.entry_stamp) > DATE_SUB(a.jwpp_start, INTERVAL ".$end_yellow." DAY)
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query,array($id_division))->result_array();
        if (count($query) > 0) {
            return $query;    
        } else {
            $table = array(
                0 => array(
                    'metode_1' => 0,
                    'metode_2' => 0,
                    'metode_3' => 0,
                    'metode_4' => 0,
                    'metode_5' => 0
                )
            );
            return $table;
        }
    }

    function count_rekap_department_fkpbj_tidak_telat($year = null,$id_division,$metode_pengadaan){
        $metode = trim($metode_pengadaan);
        if ($metode == "Pelelangan") {
            $metode_day = 60; //60 hari
        }else if ($metode == "Pengadaan Langsung") {
            $metode_day = 10;// 10 hari
        }else if ($metode == "Pemilihan Langsung"){
            $metode_day = 45; //45 hari
        }else if ($metode == "Swakelola"){
            $metode_day = 0;
        }else if ($metode == "Penunjukan Langsung") {
            $metode_day = 20;// 20 hari
        }else{
            //$metode_day = 1;
        }
        $start_yellow = $metode_day + 14 * -1;
        $end_yellow = $metode_day + 1;

            $query = "SELECT
                            a.id,
                            tb_division.id, tb_division.name divisi_name,
                            count(metode_pengadaan) as total_metod,
                            count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                            count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                            count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                            count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                            count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                            
                        FROM ms_fkpbj a
                        LEFT JOIN tb_division ON a.id_division=tb_division.id
                        WHERE a.is_approved = 3 AND a.year_anggaran = ? AND a.id_division = ? AND a.del = 0 AND DATE(a.entry_stamp) <=DATE_SUB(a.jwpp_start, INTERVAL ".$end_yellow." DAY)
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query,array($year,$id_division))->result_array();
        if (count($query) > 0) {
            return $query;    
        } else {
            $table = array(
                0 => array(
                    'metode_1' => 0,
                    'metode_2' => 0,
                    'metode_3' => 0,
                    'metode_4' => 0,
                    'metode_5' => 0
                )
            );

            return $table;
        }
    }

    function count_rekap_department_fkpbj($year = null){
        $divisi = $this->db->get('tb_division')->result_array();
        foreach ($divisi as $key => $value) {
            $query = "SELECT
                            ms_fkpbj.id,
                            tb_division.id, tb_division.name divisi_name,
                            count(metode_pengadaan) as total_metod,
                            count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                            count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                            count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                            count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                            count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                            
                        FROM ms_fkpbj
                        LEFT JOIN tb_division ON ms_fkpbj.id_division=tb_division.id 
                        WHERE ms_fkpbj.year_anggaran = ? AND ms_fkpbj.del = 0
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query,array($year))->result_array();
        }
        return $query;
    }

    
    function rekap_department($year = null){
        $divisi = $this->db->get('tb_division')->result_array();
        foreach ($divisi as $key => $value) {
            $query = "SELECT
                            ms_fppbj.id,
                            tb_division.id id_division, 
                            tb_division.name divisi_name,
                            count(metode_pengadaan) as total_metod,
                            count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                            count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                            count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                            count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                            count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                            
                        FROM ms_fppbj
                        LEFT JOIN tb_division ON ms_fppbj.id_division=tb_division.id 
                        WHERE 
                            (ms_fppbj.year_anggaran = $year AND ms_fppbj.del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))
                            OR  

                            (ms_fppbj.year_anggaran = $year AND ms_fppbj.del = 0 AND is_approved = 4 AND idr_anggaran > 100000000))

                            OR

                            (ms_fppbj.is_status = 2 AND ms_fppbj.del = 0)
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query)->result_array();

        }
        return $query;
    }

    function get_plan($year = null){
        $divisi = $this->db->get('tb_division')->result_array();
        foreach ($divisi as $key => $value) {
            $query = "SELECT
                            ms_fppbj.id,
                            tb_division.id id_division, 
                            tb_division.name divisi_name,
                            count(metode_pengadaan) as total_metod,
                            count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                            count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                            count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                            count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                            count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                            
                        FROM ms_fppbj
                        LEFT JOIN tb_division ON ms_fppbj.id_division=tb_division.id 
                        WHERE 
                            (ms_fppbj.year_anggaran = $year AND ms_fppbj.del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))
                            OR  
                            (ms_fppbj.year_anggaran = $year AND ms_fppbj.del = 0 AND is_approved = 4 AND idr_anggaran > 100000000))
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query)->result_array();

        }
        return $query;
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
        $data = $this->db->select('ms_fppbj.nama_pengadaan nama_pengadaan_fppbj, ms_fp3.nama_pengadaan, tb_proc_method.name metode_pengadaan, ms_fp3.jadwal_pengadaan, ms_fp3.idr_anggaran, ms_fp3.desc, ms_fp3.status, tb_division.name nama_divisi,ms_fppbj.jwpp_start jadwal_start_fppbj,ms_fppbj.jwpp_end jadwal_end_fppbj,ms_fp3.jwpp_start,ms_fp3.jwpp_end')
                        ->where('ms_fp3.id_fppbj', $id_fppbj)
                        ->join('ms_fppbj', 'ms_fppbj.id = ms_fp3.id_fppbj', 'LEFT')
                        ->join('tb_proc_method', 'tb_proc_method.id = ms_fp3.metode_pengadaan', 'LEFT')
                        ->join('tb_division', 'tb_division.id = ms_fppbj.id_division', 'LEFT')
                        ->group_by('ms_fp3.id')
                        ->get('ms_fp3');
                        
        return $data->result_array();
    }

    function get_exportUser(){

        $query = "  SELECT
                        a.name,
                        b.username,
                        b.password,
                        a.id,
						a.email,
						c.name division
                    FROM
                        ms_admin a
                    JOIN
                        ms_login b ON b.id_user=a.id AND b.type = 'admin'
					LEFT JOIN
						eproc_perencanaan.tb_division c ON c.id=a.id_division
                    WHERE
                        a.del = 0 AND a.id_role_app2 != 0 ORDER BY a.id DESC";
        $data = $this->eproc_db->query($query)->result_array();

        return $data;
    }

    public function getDivision($year)
    {
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
        $id_division = substr($id_division_, 0, -1);

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

        return $this->db->query($query)->result_array();
    }

    public function get_fkpbj($id_fppbj)
    {
        $query = "  SELECT
                        a.*,
                        b.name divisi,
                        c.name metode_pengadaan_name
                    FROM
                        ms_fkpbj a
                    INNER JOIN
                        tb_division b ON b.id=a.id_division
                    INNER JOIN
                        tb_proc_method c ON c.id=a.metode_pengadaan
                    WHERE
                        a.del = 0 AND a.id_fppbj = ?
         ";
         $query = $this->db->query($query,array($id_fppbj));
         return $query->row_array();
    }

    public function get_fp3($id_fppbj)
    {
        $query = "  SELECT
                        a.*,
                        b.name divisi,
                        c.name metode_pengadaan,
                        d.id_division,
                        d.year_anggaran year,
                        d.jenis_pengadaan,
                        d.idr_anggaran idr_anggaran_fppbj
                    FROM
                        ms_fp3 a
                    INNER JOIN
                        tb_proc_method c ON c.id=a.metode_pengadaan
                    INNER JOIN
                        ms_fppbj d ON d.id=a.id_fppbj
                    INNER JOIN
                        tb_division b ON b.id=d.id_division
                    WHERE
                        a.del = 0 AND a.id_fppbj = ?
         ";
         $query = $this->db->query($query,array($id_fppbj));
         return $query->result_array();
    }
}
