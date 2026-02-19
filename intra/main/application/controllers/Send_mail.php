<?php 
/**
 * 
 */
class Send_mail extends MY_Controller
{
	public function send($to,$sub,$msg)
	{
		$to  = base64_decode($to);
		$sub = base64_decode($sub);
		$msg = base64_decode($msg);
		return $this->sendMailEproc($to,$sub,$msg);
	}
}