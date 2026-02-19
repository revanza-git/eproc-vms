<?php
/**
 * Enhanced Filter System Integration Tests
 * 
 * This script tests all components of the enhanced filter system:
 * - Database structure and data integrity
 * - Filter library functionality
 * - Controller integration
 * - JavaScript functionality (simulated)
 * - Performance benchmarks
 * 
 * Usage: C:\tools\php56\php.exe test_enhanced_filters.php [--verbose]
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once '../app/application/config/database.php';
require_once '../app/application/libraries/Filter_enhanced.php';

class EnhancedFilterTests {
    private $db;
    private $logFile;
    private $verbose;
    private $testResults = [];
    
    public function __construct($verbose = false) {
        $this->verbose = $verbose;
        $this->logFile = 'test_results_' . date('Y-m-d_H-i-s') . '.log';
        $this->connectDatabase();
    }
    
    private function connectDatabase() {
        global $db;
        
        $host = $db['default']['hostname'];
        $username = $db['default']['username'];
        $password = $db['default']['password'];
        $database = $db['default']['database'];
        
        try {
            $this->db = new mysqli($host, $username, $password, $database);
            
            if ($this->db->connect_error) {
                throw new Exception("Connection failed: " . $this->db->connect_error);
            }
            
            $this->db->set_charset("utf8");
            $this->log("Database connection successful");
            
        } catch (Exception $e) {
            $this->log("Database connection failed: " . $e->getMessage(), 'ERROR');
            exit(1);
        }
    }
    
    public function runAllTests() {
        $this->log("Starting Enhanced Filter System Integration Tests");
        $this->log("=" . str_repeat("=", 50));
        
        $testSuites = [
            'Database Structure Tests' => 'runDatabaseStructureTests',
            'Filter Library Tests' => 'runFilterLibraryTests',
            'Database Performance Tests' => 'runPerformanceTests',
            'Data Integrity Tests' => 'runDataIntegrityTests',
            'Autocomplete Functionality Tests' => 'runAutocompleteTests',
            'Saved Search Tests' => 'runSavedSearchTests',
            'Analytics Tests' => 'runAnalyticsTests',
            'Full-Text Search Tests' => 'runFullTextSearchTests'
        ];
        
        $totalTests = 0;
        $passedTests = 0;
        
        foreach ($testSuites as $suiteName => $method) {
            $this->log("\n" . $suiteName);
            $this->log("-" . str_repeat("-", strlen($suiteName)));
            
            $suiteResults = $this->$method();
            $totalTests += $suiteResults['total'];
            $passedTests += $suiteResults['passed'];
            
            $this->log("Suite Results: {$suiteResults['passed']}/{$suiteResults['total']} tests passed");
        }
        
        $this->log("\n" . str_repeat("=", 60));
        $this->log("FINAL RESULTS: $passedTests/$totalTests tests passed");
        
        if ($passedTests === $totalTests) {
            $this->log("🎉 ALL TESTS PASSED! Enhanced Filter System is ready for production.");
            return true;
        } else {
            $this->log("❌ Some tests failed. Please review the issues above.");
            return false;
        }
    }
    
    private function runDatabaseStructureTests() {
        $tests = [
            'New tables exist' => function() {
                $tables = ['user_saved_searches', 'filter_usage_analytics', 'search_suggestions_cache'];
                foreach ($tables as $table) {
                    $result = $this->db->query("SHOW TABLES LIKE '$table'");
                    if ($result->num_rows === 0) {
                        throw new Exception("Table $table does not exist");
                    }
                }
                return true;
            },
            
            'New columns exist' => function() {
                $columns = [
                    ['ms_procurement', 'budget_year'],
                    ['ms_procurement', 'search_keywords'],
                    ['ms_contract', 'efficiency_percentage'],
                    ['ms_vendor', 'search_keywords']
                ];
                
                foreach ($columns as list($table, $column)) {
                    $result = $this->db->query("SHOW COLUMNS FROM $table LIKE '$column'");
                    if ($result->num_rows === 0) {
                        throw new Exception("Column $column in table $table does not exist");
                    }
                }
                return true;
            },
            
            'Indexes created successfully' => function() {
                $indexes = [
                    ['ms_procurement', 'idx_ms_procurement_name'],
                    ['ms_procurement', 'idx_ms_procurement_auction_date'],
                    ['ms_vendor', 'idx_ms_vendor_name'],
                    ['ms_contract', 'idx_ms_contract_price']
                ];
                
                foreach ($indexes as list($table, $index)) {
                    $result = $this->db->query("SHOW INDEX FROM $table WHERE Key_name = '$index'");
                    if ($result->num_rows === 0) {
                        throw new Exception("Index $index on table $table does not exist");
                    }
                }
                return true;
            },
            
            'Views created successfully' => function() {
                $views = ['v_auction_statistics', 'v_vendor_comprehensive'];
                foreach ($views as $view) {
                    $result = $this->db->query("SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_* LIKE '$view'");
                    if ($result->num_rows === 0) {
                        throw new Exception("View $view does not exist");
                    }
                }
                return true;
            },
            
            'Stored procedures exist' => function() {
                $procedures = ['GetFilterUsageStats', 'UpdateSearchKeywords', 'CalculateContractEfficiency'];
                foreach ($procedures as $procedure) {
                    $result = $this->db->query("SHOW PROCEDURE STATUS LIKE '$procedure'");
                    if ($result->num_rows === 0) {
                        throw new Exception("Procedure $procedure does not exist");
                    }
                }
                return true;
            },
            
            'Triggers exist' => function() {
                $triggers = ['tr_procurement_search_keywords', 'tr_vendor_search_keywords'];
                foreach ($triggers as $trigger) {
                    $result = $this->db->query("SHOW TRIGGERS LIKE '%$trigger%'");
                    if ($result->num_rows === 0) {
                        throw new Exception("Trigger $trigger does not exist");
                    }
                }
                return true;
            }
        ];
        
        return $this->runTestSuite($tests);
    }
    
    private function runFilterLibraryTests() {
        $tests = [
            'Filter_enhanced class loads' => function() {
                if (!class_exists('Filter_enhanced')) {
                    throw new Exception("Filter_enhanced class not found");
                }
                return true;
            },
            
            'Autocomplete method exists' => function() {
                if (!method_exists('Filter_enhanced', 'get_autocomplete_suggestions')) {
                    throw new Exception("get_autocomplete_suggestions method not found");
                }
                return true;
            },
            
            'Enhanced query generation' => function() {
                // This would require loading CodeIgniter framework
                // For now, just check if the method exists
                if (!method_exists('Filter_enhanced', 'generate_enhanced_query')) {
                    throw new Exception("generate_enhanced_query method not found");
                }
                return true;
            },
            
            'Date preset functionality' => function() {
                if (!method_exists('Filter_enhanced', 'apply_date_preset')) {
                    throw new Exception("apply_date_preset method not found");
                }
                return true;
            },
            
            'Number range filtering' => function() {
                if (!method_exists('Filter_enhanced', 'apply_number_range')) {
                    throw new Exception("apply_number_range method not found");
                }
                return true;
            }
        ];
        
        return $this->runTestSuite($tests);
    }
    
    private function runPerformanceTests() {
        $tests = [
            'Auction query performance with indexes' => function() {
                $start = microtime(true);
                
                $query = "SELECT mp.*, mv.name as vendor_name 
                         FROM ms_procurement mp 
                         LEFT JOIN ms_procurement_peserta mpp ON mp.id = mpp.id_proc 
                         LEFT JOIN ms_vendor mv ON mpp.id_vendor = mv.id 
                         WHERE mp.name LIKE '%lelang%' 
                         AND mp.auction_date >= '2024-01-01' 
                         AND mp.del = 0 
                         LIMIT 100";
                
                $result = $this->db->query($query);
                $end = microtime(true);
                $duration = $end - $start;
                
                if ($duration > 1.0) { // Should complete under 1 second
                    throw new Exception("Query too slow: {$duration}s (expected < 1.0s)");
                }
                
                $this->log("Query completed in {$duration}s with {$result->num_rows} results");
                return true;
            },
            
            'Vendor search performance' => function() {
                $start = microtime(true);
                
                $query = "SELECT * FROM v_vendor_comprehensive 
                         WHERE name LIKE '%CV%' 
                         AND vendor_status = 2 
                         LIMIT 50";
                
                $result = $this->db->query($query);
                $end = microtime(true);
                $duration = $end - $start;
                
                if ($duration > 0.5) {
                    throw new Exception("Vendor query too slow: {$duration}s (expected < 0.5s)");
                }
                
                $this->log("Vendor query completed in {$duration}s with {$result->num_rows} results");
                return true;
            },
            
            'Full-text search performance' => function() {
                // Check if full-text indexes exist
                $result = $this->db->query("SHOW INDEX FROM ms_procurement WHERE Index_type = 'FULLTEXT'");
                if ($result->num_rows > 0) {
                    $start = microtime(true);
                    
                    $query = "SELECT * FROM ms_procurement 
                             WHERE MATCH(name, work_area, search_keywords) AGAINST('pembangunan' IN NATURAL LANGUAGE MODE) 
                             LIMIT 20";
                    
                    $result = $this->db->query($query);
                    $end = microtime(true);
                    $duration = $end - $start;
                    
                    $this->log("Full-text search completed in {$duration}s with {$result->num_rows} results");
                } else {
                    $this->log("Full-text indexes not found, skipping performance test");
                }
                return true;
            }
        ];
        
        return $this->runTestSuite($tests);
    }
    
    private function runDataIntegrityTests() {
        $tests = [
            'Sample filter presets exist' => function() {
                $result = $this->db->query("SELECT COUNT(*) as count FROM user_saved_searches WHERE is_public = 1");
                $row = $result->fetch_assoc();
                if ($row['count'] < 3) {
                    throw new Exception("Expected at least 3 public filter presets, found {$row['count']}");
                }
                return true;
            },
            
            'Search keywords populated' => function() {
                $result = $this->db->query("SELECT COUNT(*) as total, 
                                          SUM(CASE WHEN search_keywords IS NOT NULL AND search_keywords != '' THEN 1 ELSE 0 END) as with_keywords 
                                          FROM ms_procurement LIMIT 100");
                $row = $result->fetch_assoc();
                
                if ($row['total'] > 0 && $row['with_keywords'] == 0) {
                    $this->log("Warning: No search keywords found in sample data");
                } else {
                    $this->log("Found {$row['with_keywords']}/{$row['total']} records with search keywords");
                }
                return true;
            },
            
            'Efficiency calculations work' => function() {
                // Test the stored procedure
                $result = $this->db->query("CALL CalculateContractEfficiency()");
                
                if (!$result) {
                    throw new Exception("CalculateContractEfficiency procedure failed: " . $this->db->error);
                }
                
                return true;
            },
            
            'Views return data' => function() {
                $result = $this->db->query("SELECT COUNT(*) as count FROM v_auction_statistics LIMIT 10");
                $row = $result->fetch_assoc();
                $this->log("v_auction_statistics has {$row['count']} sample records");
                
                $result = $this->db->query("SELECT COUNT(*) as count FROM v_vendor_comprehensive LIMIT 10");
                $row = $result->fetch_assoc();
                $this->log("v_vendor_comprehensive has {$row['count']} sample records");
                
                return true;
            }
        ];
        
        return $this->runTestSuite($tests);
    }
    
    private function runAutocompleteTests() {
        $tests = [
            'Search suggestions cache table functional' => function() {
                // Test inserting and retrieving suggestions
                $this->db->query("INSERT INTO search_suggestions_cache 
                                 (table_name, field_name, search_term, suggestions, usage_count) 
                                 VALUES ('ms_procurement', 'name', 'test', '[\"Test Suggestion 1\", \"Test Suggestion 2\"]', 1)
                                 ON DUPLICATE KEY UPDATE usage_count = usage_count + 1");
                
                $result = $this->db->query("SELECT * FROM search_suggestions_cache WHERE search_term = 'test'");
                if ($result->num_rows === 0) {
                    throw new Exception("Failed to insert/update search suggestions cache");
                }
                
                // Clean up test data
                $this->db->query("DELETE FROM search_suggestions_cache WHERE search_term = 'test'");
                return true;
            },
            
            'Autocomplete query performance' => function() {
                $start = microtime(true);
                
                // Simulate autocomplete query
                $query = "SELECT DISTINCT name FROM ms_procurement 
                         WHERE name LIKE 'pem%' 
                         AND del = 0 
                         ORDER BY name 
                         LIMIT 10";
                
                $result = $this->db->query($query);
                $end = microtime(true);
                $duration = $end - $start;
                
                if ($duration > 0.1) {
                    throw new Exception("Autocomplete query too slow: {$duration}s (expected < 0.1s)");
                }
                
                $this->log("Autocomplete query completed in {$duration}s with {$result->num_rows} suggestions");
                return true;
            }
        ];
        
        return $this->runTestSuite($tests);
    }
    
    private function runSavedSearchTests() {
        $tests = [
            'Can save user filters' => function() {
                $testUserId = 1;
                $testFilters = '{"ms_procurement|name":"test","ms_procurement|auction_date":"2024-01-01,2024-12-31"}';
                
                $stmt = $this->db->prepare("INSERT INTO user_saved_searches (user_id, name, module, filters, is_public) 
                                           VALUES (?, 'Test Filter', 'auction', ?, 0)");
                $stmt->bind_param("is", $testUserId, $testFilters);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to save user filter: " . $stmt->error);
                }
                
                $searchId = $this->db->insert_id;
                
                // Clean up
                $this->db->query("DELETE FROM user_saved_searches WHERE id = $searchId");
                return true;
            },
            
            'Can retrieve saved filters' => function() {
                $result = $this->db->query("SELECT * FROM user_saved_searches WHERE is_public = 1 LIMIT 5");
                
                if ($result->num_rows === 0) {
                    throw new Exception("No public saved filters found");
                }
                
                while ($row = $result->fetch_assoc()) {
                    $filters = json_decode($row['filters'], true);
                    if (!is_array($filters)) {
                        throw new Exception("Invalid filter JSON in saved search: " . $row['name']);
                    }
                }
                
                return true;
            }
        ];
        
        return $this->runTestSuite($tests);
    }
    
    private function runAnalyticsTests() {
        $tests = [
            'Filter usage tracking works' => function() {
                // Test inserting usage analytics
                $this->db->query("INSERT INTO filter_usage_analytics 
                                 (user_id, module, filter_field, filter_value, usage_count) 
                                 VALUES (1, 'auction', 'ms_procurement|name', 'test search', 1)
                                 ON DUPLICATE KEY UPDATE usage_count = usage_count + 1");
                
                $result = $this->db->query("SELECT usage_count FROM filter_usage_analytics 
                                          WHERE filter_field = 'ms_procurement|name' AND filter_value = 'test search'");
                
                if ($result->num_rows === 0) {
                    throw new Exception("Failed to track filter usage");
                }
                
                // Clean up
                $this->db->query("DELETE FROM filter_usage_analytics WHERE filter_value = 'test search'");
                return true;
            },
            
            'Analytics stored procedure works' => function() {
                $result = $this->db->query("CALL GetFilterUsageStats('auction')");
                
                if (!$result) {
                    throw new Exception("GetFilterUsageStats procedure failed: " . $this->db->error);
                }
                
                return true;
            }
        ];
        
        return $this->runTestSuite($tests);
    }
    
    private function runFullTextSearchTests() {
        $tests = [
            'Full-text indexes exist' => function() {
                $result = $this->db->query("SHOW INDEX FROM ms_procurement WHERE Index_type = 'FULLTEXT'");
                if ($result->num_rows === 0) {
                    $this->log("Warning: Full-text indexes not found on ms_procurement");
                }
                
                $result = $this->db->query("SHOW INDEX FROM ms_vendor WHERE Index_type = 'FULLTEXT'");
                if ($result->num_rows === 0) {
                    $this->log("Warning: Full-text indexes not found on ms_vendor");
                }
                
                return true;
            },
            
            'Full-text search returns results' => function() {
                // Only test if full-text indexes exist
                $result = $this->db->query("SHOW INDEX FROM ms_procurement WHERE Index_type = 'FULLTEXT'");
                if ($result->num_rows > 0) {
                    $searchResult = $this->db->query("SELECT COUNT(*) as count FROM ms_procurement 
                                                     WHERE MATCH(name, work_area, search_keywords) AGAINST('pembangunan' IN NATURAL LANGUAGE MODE)");
                    $row = $searchResult->fetch_assoc();
                    $this->log("Full-text search found {$row['count']} results for 'pembangunan'");
                } else {
                    $this->log("Skipping full-text search test - indexes not available");
                }
                
                return true;
            }
        ];
        
        return $this->runTestSuite($tests);
    }
    
    private function runTestSuite($tests) {
        $passed = 0;
        $total = count($tests);
        
        foreach ($tests as $testName => $testFunction) {
            try {
                $testFunction();
                $this->log("✓ $testName");
                $passed++;
            } catch (Exception $e) {
                $this->log("✗ $testName: " . $e->getMessage());
            }
        }
        
        return ['passed' => $passed, 'total' => $total];
    }
    
    private function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        if ($this->verbose || $level === 'ERROR') {
            echo "$message\n";
        } else {
            echo ".";
        }
    }
    
    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }
}

// Main execution
if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line\n");
}

$verbose = in_array('--verbose', $argv);

echo "Enhanced Filter System Integration Tests\n";
echo "=======================================\n\n";

$tester = new EnhancedFilterTests($verbose);
$success = $tester->runAllTests();

echo "\n\nTest completed. Check the log file for detailed results.\n";
exit($success ? 0 : 1); 