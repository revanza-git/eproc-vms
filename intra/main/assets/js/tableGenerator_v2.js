;(function($, window, document, undefined){
	var pluginName 	= 'tableGenerator';

	function Plugin(element, options){ 
		defaults 	= {
			url	: null,
			method : 'POST', 
			limit : [10, 25, 50, 100],
			pagination: true,
			data : {},
			headers : [],
			optHeaders : [],
			columnDefs : [],
			filter:{
				wrapper : null,
				data : []
			},
			init: function(){

			},
			renderCell: function(data, value, key){
				return value;
			},
			additionFeature : function(element){
				return "";
			},
			finish: function(el){
				return true;
			}
		};
		
		var _thisTableGenerator = this;

		this.element = element;

		this.options = $.extend( {}, defaults, options) ;
		

		this._defaults = defaults;
		this._name = pluginName;
		this.data = {};

		this.init();
		this.finish();

		$('.showLimit',element).change(function(e){

			e.preventDefault();
			if(_thisTableGenerator.data.totalData < (_thisTableGenerator.pagination.current * $(this).val())) {
				_thisTableGenerator.options.data.page = Math.ceil(_thisTableGenerator.data.totalData / $(this).val());
			}
			
			_thisTableGenerator.fetchData();
		});

		$(element).on( 'click' , 'th.header-table.sorting' , function(e){
			var _sort = '';
			var _id = $(this).attr('id');	

			var _id = _id.replace(/header-/g,"");

			if($(this).hasClass('sort')){
				$(this).addClass('sort-asc');
				_thisTableGenerator.options.data.order = _id;
				_thisTableGenerator.options.data.sort = 'ASC';


			}else if($(this).hasClass('sort-asc')){
				$(this).addClass('sort-desc');
				_thisTableGenerator.options.data.order = _id;
				_thisTableGenerator.options.data.sort = 'DESC';

			}else if($(this).hasClass('sort-desc')){
				_thisTableGenerator.options.data.order = null;
				_thisTableGenerator.options.data.sort = 'ASC';
			}
						_thisTableGenerator.fetchData();
		});
		$('.buttonFilter',element).on('click', function(){
			var __id = $(element).context.id;
			$('.filterBlock.close[data-role="'+__id+'"]').toggleClass('close');
			$('.filter-overlay').toggleClass('filter-overlay-close');
		});
		return this;
	}

	Plugin.prototype = {
		init : function(){

			this.generateTable();
			this.fetchData();

			this.options.additionFeature($('.tableAddition', this.element));

			this.finish(this.element);
			return this;
		},

		/**
		Generate HTML table
		**/

		generateTable : function( ){

			var html = '<div class="tableRow">'+ ((this.options.limit!=false) ? this.showLimit() : '') + this.generateAddition() +'</div><table class="table">';
			

			html += '</table>';
			html += '<div class="tableRow">' + this.initOffset() +  ((this.options.pagination!=false) ? this.initPagination() : '') +  '</div>';
			$(this.element).html(html);
		},

		generateAddition : function( ){
			var _filter ='';
			if(typeof this.options.filter.data.data!='undefined'){

				_filter = '<button class="button tableButton buttonFilter"><span class="icon"><i class="fa fa-sliders"></i></span>Filter</a>';
				this.filter.wrapper = this.options.filter.wrapper;
				this.filter.table = this.element;
				this.filter.element = this;
				this.filter.options = this.options;
				this.filter.generateFilter();

			}
			html = "<div class='table-6 tableAddition '>"+_filter+"</div>";
			return html;
		},
		filter: {
			generateFilter: function(){



				if(typeof this.options.filter.data.data!='undefined'){

					html		= "<div class='filter-overlay filter-overlay-close'></div><div class='filterBlock close' data-role='"+this.table.id+"'><div class='filterHeader'><h3><span class='icon'><i class='fa fa-sliders'></i></span>Filter<a class='button closeFilter'><i class='fa fa-times'></i></a></h3></div><div class='formFilter'></div></div>";

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
					$(value).append('<div class="filterChooseGroup"><button class="button is-primary btnAddFilter" type="button"><span class="icon"><i class="fa fa-plus"></i></span>Tambah</button><ul class="filterChooseWrapper"></ul></div>');

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
		/**
		Generate Show Limit
		**/
		

		showLimit : function( ){
			var limit 		= this.options.limit;
			var limitLength = limit.length;
			var html 	=  "<div class='table-6'>"

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
			_thisTableGenerator = this;
			if(this.options.limit){
				this.getShowLimit();
			}
			if (typeof this._csrfRetry === 'undefined') {
				this._csrfRetry = false;
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
				_thisTableGenerator._csrfRetry = false;
				_thisTableGenerator.data = data;
				

				_thisTableGenerator.destroy($('table.table ',_thisTableGenerator.element));
				_thisTableGenerator.renderTable($('table.table ',_thisTableGenerator.element));
				
			}).fail(function( jqXHR, textStatus ) {
				if (jqXHR && jqXHR.status === 403 && !_thisTableGenerator._csrfRetry) {
					try {
						if (jqXHR.getResponseHeader) {
							var nextToken = jqXHR.getResponseHeader('X-CSRF-Token');
							if (nextToken) {
								csrf_hash = nextToken;
							}
						}
					} catch (e) {}
					_thisTableGenerator._csrfRetry = true;
					_thisTableGenerator.fetchData();
					return;
				}
				try {
					if (jqXHR && jqXHR.status >= 500 && jqXHR.responseText) {
						console.log("Server response (" + jqXHR.status + "): " + jqXHR.responseText.substr(0, 1200));
					}
				} catch (e) {}
			  	console.log( "Request failed: " + textStatus );
			});
			return this;
		},

		pagination : {
			wrapper		:   null,
			suffix		: 	"<ul>",
			first	 	: 	"<li class='paginate_button first'><a href='#'></a></li>",
			previous 	: 	"<li class='paginate_button previous'><a href='#'></a></li>",
			list		: 	"",
			current		: 	1,
			max_page	: 	5,
			next	 	: 	"<li class='paginate_button next'><a href='#'></a></li>",
			last 		: 	"<li class='paginate_button last'><a href='#'></a></li>",
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
								
								if(_thisTableGenerator.data.totalData < (this.current * _thisTableGenerator.data.limit)) {
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

			html		= "<div class='table-pagination table-6'></div>";
			return html;			
		},

		/**
		Menampilkan per offset info
		**/
		initOffset : function(){

			html		= "<div class='table-offset table-6'></div>";
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
		renderTable : function(el){
			this.options.init();
			var header 	= this.renderHeader(el);
			var body 	= this.renderBody(el);
			if(this.options.pagination){
				this.pagination.render( $('.table-pagination', this.element) , this);
			}
			
			// this.pagination.render( this.element , this );

			this.offset();
			return this;
		},

		/**
		Menampilkan data header
		**/
		initHeader: function(){
			var html = "<thead><tr></tr></thead>";
			return html;
		},

		addHeader : function(data){
			return data.concat(this.options.optHeaders);
		},

		renderHeader : function(el){
			this.destroy(el);
			el.append(this.initHeader());

			$.each( this.options.headers , function(key, value){
				html = "";
				_class = '';
				if( value.sort !== false ){

					if(_thisTableGenerator.data.order == value.key){
						switch(_thisTableGenerator.options.data.sort){
							case 'ASC' 	: _class = 'sort-asc'; 	break;
							case 'DESC' : _class = 'sort-desc'; break;
							default 	: _class = 'sort'; 		break;
						}
					}else{
						_class = 'sort';
					}	
					_class += ' sorting ';
				}
					

				html += "<th class='header-table " + _class + "' index='" + key + "' id='header-" + value.key + "'><span>" + value.value + "</span></th>";
				$('thead tr',el).append(html);
			});
		},

		emptyHeader: function( el ){
			$('thead',el).empty();
		},

		/**
		Menampilkan data dari db
		**/
		renderBody : function(el){
			var html = "<tbody></tbody>";
			el.append(html);
			this.renderRow(el.find('tbody'));
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
				_span = this.options.headers.length;
				
				html = '<tr><td class="noData" colspan="'+_span+'">Tidak ada Data</td></tr>';
				el.append(html);
			}else{
				$.each(row, function(key, value){
					html = '<tr></tr>';
					el.append(html);

					tr = $(el.find('tr')[key]);

					// console.log($(el.find('tr')));
					tr.addClass('row').prop('index', key);
					
					_thisTableGenerator.initCell(tr, value);
				})

			}
			return this;
		},

		column : function(row){
			return;
		},
		/*Render Cell*/
		initCell : function(el, data){
			
			$.each(this.options.headers, function(key, value){

				html = '<td></td>';
				$(el).append(html);	
				
				cellValue = (is_empty(data[key])) ? '' : data[key].value;

				$.each(_thisTableGenerator.options.columnDefs, function(keyCol,valueCol){

					if(valueCol.target.length>1){
						$.each(valueCol.target, function(keyTarget, valueTarget){

							if(key == valueTarget){
								_render = valueCol.renderCell(data, cellValue, key, el.find('td')[key]);
								
								if(typeof _render !== 'undefined'){
									cellValue = _render;
								}

							}
						});
					}else{
						if(key == valueCol.target){
							_render = valueCol.renderCell(data, cellValue, key, el.find('td')[key]);
							
							if(typeof _render !== 'undefined'){
								cellValue = _render;
							}

						}
					}
				})

				$(el.find('td')[key]).append(cellValue);

				
			});
			this.finish(el);
			return this;
		},

		destroy : function(el){
			el.empty();
		},
		finish: function(el){
			this.options.finish($(el));
			
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

