<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_basic extends CI_Controller
{
    public function index()
    {
        echo "<h1>✅ CodeIgniter is working!</h1>";
        echo "<p>PHP Version: " . phpversion() . "</p>";
        echo "<p>CodeIgniter loaded successfully!</p>";
        echo "<p>Base URL: " . base_url() . "</p>";
    }
}
?> 