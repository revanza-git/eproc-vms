/**
 * Enhanced Filter System for VMS
 * Provides advanced filtering capabilities with autocomplete, quick filters, and better UX
 * Compatible with PHP 5.6 and MySQL 5.7.44 environment
 */
class EnhancedFilter {
    constructor(options = {}) {
        this.options = Object.assign({
            container: '.filterWrapper',
            formSelector: '.filterForm',
            autocompleteDelay: 300,
            cacheTimeout: 300000, // 5 minutes
            maxSuggestions: 10,
            previewDelay: 500,
            enableAnalytics: true,
            baseUrl: typeof base_url !== 'undefined' ? base_url : ''
        }, options);
        
        this.cache = new Map();
        this.activeFilters = new Map();
        this.autocompleteTimers = new Map();
        this.previewTimer = null;
        this.analyticsTimer = null;
        this.savedPresets = [];
        this.filterStats = {
            totalResults: 0,
            filteredResults: 0,
            activeFiltersCount: 0
        };
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeAutocomplete();
        this.initializeQuickFilters();
        this.initializeDatePresets();
        this.initializeNumberRanges();
        this.initializeRangeSliders();
        this.loadSavedFilters();
        this.setupFilterStatistics();
        this.initializeTooltips();
        this.setupKeyboardShortcuts();
    }
    
    bindEvents() {
        // Enhanced filter toggle
        $(document).on('click', '.filterBtn', (e) => {
            e.preventDefault();
            this.toggleFilterPanel();
        });
        
        // Clear all filters
        $(document).on('click', '.clearAllFilters', (e) => {
            e.preventDefault();
            this.clearAllFilters();
        });
        
        // Save current filter as preset
        $(document).on('click', '.saveFilterPreset', (e) => {
            e.preventDefault();
            this.showSaveFilterDialog();
        });
        
        // Export filtered results
        $(document).on('click', '.exportFiltered', (e) => {
            e.preventDefault();
            this.exportFilteredResults($(e.target).data('format'));
        });
        
        // Filter form submission
        $(document).on('submit', `${this.options.container} form`, (e) => {
            e.preventDefault();
            this.applyFilters();
        });
        
        // Real-time filter updates
        $(document).on('input change', '.filter-input', (e) => {
            this.updateFilterCount();
            this.updateActiveFiltersDisplay();
            this.scheduleFilterPreview();
            if (this.options.enableAnalytics) {
                this.trackFilterUsage();
            }
        });
        
        // Group form toggle
        $(document).on('click', '.groupFormHeader', (e) => {
            const $content = $(e.currentTarget).next('.groupFormContent');
            $content.slideToggle(200);
            $(e.currentTarget).find('i').toggleClass('fa-sort-desc fa-sort-up');
        });
        
        // Add/Remove filter fields
        $(document).on('click', '.addFilterGroup', (e) => {
            e.preventDefault();
            this.addFilterField($(e.target).closest('.groupFieldBlock'));
        });
        
        $(document).on('click', '.removeFilterGroup', (e) => {
            e.preventDefault();
            this.removeFilterField($(e.target).closest('.groupFieldBlock'));
        });
        
        // Date/Number range specific handlers
        $(document).on('click', '.addFilterGroupDate', (e) => {
            e.preventDefault();
            this.addDateRangeField($(e.target).closest('.groupFieldBlock'));
        });
        
        $(document).on('click', '.removeFilterGroupDate', (e) => {
            e.preventDefault();
            this.removeDateRangeField($(e.target).closest('.groupFieldBlock'));
        });
        
        $(document).on('click', '.addFilterNumberRange', (e) => {
            e.preventDefault();
            this.addNumberRangeField($(e.target).closest('.groupFieldBlock'));
        });
        
        $(document).on('click', '.removeFilterNumberRange', (e) => {
            e.preventDefault();
            this.removeNumberRangeField($(e.target).closest('.groupFieldBlock'));
        });
        
        // Close filter panel
        $(document).on('click', '.filterBtnClose, .filterWrapperOverlay', (e) => {
            e.preventDefault();
            this.closeFilterPanel();
        });
        
        // Remove active filter tags
        $(document).on('click', '.filter-tag .remove', (e) => {
            e.preventDefault();
            const field = $(e.target).closest('.filter-tag').data('field');
            this.removeActiveFilter(field);
        });
    }
    
    initializeAutocomplete() {
        $('.filter-input[data-autocomplete="true"]').each((index, element) => {
            const $input = $(element);
            const field = $input.data('field');
            
            $input.autocomplete({
                source: (request, response) => {
                    this.getAutocompleteSuggestions(field, request.term)
                        .then(suggestions => response(suggestions))
                        .catch(() => response([]));
                },
                minLength: 2,
                delay: this.options.autocompleteDelay,
                select: (event, ui) => {
                    $input.val(ui.item.value);
                    this.addActiveFilter(field, ui.item.value, ui.item.label);
                    return false;
                },
                focus: (event, ui) => {
                    $input.val(ui.item.value);
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append(`<div class="autocomplete-item">
                        <strong>${item.label}</strong>
                        <small class="text-muted">${item.value}</small>
                    </div>`)
                    .appendTo(ul);
            };
        });
    }
    
    initializeQuickFilters() {
        $('.quick-filter-btn').on('click', (e) => {
            e.preventDefault();
            const $btn = $(e.target);
            const filterType = $btn.data('filter-type');
            const filterValue = $btn.data('filter-value');
            
            this.applyQuickFilter(filterType, filterValue);
        });
        
        // Add predefined quick filters
        this.addQuickFilterButtons();
    }
    
    addQuickFilterButtons() {
        const quickFilters = [
            { label: 'Hari Ini', type: 'date_preset', value: 'today', icon: 'fa-calendar' },
            { label: 'Minggu Ini', type: 'date_preset', value: 'week', icon: 'fa-calendar-week' },
            { label: 'Bulan Ini', type: 'date_preset', value: 'month', icon: 'fa-calendar-alt' },
            { label: 'Auction Aktif', type: 'status_preset', value: 'active', icon: 'fa-play' },
            { label: 'Selesai', type: 'status_preset', value: 'finished', icon: 'fa-check' },
            { label: 'Budget > 1M', type: 'budget_preset', value: 'high', icon: 'fa-dollar-sign' }
        ];
        
        const $container = $('.quick-filter-buttons');
        quickFilters.forEach(filter => {
            const $btn = $(`<button type="button" class="quick-filter-btn btn btn-sm btn-outline-primary mr-2 mb-2" 
                            data-filter-type="${filter.type}" data-filter-value="${filter.value}">
                <i class="fa ${filter.icon}"></i> ${filter.label}
            </button>`);
            $container.append($btn);
        });
    }
    
    initializeDatePresets() {
        $('.date-preset').on('click', (e) => {
            e.preventDefault();
            const preset = $(e.target).data('preset');
            const $dateGroup = $(e.target).closest('.enhanced-date-filter');
            
            this.applyDatePreset($dateGroup, preset);
        });
    }
    
    initializeNumberRanges() {
        $('.range-slider').each((index, element) => {
            const $slider = $(element);
            const min = parseInt($slider.data('min')) || 0;
            const max = parseInt($slider.data('max')) || 100;
            const $wrapper = $slider.closest('.number-range-wrapper');
            const $minInput = $wrapper.find('input[name*="start_value"]');
            const $maxInput = $wrapper.find('input[name*="end_value"]');
            
            $slider.slider({
                range: true,
                min: min,
                max: max,
                values: [
                    parseInt($minInput.val()) || min,
                    parseInt($maxInput.val()) || max
                ],
                slide: (event, ui) => {
                    $minInput.val(ui.values[0]);
                    $maxInput.val(ui.values[1]);
                },
                change: (event, ui) => {
                    this.updateFilterCount();
                }
            });
            
            // Update slider when inputs change
            $minInput.add($maxInput).on('input', () => {
                const minVal = parseInt($minInput.val()) || min;
                const maxVal = parseInt($maxInput.val()) || max;
                $slider.slider('values', [minVal, maxVal]);
            });
        });
    }
    
    applyDatePreset($dateGroup, preset) {
        const today = new Date();
        let startDate, endDate;
        
        switch(preset) {
            case 'today':
                startDate = endDate = this.formatDate(today);
                break;
            case 'week':
                const weekStart = new Date(today);
                weekStart.setDate(today.getDate() - today.getDay());
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekStart.getDate() + 6);
                startDate = this.formatDate(weekStart);
                endDate = this.formatDate(weekEnd);
                break;
            case 'month':
                const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                startDate = this.formatDate(monthStart);
                endDate = this.formatDate(monthEnd);
                break;
            case 'quarter':
                const quarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth()/3)*3, 1);
                const quarterEnd = new Date(today.getFullYear(), Math.floor(today.getMonth()/3)*3 + 3, 0);
                startDate = this.formatDate(quarterStart);
                endDate = this.formatDate(quarterEnd);
                break;
            case 'year':
                const yearStart = new Date(today.getFullYear(), 0, 1);
                const yearEnd = new Date(today.getFullYear(), 11, 31);
                startDate = this.formatDate(yearStart);
                endDate = this.formatDate(yearEnd);
                break;
        }
        
        // Find the active date input fields - try specific group first, then fallback to general
        let activeField = $dateGroup.find('input[name*="start_date"]:visible').first().attr('name');
        
        // If no field found in specific group, try general selector
        if (!activeField) {
            activeField = $('.enhanced-date-filter:visible input[name*="start_date"]').first().attr('name');
        }
        
        if (activeField) {
            const fieldBase = activeField.replace(/\[start_date\]\[\d+\]/, '');
            $(`input[name="${fieldBase}[start_date][0]"]`).val(startDate);
            $(`input[name="${fieldBase}[end_date][0]"]`).val(endDate);
        }
        
        this.updateFilterCount();
    }
    
    formatDate(date) {
        return date.toISOString().split('T')[0];
    }
    
    addActiveFilter(field, value, label) {
        this.activeFilters.set(field, { value, label });
        this.updateActiveFiltersDisplay();
    }
    
    removeActiveFilter(field) {
        this.activeFilters.delete(field);
        this.updateActiveFiltersDisplay();
        $(`.filter-input[data-field="${field}"]`).val('');
    }
    
    updateActiveFiltersDisplay() {
        const $container = $('.active-filters-container');
        const $activeFilters = $container.find('.active-filters');
        
        $activeFilters.empty();
        this.activeFilters.forEach((filter, field) => {
            const $tag = $(`
                <span class="filter-tag" data-field="${field}">
                    ${filter.label}
                    <button type="button" class="remove-filter" data-field="${field}">×</button>
                </span>
            `);
            $activeFilters.append($tag);
        });
        
        // Update filter count badge
        $('.filter-count-badge').text(this.activeFilters.size);
    }
    
    toggleFilterPanel() {
        const $panel = $(this.options.container);
        const $overlay = $('.filterWrapperOverlay');
        
        if ($panel.is(':visible')) {
            $panel.slideUp(300);
            $overlay.fadeOut(300);
        } else {
            $panel.slideDown(300);
            $overlay.fadeIn(300);
            this.updateFilterStatistics();
        }
    }
    
    applyQuickFilter(filterType, filterValue) {
        // Clear existing filters of the same type
        $(`.filter-input[data-field="${filterType}"]`).val('');
        
        // Apply the quick filter
        if (filterType.includes('_preset')) {
            const [field, preset] = filterType.split('_preset:');
            this.applyDatePreset(field, filterValue);
        } else {
            $(`.filter-input[data-field="${filterType}"]`).val(filterValue);
        }
        
        this.applyFilters();
    }
    
    updateFilterCount() {
        const activeCount = $('.filter-input').filter(function() {
            return $(this).val() && $(this).val().trim() !== '';
        }).length;
        
        $('.filter-count').text(activeCount);
        $('.filterBtn').toggleClass('has-filters', activeCount > 0);
    }
    
    updateFilterPreview() {
        const preview = [];
        $('.filter-input').each(function() {
            const $input = $(this);
            const value = $input.val();
            if (value && value.trim() !== '') {
                const label = $input.closest('.groupFieldBlock').find('.title').first().text();
                preview.push(`${label}: ${value}`);
            }
        });
        
        $('.filter-preview').text(preview.join(', ') || 'Tidak ada filter aktif');
    }
    
    clearAllFilters() {
        $('.filter-input').val('');
        this.activeFilters.clear();
        this.updateActiveFiltersDisplay();
        this.updateFilterCount();
        this.updateFilterPreview();
        
        // Reset sliders
        $('.range-slider').each(function() {
            const $slider = $(this);
            const min = parseInt($slider.data('min')) || 0;
            const max = parseInt($slider.data('max')) || 100;
            $slider.slider('values', [min, max]);
        });
        
        // Submit to clear server-side filters
        this.applyFilters();
    }
    
    applyFilters() {
        const $form = $(`${this.options.container} form`);
        $('.filter-loading').show();
        
        // Get form data
        const formData = new FormData($form[0]);
        
        // Send AJAX request or submit form
        if (this.options.useAjax) {
            this.submitFiltersAjax(formData);
        } else {
            $form[0].submit();
        }
    }
    
    submitFiltersAjax(formData) {
        $.ajax({
            url: $form.attr('action') || window.location.href,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                // Update content area with filtered results
                if (response.html) {
                    $('.tableWrapper').html(response.html);
                }
                $('.filter-loading').hide();
                this.updateFilterStatistics();
            },
            error: () => {
                $('.filter-loading').hide();
                this.showNotification('Error applying filters', 'error');
            }
        });
    }
    
    async getAutocompleteSuggestions(field, term) {
        const cacheKey = `${field}:${term}`;
        
        // Check cache first
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.options.cacheTimeout) {
                return cached.data;
            }
        }
        
        try {
            const response = await $.ajax({
                url: base_url + 'auction/autocomplete',
                type: 'POST',
                data: { field, term, limit: this.options.maxSuggestions },
                dataType: 'json'
            });
            
            // Cache the results
            this.cache.set(cacheKey, {
                data: response,
                timestamp: Date.now()
            });
            
            return response;
        } catch (error) {
            console.error('Autocomplete error:', error);
            return [];
        }
    }
    
    showSaveFilterDialog() {
        const modal = `
            <div class="modal fade" id="saveFilterModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Simpan Filter Preset</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="saveFilterForm">
                                <div class="form-group">
                                    <label>Nama Preset</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="confirmSaveFilter">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $(modal).modal('show');
        
        $('#confirmSaveFilter').on('click', () => {
            this.saveFilterPreset();
        });
    }
    
    saveFilterPreset() {
        const formData = $('#saveFilterForm').serialize();
        
        $.ajax({
            url: base_url + 'auction/save_filter_preset',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.showNotification(response.message, 'success');
                    $('#saveFilterModal').modal('hide');
                    this.loadSavedFilters();
                } else {
                    this.showNotification(response.message, 'error');
                }
            },
            error: () => {
                this.showNotification('Error saving filter preset', 'error');
            }
        });
    }
    
    loadSavedFilters() {
        // Load saved filter presets into dropdown
        $.ajax({
            url: base_url + 'auction/get_saved_filters',
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                const $dropdown = $('.saved-filters-dropdown');
                $dropdown.empty();
                
                response.forEach(filter => {
                    const $option = $(`<option value="${filter.id}">${filter.name}</option>`);
                    $dropdown.append($option);
                });
            }
        });
    }
    
    exportFilteredResults(format) {
        const $form = $(`${this.options.container} form`);
        const formData = new FormData($form[0]);
        formData.append('format', format);
        
        // Create temporary form for download
        const $tempForm = $('<form>', {
            method: 'POST',
            action: base_url + 'auction/export',
            style: 'display: none;'
        });
        
        // Add form data as hidden inputs
        for (let [key, value] of formData.entries()) {
            $tempForm.append($('<input>', {
                type: 'hidden',
                name: key,
                value: value
            }));
        }
        
        $('body').append($tempForm);
        $tempForm.submit();
        $tempForm.remove();
        
        this.showNotification(`Exporting data in ${format.toUpperCase()} format...`, 'info');
    }
    
    updateFilterStatistics() {
        $.ajax({
            url: base_url + 'auction/get_filter_statistics',
            type: 'GET',
            dataType: 'json',
            success: (stats) => {
                $('.filter-stats-total').text(stats.total_auctions);
                $('.filter-stats-active').text(stats.active_auctions);
                $('.filter-stats-finished').text(stats.finished_auctions);
                $('.filter-stats-month').text(stats.month_auctions);
            }
        });
    }
    
    showNotification(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        const $notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show notification-toast" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 300px;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);
        
        $('body').append($notification);
        
        setTimeout(() => {
            $notification.alert('close');
        }, 5000);
    }
    
    setupFilterStatistics() {
        // Add filter usage analytics
        $(document).on('input change', '.filter-input', () => {
            this.trackFilterUsage();
        });
    }
    
    trackFilterUsage() {
        // Track which filters are used most often for analytics
        const usedFilters = [];
        $('.filter-input').each(function() {
            const $input = $(this);
            const value = $input.val();
            if (value && value.trim() !== '') {
                const field = $input.data('field') || $input.attr('name');
                usedFilters.push(field);
            }
        });
        
        // Send analytics data (debounced)
        clearTimeout(this.analyticsTimer);
        this.analyticsTimer = setTimeout(() => {
            if (usedFilters.length > 0) {
                $.ajax({
                    url: base_url + 'auction/track_filter_usage',
                    type: 'POST',
                    data: { filters: usedFilters },
                    dataType: 'json'
                });
            }
        }, 2000);
    }
}

// Enhanced date picker for Indonesian locale
$.datepicker.regional['id'] = {
    closeText: 'Tutup',
    prevText: '&#x3C;mundur',
    nextText: 'maju&#x3E;',
    currentText: 'hari ini',
    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
    monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    dayNames: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
    dayNamesShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
    dayNamesMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
    weekHeader: 'Mg',
    dateFormat: 'yy-mm-dd',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['id']);

// Number formatting for Indonesian locale
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Initialize enhanced filter when document is ready
$(document).ready(function() {
    // Initialize enhanced filter system
    window.enhancedFilter = new EnhancedFilter({
        useAjax: false, // Set to true for AJAX filtering
        container: '.filterWrapper'
    });
    
    // Enhanced filter toggle button
    $('.filterBtn').on('click', function() {
        window.enhancedFilter.toggleFilterPanel();
    });
    
    // Remove active filter tags
    $(document).on('click', '.remove-filter', function() {
        const field = $(this).data('field');
        window.enhancedFilter.removeActiveFilter(field);
    });
    
    // Initialize date pickers with Indonesian locale
    $('.calendar-input').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        regional: 'id'
    });
    
    // Initialize number inputs with formatting
    $('.currency-input').on('input', function() {
        const value = $(this).val().replace(/[^\d]/g, '');
        if (value) {
            $(this).val(formatNumber(value));
        }
    });
    
    // Enhanced form validation
    $('form').on('submit', function() {
        let isValid = true;
        
        // Validate required fields
        $(this).find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            window.enhancedFilter.showNotification('Mohon lengkapi semua field yang diperlukan', 'error');
        }
        
        return isValid;
    });
    
    // Auto-save filter state
    setInterval(() => {
        const filterState = {};
        $('.filter-input').each(function() {
            const $input = $(this);
            const value = $input.val();
            if (value && value.trim() !== '') {
                filterState[$input.attr('name')] = value;
            }
        });
        
        if (Object.keys(filterState).length > 0) {
            localStorage.setItem('vms_filter_state', JSON.stringify(filterState));
        }
    }, 30000); // Save every 30 seconds
    
    // Restore filter state on page load
    const savedState = localStorage.getItem('vms_filter_state');
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState);
            Object.keys(filterState).forEach(name => {
                $(`[name="${name}"]`).val(filterState[name]);
            });
            window.enhancedFilter.updateFilterCount();
        } catch (e) {
            console.error('Error restoring filter state:', e);
        }
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedFilter;
} 