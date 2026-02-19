<?php

/**
 * 
 */
class Export_test_model extends MY_Model
{
    function rekap_department($year = null)
    {
        $yearInt = (int) $year;
        $divisi = $this->db->where('del', 0)->get('tb_division')->result_array();
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
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON ms_fppbj.id_division=tb_division.id 
                        WHERE 
                            (ms_fppbj.del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))
                            OR  

                            (ms_fppbj.del = 0 AND is_approved = 4 AND idr_anggaran > 100000000))

                            OR

                            (ms_fppbj.is_status = 2 AND ms_fppbj.del = 0)

                            OR

                            (ms_fppbj.is_status = 1 AND ms_fppbj.del = 0)

                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query)->result_array();
        }
        // print_r($query);die;AND is_approved = 3
        // (
        //     (
        //         ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND 
        //         ms_fppbj.is_status = 1 AND 
        //         ms_fppbj.del = 0 AND 
        //         is_approved = 3 AND 
        //         (
        //             idr_anggaran <= 100000000 OR 
        //             (
        //                 idr_anggaran > 100000000 AND metode_pengadaan = 3
        //             )
        //         )
        //     )
        //     OR
        //     (
        //         ms_fppbj.entry_stamp LIKE '%" . $year . "%' AND 
        //         ms_fppbj.is_status = 1 AND 
        //         ms_fppbj.del = 0 AND 
        //         is_approved = 4 AND 
        //         idr_anggaran > 100000000
        //     )
        // )
        return $query;
    }

    function rekap_fppbj_perencanaan($year = null, $id_division)
    {
        $yearInt = (int) $year;
        $divisi = $this->db->where('del', 0)->get('tb_division')->result_array();
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
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON ms_fppbj.id_division=tb_division.id 
                        WHERE 
                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.id_division = " . $id_division . " AND ms_fppbj.del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))
                            OR  

                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.id_division = " . $id_division . " AND ms_fppbj.del = 0 AND is_approved = 4 AND idr_anggaran > 100000000))

                            OR

                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.id_division = " . $id_division . " AND ms_fppbj.is_status = 2 AND ms_fppbj.del = 0)

                            OR

                            (ms_fppbj.is_perencanaan = 1 AND ms_fppbj.id_division = " . $id_division . " AND ms_fppbj.is_status = 1 AND ms_fppbj.del = 0)

                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query)->result_array();
        }
        return $query;
    }

    function rekap_department_fppbj($year = null, $id_division, $type = 2)
    {
        $yearInt = (int) $year;
        $w_t = "a.is_perencanaan = '" . $type . "'  AND ";

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
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON a.id_division=tb_division.id 
                        WHERE  
                            is_reject = 0 
                            AND a.del = 0
                            AND is_approved_hse < 2
                            AND (($w_t is_status = 0 AND id_division = " . $id_division . " AND a.del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))))
                            OR  ($w_t is_status = 0 AND id_division = " . $id_division . " AND a.del = 0 AND is_approved = 4 AND idr_anggaran > 100000000)";
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

    function rekap_total_department_fppbj($year = null, $type = 2)
    {
        $type = (int) $type;
        $year = (string) $year;

        $query = "SELECT
                        count(metode_pengadaan) as total_metod,
                        count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                        count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                        count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                        count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                        count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                    FROM ms_fppbj a
                    JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id AND ya.year_anggaran = ?
                    WHERE
                        is_reject = 0
                        AND a.del = 0
                        AND is_approved_hse < 2
                        AND a.is_perencanaan = ?
                        AND a.is_status = 0
                        AND (
                            (
                                is_approved = 3 AND
                                (
                                    idr_anggaran <= 100000000 OR
                                    (
                                        idr_anggaran > 100000000 AND metode_pengadaan = 3
                                    )
                                )
                            )
                            OR
                            (
                                is_approved = 4 AND idr_anggaran > 100000000
                            )
                        )";
        $res = $this->db->query($query, array((int) $year, $type))->result_array();

        if (count($res) > 0) {
            return $res;
        }

        return array(
            0 => array(
                'metode_1' => 0,
                'metode_2' => 0,
                'metode_3' => 0,
                'metode_4' => 0,
                'metode_5' => 0
            )
        );
    }

    function rekap_department_fkpbj($year = null, $id_division)
    {
        $yearInt = (int) $year;
        // echo "string1 - ".$year." - string2 ".$id_division;die;
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
                    JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = " . $yearInt . "
                    LEFT JOIN tb_division ON ms_fppbj.id_division=tb_division.id
                    LEFT JOIN tb_proc_method c ON ms_fppbj.metode_pengadaan=c.id 
                    WHERE ms_fppbj.is_status = 2 AND ms_fppbj.del = 0 AND ms_fppbj.id_division = $id_division
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

    function rekap_total_department_fkpbj($year = null)
    {
        $year = (string) $year;
        $query = "SELECT
                        count(metode_pengadaan) as total_metod,
                        count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                        count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                        count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                        count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                        count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                    FROM ms_fppbj
                    JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ?
                    WHERE is_status = 2 AND del = 0";
        $res = $this->db->query($query, array((int) $year))->result_array();

        if (count($res) > 0) {
            return $res;
        }

        return array(
            0 => array(
                'metode_1' => 0,
                'metode_2' => 0,
                'metode_3' => 0,
                'metode_4' => 0,
                'metode_5' => 0
            )
        );
    }

    function rekap_department_fkpbj_baru($year = null, $id_division, $type = "")
    {
        $yearInt = (int) $year;
        // echo "string1 - ".$year." - string2 ".$id_division;die;
        if ($type == "") {
            $w_t = "a.is_perencanaan != 1 AND ";
        } else {
            $w_t = "a.is_perencanaan = '" . $type . "'  AND ";
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
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON a.id_division=tb_division.id 
                        WHERE  
                            is_reject = 0 
                            AND a.del = 0
                            AND  ($w_t is_status = 2 AND id_division = " . $id_division . " AND a.del = 0)";
        //AND is_approved = 3
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

    function rekap_total_department_fkpbj_baru($year = null, $type = "")
    {
        $year = (string) $year;
        if ($type === "" || $type === null) {
            $where = "is_perencanaan != 1";
            $bind = array((int) $year);
        } else {
            $where = "is_perencanaan = ?";
            $bind = array((int) $year, (int) $type);
        }

        $query = "SELECT
                        count(metode_pengadaan) as total_metod,
                        count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                        count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                        count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                        count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                        count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                    FROM ms_fppbj
                    JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ?
                    WHERE is_reject = 0 AND del = 0 AND is_status = 2 AND {$where}";
        $res = $this->db->query($query, $bind)->result_array();

        if (count($res) > 0) {
            return $res;
        }

        return array(
            0 => array(
                'metode_1' => 0,
                'metode_2' => 0,
                'metode_3' => 0,
                'metode_4' => 0,
                'metode_5' => 0
            )
        );
    }

    function rekap_department_fp3($year = null, $id_division, $type = "", $is_perencanaan = 1)
    {
        $yearInt = (int) $year;
        // echo "string1 - ".$year." - string2 ".$id_division;die;
        if ($type == "") {
            $w_t = " AND abc.del = 0";
        } else {
            if ($type == "hapus") {
                $w_t = " AND abc.status = 'hapus'  AND abc.del = 0";
            } else {
                $w_t = " AND abc.perubahan = '" . $type . "'  AND abc.del = 0";
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
                    JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id AND ya.year_anggaran = " . $yearInt . "
                    LEFT JOIN ms_fp3 abc ON abc.id_fppbj=a.id
                    LEFT JOIN tb_division ON a.id_division=tb_division.id
                    LEFT JOIN tb_proc_method c ON a.metode_pengadaan=c.id 
                    WHERE 
                        a.is_perencanaan = " . $is_perencanaan . " AND a.id_division = $id_division $w_t AND a.del = 0 

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

    function rekap_department_fp3_by_type($year = null, $id_division, $type = "")
    {
        $yearInt = (int) $year;
        // echo "string1 - ".$year." - string2 ".$id_division;die;
        if ($type == "") {
            $w_t = "a.is_perencanaan != 1 AND ";
        } else {
            $w_t = "a.is_perencanaan = '" . $type . "'  AND ";
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
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON a.id_division=tb_division.id 
                        WHERE  
                            is_reject = 0 
                            AND a.del = 0
                            AND  ($w_t is_status = 1 AND id_division = " . $id_division . " AND a.del = 0)";
        //AND is_approved = 3
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

    function rekap_total_department_fp3_by_type($year = null, $type = "")
    {
        $year = (string) $year;
        if ($type === "" || $type === null) {
            $where = "is_perencanaan != 1";
            $bind = array((int) $year);
        } else {
            $where = "is_perencanaan = ?";
            $bind = array((int) $year, (int) $type);
        }

        $query = "SELECT
                        count(metode_pengadaan) as total_metod,
                        count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                        count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                        count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                        count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                        count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                    FROM ms_fppbj
                    JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ?
                    WHERE is_reject = 0 AND del = 0 AND is_status = 1 AND {$where}";
        $res = $this->db->query($query, $bind)->result_array();

        if (count($res) > 0) {
            return $res;
        }

        return array(
            0 => array(
                'metode_1' => 0,
                'metode_2' => 0,
                'metode_3' => 0,
                'metode_4' => 0,
                'metode_5' => 0
            )
        );
    }

    function rekap_department_fp3_timeline($year = null, $id_division, $metode, $is_perencanaan = 1)
    {
        $yearInt = (int) $year;
        // echo "string1 - ".$year." - string2 ".$id_division;die;

        // OR

        //                 (fk.is_approved = 3 AND fk.entry_stamp LIKE '%".$year."%' AND fk.id_division = $id_division AND fk.del = 0 AND DATE(fk.entry_stamp) > DATE_SUB(fk.jwpp_start, INTERVAL ".$end_yellow." DAY)
        //                 )

        $metode = trim($metode);
        // echo($metode_pengadaan."-".$id_division."-".$year);die;
        if ($metode == "Pelelangan") {
            $metode_day = 60; //60 hari
        } else if ($metode == "Pengadaan Langsung") {
            $metode_day = 10; // 10 hari
        } else if ($metode == "Pemilihan Langsung") {
            $metode_day = 45; //45 hari
        } else if ($metode == "Swakelola") {
            $metode_day = 0;
        } else if ($metode == "Penunjukan Langsung") {
            $metode_day = 20; // 20 hari
        } else {
            //$metode_day = 1;
        }
        $start_yellow = $metode_day + 14 * -1;
        $end_yellow = $metode_day + 1;

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
                    JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id AND ya.year_anggaran = " . $yearInt . "
                    LEFT JOIN ms_fp3 abc ON abc.id_fppbj=a.id
                    LEFT JOIN tb_division ON a.id_division=tb_division.id
                    LEFT JOIN tb_proc_method c ON a.metode_pengadaan=c.id 
                    WHERE 
                        (
							a.is_status = 1 AND 
							a.is_perencanaan = " . $is_perencanaan . " AND 
							a.id_division = $id_division AND 
							abc.perubahan = 'time_line' AND 
							a.del = 0 AND 
                            abc.del = 0
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

    function get_perencanaan($year = null)
    {
        $yearInt = (int) $year;
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
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON ms_fppbj.id_division=tb_division.id 
                        WHERE 
                            (ms_fppbj.del = 0 AND is_approved = 3 AND (idr_anggaran <= 100000000 OR (idr_anggaran > 100000000 AND metode_pengadaan = 3))
                            OR  

                            (ms_fppbj.del = 0 AND is_approved = 4 AND idr_anggaran > 100000000))

                            -- OR

                            -- (ms_fppbj.is_status = 2 AND ms_fppbj.del = 0)
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
            $query = $this->db->query($query)->result_array();
        }
        return $query;
    }

    function count_rekap_department_fkpbj($year = null, $id_division, $metode_pengadaan)
    {
        $yearInt = (int) $year;
        $metode = trim($metode_pengadaan);
        // echo($metode_pengadaan."-".$id_division."-".$year);die;
        if ($metode == "Pelelangan") {
            $metode_day = 60; //60 hari
        } else if ($metode == "Pengadaan Langsung") {
            $metode_day = 10; // 10 hari
        } else if ($metode == "Pemilihan Langsung") {
            $metode_day = 45; //45 hari
        } else if ($metode == "Swakelola") {
            $metode_day = 0;
        } else if ($metode == "Penunjukan Langsung") {
            $metode_day = 20; // 20 hari
        } else {
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
                            
                        FROM ms_fppbj a
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON a.id_division=tb_division.id
                        WHERE a.is_perencanaan = 1 AND a.is_status = 2 AND a.id_division = ? AND a.del = 0
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
        $query = $this->db->query($query, array($id_division))->result_array();
        // echo $this->db->last_query();die;
        // print_r($query);die; AND a.is_approved = 3 
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

    function count_total_department_fkpbj($year = null)
    {
        $year = (string) $year;
        $query = "SELECT
                        count(metode_pengadaan) as total_metod,
                        count(CASE WHEN `metode_pengadaan` LIKE '1' THEN 1 END) as metode_1,
                        count(CASE WHEN `metode_pengadaan` LIKE '2' THEN 1 END) as metode_2,
                        count(CASE WHEN `metode_pengadaan` LIKE '3' THEN 1 END) as metode_3,
                        count(CASE WHEN `metode_pengadaan` LIKE '4' THEN 1 END) as metode_4,
                        count(CASE WHEN `metode_pengadaan` LIKE '5' THEN 1 END) as metode_5
                    FROM ms_fppbj
                    JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = ms_fppbj.id AND ya.year_anggaran = ?
                    WHERE is_perencanaan = 1 AND is_status = 2 AND del = 0";
        $res = $this->db->query($query, array((int) $year))->result_array();

        if (count($res) > 0) {
            return $res;
        }

        return array(
            0 => array(
                'metode_1' => 0,
                'metode_2' => 0,
                'metode_3' => 0,
                'metode_4' => 0,
                'metode_5' => 0
            )
        );
    }

    function count_rekap_department_fkpbj_telat($year = null, $id_division, $metode_pengadaan)
    {
        $yearInt = (int) $year;
        $metode = trim($metode_pengadaan);
        // echo($metode_pengadaan."-".$id_division."-".$year);die;
        if ($metode == "Pelelangan") {
            $metode_day = 60; //60 hari
        } else if ($metode == "Pengadaan Langsung") {
            $metode_day = 10; // 10 hari
        } else if ($metode == "Pemilihan Langsung") {
            $metode_day = 45; //45 hari
        } else if ($metode == "Swakelola") {
            $metode_day = 0;
        } else if ($metode == "Penunjukan Langsung") {
            $metode_day = 20; // 20 hari
        } else {
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
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id_fppbj AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON a.id_division=tb_division.id
                        WHERE a.is_approved = 3 AND a.id_division = ? AND a.del = 0 AND DATE(a.entry_stamp) > DATE_SUB(a.jwpp_start, INTERVAL " . $end_yellow . " DAY)
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
        $query = $this->db->query($query, array($id_division))->result_array();
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

    function count_rekap_department_fkpbj_tidak_telat($year = null, $id_division, $metode_pengadaan)
    {
        $yearInt = (int) $year;
        $metode = trim($metode_pengadaan);
        // echo($metode);die;
        if ($metode == "Pelelangan") {
            $metode_day = 60; //60 hari
        } else if ($metode == "Pengadaan Langsung") {
            $metode_day = 10; // 10 hari
        } else if ($metode == "Pemilihan Langsung") {
            $metode_day = 45; //45 hari
        } else if ($metode == "Swakelola") {
            $metode_day = 0;
        } else if ($metode == "Penunjukan Langsung") {
            $metode_day = 20; // 20 hari
        } else {
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
                        JOIN ms_fppbj_year_anggaran ya ON ya.id_fppbj = a.id_fppbj AND ya.year_anggaran = " . $yearInt . "
                        LEFT JOIN tb_division ON a.id_division=tb_division.id
                        WHERE a.id_division = ? AND a.del = 0 AND DATE(a.entry_stamp) <= DATE_SUB(a.jwpp_start, INTERVAL " . $end_yellow . " DAY)
                        GROUP BY tb_division.id
                        order by tb_division.id DESC";
        $query = $this->db->query($query, array($id_division))->result_array();
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

    public function getDivision()
    {
        $query = $this->db->where('del', 0)->get('tb_division');
        return $query->result_array();
    }
}
