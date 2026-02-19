<?php
/**
 * Enhanced Filter Database Migration Runner
 * 
 * This script executes the enhanced_filter_improvements.sql migration file
 * with proper error handling and rollback capabilities.
 * 
 * Usage: C:\tools\php56\php.exe migration_runner.php [--rollback]
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once '../app/application/config/database.php';

class MigrationRunner {
    private $db;
    private $logFile;
    
    public function __construct() {
        $this->logFile = 'migration_' . date('Y-m-d_H-i-s') . '.log';
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
    
    public function runMigration($rollback = false) {
        $this->log("Starting " . ($rollback ? "rollback" : "migration") . " process");
        
        try {
            // Begin transaction
            $this->db->autocommit(false);
            
            if ($rollback) {
                $this->runRollback();
            } else {
                $this->runUpgrade();
            }
            
            // Commit transaction
            $this->db->commit();
            $this->log("Migration completed successfully");
            echo "Migration completed successfully! Check {$this->logFile} for details.\n";
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            $this->log("Migration failed: " . $e->getMessage(), 'ERROR');
            echo "Migration failed: " . $e->getMessage() . "\n";
            echo "Check {$this->logFile} for details.\n";
            exit(1);
        }
    }
    
    private function runUpgrade() {
        $sqlFile = 'enhanced_filter_improvements.sql';
        
        if (!file_exists($sqlFile)) {
            throw new Exception("Migration file not found: $sqlFile");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Remove rollback section from execution
        $sql = preg_replace('/\/\*.*?ROLLBACK SCRIPT.*?\*\//s', '', $sql);
        
        // Split SQL statements
        $statements = $this->splitSqlStatements($sql);
        
        $this->log("Executing " . count($statements) . " SQL statements");
        
        $successCount = 0;
        foreach ($statements as $i => $statement) {
            $statement = trim($statement);
            if (empty($statement) || substr($statement, 0, 2) === '--') {
                continue;
            }
            
            $this->log("Executing statement " . ($i + 1) . ": " . substr($statement, 0, 100) . "...");
            
            if (!$this->db->query($statement)) {
                // Some errors are acceptable (like index already exists)
                $error = $this->db->error;
                if (strpos($error, 'already exists') !== false || 
                    strpos($error, 'Duplicate key') !== false ||
                    strpos($error, 'already have') !== false) {
                    $this->log("Skipping existing: " . $error, 'WARNING');
                } else {
                    throw new Exception("SQL Error in statement " . ($i + 1) . ": " . $error);
                }
            } else {
                $successCount++;
            }
        }
        
        $this->log("Successfully executed $successCount statements");
        
        // Run verification queries
        $this->runVerification();
    }
    
    private function runRollback() {
        $this->log("Starting rollback process");
        
        $rollbackSql = "
            -- Drop triggers
            DROP TRIGGER IF EXISTS tr_procurement_search_keywords;
            DROP TRIGGER IF EXISTS tr_vendor_search_keywords;
            
            -- Drop procedures
            DROP PROCEDURE IF EXISTS GetFilterUsageStats;
            DROP PROCEDURE IF EXISTS UpdateSearchKeywords;
            DROP PROCEDURE IF EXISTS CalculateContractEfficiency;
            
            -- Drop views
            DROP VIEW IF EXISTS v_auction_statistics;
            DROP VIEW IF EXISTS v_vendor_comprehensive;
            
            -- Drop new tables
            DROP TABLE IF EXISTS user_saved_searches;
            DROP TABLE IF EXISTS filter_usage_analytics;
            DROP TABLE IF EXISTS search_suggestions_cache;
            
            -- Remove new columns (check if exists first)
            SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                          WHERE table_name = 'ms_procurement' AND column_name = 'budget_year') > 0,
                          'ALTER TABLE ms_procurement DROP COLUMN budget_year', 
                          'SELECT \"Column budget_year does not exist\"');
            PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
            
            SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                          WHERE table_name = 'ms_procurement' AND column_name = 'search_keywords') > 0,
                          'ALTER TABLE ms_procurement DROP COLUMN search_keywords', 
                          'SELECT \"Column search_keywords does not exist\"');
            PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
            
            SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                          WHERE table_name = 'ms_contract' AND column_name = 'efficiency_percentage') > 0,
                          'ALTER TABLE ms_contract DROP COLUMN efficiency_percentage', 
                          'SELECT \"Column efficiency_percentage does not exist\"');
            PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
            
            SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                          WHERE table_name = 'ms_vendor' AND column_name = 'search_keywords') > 0,
                          'ALTER TABLE ms_vendor DROP COLUMN search_keywords', 
                          'SELECT \"Column search_keywords does not exist\"');
            PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
        ";
        
        $statements = $this->splitSqlStatements($rollbackSql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement) || substr($statement, 0, 2) === '--') {
                continue;
            }
            
            if (!$this->db->query($statement)) {
                $this->log("Rollback warning: " . $this->db->error, 'WARNING');
            }
        }
        
        $this->log("Rollback completed");
    }
    
    private function runVerification() {
        $this->log("Running verification checks");
        
        $checks = [
            "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'user_saved_searches'" => "user_saved_searches table",
            "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'filter_usage_analytics'" => "filter_usage_analytics table",
            "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'search_suggestions_cache'" => "search_suggestions_cache table",
            "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.VIEWS WHERE table_name = 'v_auction_statistics'" => "v_auction_statistics view",
            "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.VIEWS WHERE table_name = 'v_vendor_comprehensive'" => "v_vendor_comprehensive view",
            "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.ROUTINES WHERE routine_name = 'GetFilterUsageStats'" => "GetFilterUsageStats procedure",
            "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TRIGGERS WHERE trigger_name = 'tr_procurement_search_keywords'" => "tr_procurement_search_keywords trigger"
        ];
        
        $allGood = true;
        foreach ($checks as $sql => $description) {
            $result = $this->db->query($sql);
            if ($result) {
                $row = $result->fetch_assoc();
                if ($row['count'] > 0) {
                    $this->log("✓ $description exists");
                } else {
                    $this->log("✗ $description not found", 'ERROR');
                    $allGood = false;
                }
            }
        }
        
        if ($allGood) {
            $this->log("All verification checks passed");
        } else {
            throw new Exception("Some verification checks failed");
        }
    }
    
    private function splitSqlStatements($sql) {
        // Handle DELIMITER changes for procedures/triggers
        $sql = preg_replace('/DELIMITER\s+\/\/.*?DELIMITER\s+;/s', function($matches) {
            $content = $matches[0];
            // Replace ; with temporary marker inside procedures
            $content = str_replace('DELIMITER //', '', $content);
            $content = str_replace('DELIMITER ;', '', $content);
            return $content;
        }, $sql);
        
        // Split by semicolon, but not inside quotes or procedures
        $statements = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = '';
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            
            if (!$inQuotes && ($char === '"' || $char === "'")) {
                $inQuotes = true;
                $quoteChar = $char;
            } elseif ($inQuotes && $char === $quoteChar) {
                $inQuotes = false;
            } elseif (!$inQuotes && $char === ';') {
                $statements[] = $current;
                $current = '';
                continue;
            }
            
            $current .= $char;
        }
        
        if (!empty(trim($current))) {
            $statements[] = $current;
        }
        
        return $statements;
    }
    
    private function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        if ($level === 'ERROR') {
            echo "ERROR: $message\n";
        } elseif ($level === 'WARNING') {
            echo "WARNING: $message\n";
        } else {
            echo "$message\n";
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

$rollback = in_array('--rollback', $argv);

echo "Enhanced Filter Database Migration Runner\n";
echo "========================================\n\n";

if ($rollback) {
    echo "WARNING: This will rollback all enhanced filter database changes!\n";
    echo "Are you sure? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $confirm = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirm) !== 'y') {
        echo "Rollback cancelled.\n";
        exit(0);
    }
}

$runner = new MigrationRunner();
$runner->runMigration($rollback); 