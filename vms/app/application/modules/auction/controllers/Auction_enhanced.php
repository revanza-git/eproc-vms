<?php
/**
 * Enhanced Auction Controller for VMS
 * Provides comprehensive filtering capabilities with advanced search options
 * and improved user experience for procurement management
 * 
 * Compatible with PHP 5.6 and MySQL 5.7.44
 */
class Auction_enhanced extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('auction/auction_model', 'am');
        $this->load->library('form');
        $this->load->library('datatables');
        $this->load->library('securities');
        $this->load->library('utility');
        $this->load->library('filter_enhanced');
        $this->load->helper('url');
        $this->load->helper('form');
        
        // Ensure user is logged in
        if(!$this->session->userdata('admin')){
            redirect('admin/login');
        }
    }
    
    /**
     * Enhanced filter configuration with comprehensive business filters
     * Covers all aspects of procurement management for better search capabilities
     */
    public function get_enhanced_filter_config() {
        return array(
            // Package Information Group
            array(
                'label' => 'Package Information',
                'filter' => array(
                    array(
                        'table' => 'ms_procurement|name',
                        'type' => 'fulltext',
                        'label' => 'Package Name (Full-text Search)',
                        'placeholder' => 'Search in package names, descriptions...'
                    ),
                    array(
                        'table' => 'ms_procurement|work_area|get_work_areas',
                        'type' => 'dropdown',
                        'label' => 'Work Area / Location'
                    ),
                    array(
                        'table' => 'ms_procurement|auction_type|get_auction_types',
                        'type' => 'dropdown',
                        'label' => 'Procurement Method'
                    ),
                    array(
                        'table' => 'ms_procurement|procurement_category|get_procurement_categories',
                        'type' => 'dropdown',
                        'label' => 'Procurement Category'
                    ),
                    array(
                        'table' => 'ms_procurement|contract_type|get_contract_types',
                        'type' => 'dropdown',
                        'label' => 'Contract Type'
                    )
                )
            ),
            
            // Budget & Financial Information
            array(
                'label' => 'Budget & Financial',
                'filter' => array(
                    array(
                        'table' => 'ms_procurement|hps_value',
                        'type' => 'number_range',
                        'label' => 'HPS Value (IDR)',
                        'min' => 0,
                        'max' => 50000000000,
                        'step' => 1000000,
                        'format' => 'currency'
                    ),
                    array(
                        'table' => 'ms_procurement|contract_value',
                        'type' => 'number_range',
                        'label' => 'Contract Value (IDR)',
                        'min' => 0,
                        'max' => 50000000000,
                        'step' => 1000000,
                        'format' => 'currency'
                    ),
                    array(
                        'table' => 'ms_procurement|budget_year|get_budget_years',
                        'type' => 'dropdown',
                        'label' => 'Budget Year'
                    ),
                    array(
                        'table' => 'ms_procurement|budget_source|get_budget_sources',
                        'type' => 'dropdown',
                        'label' => 'Budget Source'
                    ),
                    array(
                        'table' => 'ms_procurement|efficiency_percentage',
                        'type' => 'number_range',
                        'label' => 'Cost Efficiency (%)',
                        'min' => 0,
                        'max' => 100,
                        'step' => 1
                    )
                )
            ),
            
            // Time & Schedule Management
            array(
                'label' => 'Time & Schedule',
                'filter' => array(
                    array(
                        'table' => 'ms_procurement|auction_date',
                        'type' => 'date_range',
                        'label' => 'Auction Date',
                        'presets' => array(
                            'today' => 'Today',
                            'tomorrow' => 'Tomorrow',
                            'this_week' => 'This Week',
                            'next_week' => 'Next Week',
                            'this_month' => 'This Month',
                            'next_month' => 'Next Month',
                            'this_quarter' => 'This Quarter',
                            'this_year' => 'This Year'
                        )
                    ),
                    array(
                        'table' => 'ms_procurement|registration_start',
                        'type' => 'date_range',
                        'label' => 'Registration Period Start'
                    ),
                    array(
                        'table' => 'ms_procurement|registration_end',
                        'type' => 'date_range',
                        'label' => 'Registration Period End'
                    ),
                    array(
                        'table' => 'ms_procurement|submission_deadline',
                        'type' => 'date_range',
                        'label' => 'Submission Deadline'
                    ),
                    array(
                        'table' => 'ms_procurement|auction_duration',
                        'type' => 'number_range',
                        'label' => 'Auction Duration (Hours)',
                        'min' => 1,
                        'max' => 168,
                        'step' => 1
                    ),
                    array(
                        'table' => 'ms_procurement|contract_duration',
                        'type' => 'number_range',
                        'label' => 'Contract Duration (Days)',
                        'min' => 1,
                        'max' => 1095,
                        'step' => 1
                    )
                )
            ),
            
            // Status & Progress Tracking
            array(
                'label' => 'Status & Progress',
                'filter' => array(
                    array(
                        'table' => 'ms_procurement|status_procurement|get_procurement_status',
                        'type' => 'dropdown',
                        'label' => 'Procurement Status'
                    ),
                    array(
                        'table' => 'ms_procurement|is_started|get_boolean_options',
                        'type' => 'dropdown',
                        'label' => 'Is Started'
                    ),
                    array(
                        'table' => 'ms_procurement|is_finished|get_boolean_options',
                        'type' => 'dropdown',
                        'label' => 'Is Finished'
                    ),
                    array(
                        'table' => 'ms_procurement|is_suspended|get_boolean_options',
                        'type' => 'dropdown',
                        'label' => 'Is Suspended'
                    ),
                    array(
                        'table' => 'ms_procurement|is_cancelled|get_boolean_options',
                        'type' => 'dropdown',
                        'label' => 'Is Cancelled'
                    ),
                    array(
                        'table' => 'ms_procurement|winner_selected|get_boolean_options',
                        'type' => 'dropdown',
                        'label' => 'Winner Selected'
                    ),
                    array(
                        'table' => 'ms_procurement|contract_signed|get_boolean_options',
                        'type' => 'dropdown',
                        'label' => 'Contract Signed'
                    )
                )
            ),
            
            // Officials & Organization
            array(
                'label' => 'Officials & Organization',
                'filter' => array(
                    array(
                        'table' => 'ms_procurement|id_pejabat_pengadaan|get_procurement_officials',
                        'type' => 'dropdown',
                        'label' => 'Procurement Official'
                    ),
                    array(
                        'table' => 'ms_procurement|budget_holder|get_budget_holders',
                        'type' => 'dropdown',
                        'label' => 'Budget Holder'
                    ),
                    array(
                        'table' => 'ms_procurement|budget_spender|get_budget_spenders',
                        'type' => 'dropdown',
                        'label' => 'Budget Spender'
                    ),
                    array(
                        'table' => 'ms_procurement|id_division|get_divisions',
                        'type' => 'dropdown',
                        'label' => 'Division'
                    ),
                    array(
                        'table' => 'ms_procurement|committee_type|get_committee_types',
                        'type' => 'dropdown',
                        'label' => 'Committee Type'
                    )
                )
            ),
            
            // Participants & Winners
            array(
                'label' => 'Participants & Winners',
                'filter' => array(
                    array(
                        'table' => 'ms_vendor|name',
                        'type' => 'text',
                        'label' => 'Participating Vendor Name'
                    ),
                    array(
                        'table' => 'ms_vendor|vendor_status|get_vendor_status',
                        'type' => 'dropdown',
                        'label' => 'Vendor Status'
                    ),
                    array(
                        'table' => 'ms_procurement_peserta|is_winner|get_boolean_options',
                        'type' => 'dropdown',
                        'label' => 'Is Winner'
                    ),
                    array(
                        'table' => 'ms_procurement|total_participants',
                        'type' => 'number_range',
                        'label' => 'Number of Participants',
                        'min' => 0,
                        'max' => 100,
                        'step' => 1
                    ),
                    array(
                        'table' => 'tr_assessment_point|total_score',
                        'type' => 'number_range',
                        'label' => 'Vendor Assessment Score',
                        'min' => 0,
                        'max' => 100,
                        'step' => 1
                    ),
                    array(
                        'table' => 'ms_vendor|kualifikasi|get_kualifikasi',
                        'type' => 'dropdown',
                        'label' => 'Vendor Qualification'
                    )
                )
            ),
            
            // Goods & Services Details
            array(
                'label' => 'Goods & Services',
                'filter' => array(
                    array(
                        'table' => 'ms_procurement_barang|item_name',
                        'type' => 'fulltext',
                        'label' => 'Item/Service Name'
                    ),
                    array(
                        'table' => 'ms_procurement_barang|specification',
                        'type' => 'fulltext',
                        'label' => 'Specification'
                    ),
                    array(
                        'table' => 'ms_procurement_barang|quantity',
                        'type' => 'number_range',
                        'label' => 'Quantity',
                        'min' => 1,
                        'max' => 10000,
                        'step' => 1
                    ),
                    array(
                        'table' => 'ms_procurement_barang|unit_price',
                        'type' => 'number_range',
                        'label' => 'Unit Price (IDR)',
                        'min' => 0,
                        'max' => 10000000,
                        'step' => 1000,
                        'format' => 'currency'
                    ),
                    array(
                        'table' => 'ms_material|category|get_material_categories',
                        'type' => 'dropdown',
                        'label' => 'Material Category'
                    ),
                    array(
                        'table' => 'ms_procurement|delivery_location',
                        'type' => 'text',
                        'label' => 'Delivery Location'
                    )
                )
            ),
            
            // Advanced Filters
            array(
                'label' => 'Advanced Search',
                'filter' => array(
                    array(
                        'table' => 'ms_procurement|search_keywords',
                        'type' => 'fulltext',
                        'label' => 'Advanced Keyword Search',
                        'description' => 'Search across all text fields including descriptions, specifications, remarks'
                    ),
                    array(
                        'table' => 'ms_procurement|risk_level|get_risk_levels',
                        'type' => 'dropdown',
                        'label' => 'Risk Level'
                    ),
                    array(
                        'table' => 'ms_procurement|complexity_level|get_complexity_levels',
                        'type' => 'dropdown',
                        'label' => 'Complexity Level'
                    ),
                    array(
                        'table' => 'ms_procurement|priority_level|get_priority_levels',
                        'type' => 'dropdown',
                        'label' => 'Priority Level'
                    ),
                    array(
                        'table' => 'ms_procurement|created_date',
                        'type' => 'date_range',
                        'label' => 'Creation Date'
                    ),
                    array(
                        'table' => 'ms_procurement|last_modified',
                        'type' => 'date_range',
                        'label' => 'Last Modified'
                    )
                )
            )
        );
    }
    
    /**
     * Enhanced index method with comprehensive filtering
     */
    public function index() {
        $search = $this->input->get('q');
        $page = '';
        $post = $this->input->post();
        $per_page = 10;
        $sort = $this->utility->generateSort(array('ms_procurement.name', 'auction_date', 'work_area'));
        
        // Get filter configuration
        $filter_config = $this->get_enhanced_filter_config();
        
        // Apply enhanced filtering
        $filter_data = $this->session->userdata('filter')[$this->uri->uri_string()];
        $data['auction_list'] = $this->am->get_enhanced_auction_list($search, $sort, $page, $per_page, TRUE, $filter_data);
        
        // Generate enhanced filter UI
        $data['filter_list'] = $this->filter_enhanced->group_filter_post($filter_config);
        
        // Enhanced pagination with filter awareness
        $data['pagination'] = $this->utility->generate_page('auction', $sort, $per_page, 
            $this->am->get_enhanced_auction_list($search, $sort, '', '', FALSE, $filter_data));
        $data['sort'] = $sort;
        
        // Filter statistics
        $data['filter_stats'] = $this->get_filter_statistics($filter_data);
        
        // Load views
        $layout['menu'] = $this->am->get_auction_list();
        $layout['content'] = $this->load->view('auction/enhanced_content', $data, TRUE);
        $layout['script'] = $this->load->view('auction/enhanced_content_js', $data, TRUE);
        $item['header'] = $this->load->view('auction/header', $this->session->userdata('admin'), TRUE);
        $item['content'] = $this->load->view('dashboard', $layout, TRUE);
        
        $this->load->view('template', $item);
    }
    
    /**
     * AJAX endpoint for autocomplete functionality
     */
    public function autocomplete_filter() {
        $field = $this->input->post('field');
        $term = $this->input->post('term');
        $limit = $this->input->post('limit', 10);
        
        $suggestions = $this->filter_enhanced->get_autocomplete_suggestions($field, $term, $limit);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($suggestions));
    }
    
    /**
     * AJAX endpoint for real-time filter preview
     */
    public function filter_preview() {
        $filter_data = $this->input->post('filter');
        
        // Get base query for counting
        $base_query = $this->am->get_base_query();
        
        // Get statistics
        $stats = $this->filter_enhanced->get_filter_statistics($filter_data, $base_query);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($stats));
    }
    
    /**
     * Save filter preset
     */
    public function save_filter_preset() {
        $user_id = $this->session->userdata('admin')['id'];
        $preset_name = $this->input->post('preset_name');
        $filter_data = $this->input->post('filter_data');
        $is_public = $this->input->post('is_public', 0);
        
        $data = array(
            'user_id' => $user_id,
            'preset_name' => $preset_name,
            'filter_data' => json_encode($filter_data),
            'is_public' => $is_public,
            'module' => 'auction',
            'created_date' => date('Y-m-d H:i:s')
        );
        
        $result = $this->am->save_filter_preset($data);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('success' => $result)));
    }
    
    /**
     * Load saved filter presets
     */
    public function load_filter_presets() {
        $user_id = $this->session->userdata('admin')['id'];
        $presets = $this->am->get_filter_presets($user_id, 'auction');
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($presets));
    }
    
    /**
     * Export filtered results
     */
    public function export_filtered($format = 'excel') {
        $filter_data = $this->session->userdata('filter')[$this->uri->uri_string()];
        $search = $this->input->get('q');
        $sort = $this->utility->generateSort(array('ms_procurement.name', 'auction_date', 'work_area'));
        
        // Get all filtered data (no pagination)
        $data = $this->am->get_enhanced_auction_list($search, $sort, '', '', FALSE, $filter_data);
        
        if ($format == 'excel') {
            $this->export_to_excel($data);
        } elseif ($format == 'csv') {
            $this->export_to_csv($data);
        } elseif ($format == 'pdf') {
            $this->export_to_pdf($data);
        }
    }
    
    /**
     * Track filter usage for analytics
     */
    public function track_filter_usage() {
        $filters = $this->input->post('filters');
        $user_id = $this->session->userdata('admin')['id'];
        
        foreach ($filters as $filter_field) {
            $data = array(
                'user_id' => $user_id,
                'filter_field' => $filter_field,
                'module' => 'auction',
                'usage_date' => date('Y-m-d H:i:s'),
                'session_id' => session_id()
            );
            
            $this->am->track_filter_usage($data);
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('success' => true)));
    }
    
    /**
     * Get filter statistics and analytics
     */
    private function get_filter_statistics($filter_data) {
        if (empty($filter_data)) {
            return array(
                'total_filters' => 0,
                'has_filters' => false,
                'estimated_results' => $this->am->get_total_auctions()
            );
        }
        
        $base_query = $this->am->get_base_query();
        $stats = $this->filter_enhanced->get_filter_statistics($filter_data, $base_query);
        
        return array(
            'total_filters' => count($filter_data),
            'has_filters' => true,
            'estimated_results' => $stats['expected_results'],
            'filter_applied' => $stats['filter_applied']
        );
    }
    
    // ==============================================
    // DROPDOWN DATA METHODS FOR ENHANCED FILTERS
    // ==============================================
    
    public function get_work_areas() {
        return array(
            'kantor_pusat' => 'Head Office',
            'site_office' => 'Site Office',
            'regional_office' => 'Regional Office',
            'project_site' => 'Project Site'
        );
    }
    
    public function get_procurement_categories() {
        return array(
            'goods' => 'Goods',
            'services' => 'Services', 
            'construction' => 'Construction',
            'consulting' => 'Consulting Services',
            'maintenance' => 'Maintenance Services'
        );
    }
    
    public function get_contract_types() {
        return array(
            'lump_sum' => 'Lump Sum',
            'unit_price' => 'Unit Price',
            'cost_plus' => 'Cost Plus',
            'time_material' => 'Time & Material',
            'fixed_price' => 'Fixed Price'
        );
    }
    
    public function get_budget_years() {
        $current_year = date('Y');
        $years = array();
        for ($i = $current_year - 5; $i <= $current_year + 2; $i++) {
            $years[$i] = $i;
        }
        return $years;
    }
    
    public function get_budget_sources() {
        return array(
            'company_budget' => 'Company Budget',
            'project_budget' => 'Project Budget', 
            'operational_budget' => 'Operational Budget',
            'capex_budget' => 'CAPEX Budget',
            'opex_budget' => 'OPEX Budget',
            'contingency_budget' => 'Contingency Budget'
        );
    }
    
    public function get_procurement_status() {
        return array(
            '0' => 'Draft',
            '1' => 'Published',
            '2' => 'Registration Open',
            '3' => 'Registration Closed',
            '4' => 'Bidding Open',
            '5' => 'Bidding Closed',
            '6' => 'Under Evaluation',
            '7' => 'Winner Selected',
            '8' => 'Contract Negotiation',
            '9' => 'Contract Signed',
            '10' => 'Completed',
            '11' => 'Cancelled',
            '12' => 'Suspended'
        );
    }
    
    public function get_boolean_options() {
        return array(
            '1' => 'Yes',
            '0' => 'No'
        );
    }
    
    public function get_procurement_officials() {
        $officials = $this->am->get_pejabat();
        return $officials;
    }
    
    public function get_budget_holders() {
        $holders = $this->am->get_budget_holder();
        return $holders;
    }
    
    public function get_budget_spenders() {
        $spenders = $this->am->get_budget_spender();
        return $spenders;
    }
    
    public function get_divisions() {
        return $this->CI->db->select('id, name')
                           ->where('del', 0)
                           ->get('ms_division')
                           ->result_array();
    }
    
    public function get_committee_types() {
        return array(
            'panitia_pengadaan' => 'Procurement Committee',
            'pokja_pemilihan' => 'Selection Working Group',
            'pokja_teknis' => 'Technical Working Group',
            'tim_ahli' => 'Expert Team'
        );
    }
    
    public function get_vendor_status() {
        return array(
            '0' => 'Pending',
            '1' => 'Active',
            '2' => 'Verified',
            '3' => 'Suspended',
            '4' => 'Blacklisted'
        );
    }
    
    public function get_material_categories() {
        return $this->CI->db->select('id, name')
                           ->where('del', 0)
                           ->get('tb_material_category')
                           ->result_array();
    }
    
    public function get_risk_levels() {
        return array(
            'low' => 'Low Risk',
            'medium' => 'Medium Risk',
            'high' => 'High Risk',
            'critical' => 'Critical Risk'
        );
    }
    
    public function get_complexity_levels() {
        return array(
            'simple' => 'Simple',
            'moderate' => 'Moderate', 
            'complex' => 'Complex',
            'very_complex' => 'Very Complex'
        );
    }
    
    public function get_priority_levels() {
        return array(
            'low' => 'Low Priority',
            'normal' => 'Normal Priority',
            'high' => 'High Priority',
            'urgent' => 'Urgent',
            'critical' => 'Critical'
        );
    }
    
    // ==============================================
    // EXPORT FUNCTIONALITY
    // ==============================================
    
    private function export_to_excel($data) {
        $this->load->library('excel');
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        
        // Set headers
        $headers = array('Package Name', 'Auction Date', 'Location', 'Status', 'HPS Value', 'Contract Value', 'Winner');
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'1', $header);
            $col++;
        }
        
        // Set data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A'.$row, $item['name']);
            $sheet->setCellValue('B'.$row, $item['auction_date']);
            $sheet->setCellValue('C'.$row, $item['work_area']);
            $sheet->setCellValue('D'.$row, $item['status_procurement']);
            $sheet->setCellValue('E'.$row, $item['hps_value']);
            $sheet->setCellValue('F'.$row, $item['contract_value']);
            $sheet->setCellValue('G'.$row, $item['winner_name']);
            $row++;
        }
        
        $filename = 'auction_filtered_export_'.date('Y-m-d_H-i-s').'.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
    
    private function export_to_csv($data) {
        $filename = 'auction_filtered_export_'.date('Y-m-d_H-i-s').'.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, array('Package Name', 'Auction Date', 'Location', 'Status', 'HPS Value', 'Contract Value', 'Winner'));
        
        // Data
        foreach ($data as $item) {
            fputcsv($output, array(
                $item['name'],
                $item['auction_date'],
                $item['work_area'],
                $item['status_procurement'],
                $item['hps_value'],
                $item['contract_value'],
                $item['winner_name']
            ));
        }
        
        fclose($output);
    }
    
    private function export_to_pdf($data) {
        $this->load->library('dompdf_lib');
        
        $html = '<h1>Auction Export Report</h1>';
        $html .= '<table border="1" cellspacing="0" cellpadding="5">';
        $html .= '<thead><tr><th>Package Name</th><th>Auction Date</th><th>Location</th><th>Status</th><th>HPS Value</th><th>Winner</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach ($data as $item) {
            $html .= '<tr>';
            $html .= '<td>'.htmlspecialchars($item['name']).'</td>';
            $html .= '<td>'.$item['auction_date'].'</td>';
            $html .= '<td>'.$item['work_area'].'</td>';
            $html .= '<td>'.$item['status_procurement'].'</td>';
            $html .= '<td>'.number_format($item['hps_value']).'</td>';
            $html .= '<td>'.htmlspecialchars($item['winner_name']).'</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        
        $filename = 'auction_filtered_export_'.date('Y-m-d_H-i-s');
        $this->dompdf_lib->generate_pdf($html, $filename);
    }
    
    // ==============================================
    // ENHANCED SEARCH METHODS (extend existing)
    // ==============================================
    
    /**
     * Advanced search with full-text capabilities
     */
    public function advanced_search() {
        $search_term = $this->input->post('search_term');
        $search_options = $this->input->post('search_options', array());
        
        $results = $this->am->perform_advanced_search($search_term, $search_options);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($results));
    }
    
    /**
     * Get filter field suggestions based on user input patterns
     */
    public function get_filter_suggestions() {
        $user_id = $this->session->userdata('admin')['id'];
        $suggestions = $this->am->get_filter_usage_suggestions($user_id, 'auction');
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($suggestions));
    }
} 