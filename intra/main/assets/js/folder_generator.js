;(function($, window, document, undefined){
	var pluginName 	= 'folder';

	function Plugin(element, options){ 
		defaults 	= {
			url	: null,
			method : 'POST', 
			limit : [25, 50, 75, 100],
			pagination: true,
			data : {},
			filter:{
				wrapper : null,
				data : []
			},
			init: function(){

			},
			rightClickMenu: [{
				icon : 'trash',
				label: 'Hapus',
				class: 'buttonDelete',
				href:''
			},{
				icon : 'cog',
				label: 'Edit',
				class: 'buttonEdit',
				href:''
			}],
			dataRightClick: function(key, btn, value){
				return btn;
			},
			renderContent: function(el, value, key){
				return value;
			},
			callbackFunctionRightClick: function(){
				return true;
			},
			finish: function(el){
				return true;
			}
		};
		
		var _thisFolder = this;

		this.element = element;

		this.options = $.extend( {}, defaults, options) ;
		

		this._defaults = defaults;
		this._name = pluginName;
		this.data = {};

		this.init();
		this.finish();

		$('.showLimit',element).change(function(e){

			e.preventDefault();
			if(_thisFolder.data.totalData < (_thisFolder.pagination.current * $(this).val())) {
				_thisFolder.options.data.page = Math.ceil(_thisFolder.data.totalData / $(this).val());
			}
			
			_thisFolder.fetchData();
		});

		
		$('.buttonFilter',element).on('click', function(){
			var __id = $(element).context.id;
			$('.filterBlock.close[data-role="'+__id+'"]').toggleClass('close');
			$('.filter-overlay').toggleClass('filter-overlay-close');
		});
		$view = $('.view');
		$('.switch').on('click', function(e){

			if ($(this).hasClass('box-switch')) {

				$view.removeClass('view-list');
				$view.addClass('view-box');

				$view.parent().removeClass('col-12');
				$view.parent().addClass('col-4');

			} else if ($(this).hasClass('list-switch')) {

				$view.removeClass('view-box');
				$view.addClass('view-list');

				$view.parent().removeClass('col-4');
				$view.parent().addClass('col-12');
			}
		})
		
		return this;
	}

	Plugin.prototype = {
		init : function(){
			$(this.element).addClass('folderGenerator');
			this.generateFolder();
			this.fetchData();

			this.options.additionFeature($('.folderAddition', this.element));
			
			this.finish(this.element);
			return this;
		},

		/**
		Generate HTML table
		**/

		generateFolder : function( ){

			var html = '<div class="folderSection folderHeader">'+ ((this.options.limit!=false) ? this.showLimit() : '') + this.generateAddition() +'</div><div class="folderWrapper">';
			

			html += '</div>';
			html += '<div class="folderSection">' + this.initOffset() +  ((this.options.pagination!=false) ? this.initPagination() : '') +  '</div>';
			$(this.element).html(html);
		},

		generateAddition : function( ){
			var _filter ='';
			if(typeof this.options.filter.data.data!='undefined'){

				_filter = '<button class="button is-default tableButton buttonFilter"><i style="line-height: 27px;" class="fas fa-filter"></i>&nbsp;<span class="icon-text">Filter</span></a>';
				_filter +='<button class="button is-rounded box-switch switch"><span class="icon"><i class="fas fa-th"></i></span></button>';
				_filter +='<button class="button is-rounded list-switch switch"><span class="icon"><i class="fas fa-list"></i></span></button>';
				this.filter.wrapper = this.options.filter.wrapper;
				this.filter.table = this.element;
				this.filter.element = this;
				this.filter.options = this.options;
				this.filter.generateFilter();

			}
			html = "<div class='col col-6 folderAddition '>"+_filter+"</div>";
			return html;
		},
		filter: {
			generateFilter: function(){



				if(typeof this.options.filter.data.data!='undefined'){

					html		= "<div class='filter-overlay filter-overlay-close'></div><div class='filterBlock close' data-role='"+this.table.id+"'><div class='filterHeader'><div class='headerStart'><h3><span class='icon'><i class='fa fa-sliders'></i></span>Filter</h3></div><div class='headerEnd'><a class='button closeFilter'><i class='fa fa-times'></i></a></div></div><div class='formFilter'></div></div>";

					this.wrapper.append(html);

					var __id = this.element.element.id;

					

					$('.filterBlock[data-role="'+__id+'"] .closeFilter ').on('click', function(){

						$('.filterBlock[data-role="'+__id+'"]').toggleClass('close');

						$('.filter-overlay').toggleClass('filter-overlay-close');

					})

					this.generateForm();

				}

			},
			generateForm: function(){
				var __this = this.element;
				var __self = this;
				$('.filterBlock[data-role="'+this.table.id+'"] .formFilter').form(this.options.filter.data.data);
				this.addButton($('.filterBlock[data-role="'+this.table.id+'"] .formFilter fieldset.form-group'));

				$('.filterBlock[data-role="'+__self.table.id+'"] .btn-filter').on('click', function(){
					// console.log(__this.element.id);
					$('.filterBlock').removeClass('filterblock-open');
					$('.filterBlock').addClass('filterblock-close');
					$('.filter-overlay').toggleClass('filter-overlay-close');
					// formData = new FormData($('.filterBlock[data-role="'+__this.element.id+'"] form')[0]);
					var formData = $('.filterBlock[data-role="'+__this.element.id+'"] form .hiddenFilter').serializeObject();
					__this.options.data = formData;
					__this.fetchData();

					// __this.fetchData();
				})

				$('.btn-reset').on('click', function(){
					__self.reset($('.filterBlock[data-role="'+__self.table.id+'"] .formFilter'));
				});
			},
			addButton: function(el){
				__data = this.options.filter.data.data.form;
				
				$.each(el, function(key, value){
					$(value).append('<div class="filterChooseGroup"><button class="button is-primary btnAddFilter" type="button"><i class="fa fa-plus"></i>&nbsp; Tambah</button><ul class="filterChooseWrapper"></ul></div>');

					$( value).on('click', '.btnAddFilter',function(){

						__field = __data[key];
						var _value = real_value = '';

						switch (__field.type){
							case 'text' : 

								_value = real_value = $('.form-control',value).val(); 
								$('.filterChooseWrapper',value).append('<li class="filterChooseList" style="background-color: '+randomColor()+'; color: #fff"><span>'+_value+'</span><input type="hidden" class="hiddenFilter" name="filter['+__field.field+'][]" value="'+real_value+'">&nbsp;<i class="fa fa-close removeFilterChoose"></i></li>');
								break;
							case 'dropdown' : 
								real_value = $('.form-control',value).val();
								_value = __field.source[real_value];
								$('.filterChooseWrapper',value).append('<li class="filterChooseList" style="background-color: '+randomColor()+'; color: #fff"><span>'+_value+'</span><input type="hidden" class="hiddenFilter" name="filter['+__field.field+'][]" value="'+real_value+'">&nbsp;<i class="fa fa-close removeFilterChoose"></i></li>');
								break;
							case 'date_range' :
								var __input = '';
								var prefix = '';
								__lto = $('.filterChooseWrapper .filterChooseList', value).length;
								var _value='';
								$.each(__field.field, function(keyField, valueField){
									real_value = $('.form-control[name="'+valueField+'"]',value).val(); 
									__input +='<input type="hidden" class="hiddenFilter" name="filter['+__field.name+']['+__lto+']['+valueField+']" value="'+real_value+'">';

									if(keyField == (__field.field.length - 1)) prefix = '';
									else prefix = ' - ';
									if(real_value!='')
										_value+=defaultDate(real_value)+prefix;
									else
										_value+='&#8734;'+prefix;
								})
								
								$('.filterChooseWrapper',value).append('<li class="filterChooseList" style="background-color: '+randomColor()+'; color: #fff"><span>'+_value+'</span>'+__input+'&nbsp;<i class="fa fa-close removeFilterChoose"></i></li>');
								break;
							case 'date_range_lifetime' :
								var __input = '';
								var prefix = '';
								__lto = $('.filterChooseWrapper .filterChooseList', value).length;
								__lifetime = $('.form-control.lifetime',value); 
								var _value='';
								if(__lifetime.is(':checked')){
									_value = 'Seumur Hidup';
									__input +='<input type="hidden" class="hiddenFilter" name="filter['+__field.name+']['+__lto+']" value="lifetime">';
	
								}else{
									$.each(__field.field, function(keyField, valueField){
										real_value = $('.form-control[name="'+valueField+'"]',value).val(); 
										__input +='<input type="hidden" class="hiddenFilter" name="filter['+__field.name+']['+__lto+']['+valueField+']" value="'+real_value+'">';
		
										if(keyField == (__field.field.length - 1)) prefix = '';
										else prefix = ' - ';
										if(real_value!='')
											_value+=defaultDate(real_value)+prefix;
										else
											_value+='&#8734;'+prefix;
									})
								}
								
								
								$('.filterChooseWrapper',value).append('<li class="filterChooseList" style="background-color: '+randomColor()+'; color: #fff"><span>'+_value+'</span>'+__input+'&nbsp;<i class="fa fa-close removeFilterChoose"></i></li>');
								break;
						}

						$('.filterChooseWrapper .removeFilterChoose' ,value).on('click', function(e){
							$(this).closest('.filterChooseList').remove();
						})						
						
					})
				})
			}, 
			reset: function(el){
				// console.log($('.filterChooseList', el));
				$('.filterChooseList', el).remove();
			}
		},
		right_click: {

			generateWrapper : function(){
				_this = this;
				var __elem = $('.folderHeader');
				__elem.append('<ul id="click-menu-content" class="click-menu"></ul>');
				$('.view-item').contextmenu(function(e) {
					e.stopPropagation();
					// console.log($(this));
					// console.log()
					_thisFolder.options.rightClickMenu = _thisFolder.options.dataRightClick($(this).data('key'), _thisFolder.options.rightClickMenu, _thisFolder.initRow());

					$('#click-menu-content').addClass('item-click');
					$('#click-menu-content').slideDown(150);
					$('#click-menu-content').css('display','block');

					// $('#click-menu-content').offset({left:e.pageX, top:e.pageY});
					 _this.generateMenu($('#click-menu-content',__elem));


					var li_count = $('#click-menu-content li').length;
					var li_len = $('#click-menu-content li').outerHeight();
					var ul_len = parseInt(li_len * li_count);
					var half_height = parseInt($(document).height() / 2 - 100);

					if(e.pageY > half_height){
						$('#click-menu-content').offset({left:e.pageX, top:e.pageY - ul_len});
						// console.log(li_count);
					}
					else{
						$('#click-menu-content').offset({left:e.pageX, top:e.pageY});
						// console.log('dari atas');
					}

					$(window).scroll(function() {    
     
				        var scroll = $(window).scrollTop();

				        if (scroll >= 12) {
				            $('#click-menu-content').removeClass('item-click');
							$('#click-menu-content').slideUp(150);
							$('#click-menu-content').css('display','none');
				        } else {
				            $(".bottom").removeClass("active");
				        }
				    });
					

					// console.log();
		        });
		        $(document).on('click', function(e) {
					e.stopPropagation();
					$('#click-menu-content').css('display','none');
		        });
		        $(document).contextmenu(function(e){
		        	e.stopPropagation();
		        	$('#click-menu-content').css('display','none');
		        })
		       
			},
			generateMenu : function(el){
				el.html('');
				var dataMenu = _thisFolder.options.rightClickMenu;

				$.each(dataMenu, function(key, value){
					
					el.append('<li class="'+value.class+'" href="'+value.href+'"><i class="fas fa-'+value.icon+'"></i>'+value.label+'</li>')
				})
				_thisFolder.options.callbackFunctionRightClick();
			}
		},
		/**
		Generate Show Limit
		**/
		

		showLimit : function( ){
			var limit 		= this.options.limit;
			var limitLength = limit.length;
			var html 	=  "<div class='col col-6'>"

			html 		+= "Menampilkan ";
			html 		+= "<select class='showLimit' name='showLimit'>";
			for(i = 0; i<limitLength; i++){
				html	+= "<option value='"+limit[i]+"'>"+limit[i]+"</option>";
			}
			html 		+= "</select>";
			html 		+= " entry";
			html 		+= "</div>";

			return html;
		},

		getShowLimit : function( ){
			showLimit = $('.showLimit', this.element).val();

			this.options.data.limit = showLimit;
		},

		fetchData : function( ){
			_thisFolder = this;
			if(this.options.limit){
				this.getShowLimit();
			}
			
			$.ajax({

				method 	: this.options.method,
				url		: this.options.url,
				data 	: this.options.data,
				dataType: 'json',
				async	: false

			}).done(function(data, textStatus, jqXHR){
				try {
					if (jqXHR && typeof jqXHR.getResponseHeader === 'function') {
						var nextToken = jqXHR.getResponseHeader('X-CSRF-Token');
						if (nextToken && typeof csrf_hash !== 'undefined') {
							csrf_hash = nextToken;
						}
					}
				} catch (e) {}
				_thisFolder.data = data;
				

				_thisFolder.destroy($('.folderWrapper ',_thisFolder.element));
				_thisFolder.renderFolder($('.folderWrapper ',_thisFolder.element));
				
			}).fail(function( jqXHR, textStatus ) {
			  	// console.log( "Request failed: " + textStatus );
			});
			return this;
		},

		pagination : {
			wrapper		:   null,
			suffix		: 	"<ul class='pagination'>",
			first	 	: 	"<li class='paginate_button '><a href='#'><i class='fas fa-angle-double-left'></i></a></li>",
			previous 	: 	"<li class='paginate_button previous'><a href='#'><i class='fas fa-angle-left'></i></a></li>",
			list		: 	"",
			current		: 	1,
			max_page	: 	5,
			next	 	: 	"<li class='paginate_button next'><a href='#'><i class='fas fa-angle-right'></i></a></li>",
			last 		: 	"<li class='paginate_button '><a href='#'><i class='fas fa-angle-double-right'></i></a></li>",
			prefix		: 	"</ul>",
			assignPage	: 	function(el){
								$.each( el, function(key, value){
									$(this).prop('id_page', parseInt( $(this).text() ) );

								})
							},
			changePage	: 	function(parent, el, obj){
								
								listParent = $(el).parent();
								
								if($(listParent).hasClass('first')){
									this.current = 1;
								}else if($(listParent).hasClass('last')){
									this.current = this.totalPage(obj);
								}else if($(listParent).hasClass('previous')){
									this.current--;
								}else if($(listParent).hasClass('next')){
									this.current++;
								}else if($(listParent).hasClass('list')){
									this.current = el.id_page;
								}
								
								parent.empty();

								obj.options.data.page = this.current;
								
								obj.fetchData()

								this.render(parent, obj);
								
								
							},	
			totalPage 	: 	function(obj){
								return Math.ceil( obj.data.totalData / obj.options.data.limit);
							},
			render 		: 	function(el, obj){
								
								parent = el;
								// el = $('.table-pagination', el) ;
								var start 	= 1;
								var end 	= this.totalPage(obj);

								this.list 	= "";
								
								
								if(this.totalPage(obj) > this.max_page){
									half_page = Math.ceil(this.max_page/2);

									if( half_page >= this.current ){
										end 		= this.max_page;
									}else{
										start		= this.current - ( half_page - 1 );

										_max_page 	= this.current + ( half_page - 1 );

										if(this.totalPage(obj) > _max_page){
											end 	= _max_page;
										}

										if( this.current >= ( this.totalPage(obj) - (half_page - 1) ) ){
											start 	= this.totalPage(obj) - ( half_page + 1 );
										}
										
									}
									
								}
								
								if(_thisFolder.data.totalData < (this.current * _thisFolder.data.limit)) {
									this.current = this.totalPage(obj);
								}
								
								for(i = start; i<= end; i++){

									_class_current = (this.current == i) ? 'active ' : '';

									this.list += "<li class='paginate_button list " + _class_current + "'><a href='#'>" + i + "</a></li>";
								}

								previous = (this.current == 1) ? '' : this.previous;
								next = (this.current == this.totalPage(obj)) ? '' : this.next;
								
								html = this.suffix + this.first + previous;
								html += this.list;
								html += next + this.last + this.prefix;

								$(el).html(html);

								var page_list = $('.paginate_button a',el);
								this.assignPage(page_list);
								
								$(page_list).bind({
									'click'	: function(e){
										e.preventDefault();
										
										obj.pagination.changePage(el,  $(this).get(0) , obj);
									}
								});

							}
		},

		initPagination : function(){

			html		= "<div class='container-footer table-pagination'></div>";
			return html;			
		},

		/**
		Menampilkan per offset info
		**/
		initOffset : function(){
			html ="";
			// html		= "<div class='table-offset table-6'></div>";
			return html;
		},

		offset : function(){
			var limit 	= this.data.limit;
			var start	= ( (this.pagination.current - 1) * limit ) + 1;
			var total 	= this.data.totalData;
			var end 	= ( (this.pagination.current * limit) > total) ? total  :  (this.pagination.current * limit);
			
			html 		= "Menampilkan data ke-" + start + " sampai ke-" + end + " data dari " + total + " data ";
			$('.table-offset',this).html(html);
		},

		/**
		Menampilkan table
		**/
		renderFolder : function(el){
			this.options.init();

			var body 	= this.renderBody(el);
			if(this.options.pagination){
				this.pagination.render( $('.table-pagination', this.element) , this);
			}
			
			// this.pagination.render( this.element , this );

			this.offset();
			return this;
		},

		
		/**
		Menampilkan data dari db
		**/
		renderBody : function(el){
			var html = "<ul class='folderWrapperChild'></ul>";
			el.append(html);
			this.renderRow(el.find('.folderWrapperChild'));
			return this;
		},

		initRow : function(){
			var data 	= this.data.data;
			
			var row 	= {};
			$.each(data, function(key, value){
				
				var column = [];
				
				$.each(value, function(keyColumn, valueColumn){
					column[column.length] = {
												key 	: keyColumn,
												value 	: valueColumn
											};
				});

				row[key]	= column;
			});
			
			return row;
		},

		renderRow : function(el){
			row = this.initRow();

			if($.isEmptyObject(row)){
				html = '<p class="noData">Tidak ada Data</p>';
				el.append(html);
			}else{
				$.each(row, function(key, value){
						if (typeof value[9] !== 'undefined') {
							var id_ = value[9].value;
						}else{
							var id_ = 0;
						}
					html = '<li class="folder_panel col-12" ><div class="view view-list"><div class="view-item" oncontextmenu="return false;" data-key="'+key+'"> <img src="'+base_url+'assets/images/folder.png" alt=""><img src="'+base_url+'assets/images/down-arrow-inside-a-circle.png" alt=""><span class="icon-info is-warning tooltip note'+id_+'">';
					el.append(html);
					panel = $(el.find('.folder_panel .view-item')[key]);
					_thisFolder.generateContent(panel, value, key);
				})

			}
			return this;
		},

		column : function(row){
			return;
		},
		/*Render Cell*/
		generateContent : function(el, data, key){
			_render = _thisFolder.options.renderContent(el, data, key);

			$(el).append(_render);
			this.finish(el);
			return this;
		},

		destroy : function(el){
			el.empty();
		},
		finish: function(el){
			this.options.finish($(el));
			this.right_click.generateWrapper();
			return this;

		}
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

