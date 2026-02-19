;(function($, window, document, undefined){
	
	var pluginName 	= 'dataFilter';

	function filterButton(id, value){
		if(value==null) value = "Filter";
			html = "<a href='#' class='button tableButton filterbtn'><span class='icon'><i class='fa-fa-sliders'></i></span>"+value+"</a>";
		return html;
	}

	function Plugin(element, options){

		defaults 	= {
			object : null,
			init:function(el){
				return true;
			},
			targetButton: $(this),
			generateButton: function(el){
				$(el).append(filterButton('filter', 'Filter'));
			},
			callback: function(){
				return true;
			}
		};
		_thisDataFilter = this;

		this.element = element;

		this.options = $.extend( {}, defaults, options) ;


		this._defaults = defaults;
		this._name = pluginName;
		
		this.init();
		
	}

	Plugin.prototype = {
		init : function(){
			
			target = this.options.init(this.element);
			
			this.options.generateButton(this.options.targetButton);

			this.options.callback(this.element);
		},

		/**
		Generate HTML table
		**/

		destroy : function(el){
			// el.empty();
		},

	};

	$.fn[ pluginName ] = function ( options ) {

       	this.each(function() {
                if ( !$.data( this, "plugin_" + pluginName ) ) {
                        $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
                }
        });
        
        // chain jQuery functions
        return this;
    };

})( jQuery, window, document )

