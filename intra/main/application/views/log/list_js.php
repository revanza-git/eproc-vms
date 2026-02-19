<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};
	var _xhr;
	$.ajax({
					url: '<?php echo site_url('master/kurs/formFilter')?>',
					async: false,
					dataType: 'json',
					success:function(xhr){
						_xhr = xhr;
					}
				})
	var table = $('#tableGenerator').tableGenerator({
		url: '<?php echo site_url('log/getData'); ?>',
		data: dataPost,
		
		headers: [
			{
				"key"	: "name",
				"value"	: "Aktivitas"
			},{
				"key"	: "symbol",
				"value"	: "Tanggal"
			}
		],
		
		columnDefs : [
		// {
		// 	renderCell: function(data, row, key, el){
		// 		var html = '';
		// 		html +=editButton(site_url+"master/kurs/edit/"+data[2].value, data[2].value);
		// 		html +=deleteButton(site_url+"master/kurs/remove/"+data[2].value, data[2].value);
				
		// 		return html;
		// 	},
		// 	target : [2]
		// }
		],
		
		additionFeature: function(el){
			// el.append(insertButton(site_url+"master/kurs/insert"));
		},
		finish: function(){
			var edit = $('.buttonEdit').modal({
				render : function(el, data){
					_self = edit;

					data.onSuccess = function(){
						
						table.data('plugin_tableGenerator').fetchData();
						
					};
					data.isReset = false;
					
					$(el).form(data).data('form');

				}
			});

			var del = $('.buttonDelete').modal({
				header: 'Hapus Data',
				render : function(el, data){
					_self = edit;
					el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin menghapus data?<span><div class="form"></div><div>');
					data.onSuccess = function(){

						$(del).data('modal').close();

						table.data('plugin_tableGenerator').fetchData();
						
					};
					data.isReset = true;
					$('.form', el).form(data).data('form');
				}
			});
		},

		filter: {
			wrapper: $('.contentWrap'),
			data : {
				data: _xhr
			}
		}
	});
	var add = $('.buttonAdd').modal({
		render : function(el, data){
			data.onSuccess = function(){
				table.data('plugin_tableGenerator').fetchData();
			}
			$(el).form(data);
		}
	});
});


</script>