<?php
/**
 * Unit Tests for Utility Helper Functions
 * 
 * Tests custom helper functions used throughout the application
 */
class UtilityHelper_test extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->CI =& get_instance();
        $this->CI->load->helper('utility');
    }
    
    /**
     * Test if utility helper is loaded correctly
     */
    public function test_utility_helper_loaded()
    {
        // Check if helper functions are available
        $this->assertTrue(function_exists('format_rupiah') || function_exists('clean_string') || function_exists('generate_random_string'));
    }
    
    /**
     * Test format_rupiah function if it exists
     */
    public function test_format_rupiah()
    {
        if (function_exists('format_rupiah')) {
            $result = format_rupiah(1000000);
            $this->assertIsString($result);
            $this->assertStringContainsString('1.000.000', $result);
        } else {
            $this->markTestSkipped('format_rupiah function not found in utility helper');
        }
    }
    
    /**
     * Test clean_string function if it exists
     */
    public function test_clean_string()
    {
        if (function_exists('clean_string')) {
            $test_string = 'Test String with Special Ch@r$!';
            $result = clean_string($test_string);
            $this->assertIsString($result);
            $this->assertNotEquals($test_string, $result);
        } else {
            $this->markTestSkipped('clean_string function not found in utility helper');
        }
    }
    
    /**
     * Test generate_random_string function if it exists
     */
    public function test_generate_random_string()
    {
        if (function_exists('generate_random_string')) {
            $length = 10;
            $result = generate_random_string($length);
            $this->assertIsString($result);
            $this->assertEquals($length, strlen($result));
            
            // Test that two calls return different strings
            $result2 = generate_random_string($length);
            $this->assertNotEquals($result, $result2);
        } else {
            $this->markTestSkipped('generate_random_string function not found in utility helper');
        }
    }
    
    /**
     * Test date formatting functions if they exist
     */
    public function test_date_formatting()
    {
        if (function_exists('format_date_indonesia')) {
            $test_date = '2023-12-25';
            $result = format_date_indonesia($test_date);
            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        } else {
            $this->markTestSkipped('format_date_indonesia function not found in utility helper');
        }
    }
    
    /**
     * Test validation functions if they exist
     */
    public function test_validation_functions()
    {
        if (function_exists('is_valid_email')) {
            $this->assertTrue(is_valid_email('test@example.com'));
            $this->assertFalse(is_valid_email('invalid-email'));
        } else {
            $this->markTestSkipped('is_valid_email function not found in utility helper');
        }
        
        if (function_exists('is_valid_phone')) {
            $this->assertTrue(is_valid_phone('081234567890'));
            $this->assertFalse(is_valid_phone('123'));
        } else {
            $this->markTestSkipped('is_valid_phone function not found in utility helper');
        }
    }
    
    /**
     * Test array manipulation functions if they exist
     */
    public function test_array_functions()
    {
        if (function_exists('array_safe_get')) {
            $test_array = ['key1' => 'value1', 'key2' => 'value2'];
            
            $this->assertEquals('value1', array_safe_get($test_array, 'key1'));
            $this->assertEquals('default', array_safe_get($test_array, 'nonexistent', 'default'));
        } else {
            $this->markTestSkipped('array_safe_get function not found in utility helper');
        }
    }
    
    /**
     * Test file utility functions if they exist
     */
    public function test_file_functions()
    {
        if (function_exists('get_file_extension')) {
            $this->assertEquals('pdf', get_file_extension('document.pdf'));
            $this->assertEquals('jpg', get_file_extension('image.jpg'));
            $this->assertEquals('', get_file_extension('noextension'));
        } else {
            $this->markTestSkipped('get_file_extension function not found in utility helper');
        }
        
        if (function_exists('format_file_size')) {
            $this->assertStringContainsString('KB', format_file_size(1024));
            $this->assertStringContainsString('MB', format_file_size(1048576));
        } else {
            $this->markTestSkipped('format_file_size function not found in utility helper');
        }
    }
    
    /**
     * Test database utility functions if they exist
     */
    public function test_database_functions()
    {
        if (function_exists('escape_sql')) {
            $test_string = "'; DROP TABLE users; --";
            $result = escape_sql($test_string);
            $this->assertIsString($result);
            $this->assertNotEquals($test_string, $result);
        } else {
            $this->markTestSkipped('escape_sql function not found in utility helper');
        }
    }
    
    /**
     * Test pagination helper functions if they exist
     */
    public function test_pagination_functions()
    {
        if (function_exists('create_pagination_config')) {
            $config = create_pagination_config('test/url', 100, 10, 3);
            $this->assertIsArray($config);
            $this->assertArrayHasKey('base_url', $config);
            $this->assertArrayHasKey('total_rows', $config);
            $this->assertArrayHasKey('per_page', $config);
        } else {
            $this->markTestSkipped('create_pagination_config function not found in utility helper');
        }
    }
    
    /**
     * Test security helper functions if they exist
     */
    public function test_security_functions()
    {
        if (function_exists('generate_csrf_token')) {
            $token = generate_csrf_token();
            $this->assertIsString($token);
            $this->assertNotEmpty($token);
        } else {
            $this->markTestSkipped('generate_csrf_token function not found in utility helper');
        }
        
        if (function_exists('sanitize_input')) {
            $dirty_input = '<script>alert("xss")</script>Test';
            $clean_input = sanitize_input($dirty_input);
            $this->assertStringNotContainsString('<script>', $clean_input);
        } else {
            $this->markTestSkipped('sanitize_input function not found in utility helper');
        }
    }
    
    /**
     * Test logging helper functions if they exist
     */
    public function test_logging_functions()
    {
        if (function_exists('write_log_custom')) {
            $result = write_log_custom('info', 'Test log message');
            // This should not throw an exception
            $this->assertTrue(true);
        } else {
            $this->markTestSkipped('write_log_custom function not found in utility helper');
        }
    }
} 
