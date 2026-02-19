<?php 
/**
 * Enhanced Filter Library for VMS
 * Provides advanced filtering capabilities with performance optimizations,
 * autocomplete functionality, and improved user experience
 * 
 * Compatible with PHP 5.6 and MySQL 5.7.44
 * Maintains backward compatibility with existing Filter.php
 */
class Filter_enhanced {
    private $CI;
    private $cache_timeout = 300; // 5 minutes
    private $max_autocomplete_results = 10;
    
    public function __construct(){
        $this->CI =& get_instance(); 
        $this->CI->load->library('upload');
        $this->CI->load->driver('cache', array('adapter' => 'file'));
    }

    /**
     * Enhanced version of group_filter_post with autocomplete and better UX
     */
    public function group_filter_post($field = null){
        if($field === null){
            $field = $this->get_field();
        }
        
        $filter = $this->CI->input->post('filter');
        
        $html = '<div class="filter enhanced-filter">
                <div class="filterHeader">
                    <h2>Advanced Filter</h2>
                    <div class="filter-controls">
                        <span class="filter-count-badge">0</span>
                        <a class="filterBtn"><i class="fa fa-filter"></i>&nbsp;Filter&nbsp;<i class="fa fa-angle-down"></i></a>
                        <a href="#" class="clearAllFilters"><i class="fa fa-times"></i>&nbsp;Clear All</a>
                        <a href="#" class="saveFilterPreset"><i class="fa fa-bookmark"></i>&nbsp;Save</a>
                        <a href="#" class="exportFiltered" data-format="excel"><i class="fa fa-download"></i>&nbsp;Export</a>
                    </div>
                    <span class="editBtn filterBtnClose iconOnly"><i class="fa fa-times"></i></span>
                </div>
                
                <div class="filter-quick-access">
                    <div class="quick-filter-buttons">
                        <button type="button" class="quick-filter-btn" data-filter-type="auction_date_preset" data-filter-value="today">Today</button>
                        <button type="button" class="quick-filter-btn" data-filter-type="auction_date_preset" data-filter-value="this_week">This Week</button>
                        <button type="button" class="quick-filter-btn" data-filter-type="auction_date_preset" data-filter-value="this_month">This Month</button>
                        <button type="button" class="quick-filter-btn" data-filter-type="ms_procurement|auction_type" data-filter-value="tender_terbuka">Tender Terbuka</button>
                        <button type="button" class="quick-filter-btn" data-filter-type="status_active" data-filter-value="1">Active Only</button>
                    </div>
                </div>
                
                <div class="filter-preview">
                    <div class="active-filters">
                        <strong>Active Filters:</strong>
                        <div class="filter-tags"></div>
                    </div>
                    <div class="filter-stats">
                        <span class="expected-results">Expected Results: <span class="count">-</span></span>
                    </div>
                </div>
                
                <div class="groupFilterArea clearfix" style="display: block;">
                    <div class="filterForm">';
        
        foreach($field as $row){
            $displayActive = (count($row['filter']) > 0) ? 'style="display: block;"' : '';
            $html .= '<div class="groupForm">
                        <div class="groupFormHeader">
                            <label class="title">'.$row['label'].'</label><i class="fa fa-sort-desc"></i>
                        </div>
                        <div class="groupFormContent" '.$displayActive.'>';
                        
            foreach($row['filter'] as $key => $value){
                $field_parts = explode('|', $value['table']);
                $html .= $this->generate_enhanced_field($value, $filter, $field_parts);
            }
            
            $html .= '</div></div>';
        }
        
        $html .= '    <div class="filterSubmitArea">
                        <button type="submit" class="btnBlue">Apply Filters</button>
                        <button type="button" class="btnGray clearAllFilters">Clear All</button>
                        <div class="filter-loading" style="display: none;">
                            <i class="fa fa-spinner fa-spin"></i> Applying filters...
                        </div>
                    </div>
                </div>
            </div>';
        
        return $html;
    }
    
    /**
     * Generate enhanced field with autocomplete and improved UX
     */
    private function generate_enhanced_field($value, $filter, $field_parts) {
        $html = '';
        $field_id = str_replace(array('|', '.'), '_', $value['table']);
        
        switch ($value['type']) {
            case 'text':
                $html .= '<div class="groupFieldBlock enhanced-text-field">
                            <label class="title">'.$value['label'].'</label>
                            <div class="autocomplete-wrapper">
                                <input type="text" 
                                       class="filter-input autocomplete-input" 
                                       name="filter['.$value['table'].'][]" 
                                       data-field="'.$field_id.'"
                                       data-autocomplete-url="'.site_url('auction/autocomplete_filter').'"
                                       data-autocomplete-field="'.$value['table'].'"
                                       placeholder="Start typing to search..."
                                       value="'.(isset($filter[$value['table']][0]) ? $filter[$value['table']][0] : '').'">';
                
                if(isset($filter[$value['table']]) && count($filter[$value['table']]) > 1) {
                    $html .= '<div class="additional-inputs">';
                    for($i = 1; $i < count($filter[$value['table']]); $i++) {
                        $html .= '<input type="text" 
                                        class="filter-input autocomplete-input" 
                                        name="filter['.$value['table'].'][]" 
                                        data-field="'.$field_id.'"
                                        value="'.$filter[$value['table']][$i].'">';
                    }
                    $html .= '</div>';
                }
                
                $html .= '      <div class="autocomplete-suggestions"></div>
                                <div class="field-actions">
                                    <a href="#" class="addFilterGroup"><i class="fa fa-plus"></i></a>
                                    <a href="#" class="removeFilterGroup"><i class="fa fa-minus"></i></a>
                                </div>
                            </div>
                        </div>';
                break;
                
            case 'dropdown':
                $data_dropdown = $this->get_cached_dropdown_data($field_parts[2]);
                $html .= '<div class="groupFieldBlock enhanced-dropdown-field">
                            <label class="title">'.$value['label'].'</label>
                            <div class="dropdown-wrapper">
                                <select class="filter-input enhanced-select" 
                                        name="filter['.$value['table'].'][]" 
                                        data-field="'.$field_id.'"
                                        data-placeholder="Select '.$value['label'].'">
                                    <option value="">-- Select '.$value['label'].' --</option>';
                
                foreach($data_dropdown as $k => $v) {
                    $selected = (isset($filter[$value['table']][0]) && $filter[$value['table']][0] == $k) ? 'selected' : '';
                    $html .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
                }
                
                $html .= '          </select>
                                <div class="field-actions">
                                    <a href="#" class="addFilterGroup"><i class="fa fa-plus"></i></a>
                                    <a href="#" class="removeFilterGroup"><i class="fa fa-minus"></i></a>
                                </div>
                            </div>
                        </div>';
                break;
                
            case 'date':
            case 'date_range':
                $html .= '<div class="groupFieldBlock enhanced-date-field">
                            <label class="title">'.$value['label'].'</label>
                            <div class="date-range-wrapper">
                                <div class="date-presets">
                                    <button type="button" class="date-preset-btn" data-preset="today">Today</button>
                                    <button type="button" class="date-preset-btn" data-preset="yesterday">Yesterday</button>
                                    <button type="button" class="date-preset-btn" data-preset="this_week">This Week</button>
                                    <button type="button" class="date-preset-btn" data-preset="last_week">Last Week</button>
                                    <button type="button" class="date-preset-btn" data-preset="this_month">This Month</button>
                                    <button type="button" class="date-preset-btn" data-preset="last_month">Last Month</button>
                                    <button type="button" class="date-preset-btn" data-preset="this_quarter">This Quarter</button>
                                    <button type="button" class="date-preset-btn" data-preset="this_year">This Year</button>
                                </div>
                                <div class="date-inputs">
                                    <div class="date-input-group">
                                        <label>From:</label>
                                        <input type="date" 
                                               class="filter-input date-input" 
                                               name="filter['.$value['table'].'][start_date][]" 
                                               data-field="'.$field_id.'_start"
                                               value="'.(isset($filter[$value['table']]['start_date'][0]) ? $filter[$value['table']]['start_date'][0] : '').'">
                                    </div>
                                    <div class="date-input-group">
                                        <label>To:</label>
                                        <input type="date" 
                                               class="filter-input date-input" 
                                               name="filter['.$value['table'].'][end_date][]" 
                                               data-field="'.$field_id.'_end"
                                               value="'.(isset($filter[$value['table']]['end_date'][0]) ? $filter[$value['table']]['end_date'][0] : '').'">
                                    </div>
                                </div>
                                <div class="field-actions">
                                    <a href="#" class="addFilterGroupDate"><i class="fa fa-plus"></i></a>
                                    <a href="#" class="removeFilterGroupDate"><i class="fa fa-minus"></i></a>
                                </div>
                            </div>
                        </div>';
                break;
                
            case 'number_range':
                $html .= '<div class="groupFieldBlock enhanced-number-field">
                            <label class="title">'.$value['label'].'</label>
                            <div class="number-range-wrapper">
                                <div class="range-slider-container">
                                    <div class="range-slider" 
                                         data-min="'.(isset($value['min']) ? $value['min'] : '0').'" 
                                         data-max="'.(isset($value['max']) ? $value['max'] : '1000000').'"
                                         data-step="'.(isset($value['step']) ? $value['step'] : '1000').'">
                                    </div>
                                    <div class="range-labels">
                                        <span class="min-label">Min</span>
                                        <span class="max-label">Max</span>
                                    </div>
                                </div>
                                <div class="number-inputs">
                                    <div class="number-input-group">
                                        <label>Min Value:</label>
                                        <input type="number" 
                                               class="filter-input number-input" 
                                               name="filter['.$value['table'].'][start_value][]" 
                                               data-field="'.$field_id.'_min"
                                               placeholder="Minimum"
                                               value="'.(isset($filter[$value['table']]['start_value'][0]) ? $filter[$value['table']]['start_value'][0] : '').'">
                                    </div>
                                    <div class="number-input-group">
                                        <label>Max Value:</label>
                                        <input type="number" 
                                               class="filter-input number-input" 
                                               name="filter['.$value['table'].'][end_value][]" 
                                               data-field="'.$field_id.'_max"
                                               placeholder="Maximum"
                                               value="'.(isset($filter[$value['table']]['end_value'][0]) ? $filter[$value['table']]['end_value'][0] : '').'">
                                    </div>
                                </div>
                                <div class="field-actions">
                                    <a href="#" class="addFilterNumberRange"><i class="fa fa-plus"></i></a>
                                    <a href="#" class="removeFilterNumberRange"><i class="fa fa-minus"></i></a>
                                </div>
                            </div>
                        </div>';
                break;
                
            case 'fulltext':
                $html .= '<div class="groupFieldBlock enhanced-fulltext-field">
                            <label class="title">'.$value['label'].'</label>
                            <div class="fulltext-wrapper">
                                <input type="text" 
                                       class="filter-input fulltext-input" 
                                       name="filter['.$value['table'].'][]" 
                                       data-field="'.$field_id.'"
                                       placeholder="Search keywords..."
                                       value="'.(isset($filter[$value['table']][0]) ? $filter[$value['table']][0] : '').'">
                                <div class="search-options">
                                    <label><input type="checkbox" name="filter['.$value['table'].'_options][exact_match]"> Exact Match</label>
                                    <label><input type="checkbox" name="filter['.$value['table'].'_options][case_sensitive]"> Case Sensitive</label>
                                </div>
                            </div>
                        </div>';
                break;
        }
        
        return $html;
    }
    
    /**
     * Enhanced query generation with performance optimizations
     */
    public function generate_query($instance, $filter, $options = array()) {
        $default_options = array(
            'use_fulltext' => true,
            'optimize_joins' => true,
            'use_indexes' => true,
            'cache_results' => true
        );
        $options = array_merge($default_options, $options);
        
        // Clean and validate filter data
        $clean_filter = $this->clean_filter_data($filter);
        
        // Handle session-based filter persistence
        $filter_data = $this->handle_filter_session($clean_filter);
        
        if (empty($filter_data)) {
            return $instance;
        }
        
        $field_array = $this->enhanced_field_array();
        $joins_needed = array();
        
        foreach ($filter_data as $key => $row) {
            $field_parts = explode('|', $key);
            $table = $field_parts[0];
            $column = $field_parts[1];
            
            // Handle different filter types
            if (isset($row['start_date']) || isset($row['end_date'])) {
                $this->apply_date_filter($instance, $table, $column, $row, $joins_needed, $field_array);
            } elseif (isset($row['start_value']) || isset($row['end_value'])) {
                $this->apply_number_range_filter($instance, $table, $column, $row, $joins_needed, $field_array);
            } else {
                $this->apply_text_filter($instance, $table, $column, $row, $joins_needed, $field_array, $options);
            }
        }
        
        // Apply optimized joins
        if ($options['optimize_joins']) {
            $this->apply_optimized_joins($instance, $joins_needed, $field_array);
        }
        
        return $instance;
    }
    
    /**
     * Apply optimized text filtering with index usage
     */
    private function apply_text_filter($instance, $table, $column, $row, &$joins_needed, $field_array, $options) {
        $conditions = array();
        
        foreach ($row as $value) {
            if (!empty($value)) {
                // Determine join requirements
                if (isset($field_array[$table])) {
                    $joins_needed[$table] = $field_array[$table];
                }
                
                // Use optimized search strategies
                if ($options['use_fulltext'] && $this->is_fulltext_column($table, $column)) {
                    // Use FULLTEXT search for better performance
                    $conditions[] = "MATCH($table.$column) AGAINST('$value' IN BOOLEAN MODE)";
                } elseif (is_numeric($value)) {
                    // Exact match for numeric values (uses indexes)
                    $conditions[] = "$table.$column = '$value'";
                } elseif (strlen($value) >= 3) {
                    // Use prefix search for better index usage
                    $conditions[] = "$table.$column LIKE '$value%'";
                } else {
                    // Fall back to LIKE search for short terms
                    $conditions[] = "$table.$column LIKE '%$value%'";
                }
            }
        }
        
        if (!empty($conditions)) {
            $instance->where('(' . implode(' OR ', $conditions) . ')', null, false);
        }
    }
    
    /**
     * Apply date range filtering with optimization
     */
    private function apply_date_filter($instance, $table, $column, $row, &$joins_needed, $field_array) {
        $conditions = array();
        
        if (isset($field_array[$table])) {
            $joins_needed[$table] = $field_array[$table];
        }
        
        foreach ($row['start_date'] as $k => $start_date) {
            if (!empty($start_date) && !empty($row['end_date'][$k])) {
                $conditions[] = "(`$table`.`$column` >= '$start_date' AND `$table`.`$column` <= '{$row['end_date'][$k]}')";
            }
        }
        
        if (!empty($conditions)) {
            $instance->where('(' . implode(' OR ', $conditions) . ')', null, false);
        }
    }
    
    /**
     * Apply number range filtering
     */
    private function apply_number_range_filter($instance, $table, $column, $row, &$joins_needed, $field_array) {
        $conditions = array();
        
        if (isset($field_array[$table])) {
            $joins_needed[$table] = $field_array[$table];
        }
        
        foreach ($row['start_value'] as $k => $start_value) {
            if (!empty($start_value) && !empty($row['end_value'][$k])) {
                $conditions[] = "(`$table`.`$column` >= '$start_value' AND `$table`.`$column` <= '{$row['end_value'][$k]}')";
            }
        }
        
        if (!empty($conditions)) {
            $instance->where('(' . implode(' OR ', $conditions) . ')', null, false);
        }
    }
    
    /**
     * Apply joins in an optimized manner
     */
    private function apply_optimized_joins($instance, $joins_needed, $field_array) {
        foreach ($joins_needed as $table => $join_condition) {
            $instance->join($table, $join_condition, 'LEFT');
        }
    }
    
    /**
     * Enhanced field array with better relationships
     */
    public function enhanced_field_array() {
        return array(
            'ms_pemilik' => 'ms_pemilik.id_vendor = ms_vendor.id',
            'ms_ijin_usaha miu' => 'miu.id_vendor = ms_vendor.id',
            'ms_iu_bsb' => 'ms_iu_bsb.id_vendor = ms_vendor.id',
            'tr_dpt' => 'tr_dpt.id_vendor = ms_vendor.id',
            'ms_agen' => 'ms_agen.id_vendor = ms_vendor.id',
            'ms_agen_produk' => 'ms_agen_produk.id_agen = ms_agen.id',
            'tr_assessment tra' => 'tra.id_vendor = ms_vendor.id',
            'ms_procurement_peserta mpp' => 'mpp.id_proc = ms_procurement.id',
            'ms_procurement_barang mpb' => 'mpb.id_procurement = ms_procurement.id',
            'ms_budget mb' => 'mb.id = ms_procurement.id_budget',
            'ms_division md' => 'md.id = ms_procurement.id_division',
            'ms_pejabat_pengadaan mpp2' => 'mpp2.id = ms_procurement.id_pejabat_pengadaan',
            // Additional performance-optimized joins
            'ms_vendor mv' => 'mv.id = mpp.id_vendor',
            'tr_assessment_point tap' => 'tap.id_vendor = mv.id'
        );
    }
    
    /**
     * Get cached dropdown data for performance
     */
    private function get_cached_dropdown_data($method_name) {
        $cache_key = 'dropdown_' . $method_name;
        $cached_data = $this->CI->cache->get($cache_key);
        
        if ($cached_data !== false) {
            return $cached_data;
        }
        
        // Generate dropdown data
        if (method_exists($this, $method_name)) {
            $data = $this->$method_name();
        } else {
            $data = array();
        }
        
        // Cache the data
        $this->CI->cache->save($cache_key, $data, $this->cache_timeout);
        
        return $data;
    }
    
    /**
     * Clean and validate filter input data
     */
    private function clean_filter_data($filter) {
        if (empty($filter)) {
            return array();
        }
        
        $clean_filter = array();
        
        foreach ($filter as $key => $value) {
            if (is_array($value)) {
                // Handle array values (for multiple inputs)
                $clean_value = array();
                foreach ($value as $v) {
                    if (is_string($v)) {
                        $cleaned = trim($this->CI->security->xss_clean($v));
                        if (!empty($cleaned)) {
                            $clean_value[] = $cleaned;
                        }
                    } elseif (is_array($v)) {
                        $clean_value[] = $v; // For date/number ranges
                    }
                }
                if (!empty($clean_value)) {
                    $clean_filter[$key] = $clean_value;
                }
            } else {
                $cleaned = trim($this->CI->security->xss_clean($value));
                if (!empty($cleaned)) {
                    $clean_filter[$key] = array($cleaned);
                }
            }
        }
        
        return $clean_filter;
    }
    
    /**
     * Handle filter session management
     */
    private function handle_filter_session($post_filter) {
        $uri_string = $this->CI->uri->uri_string();
        $sess_filter = $this->CI->session->userdata('filter')[$uri_string];
        
        // Clean empty arrays
        foreach ($post_filter as $key => $val_arr) {
            if (isset($val_arr[0]) && count($val_arr) == 1 && $val_arr[0] == '') {
                unset($post_filter[$key]);
            }
        }
        
        foreach ($sess_filter as $key => $val_arr) {
            if (!isset($post_filter[$key])) {
                unset($sess_filter[$key]);
            }
        }
        
        // Update session
        $filter_session = $this->CI->session->userdata('filter') ?: array();
        $filter_session[$uri_string] = !empty($post_filter) ? $post_filter : (!empty($sess_filter) ? $sess_filter : array());
        $this->CI->session->set_userdata('filter', $filter_session);
        
        return $filter_session[$uri_string];
    }
    
    /**
     * Check if column supports fulltext search
     */
    private function is_fulltext_column($table, $column) {
        $fulltext_columns = array(
            'ms_procurement' => array('name', 'description'),
            'ms_vendor' => array('name', 'description'),
            'ms_material' => array('name', 'description', 'specification')
        );
        
        return isset($fulltext_columns[$table]) && in_array($column, $fulltext_columns[$table]);
    }
    
    /**
     * Generate autocomplete suggestions
     */
    public function get_autocomplete_suggestions($field, $term, $limit = 10) {
        $cache_key = 'autocomplete_' . md5($field . $term);
        $cached = $this->CI->cache->get($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $field_parts = explode('|', $field);
        $table = $field_parts[0];
        $column = $field_parts[1];
        
        $this->CI->db->select("DISTINCT $column as value")
                     ->from($table)
                     ->like($column, $term, 'after')
                     ->where("$column IS NOT NULL")
                     ->where("$column != ''")
                     ->order_by($column, 'ASC')
                     ->limit($limit);
        
        $results = $this->CI->db->get()->result_array();
        
        $suggestions = array();
        foreach ($results as $row) {
            $suggestions[] = $row['value'];
        }
        
        // Cache for 5 minutes
        $this->CI->cache->save($cache_key, $suggestions, 300);
        
        return $suggestions;
    }
    
    /**
     * Get filter statistics for preview
     */
    public function get_filter_statistics($filter_data, $base_query) {
        // Clone the query to avoid affecting the original
        $count_query = clone $base_query;
        
        // Apply filters to count query
        $filtered_query = $this->generate_query($count_query, $filter_data);
        
        // Get count
        $count = $filtered_query->count_all_results();
        
        return array(
            'expected_results' => $count,
            'filter_applied' => !empty($filter_data)
        );
    }
    
    // Existing dropdown methods with caching
    public function get_legal() {
        $arr = $this->CI->db->select('id,name')->where('del', 0)->get('tb_legal')->result_array();
        $result = array();
        foreach ($arr as $row) {
            $result[$row['id']] = $row['name'];
        }
        return $result;
    }
    
    public function get_bidang() {
        $arr = $this->CI->db->select('id,name')->where('del', 0)->get('tb_bidang')->result_array();
        $result = array();
        foreach ($arr as $row) {
            $result[$row['id']] = $row['name'];
        }
        return $result;
    }
    
    public function get_kualifikasi() {
        return array(
            'kecil' => 'Kecil',
            'menengah' => 'Menengah', 
            'besar' => 'Besar'
        );
    }
    
    public function get_auction_types() {
        return array(
            'tender_terbuka' => 'Tender Terbuka',
            'tender_terbatas' => 'Tender Terbatas',
            'penunjukan_langsung' => 'Penunjukan Langsung',
            'pengadaan_langsung' => 'Pengadaan Langsung',
            'kontes' => 'Kontes',
            'sayembara' => 'Sayembara'
        );
    }
    
    public function get_auction_status() {
        return array(
            '0' => 'Draft',
            '1' => 'Published',
            '2' => 'Active',
            '3' => 'Finished',
            '4' => 'Cancelled',
            '5' => 'Suspended'
        );
    }
    
    public function get_procurement_methods() {
        return array(
            'e_auction' => 'E-Auction',
            'e_tender' => 'E-Tender', 
            'offline' => 'Offline',
            'hybrid' => 'Hybrid'
        );
    }
    
    /**
     * Enhanced field configuration - can be overridden by controllers
     */
    public function get_field($array = array()) {
        // This is the base configuration - controllers should override this
        $base_fields = array(
            array(
                'label' => 'Basic Search',
                'filter' => array(
                    array('table' => 'ms_procurement|name', 'type' => 'fulltext', 'label' => 'Full Text Search'),
                    array('table' => 'ms_procurement|auction_date', 'type' => 'date_range', 'label' => 'Auction Date'),
                    array('table' => 'ms_procurement|auction_type|get_auction_types', 'type' => 'dropdown', 'label' => 'Auction Type')
                )
            )
        );
        
        if (!empty($array)) {
            $base_fields = array_merge($base_fields, $array);
        }
        
        return $base_fields;
    }
} 