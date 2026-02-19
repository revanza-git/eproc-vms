<?php
/**
 * Unit Tests for Auction Controller
 * 
 * Tests auction management functionality, authentication requirements,
 * and CRUD operations for auction data
 */
class AuctionController_test extends TestCase
{
    protected static $prepared = false;
    
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        if (!self::$prepared) {
            // Setup test tables
            self::setupTestTables();
            self::setupTestData();
            self::$prepared = true;
        }
    }
    
    public function setUp(): void
    {
        parent::setUp();
        $this->CI =& get_instance();
        
        // Reset session and request data before each test
        TestUtilities::clearMockSession();
        TestUtilities::clearMockData();
        
        // Mock admin session by default for most tests
        TestUtilities::createMockSession([
            'admin' => [
                'id' => 1,
                'id_role' => 1,
                'username' => 'testadmin',
                'name' => 'Test Admin'
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
        // Create all necessary tables for auction testing
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
        
        TestUtilities::createTestTable('ms_procurement_peserta', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'id_proc' => ['type' => 'INT'],
            'id_vendor' => ['type' => 'INT'],
            'is_winner' => ['type' => 'INT', 'default' => 0],
            'surat' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'id_surat' => ['type' => 'INT', 'default' => 0],
            'entry_stamp' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
        ]);
        
        TestUtilities::createTestTable('ms_vendor', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE],
            'vendor_status' => ['type' => 'INT', 'default' => 0],
            'del' => ['type' => 'INT', 'default' => 0],
            'entry_stamp' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
        ]);
        
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
        
        TestUtilities::createTestTable('ms_procurement_tatacara', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'id_procurement' => ['type' => 'INT'],
            'metode_auction' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'metode_penawaran' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'entry_stamp' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
        ]);
        
        TestUtilities::createTestTable('ms_procurement_persyaratan', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'id_proc' => ['type' => 'INT'],
            'description' => ['type' => 'TEXT', 'null' => TRUE],
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
                'auction_type' => 'lelang',
                'work_area' => 'Jakarta',
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
                'auction_type' => 'langsung',
                'work_area' => 'Bandung',
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
                'auction_type' => 'lelang',
                'work_area' => 'Surabaya',
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
    }
    
    /**
     * Test that unauthenticated users are redirected
     */
    public function test_unauthenticated_access_redirects()
    {
        // Clear session to simulate unauthenticated user
        TestUtilities::clearMockSession();
        
        $output = $this->request('GET', 'auction/index');
        
        // Should redirect to login
        $this->assertRedirect('/', 302);
    }
    
    /**
     * Test index method shows auction list
     */
    public function test_index_shows_auction_list()
    {
        $output = $this->request('GET', 'auction/index');
        
        // Should return 200 status
        $this->assertResponseCode(200);
    }
    
    /**
     * Test langsung method shows ongoing auctions
     */
    public function test_langsung_shows_ongoing_auctions()
    {
        $output = $this->request('GET', 'auction/langsung');
        
        // Should return 200 status
        $this->assertResponseCode(200);
    }
    
    /**
     * Test selesai method shows finished auctions
     */
    public function test_selesai_shows_finished_auctions()
    {
        $output = $this->request('GET', 'auction/selesai');
        
        // Should return 200 status
        $this->assertResponseCode(200);
    }
    
    /**
     * Test tambah (add) method with valid data
     */
    public function test_tambah_with_valid_data()
    {
        // Mock POST data
        TestUtilities::mockPostData([
            'simpan' => '1',
            'name' => 'New Test Procurement',
            'budget_source' => 'APBN',
            'id_pejabat_pengadaan' => '1',
            'auction_type' => 'lelang',
            'work_area' => 'Jakarta',
            'room' => 'Room A',
            'auction_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'auction_duration' => '60',
            'budget_holder' => 'Test Budget Holder',
            'budget_spender' => 'Test Budget Spender',
            'id_mekanisme' => '1'
        ]);
        
        $output = $this->request('POST', 'auction/tambah');
        
        // Should redirect after successful save
        $this->assertRedirect();
        
        // Verify data was saved
        $saved_count = TestUtilities::getTableRowCount('ms_procurement', [
            'name' => 'New Test Procurement'
        ]);
        $this->assertEquals(1, $saved_count);
    }
    
    /**
     * Test tambah method with missing required fields
     */
    public function test_tambah_with_missing_required_fields()
    {
        // Mock POST data with missing required field
        TestUtilities::mockPostData([
            'simpan' => '1',
            'budget_source' => 'APBN'
            // Missing 'name' field which is required
        ]);
        
        $output = $this->request('POST', 'auction/tambah');
        
        // Should show validation errors (return 200 to show form again)
        $this->assertResponseCode(200);
    }
    
    /**
     * Test edit method with valid data
     */
    public function test_edit_with_valid_data()
    {
        // Mock POST data for editing
        TestUtilities::mockPostData([
            'simpan' => '1',
            'name' => 'Updated Test Procurement',
            'budget_source' => 'APBD',
            'id_pejabat_pengadaan' => '1',
            'auction_type' => 'langsung',
            'work_area' => 'Updated Location',
            'id_mekanisme' => '1'
        ]);
        
        $output = $this->request('POST', 'auction/edit/1');
        
        // Should redirect after successful update
        $this->assertRedirect();
        
        // Verify data was updated
        $CI =& get_instance();
        $updated_auction = $CI->db->get_where('ms_procurement', ['id' => 1])->row_array();
        $this->assertEquals('Updated Test Procurement', $updated_auction['name']);
        $this->assertEquals('APBD', $updated_auction['budget_source']);
    }
    
    /**
     * Test hapus (delete) method
     */
    public function test_hapus_soft_deletes_auction()
    {
        $output = $this->request('GET', 'auction/hapus/1');
        
        // Should redirect after deletion
        $this->assertRedirect();
        
        // Verify soft delete (del = 1)
        $CI =& get_instance();
        $deleted_auction = $CI->db->get_where('ms_procurement', ['id' => 1])->row_array();
        $this->assertEquals(1, $deleted_auction['del']);
    }
    
    /**
     * Test ubah (modify) method shows auction details
     */
    public function test_ubah_shows_auction_details()
    {
        $output = $this->request('GET', 'auction/ubah/1/tatacara');
        
        // Should return 200 status
        $this->assertResponseCode(200);
    }
    
    /**
     * Test tambah_tatacara method
     */
    public function test_tambah_tatacara()
    {
        // Mock POST data for tatacara
        TestUtilities::mockPostData([
            'simpan' => '1',
            'metode_auction' => 'Open Bidding',
            'metode_penawaran' => 'Sealed Bid'
        ]);
        
        $output = $this->request('POST', 'auction/tambah_tatacara/1/tatacara');
        
        // Should redirect after successful save
        $this->assertRedirect();
        
        // Verify data was saved
        $saved_count = TestUtilities::getTableRowCount('ms_procurement_tatacara', [
            'id_procurement' => 1
        ]);
        $this->assertEquals(1, $saved_count);
    }
    
    /**
     * Test tambah_barang method
     */
    public function test_tambah_barang()
    {
        // Mock POST data for barang
        TestUtilities::mockPostData([
            'simpan' => '1',
            'nama_barang' => 'Test Item',
            'volume' => '10',
            'nilai_hps' => '100000',
            'id_kurs' => '1',
            'is_catalogue' => '0'
        ]);
        
        $output = $this->request('POST', 'auction/tambah_barang/1');
        
        // Should redirect after successful save
        $this->assertRedirect();
        
        // Verify data was saved
        $saved_count = TestUtilities::getTableRowCount('ms_procurement_barang', [
            'id_procurement' => 1,
            'nama_barang' => 'Test Item'
        ]);
        $this->assertEquals(1, $saved_count);
    }
    
    /**
     * Test tambah_peserta method
     */
    public function test_tambah_peserta()
    {
        $output = $this->request('GET', 'auction/tambah_peserta/1');
        
        // Should return 200 status (show form)
        $this->assertResponseCode(200);
    }
    
    /**
     * Test add_peserta method
     */
    public function test_add_peserta()
    {
        $output = $this->request('GET', 'auction/add_peserta/1/1');
        
        // Should redirect after adding peserta
        $this->assertRedirect();
        
        // Verify peserta was added
        $saved_count = TestUtilities::getTableRowCount('ms_procurement_peserta', [
            'id_proc' => 1,
            'id_vendor' => 1
        ]);
        $this->assertEquals(1, $saved_count);
    }
    
    /**
     * Test hapus_peserta method
     */
    public function test_hapus_peserta()
    {
        // First add a peserta
        TestUtilities::insertTestData('ms_procurement_peserta', [
            'id' => 1,
            'id_proc' => 1,
            'id_vendor' => 1
        ]);
        
        $output = $this->request('GET', 'auction/hapus_peserta/1/1');
        
        // Should redirect after deletion
        $this->assertRedirect();
        
        // Verify peserta was removed
        $count = TestUtilities::getTableRowCount('ms_procurement_peserta', [
            'id' => 1
        ]);
        $this->assertEquals(0, $count);
    }
    
    /**
     * Test tambah_persyaratan method
     */
    public function test_tambah_persyaratan()
    {
        // Mock POST data for persyaratan
        TestUtilities::mockPostData([
            'simpan' => '1',
            'description' => 'Test requirement description'
        ]);
        
        $output = $this->request('POST', 'auction/tambah_persyaratan/1/persyaratan');
        
        // Should redirect after successful save
        $this->assertRedirect();
        
        // Verify data was saved
        $saved_count = TestUtilities::getTableRowCount('ms_procurement_persyaratan', [
            'id_proc' => 1,
            'description' => 'Test requirement description'
        ]);
        $this->assertEquals(1, $saved_count);
    }
    
    /**
     * Test autocomplete functionality
     */
    public function test_autocomplete()
    {
        // Mock POST data for autocomplete
        TestUtilities::mockPostData([
            'term' => 'Test'
        ]);
        
        $output = $this->request('POST', 'auction/autocomplete');
        
        // Should return 200 status
        $this->assertResponseCode(200);
    }
    
    /**
     * Test remark functionality
     */
    public function test_remark()
    {
        // Mock POST data for remark
        TestUtilities::mockPostData([
            'simpan' => '1',
            'remark' => 'Test remark for auction'
        ]);
        
        $output = $this->request('POST', 'auction/remark/1');
        
        // Should redirect after successful save
        $this->assertRedirect();
    }
    
    /**
     * Test duplikat (duplicate) functionality
     */
    public function test_duplikat()
    {
        // Mock POST data for duplication
        TestUtilities::mockPostData([
            'simpan' => '1',
            'total' => '2'
        ]);
        
        $output = $this->request('POST', 'auction/duplikat/1');
        
        // Should redirect after successful duplication
        $this->assertRedirect();
    }
    
    /**
     * Test GET request to tambah shows form
     */
    public function test_tambah_get_shows_form()
    {
        $output = $this->request('GET', 'auction/tambah');
        
        // Should return 200 status to show form
        $this->assertResponseCode(200);
    }
    
    /**
     * Test GET request to edit shows form with data
     */
    public function test_edit_get_shows_form_with_data()
    {
        $output = $this->request('GET', 'auction/edit/1');
        
        // Should return 200 status to show form
        $this->assertResponseCode(200);
    }
    
    /**
     * Test pagination and filtering
     */
    public function test_index_with_pagination_and_filters()
    {
        // Mock GET parameters for pagination and search
        TestUtilities::mockGetData([
            'q' => 'Test',
            'per_page' => '2',
            'sort' => 'asc',
            'by' => 'name'
        ]);
        
        $output = $this->request('GET', 'auction/index');
        
        // Should return 200 status
        $this->assertResponseCode(200);
    }
    
    /**
     * Test different auction statuses
     */
    public function test_different_auction_statuses()
    {
        // Test index (all auctions)
        $output = $this->request('GET', 'auction/index');
        $this->assertResponseCode(200);
        
        // Test langsung (ongoing)
        $output = $this->request('GET', 'auction/langsung');
        $this->assertResponseCode(200);
        
        // Test selesai (finished)
        $output = $this->request('GET', 'auction/selesai');
        $this->assertResponseCode(200);
    }
    
    /**
     * Test auction navigation tabs
     */
    public function test_auction_navigation_tabs()
    {
        // Test different tab pages
        $tabs = ['tatacara', 'peserta', 'kurs', 'persyaratan', 'barang', 'remark'];
        
        foreach ($tabs as $tab) {
            $output = $this->request('GET', "auction/ubah/1/{$tab}");
            $this->assertResponseCode(200);
        }
    }
    
    /**
     * Test role-based access restrictions
     */
    public function test_role_based_access()
    {
        // Test with different admin roles
        TestUtilities::clearMockSession();
        TestUtilities::createMockSession([
            'admin' => [
                'id' => 1,
                'id_role' => 4, // Different role
                'username' => 'limitedadmin',
                'name' => 'Limited Admin'
            ]
        ]);
        
        $output = $this->request('GET', 'auction/index');
        
        // Should still work for role 4 (procurement admin)
        $this->assertResponseCode(200);
    }
} 
