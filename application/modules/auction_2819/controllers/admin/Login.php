<?php
class Login extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('login_model');
	}
	
	function index(){
		$this->load->view('template/login');
	}
	
	function validate(){
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$is_logged = $this->login_model->cek_data($username, $password);
		
		if($is_logged){
			if($_SESSION['vm_pgn_active_user']['nama']){ $nama = $_SESSION['vm_pgn_active_user']['nama']; $is_admin = "no"; $app = "vms"; }
			if($_SESSION['vm_pgn_active_admin']['nama']){ $nama = $_SESSION['vm_pgn_active_admin']['nama']; $is_admin = "yes";  $app = "vms";  }
			if($_SESSION['auction_pgn_active_admin']['nama']){ $nama = $_SESSION['auction_pgn_active_admin']['nama']; $is_admin = "yes"; $app = "auction"; }
									
			$return = array(
				'status' => 'success',
				'nama' => $nama,
				'is_admin' => $is_admin,
				'app' => $app
			);
		}
		else
			$return = array(
				'status' => 'fail',
				'message' => 'Data anda tidak dikenal. Silahkan coba untuk login kembali'
			);

		die(json_encode($return));
	}
	
	function logout(){
		$_SESSION['vm_pgn_active_user'] = $_SESSION['vm_pgn_active_admin'] = null;
		
		echo 'anda telah ter-logout dari aplikasi Manajemen Penyedia Barang/Jasa PT Perusahaan Gas Negara (Persero) Tbk...';
		die('<script type="text/javascript">location.replace("'.base_url().'")</script>');
	}
}