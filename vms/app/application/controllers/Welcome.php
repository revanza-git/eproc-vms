<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index()
    {
        echo "<h1>Welcome Controller Test</h1>";
        echo "<p>✅ Basic CodeIgniter is working!</p>";
        echo "<p>✅ Controllers can be loaded!</p>";
        echo "<p>✅ This bypasses the HMVC system</p>";
        
        echo "<h2>Session Test:</h2>";
        $this->load->library('session');
        echo "<p>✅ Session library loaded</p>";
        
        echo "<h2>Database Test:</h2>";
        try {
            $this->load->database();
            echo "<p>✅ Database connected</p>";
        } catch (Exception $e) {
            echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
        }
        
        echo "<h2>Next Step:</h2>";
        echo "<p>If you see this, CodeIgniter works fine. The issue is with HMVC.</p>";
        echo "<p><a href='index.php/welcome'>Test this controller again</a></p>";
    }
}
?> 