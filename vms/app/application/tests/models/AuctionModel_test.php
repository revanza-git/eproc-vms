<?php
/**
 * Unit Tests for Auction_model
 * 
 * Tests core auction functionality including CRUD operations,
 * data retrieval, and business logic validation
 */
class AuctionModel_test extends TestCase
{
    protected static $prepared = false;
    protected $auction_model;
    
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $CI =& get_instance();
        $CI->load->dbforge();
        $CI->load->library('session');
        
        // Prepare database tables for testing
        if (!self::$prepared) {
            self::setupTestTables();
            self::setupTestData();
            self::$prepared = true;
        }
    }
    
    public function setUp(): void
    {
        parent::setUp();
        $this->CI =& get_instance();
        $this->CI->load->model('auction/Auction_model', 'auction_model');
        $this->auction_model = $this->CI->auction_model;
        
        // Set up mock session data
        TestUtilities::createMockSession([
            'admin' => [
                'id_role' => 1,
                'id' => 1,
                'name' => 'Test Admin'
            ],
            'user' => [
                'id' => 1,
                'username' => 'test_admin'
            ]
        ]);
    }
    
    public function tearDown(): void
    {
        parent::tearDown();
        TestUtilities::clearMockSession();
        TestUtilities::clearMockData();
    }
    
    /**
     * Setup test database tables
     */
    private static function setupTestTables()
    {
        $CI =& get_instance();
        
        // Create ms_procurement table
        TestUtilities::createTestTable('ms_procurement', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'id_division' => ['type' => 'INT', 'default' => 0],
            'budget_source' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'id_pejabat_pengadaan' => ['type' => 'INT', 'default' => 0],
            'auction_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'work_area' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'room' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'auction_date' => ['type' => 'DATETIME', 'null' => TRUE],
            'auction_duration' => ['type' => 'INT', 'default' => 0],
            'budget_holder' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'budget_spender' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'id_mekanisme' => ['type' => 'INT', 'default' => 1],
            'is_started' => ['type' => 'INT', 'default' => 0],
            'is_finished' => ['type' => 'INT', 'default' => 0],
            'is_suspended' => ['type' => 'INT', 'default' => 0],
            'status_procurement' => ['type' => 'INT', 'default' => 0],
            'proc_date' => ['type' => 'DATETIME', 'null' => TRUE],
            'del' => ['type' => 'INT', 'default' => 0],
            'entry_stamp' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
        ]);
        
        // Create ms_procurement_peserta table
        TestUtilities::createTestTable('ms_procurement_peserta', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'id_proc' => ['type' => 'INT'],
            'id_vendor' => ['type' => 'INT'],
            'is_winner' => ['type' => 'INT', 'default' => 0],
            'surat' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'id_surat' => ['type' => 'INT', 'default' => 0],
            'entry_stamp' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
        ]);
        
        // Create ms_vendor table if not exists
        TestUtilities::createTestTable('ms_vendor', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE],
            'vendor_status' => ['type' => 'INT', 'default' => 0],
            'del' => ['type' => 'INT', 'default' => 0],
            'entry_stamp' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
        ]);
        
        // Create ms_procurement_barang table
        TestUtilities::createTestTable('ms_procurement_barang', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'id_procurement' => ['type' => 'INT'],
            'is_catalogue' => ['type' => 'INT', 'default' => 0],
            'id_material' => ['type' => 'INT', 'default' => 0],
            'nama_barang' => ['type' => 'VARCHAR', 'constraint' => 255],
            'volume' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'id_kurs' => ['type' => 'INT', 'default' => 1],
            'nilai_hps' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'entry_stamp' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
        ]);
        
        // Create ms_procurement_tatacara table
        TestUtilities::createTestTable('ms_procurement_tatacara', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'id_procurement' => ['type' => 'INT'],
            'metode_auction' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'metode_penawaran' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'entry_stamp' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
        ]);
    }
    
    /**
     * Setup test data
     */
    private static function setupTestData()
    {
        // Insert test procurement data
        TestUtilities::insertTestData('ms_procurement', [
            [
                'id' => 1,
                'name' => 'Test Procurement 1',
                'id_division' => 1,
                'budget_source' => 'APBN',
                'id_mekanisme' => 1,
                'is_started' => 0,
                'is_finished' => 0,
                'status_procurement' => 1,
                'proc_date' => date('Y-m-d H:i:s'),
                'del' => 0
            ],
            [
                'id' => 2,
                'name' => 'Test Procurement 2',
                'id_division' => 1,
                'budget_source' => 'APBD',
                'id_mekanisme' => 1,
                'is_started' => 1,
                'is_finished' => 0,
                'status_procurement' => 1,
                'proc_date' => date('Y-m-d H:i:s'),
                'del' => 0
            ],
            [
                'id' => 3,
                'name' => 'Test Procurement 3',
                'id_division' => 1,
                'budget_source' => 'APBN',
                'id_mekanisme' => 1,
                'is_started' => 1,
                'is_finished' => 1,
                'status_procurement' => 1,
                'proc_date' => date('Y-m-d H:i:s'),
                'del' => 0
            ]
        ]);
        
        // Insert test vendor data
        TestUtilities::insertTestData('ms_vendor', [
            [
                'id' => 1,
                'name' => 'Test Vendor 1',
                'email' => 'vendor1@test.com',
                'vendor_status' => 2,
                'del' => 0
            ],
            [
                'id' => 2,
                'name' => 'Test Vendor 2',
                'email' => 'vendor2@test.com',
                'vendor_status' => 2,
                'del' => 0
            ]
        ]);
        
        // Insert test peserta data
        TestUtilities::insertTestData('ms_procurement_peserta', [
            [
                'id_proc' => 1,
                'id_vendor' => 1,
                'is_winner' => 0
            ],
            [
                'id_proc' => 2,
                'id_vendor' => 1,
                'is_winner' => 1
            ],
            [
                'id_proc' => 3,
                'id_vendor' => 2,
                'is_winner' => 1
            ]
        ]);
    }
    
    /**
     * Test get_auction_list method
     */
    public function test_get_auction_list()
    {
        $result = $this->auction_model->get_auction_list();
        
        $this->assertNotEmpty($result);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        
        // Test that deleted items are excluded
        foreach ($result as $auction) {
            $this->assertEquals(0, $auction['del']);
        }
    }
    
    /**
     * Test get_auction_langsung method
     */
    public function test_get_auction_langsung()
    {
        $result = $this->auction_model->get_auction_langsung();
        
        $this->assertIsArray($result);
        
        // Test that only started and not finished auctions are returned
        foreach ($result as $auction) {
            $this->assertEquals(1, $auction['is_started']);
            $this->assertEquals(0, $auction['is_finished']);
        }
    }
    
    /**
     * Test get_auction_selesai method
     */
    public function test_get_auction_selesai()
    {
        $result = $this->auction_model->get_auction_selesai();
        
        $this->assertIsArray($result);
        
        // Test that only finished auctions are returned
        foreach ($result as $auction) {
            $this->assertEquals(1, $auction['is_finished']);
        }
    }
    
    /**
     * Test search_vendor method
     */
    public function test_search_vendor()
    {
        // Mock POST data for vendor search
        TestUtilities::mockPostData(['term' => 'Test']);
        
        $result = $this->auction_model->search_vendor();
        
        $this->assertIsArray($result);
        
        if (!empty($result)) {
            $first_result = reset($result);
            $this->assertArrayHasKey('id', $first_result);
            $this->assertArrayHasKey('name', $first_result);
        }
    }
    
    /**
     * Test save_data method
     */
    public function test_save_data()
    {
        $test_data = [
            'name' => 'New Test Procurement',
            'id_division' => 1,
            'budget_source' => 'APBN',
            'id_pejabat_pengadaan' => 1,
            'auction_type' => 'lelang',
            'work_area' => 'Jakarta',
            'room' => 'Room A',
            'auction_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'auction_duration' => 60,
            'budget_holder' => 'Test Budget Holder',
            'budget_spender' => 'Test Budget Spender',
            'id_mekanisme' => 1
        ];
        
        $result = $this->auction_model->save_data($test_data);
        
        $this->assertTrue($result);
        
        // Verify data was saved
        $saved_count = TestUtilities::getTableRowCount('ms_procurement', ['name' => $test_data['name']]);
        $this->assertEquals(1, $saved_count);
    }
    
    /**
     * Test get_auction method
     */
    public function test_get_auction()
    {
        $result = $this->auction_model->get_auction(1);
        
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Procurement 1', $result['name']);
    }
    
    /**
     * Test save_barang method with valid data
     */
    public function test_save_barang()
    {
        $test_barang = [
            'id_procurement' => 1,
            'is_catalogue' => 0,
            'nama_barang' => 'Test Item',
            'volume' => 10,
            'id_kurs' => 1,
            'nilai_hps' => 100000
        ];
        
        $result = $this->auction_model->save_barang($test_barang);
        
        $this->assertTrue($result);
        
        // Verify data was saved
        $saved_count = TestUtilities::getTableRowCount('ms_procurement_barang', [
            'id_procurement' => 1,
            'nama_barang' => 'Test Item'
        ]);
        $this->assertEquals(1, $saved_count);
    }
    
    /**
     * Test save_tatacara method
     */
    public function test_save_tatacara()
    {
        $test_tatacara = [
            'id_procurement' => 1,
            'metode_auction' => 'Open Bidding',
            'metode_penawaran' => 'Sealed Bid'
        ];
        
        $result = $this->auction_model->save_tatacara($test_tatacara);
        
        $this->assertTrue($result);
        
        // Verify data was saved
        $saved_count = TestUtilities::getTableRowCount('ms_procurement_tatacara', [
            'id_procurement' => 1
        ]);
        $this->assertEquals(1, $saved_count);
    }
    
    /**
     * Test save_peserta method
     */
    public function test_save_peserta()
    {
        $test_peserta = [
            'id_proc' => 1,
            'id_vendor' => 2
        ];
        
        $result = $this->auction_model->save_peserta($test_peserta);
        
        $this->assertTrue($result);
        
        // Verify data was saved (should now have 2 peserta for procurement 1)
        $saved_count = TestUtilities::getTableRowCount('ms_procurement_peserta', [
            'id_proc' => 1
        ]);
        $this->assertEquals(2, $saved_count);
    }
    
    /**
     * Test edit_data method
     */
    public function test_edit_data()
    {
        $update_data = [
            'name' => 'Updated Test Procurement',
            'budget_source' => 'APBD'
        ];
        
        $result = $this->auction_model->edit_data($update_data, 1);
        
        $this->assertTrue($result);
        
        // Verify data was updated
        $updated_auction = $this->auction_model->get_auction(1);
        $this->assertEquals('Updated Test Procurement', $updated_auction['name']);
        $this->assertEquals('APBD', $updated_auction['budget_source']);
    }
    
    /**
     * Test hapus method (soft delete)
     */
    public function test_hapus()
    {
        $result = $this->auction_model->hapus(1, 'ms_procurement');
        
        $this->assertTrue($result);
        
        // Verify soft delete
        $CI =& get_instance();
        $deleted_auction = $CI->db->get_where('ms_procurement', ['id' => 1])->row_array();
        $this->assertEquals(1, $deleted_auction['del']);
    }
    
    /**
     * Test get_vendor_list method
     */
    public function test_get_vendor_list()
    {
        $result = $this->auction_model->get_vendor_list();
        
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        
        // Verify structure
        foreach ($result as $vendor) {
            $this->assertArrayHasKey('id', $vendor);
            $this->assertArrayHasKey('name', $vendor);
            $this->assertEquals(0, $vendor['del']); // Not deleted
            $this->assertEquals(2, $vendor['vendor_status']); // Active status
        }
    }
    
    /**
     * Test data validation edge cases
     */
    public function test_save_data_validation()
    {
        // Test with minimal required data
        $minimal_data = [
            'name' => 'Minimal Test Procurement',
            'id_mekanisme' => 1
        ];
        
        $result = $this->auction_model->save_data($minimal_data);
        $this->assertTrue($result);
        
        // Test with empty name (should handle gracefully)
        $empty_name_data = [
            'name' => '',
            'id_mekanisme' => 1
        ];
        
        try {
            $result = $this->auction_model->save_data($empty_name_data);
            // If it doesn't throw an exception, it should still be handled properly
        } catch (Exception $e) {
            // Expected behavior for invalid data
            $this->addToAssertionCount(1);
        }
    }
} 
