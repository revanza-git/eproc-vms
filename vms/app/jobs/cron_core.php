<?php
if (!defined('BASEPATH')) {
	define('BASEPATH', __DIR__ . DIRECTORY_SEPARATOR);
}
if (!defined('APPPATH')) {
	define('APPPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
}
if (!defined('ENVIRONMENT')) {
	define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : ((getenv('CI_ENV') !== false) ? getenv('CI_ENV') : 'development'));
}

require_once __DIR__ . "/../application/config/database.php";
require_once __DIR__ . "/cron_mail.php";

class cron{
	/** @var mysqli */
	private $conn;

	function __construct(){
		error_reporting(E_ERROR);
		$this->connect();
	}

	private function connect(){
		global $db;

		$config = isset($db['default']) && is_array($db['default']) ? $db['default'] : array();
		$hostname = isset($config['hostname']) ? (string) $config['hostname'] : '127.0.0.1';
		$username = isset($config['username']) ? (string) $config['username'] : '';
		$password = isset($config['password']) ? (string) $config['password'] : '';
		$database = isset($config['database']) ? (string) $config['database'] : '';
		$port = isset($config['port']) ? (int) $config['port'] : 3306;
		$charset = isset($config['char_set']) ? (string) $config['char_set'] : 'utf8';

		$this->conn = @mysqli_init();
		if ($this->conn === false) {
			throw new RuntimeException('Failed to initialize mysqli connection.');
		}

		if (!@$this->conn->real_connect($hostname, $username, $password, $database, $port)) {
			throw new RuntimeException('Failed to connect to database for cron execution.');
		}

		@$this->conn->set_charset($charset);
	}
	
	function query($sql = ''){
		return $this->conn->query($sql);
	}

	function execute($sql = '', $types = '', $params = array()){
		$stmt = $this->conn->prepare($sql);
		if ($stmt === false) {
			return false;
		}

		if (!empty($params)) {
			$bind = array($types);
			foreach ($params as $key => $value) {
				$bind[] = &$params[$key];
			}
			if (!call_user_func_array(array($stmt, 'bind_param'), $bind)) {
				$stmt->close();
				return false;
			}
		}

		$ok = $stmt->execute();
		$stmt->close();
		return $ok;
	}
	
	function num_rows($query = null){
		return ($query instanceof mysqli_result) ? $query->num_rows : 0;
	}

	function row_array($query = null){
		if (!($query instanceof mysqli_result)) {
			return array();
		}

		$row = $query->fetch_assoc();
		$query->free();

		return is_array($row) ? $row : array();
	}
	
	function result($query = null){
		if (!($query instanceof mysqli_result)) {
			return array();
		}

		$return = $query->fetch_all(MYSQLI_ASSOC);
		$query->free();
		return is_array($return) ? $return : array();
	}

	function escape_identifier($value = ''){
		$identifier = (string) $value;
		if ($identifier === '' || preg_match('/^[a-zA-Z0-9_]+$/', $identifier) !== 1) {
			return false;
		}

		return '`'.$identifier.'`';
	}

	function close(){
		if ($this->conn instanceof mysqli) {
			$this->conn->close();
		}
	}
	
	function send_email($to = '', $subject = '', $message = ''){
		
		$from = 	 'vms-noreply@nusantararegas.co.id';
		$from_name = 'Sistem Aplikasi Kelogistikan';
		$subject = 	 'Update data Penyedia Barang/Jasa : '.$subject;
 
		$header  = "Reply-To: Sistem Aplikasi Kelogistikan <vms-noreply@nusantararegas.co.id>\r\n"; 
    		$header .= "Return-Path: Sistem Aplikasi Kelogistikan <vms-noreply@nusantararegas.co.id>\r\n"; 
    		$header .= "From: Sistem Aplikasi Kelogistikan <vms-noreply@nusantararegas.co.id>\r\n"; 
    		$header .= "Organization: PT Nusantara Regas\r\n"; 
    		$header .= "Content-Type: text/html; charset=UTF-8\r\n"; 
		$header .= "X-Mailer: PHP/" . phpversion()."\n";

		mail($to, $subject, $message, $header,"-f $from");
	}

	function __destruct(){
		$this->close();
	}
}
