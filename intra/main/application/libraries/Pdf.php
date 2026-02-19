<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/dompdf2/dompdf_config.inc.php";
class Pdf extends DomPDF {
    public function __construct() {
        parent::__construct();
    }
}
