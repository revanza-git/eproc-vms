;(function($, window, document, undefined){
	
	var pluginName 	= 'tabs';

	function Plugin(element, options){

		defaults 	= {
			url : null,
		};
		self = this;

		this.element = element;

		this.options = $.extend( {}, defaults, options) ;

		this._defaults = defaults;
		this._name = pluginName;
		this.input = {};
		this.init();
		


	}

	Plugin.prototype = {
		
	};

	$.fn[ pluginName ] = function ( options ) {

       	this.each(function() {
                if ( !$.data( this, ]pluginName ) ) {
                        $.data( this,pluginName, new Plugin( this, options ) );
                }
        });
        // chain jQuery functions

        return this;

    };

    

})( jQuery, window, document )

