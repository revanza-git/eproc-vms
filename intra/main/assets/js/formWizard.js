;(function($, window, document, undefined){
	
	var pluginName 	= 'formWizard';

	function Plugin(element, options){

		defaults 	= {
			data : {},
			wrapper: null,
			currentPosition: 1,
			onNext: function(){
				return true;
			},
			onPrev: function(){
				return true;
			},
			onSuccess: function(){

			},
			onSubmit: function(){
				return true;
			},
			onSuccessSubmit: function(){

			},
			url: null,
			formFill: null
		};
		var self = this;

		this.element = element;

		this.options = $.extend( {}, defaults, options) ;

		this._defaults = defaults;
		this._name = pluginName;
		this.input = {};
		this.init();
		
		$('.btn-next').on('click', function(){
			self.nextStep();
		});
		$('.btn-prev').on('click', function(){
			self.prevStep();
		});
		
		// BUTTON TO
		$('.btn-to').on('click', function(){
			self.toPage();
		});
		$('.btn-back').on('click', function(){
			self.toForm();
		});
		$('.btn-save').on('click', function(){
			self.submit();
		});

	}

	Plugin.prototype = {
		init: function(){
			wrapper = this.generateWrapper();
			this.generateHeader(wrapper);
			this.generateContent(wrapper);
			this.onSuccess();
		},
		generateWrapper: function(){
			if(this.options.wrapper==null){
				this.options.wrapper = this.element;
			}
			var __url;
			if(this.options.url!=null) __url = this.options.url;
			$(this.element).html('<div class="formWizard"><form enctype="multitype/form-data" action="'+__url+'"></form></div>');
			return $('.formWizard form',this.element);
		},
		generateHeader: function(wrapper){
			$(wrapper).append('<div class="wizard-steps clearfix"></div>');
			var headWrapper = this.options.headerWrapper = $('.wizard-steps', this.options.wrapper);
			headWrapper.append('<ul class="step"></ul>');
			var i = 1;
			_this = this;
			$.each(this.options.data.step, function(key, value){
				$('.step',headWrapper).append('<li data-target="#step'+i+'" id="stepHeader'+i+'">'+value.label+'</span></li>');
				if(i==_this.options.currentPosition) {
					$('.step li#stepHeader'+i,headWrapper).addClass('active');
					$('.step li#stepHeader'+i+' .badge',headWrapper).addClass('badge-info');
				}
				i++;
			});
		},
		generateContent: function(wrapper){
			$(wrapper).append('<div class="wizard-content clearfix"></div>');
			var i = 1;
			_this = this;

			$.each(this.options.data.step, function(key, value){
				
				$('.wizard-content', wrapper).append('<div class="wizard-panel" id="step'+i+'"></div>');
				$('.wizard-content #step'+i, wrapper).form({
					form:value.form,
					button:value.button,
					formWrap:false
				});
				$('.wizard-content #step'+i, wrapper).hide();
				if(i==_this.options.currentPosition) {
					$('.wizard-content #step'+i, wrapper).show();
				}
				i++;
				
			});
		},
		// generateButton: function(wrapper){
		// 	$(wrapper).append('<div class="wizard-button clearfix"></div>');
		// 	var i = 1;
		// 	var _this = this;

			
		// 	this.reset();
		// 	$('.btn-submit').hide();
		
		// },

		// toResiko: function(){
		// 	var __curr = this.options.currentPosition;
		// 	if(getObjectSize(this.options.data.step)==this.options.currentPosition){
		// 		if(this.options.onNext()){
		// 			this.onSubmit();
		// 		}
		// 	}else{
		// 		if(this.options.onNext()){
		// 			this.options.currentPosition++;
		// 			this.reset();
		// 		}
		// 	}
			
		// },
		toPage: function(){
			var _toPage = $('.btn-to').attr('id');
			var _save = $('button.btn-save');
				// if(_toPage == 'finish'){
				// 	this.onSubmit();
				// 	console.log('finish!!');
				// }
			// $('button.btn-save').on('click',function() {
			// 	alert($(this).attr('id'));
			// })

			if (_toPage == 'finish') {
				this.onSubmit();
			}

			var __curr 	= this.options.currentPosition;

			console.log('ini current Position : '+__curr);
			console.log('ini current data step : '+getObjectSize(this.options.data.step));

			if(getObjectSize(this.options.data.step)==this.options.currentPosition){
				if(this.options.onNext()){
					this.onSubmit();
				}
			}else{
				if(this.options.onNext()){
					// Convert _toPage to number and validate
					var pageNumber = parseInt(_toPage);
					if (!isNaN(pageNumber) && pageNumber > 0) {
						this.options.currentPosition = pageNumber; //<< JUMP TO DESIRE PAGE /GET NUMBER FROM STEP ID\
					} else {
						this.options.currentPosition++;
					}
					this.reset();
				}
				console.log(_toPage);
			}
		},
		toForm: function(){
			if(this.options.onPrev()){
				this.options.currentPosition = 2;
				this.reset();
			}
		},
		submit: function(){
			this.onSubmit();
			$(resiko).data('modal').close();
			folder.data('plugin_folder').fetchData();
			location.reload();
			console.log(resiko);
		},
		nextStep: function(){
			var __curr = this.options.currentPosition;

			var _toPage = $('#step'+__curr+' .btn-to').attr('id');

			if (_toPage == 'finish') {
				this.onSubmit();
			}

			console.log('ini current Position : '+__curr);
			console.log('ini current data step : '+getObjectSize(this.options.data.step));

			if(getObjectSize(this.options.data.step)==this.options.currentPosition){
				if(this.options.onNext()){
					this.onSubmit();
				}
			}else{
				if(this.options.onNext()){
					this.options.currentPosition++;
					this.reset();
				}
			}
		},
		prevStep: function(){
			var __curr = this.options.currentPosition;
			var _toPage = $('#step'+__curr+' .btn-prev').attr('id');
			
			// console.log('>>>>>> btn prev id', _toPage);
			if(this.options.onPrev()){
				// Check if _toPage is a valid number
				var pageNumber = parseInt(_toPage);
				if (isNaN(pageNumber) || _toPage === undefined || _toPage === null || _toPage === '') {
					// console.log('>>>>>> btn prev id undefined or invalid');
					this.options.currentPosition--;
				} else {
					// console.log('>>>>>> btn prev id defined');
					this.options.currentPosition = pageNumber;
				}
				
				this.reset();
			}
		},
		onSubmit: function(){
			this.options.onSubmit();
			_this = this;
			formData = new FormData($('form',this.element)[0]);
			$.ajax({
				async: false,
				url : _this.options.url,
				method : 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',	
				success: function(xhr){
					if(xhr.status=='success'){
						// $('.wizard-content #step'+_this.options.currentPosition, wrapper).data('form').generateSuccess(xhr);
						console.log('Ini dari plugin'+$('.wizard-content #step'+_this.options.currentPosition, wrapper).data('form'));
					}
					_this.options.onSuccessSubmit();
				},
				error: function(xhr){
					
				},
				complete: function(xhr){
					

				}
			})
		},
		reset: function(){
			$('.step li',this.options.wrapper).removeClass('active');
			$('.wizard-content .wizard-panel').hide();
			$('.field-error',this.options.wrapper).removeClass('field-error');
			$('.form-error').removeClass('form-error');
			$('.error-help',this.options.wrapper).remove();
			$('.step li#stepHeader'+this.options.currentPosition).addClass('active');
			$('.step li#stepHeader'+this.options.currentPosition+' .badge').addClass('badge-info');
			$('.wizard-content #step'+this.options.currentPosition).show();
			
		},
		onSuccess: function(){
			this.options.onSuccess();
		}
	};

	$.fn[ pluginName ] = function ( options ) {

       	this.each(function() {
                if ( !$.data( this, pluginName ) ) {
                        $.data( this,pluginName, new Plugin( this, options ) );
                }
        });
        // chain jQuery functions

        return this;

    };

    

})( jQuery, window, document )

