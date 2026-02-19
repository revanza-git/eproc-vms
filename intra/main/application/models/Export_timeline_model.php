<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Export_timeline_model extends MY_Model {

    public $eproc_db;

    public function __construct()
    {
        parent::__construct();
        $this->eproc_db = $this->load->database('eproc',true);
    }

    function rekap_perencanaan($year = null){
        $division = $this->db    ->select('tb_kadiv.name id_kadiv, tb_division.name, tb_division.id')
                                 ->join('tb_kadiv','tb_division.id_kadiv=tb_kadiv.id','LEFT')
                                 ->order_by('id_kadiv','DESC')
                                 ->get('tb_division')
                                 ->result_array();

            foreach ($division as $key_ => $division_) {
                // print_r($division_);die;
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
                        ms_fppbj.is_approved,
                        ms_fppbj.tipe_pengadaan,
                        ms_fppbj.is_approved_hse,
                        ms_fppbj.is_status,
                        ms_fppbj.is_reject,
                        ms_fppbj.id_pic
                        FROM ms_fppbj 
                        LEFT JOIN tb_division ON tb_division.id = ms_fppbj.id_division
                        LEFT JOIN tb_proc_method ON tb_proc_method.id = ms_fppbj.metode_pengadaan
                        WHERE 
                        (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 0 )
                        OR
                        (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 2)
                        OR
                        (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 3)
                        ORDER BY id_kadiv DESC, divisi")->result_array();
            }
        // }
        // print_r($data);die;
            // (
            //             (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 0 AND is_approved <= 2) 
            //             OR 
            //             (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 0 AND is_approved = 3 AND is_reject = 0 AND is_writeoff = 0 AND ((idr_anggaran > 100000000 AND idr_anggaran <= 1000000000) AND (metode_pengadaan = 4 OR metode_pengadaan = 2 OR metode_pengadaan = 1))) 
            //             OR 
            //             (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 0 AND is_approved = 3 AND is_reject = 0 AND is_writeoff = 0 AND (idr_anggaran > 1000000000 AND idr_anggaran <= 10000000000) AND (metode_pengadaan = 4 OR metode_pengadaan = 2 OR metode_pengadaan = 1)) 
            //             OR 
            //             (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 0 AND is_approved = 3 AND is_reject = 0 AND is_writeoff = 0 AND idr_anggaran >= 10000000000 AND (metode_pengadaan = 4 OR metode_pengadaan = 2 OR metode_pengadaan = 1)))

            //             OR (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 2 AND is_approved <= 2)

            //             OR (id_division =".$division_['id']." AND ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 1 AND is_approved <= 2)
         // print_r($data);die;
            // echo $this->db->last_query();die;
        $id_ = array();
        foreach ($data as $kadivGroup) {
            if (!is_array($kadivGroup)) {
                continue;
            }
            foreach ($kadivGroup as $divisionRows) {
                if (!is_array($divisionRows)) {
                    continue;
                }
                foreach ($divisionRows as $row) {
                    if (is_array($row) && isset($row['id_fppbj'])) {
                        $id_[] = (int) $row['id_fppbj'];
                    }
                }
            }
        }
        $this->session->set_userdata('export_id', json_encode($id_));

        // print_r($data);die;
        return $data;
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
                            (ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 0 )
                            OR
                            (ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 2)
                            OR
                            (ms_fppbj.del = 0 AND ms_fppbj.entry_stamp LIKE '%".$year."%' AND is_status = 3)
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query)->result_array();

        }
        // print_r($query);die;
        return $query;
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
                        WHERE YEAR(ms_fkpbj.entry_stamp) = ? AND ms_fkpbj.del = 0
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query,array($year))->result_array();
        }
        // print_r($query);die;
        return $query;
    }

    function rekap_department_fppbj($year = null,$id_division, $type=""){
        // echo "string1 - ".$year." - string2 ".$id_division;die;
        if ($type == "") {
            $w_t = "a.is_perencanaan != 1 AND ";
        } else {
            $w_t = "a.is_perencanaan = '".$type."'  AND ";
        }
        $query = "SELECT
                    a.id,
                    tb_division.id id_division, 
                    tb_division.name divisi_name,
                    count(metode_pengadaan) as total_metod,
                    count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                    count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                    count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                    count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                    count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                    
                FROM ms_fppbj a
                LEFT JOIN tb_division ON a.id_division=tb_division.id 
                WHERE  
                    $w_t a.id_division = $id_division AND  a.del = 0 AND a.entry_stamp LIKE '%".$year."%' AND a.is_status = 0";
        $query = $this->db->query($query)->result_array();
        // echo $this->db->last_query();die;
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

        // (
        //                 ($w_t a.id_division = $id_division AND a.is_reject = 0 AND a.is_approved_hse < 2 AND a.del = 0 AND a.entry_stamp LIKE '%".$year."%' AND a.is_status = 0 AND a.is_approved <= 2) 
        //                 OR 
        //                 ($w_t a.id_division = $id_division AND a.is_approved_hse < 2 AND a.del = 0 AND a.entry_stamp LIKE '%".$year."%' AND a.is_status = 0 AND a.is_approved = 3 AND a.is_reject = 0 AND a.is_writeoff = 0 AND ((a.idr_anggaran > 100000000 AND a.idr_anggaran <= 1000000000) AND (a.metode_pengadaan = 4 OR a.metode_pengadaan = 2 OR a.metode_pengadaan = 1))) 
        //                 OR 
        //                 ($w_t a.id_division = $id_division AND a.is_approved_hse < 2 AND a.del = 0 AND a.entry_stamp LIKE '%".$year."%' AND a.is_status = 0 AND a.is_approved = 3 AND a.is_reject = 0 AND a.is_writeoff = 0 AND (a.idr_anggaran > 1000000000 AND a.idr_anggaran <= 10000000000) AND (a.metode_pengadaan = 4 OR a.metode_pengadaan = 2 OR a.metode_pengadaan = 1)) 
        //                 OR 
        //                 ($w_t a.id_division = $id_division AND a.is_approved_hse < 2 AND a.del = 0 AND a.entry_stamp LIKE '%".$year."%' AND a.is_status = 0 AND a.is_approved = 3 AND a.is_reject = 0 AND a.is_writeoff = 0 AND a.idr_anggaran >= 10000000000 AND (a.metode_pengadaan = 4 OR a.metode_pengadaan = 2 OR a.metode_pengadaan = 1))
        //             )
        
    }

    function rekap_department_fp3($year = null,$id_division,$type=""){
        // echo "string1 - ".$year." - string2 ".$id_division;die;
        if ($type == "") {
            $w_t = " AND abc.del = 0";
        } else {
            if ($type == "hapus") {
                $w_t = " AND abc.status = 'hapus'  AND abc.del = 0";
            } else {
                $w_t = " AND abc.perubahan = '".$type."'  AND abc.del = 0"; 
            }
        }
        $query = "SELECT
                        a.id,
                        tb_division.id id_division, 
                        tb_division.name divisi_name,
                        c.name metode_pengadaan_name,
                        abc.perubahan,
                        count(a.metode_pengadaan) as total_metod,
                        count(CASE WHEN a.metode_pengadaan LIKE '1' THEN 1 END) as metode_1,
                        count(CASE WHEN a.metode_pengadaan LIKE '2' THEN 1 END) as metode_2,
                        count(CASE WHEN a.metode_pengadaan LIKE '3' THEN 1 END) as metode_3,
                        count(CASE WHEN a.metode_pengadaan LIKE '4' THEN 1 END) as metode_4,
                        count(CASE WHEN a.metode_pengadaan LIKE '5' THEN 1 END) as metode_5
                        
                    FROM ms_fppbj a
                    LEFT JOIN ms_fp3 abc ON abc.id_fppbj=a.id
                    LEFT JOIN tb_division ON a.id_division=tb_division.id
                    LEFT JOIN tb_proc_method c ON a.metode_pengadaan=c.id 
                    WHERE a.is_status = 1 AND a.entry_stamp LIKE '%".$year."%' AND a.id_division = $id_division $w_t
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
        // echo($metode_pengadaan."-".$id_division."-".$year);die;
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
        // echo $this->db->last_query();die;
        // print_r($query);die;
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
        // echo($metode);die;
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
        $start_yellow = $metode_day + 14 * - 1;
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
                        WHERE a.entry_stamp LIKE '%".$year."%' AND a.id_division = ? AND a.del = 0 AND DATE(a.entry_stamp) <= DATE_SUB(a.jwpp_start, INTERVAL ".$end_yellow." DAY)
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query,array($id_division))->result_array();
        // echo $this->db->last_query();die;
        // print_r($query);die;
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

    function rekap_department_fp3_timeline($year = null,$id_division,$metode){
        // echo "string1 - ".$year." - string2 ".$id_division;die;

        $metode = trim($metode);
        // echo($metode_pengadaan."-".$id_division."-".$year);die;
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
                        tb_division.id id_division, 
                        tb_division.name divisi_name,
                        c.name metode_pengadaan_name,
                        abc.perubahan,
                        count(a.metode_pengadaan) as total_metod,
                        count(CASE WHEN a.metode_pengadaan LIKE '1' THEN 1 END) as metode_1,
                        count(CASE WHEN a.metode_pengadaan LIKE '2' THEN 1 END) as metode_2,
                        count(CASE WHEN a.metode_pengadaan LIKE '3' THEN 1 END) as metode_3,
                        count(CASE WHEN a.metode_pengadaan LIKE '4' THEN 1 END) as metode_4,
                        count(CASE WHEN a.metode_pengadaan LIKE '5' THEN 1 END) as metode_5
                        
                    FROM ms_fppbj a
                    LEFT JOIN ms_fp3 abc ON abc.id_fppbj=a.id
                    LEFT JOIN ms_fkpbj fk ON fk.id_fppbj=a.id
                    LEFT JOIN tb_division ON a.id_division=tb_division.id
                    LEFT JOIN tb_proc_method c ON a.metode_pengadaan=c.id 
                    WHERE 
                        (a.is_status = 1 AND a.entry_stamp LIKE '%".$year."%' AND a.id_division = $id_division AND abc.perubahan = 'time_line' AND a.del = 0) 

                        OR

                        (fk.entry_stamp LIKE '%".$year."%' AND fk.id_division = $id_division AND fk.del = 0 AND DATE(fk.entry_stamp) > DATE_SUB(fk.jwpp_start, INTERVAL ".$end_yellow." DAY)
                        )

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

    public function get_pic($id_pic)
    {
        $query = $this->eproc_db->where('id', $id_pic)->where('del',0)->get('ms_admin');
        return $query->row_array();
    }

    public function get_note_reject($id_fppbj)
    {
        $query = $this->db->where('id_fppbj',$id_fppbj)->where('type','reject')->where('is_note_reject',1)->get('tr_note');
        return $query->row_array();
    }
}

/* End of file Export_timeline_model.php */
/* Location: ./application/models/Export_timeline_model.php */
