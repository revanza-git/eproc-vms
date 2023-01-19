<?php defined('BASEPATH') or exit('No direct script access allowed');
class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->load
            ->library('email');
    }
    public function index()
    {

    }
    public function drop_dpt()
    {
        error_reporting(E_ALL);
        /*
        Query dari tr_email_blast untuk mengambil data email yang akan dikirim
        */
        $query = "SELECT * FROM tr_email_blast WHERE DATE(`date`) <= DATE('" . date('Y-m-d') . "') 

		AND id_doc != 'ms_pengurus' GROUP BY id_doc";
        $query = $this
            ->db
            ->query($query)->result_array();
        /*$table = '';
        $table .= '<table border="1">
        <tr>
        <td>Nama Vendor</td>
        <td>Dokumen</td>
        <td>Tanggal Kadaluarsa</td>
        <td>Keterangan</td>
        </tr>
        ';
        foreach($query as $key => $value){
        $sql = "SELECT a.*, b.id_vendor, c.name nama_vendor FROM tr_email_blast a JOIN
        
        ".$value['doc_type']." b ON a.id_doc=b.id JOIN ms_vendor c ON c.id=b.id_vendor WHERE a.id = ".$value
        
        ['id']." ";
        $query_= $this->db->query($sql)->row_array();
        $table .= '	<tr>
        	<td>'.$query_['nama_vendor'].'</td>
        	<td>'.$query_['doc_type'].'</td>
        	<td>'.$query_['date'].'</td>
        	<td>'.$query_['message'].'</td>
        </tr>';
        }
        $table .= '</table>';
        echo $table;die;*/

        foreach ($query as $key => $value)
        {
            if ($value['id_doc'] != '')
            {
                $queryDocument = "SELECT * FROM " . $value['doc_type'] . " WHERE id = 

			" . $value['id_doc'];
                $queryDocument = $this
                    ->db
                    ->query($queryDocument);
                $_queryDocument = $queryDocument->row_array();
                $data_vendor = "SELECT *,c.name badan_usaha, a.name name  FROM 

			ms_vendor a JOIN ms_vendor_admistrasi b ON a.id = b.id_vendor JOIN tb_legal c ON b.id_legal = c.id 

			JOIN ms_vendor_pic d ON a.id = d.id_vendor WHERE a.id = ?";
                $data_vendor = $this
                    ->db
                    ->query($data_vendor, array(
                    $_queryDocument

                    ['id_vendor']
                ))->row_array();

                if ($value['distance'] == 0)
                {
                    if ($value['doc_type'] == 'ms_ijin_usaha')
                    {
                        foreach ($queryDocument->result_array() as $keyDocument
 => $valueDocument)
                        {
                            $selectTransaction = $this
                                ->db
                                ->where('id_vendor', $valueDocument['id_vendor'])->where('id_dpt_type', $valueDocument['id_dpt_type'])->where('end_date IS NULL')
                                ->get('tr_dpt');

                            $queryTransaction = $this
                                ->db
                                ->where('id_vendor', $valueDocument['id_vendor'])->where('id_dpt_type', $valueDocument['id_dpt_type'])->where('end_date IS NULL')
                                ->update('tr_dpt', array(
                                'status' => 2,
                                'end_date' => date('Y-m-d H:i:s')
                            ));

                            if ($this
                                ->db
                                ->affected_rows() > 0 ||

                            $selectTransaction->num_rows() == 0)
                            {
                                $this
                                    ->db
                                    ->where('id_vendor',

                                $valueDocument['id_vendor'])->where('id_dpt_type', $valueDocument['id_dpt_type'])->update

                                ('tr_dpt', array(
                                    'end_date' => date('Y-

			m-d H:i:s') ,
                                    'status' => 2
                                ));
                                $this
                                    ->db
                                    ->insert('tr_dpt', array(
                                    'id_vendor' => $valueDocument

                                    ['id_vendor'],
                                    'id_dpt_type' =>

                                    $valueDocument['id_dpt_type'],
                                    'status' => 0,
                                    'entry_stamp' => date('Y-m-d 

			H:i:s')
                                ));
                            }

                            //Apakah masih ada DPT yang aktif?
                            $queryDPTCheck = $this
                                ->db
                                ->where(array(
                                'id_vendor' => $valueDocument

                                ['id_vendor'],
                                'status' => 1,
                                'end_date' => NULL,
                                'start_date' => NULL
                            ))->get('tr_dpt');

                            //Turunkan dari DPT
                            if ($queryDPTCheck->num_rows() == 0)
                            {
                                $this
                                    ->db
                                    ->where('id', $valueDocument

                                ['id_vendor'])->update('ms_vendor', array(
                                    'vendor_status' => 1
                                ));
                            }

                        }

                    }
                    else
                    {

                        foreach ($queryDocument->result_array() as $keyDocument
 => $valueDocument)
                        {

                            $queryTransaction = $this
                                ->db
                                ->where

                            ('id_vendor', $valueDocument['id_vendor'])->where('end_date IS NULL')
                                ->update('tr_dpt', array

                            (
                                'status' => 2,
                                'end_date' => date('Y-m-d H:i:s')
                            ));
                            $selectDPT = $this
                                ->db
                                ->query("SELECT * FROM 

			tr_dpt WHERE id_vendor = ? GROUP BY id_dpt_type", array(
                                $valueDocument['id_vendor']
                            ))->result_array();

                            if ($this
                                ->db
                                ->affected_rows() > 0)
                            {
                                foreach ($selectDPT as $keys => $values)
                                {
                                    $this
                                        ->db
                                        ->insert

                                    ('tr_dpt', array(
                                        'id_vendor' =>

                                        $valueDocument['id_vendor'],
                                        'id_dpt_type' =>

                                        $values['id_dpt_type'],
                                        'status' => 0,
                                        'entry_stamp' => date

                                        ('Y-m-d H:i:s')
                                    ));
                                }

                            }
                            $this
                                ->db
                                ->where('id', $valueDocument

                            ['id_vendor'])->update('ms_vendor', array(
                                'vendor_status' => 1
                            ));

                        }
                    }

                }
                $value['message'] = str_replace('\n', '<br>', $value['message']);
                $message = "
								Kepada Yth. <br/>
								" . $data_vendor['badan_usaha'] . " " . $data_vendor['name'] . ", 

			<br/><br/>
								
								Bersama ini disampaikan data  perusahaan Saudara pada aplikasi 
								Manajemen Penyedia Barang/Jasa PT Nusantara Regas yang perlu 

			diperhatikan :

								<br/>
								" . $value['message'] . "
								<br/>
								
								Silahkan memperbarui  data perusahaan Saudara<br/><br/>
								
								Demikian disampaikan untuk dapat segera ditindaklanjuti. <br/> 
								Manajemen Penyedia Barang/Jasa  - PT Nusantara Regas
							";

                //echo $message."<br><br><br><br>";
                

                $this
                    ->email
                    ->from('vms-noreply@nusantararegas.com', 'VMS REGAS');
                $this
                    ->email
                    ->to($data_vendor

                ['vendor_email']);
                $this
                    ->email
                    ->cc($data_vendor['pic_email']);
                $this
                    ->email
                    ->bcc('hanafi@nusantararegas.com');
                $this
                    ->email
                    ->bcc('logistik@nusantararegas.com');
                $this
                    ->email
                    ->bcc('muarifgustiar@gmail.com');
                $this
                    ->email
                    ->subject('Notifikasi Masa Berlaku Dokumen Aplikasi 

			Manajemen Penyedia Barang/Jasa PT Nusantara Regas');

                $this
                    ->email
                    ->message($message);
                $this
                    ->email
                    ->send();

            }

        }
    }

    public function get_drop_dpt()
    {
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'mail.nusantararegas.com',
            'smtp_port' => 587,
            'smtp_user' => 'no-reply@nusantararegas.com',
            'smtp_pass' => 'Nus@nt@r@j4y4s3nt0s4',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'smtp_crypto' => 'tls',
            'crlf' => "\r\n",
            // 'charset'   => 'iso-8859-1',
            
        );

        $this
            ->email
            ->initialize($config);
        $this
            ->email
            ->set_newline("\r\n");

        $doc = array(
            'ms_agen' => 'expire_date',
            'ms_situ' => 'expire_date',
            'ms_tdp' => 'expiry_date',
            'ms_ijin_usaha' => 'expire_date',
        );

        $data = '<table border=1>
			<thead>
				<tr>
					<th>ID</th>
					<th>Vendor</th>
					<th>Masa Berlaku</th>
					<th>Dokumen</th>
					<th>No.Dokumen</th>
					<th>Data Status</th>
				</tr>
			</thead>
			<tbody>';

        foreach ($doc as $key => $value)
        {

            if ($key == 'ms_ijin_usaha')
            {
                $file = 'Izin';
            }
            else
            {
                $e = str_replace("ms_", "", $key);
                $file = ucfirst($e);
            }

            $query = $this
                ->db
                ->select($key . '.no as no_doc,' . $value . ' as expire_date, ms_vendor.name vendor,ms_vendor.id id_vendor,tb_legal.name badan_usaha, '.$key.'.data_status')
                ->join('ms_vendor', 'ms_vendor.id=' . $key . '.id_vendor', 'LEFT')
                ->join('ms_vendor_admistrasi', 'ms_vendor.id=ms_vendor_admistrasi.id_vendor', 'LEFT')
                ->join('tb_legal', 'ms_vendor_admistrasi.id_legal=tb_legal.id', 'LEFT')
                ->where($key . '.del', 0)
                ->where($key . '.data_status', 1)
                ->where('ms_vendor.vendor_status', 2)
                ->where($key . '.' . $value . ' <=', date('Y-m-d'))
                ->get($key)
                ->result_array();

            foreach ($query as $k => $v)
            {

                $arr = array(
                    'vendor_status' => 1,
                    'edit_stamp' 	=> date('Y-m-d H:i:s')
                );

                $this->db->where('id',$v['id_vendor'])->update('ms_vendor',$arr);

                $up_tr_dpt = array(
                	'end_date'	=> date('Y-m-d H:i:s'),
                	'status'	=> 2
                );

                $this->db->where('id_vendor',$v['id_vendor'])->update('tr_dpt',$up_tr_dpt);

                $message = "
								Kepada Yth. <br/>
								" . $value['badan_usaha'] . " " . $value['vendor'] . ", 

								<br/><br/>
								
								Bersama ini disampaikan data  perusahaan Saudara pada aplikasi 
								Manajemen Penyedia Barang/Jasa PT Nusantara Regas yang perlu 

								diperhatikan :

								<br/>
								Lampiran file " . $file . " dengan nomor " . $v['no_doc'] . " masa berlakunya sudah habis.
								<br/>
								
								Silahkan memperbarui  data perusahaan Saudara<br/><br/>
								
								Demikian disampaikan untuk dapat segera ditindaklanjuti. <br/> 
								Manajemen Penyedia Barang/Jasa  - PT Nusantara Regas
							";

                // Set to, from, message
                // $this
                //     ->email
                //     ->from('no-reply@nusantararegas.com', 'System aplikasi kelogistikan');

                // $this
                //     ->email
                //     ->to('arinal.dzikrul@dekodr.co.id');
                // $this
                //     ->email
                //     ->bcc('arinaldha@gmail.com');
                // $this
                //     ->email
                //     ->subject('Notifikasi Masa Berlaku Dokumen Aplikasi Manajemen Penyedia Barang/Jasa PT Nusantara Regas');
                // $this
                //     ->email
                //     ->message($message);

                // if ($this->email->send()) {
                // 	echo "string";
                // }else{
                // 	echo $this->email->print_debugger();
                // }

                $data .= '<tr>
					<td>' . $v['id_vendor'] . '</td>
					<td>' . $v['vendor'] . '</td>
					<td>' . $v['expire_date'] . '</td>
					<td>' . $key . '</td>
					<td>' . $v['badan_usaha'] . '</td>
					<td>' . $v['data_status'] . '</td>
				</tr>';
            }
        }
        // Set to, from, message
        $this
            ->email
            ->from('no-reply@nusantararegas.com', 'System aplikasi kelogistikan');

        $this
            ->email
            ->to('arinaldha@gmail.com');
        $this
            ->email
            ->subject('Notifikasi Cron DROP DPT Server 11.5');
        $this
            ->email
            ->message(date('Y-m-d H:i:s')." Cron Working :D");

        if ($this->email->send()) {
        	echo "string";
        }else{
        	echo $this->email->print_debugger();
        }
        // echo $msg;
        $data .= '</tbody>
		</table>';

        echo $data;
    }
}

