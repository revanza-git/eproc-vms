<?php
/**
 * 
 */
class Cron extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('Cron_model','cm');
	}

	public function reminder()
	{

		$getData = $this->cm->getData();
		
		foreach ($getData as $key => $value) {
			if ($value['is_status'] == 0 || $value['is_status'] == 2) {
				// echo "Masuk 1";die;
				$yellow = $value['jwpp_start'];

				if ($value['is_status'] == 0) {
					$subject = 'Masa waktu FPPBJ hampir habis';
				} else if ($value['is_status'] == 2) {
					$subject = 'Masa waktu FKPBJ hampir habis';
				}

				$message = "Masa waktu pengadaan ".$value['nama_pengadaan']." hampir habis silahkan cek di website " . env('EXTERNAL_EPROC_URL', 'http://eproc.nusantararegas.com/internal/eproc_perencanaan') . " , <br> Terimakasih";


				$yellow = date('Y-m-d', strtotime('-26 days', strtotime($yellow)));
				
				$prevDate = date('Y-m-d', strtotime('-14 days', strtotime($yellow)));

				// echo 'asdasdas'.$prevDate;die;
				if (date('Y-m-d') == $prevDate) {
					$getEmail = $this->cm->getEmail($value['id_division']);
					foreach ($getEmail as $key => $value) {
						$this->send_mail($value['email'],$subject,$message);
					}
				}
			} elseif ($value['is_status'] == 1) {
				// echo "Masuk 2";die;
				$yellow__ = $value['jwpp_start'];

				$subject_1 = 'Masa waktu FP3 hampir habis (Warning 1)';
				$subject_2 = 'Masa waktu FP3 hampir habis (Warning 2)';
				$message = "Masa waktu pengadaan ".$value['nama_pengadaan']." hampir habis silahkan cek di website " . env('EXTERNAL_EPROC_URL', 'http://eproc.nusantararegas.com/internal/eproc_perencanaan') . " , <br> Terimakasih";

				$yellow = date('Y-m-d', strtotime('-26 days', strtotime($yellow__)));
				$yellow_ = date('Y-m-d', strtotime('-12 days', strtotime($yellow__)));
				if (date('Y-m-d') == $yellow) {
					$getEmail = $this->cm->getEmail($value['id_division']);
					foreach ($getEmail as $key => $value) {
						$this->send_mail($value['email'],$subject_1,$message);
					}
				}

				if (date('Y-m-d') == $yellow_) {
					$getEmail = $this->cm->getEmail($value['id_division']);
					foreach ($getEmail as $key => $value) {
						$this->send_mail($value['email'],$subject_2,$message);
					}
				}
			}
		}
	}

	public function send_mail($to,$subject,$message)
	{
		$to = base64_encode($to);
		$sub = base64_encode($subject);
		$message = base64_encode($message);

		$url = "http://dekodr.co.id/send_mail/send.php";
		$data = http_build_query(array(
			'to' => $to,
			'sub' => $sub,
			'message' => $message
		));

		$options = array(
	  		'http' => array(
	    			'header'  => "Content-type: application/x-www-form-urlencoded",
	    			'method'  => 'POST',
	    			'content' => $data,
	  		),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
	}
	
	public function showUser()
	{
		$query = " 	SELECT
						c.name divisi,
						b.name role,
						a.name,
						a.username,
						a.raw_password
					FROM
						ms_user a
					JOIN
						tb_role b ON b.id=a.id_role
					JOIN
						tb_division c ON c.id=a.id_division
					WHERE
						a.del = 0;
		";
		$get_data_admin = $this->db->query($query)->result_array();

		$admin = '<table border=1>
			<thead>
				<tr>
					<th colspan="6">Daftar User Admin </th>
				</tr>
				<tr>
					<th>No</th>
					<th>Role</th>
					<th>Divisi</th>
					<th>Nama</th>
					<th>Username</th>
					<th>Password</th>
				</tr>
			</thead>
			<tbody>';
			$no=1;
		foreach ($get_data_admin as $key => $value) {
			$admin .= '<tr>
				<td>'.$no.'</td>
				<td>'.$value['role'].'</td>
				<td>'.$value['divisi'].'</td>
				<td>'.$value['name'].'</td>
				<td>'.$value['username'].'</td>
				<td>'.$value['raw_password'].'</td>
			</tr>';
			$no++;
		}
				
		$admin .='</tbody>
		</table>';
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename=Daftar User Perencanaan.xls');

		echo $admin;
	}
}