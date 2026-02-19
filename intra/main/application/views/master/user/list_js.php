<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'a.id',
		sort: 'desc'
	};
	var _xhr;
	$.ajax({
					url: '<?php echo site_url('master/user/formFilter')?>',
					async: false,
					dataType: 'json',
					success:function(xhr){
						_xhr = xhr;
					}
				})
				

	var folder = $('#tableGenerator').folder({
		url: '<?php echo site_url('master/user/getData/'.(isset($id) ? $id : '')); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			_id = value[key][3].value;

			btn = [{
				icon : 'cog',
				label: 'Edit',
				class: 'buttonEdit',
				href:site_url+"master/user/edit/"+_id
			},{
				icon : 'trash',
				label: 'Hapus',
				class: 'buttonDelete',
				href:site_url+"master/user/remove/"+_id
			}];
			return btn;
		},
		callbackFunctionRightClick: function(){
			var edit = $('.buttonEdit').modal({
				render : function(el, data){
					_self = edit;

					data.onSuccess = function(){
						$(edit).data('modal').close();
						folder.data('plugin_folder').fetchData();
						
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

						folder.data('plugin_folder').fetchData();
						
					};
					data.isReset = true;
					$('.form', el).form(data).data('form');
				}
			});

			var aktif = $('.buttonAktifkan').modal({
				header: 'Aktifkan Data?',
				render : function(el, data){
					_self = edit;
					el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin mengaktifkan data?<span><div class="form"></div><div>');
					data.onSuccess = function(){

						$(aktif).data('modal').close();

						folder.data('plugin_tableGenerator').fetchData();
						
					};
					data.isReset = true;
					$('.form', el).form(data).data('form');
				}
			});
			console.log(aktif)
			var batal = $('.buttonBatalkan').modal({
				header: 'Batalkan Data?',
				render : function(el, data){
					_self = edit;
					el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin membatalkan data?<span><div class="form"></div><div>');
					data.onSuccess = function(){

						$(batal).data('modal').close();

						folder.data('plugin_tableGenerator').fetchData();
						
					};
					data.isReset = true;
					$('.form', el).form(data).data('form');
				}
			});
		},

		renderContent: function(el, value, key){
			html = '';
			html += '<div class="caption"><p>'+value[0].value+'</p><p>'+value[1].value+'</p><p>'+value[2].value+'</p></div>';
			console.log(folder);
			return html;
		},
		additionFeature: function(el){
			el.prepend(insertButton(site_url+"master/user/insert/<?php echo isset($id) ? $id : ''; ?>"));
		},
		finish: function(){
     		

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
				$(add).data('modal').close();
				folder.data('plugin_folder').fetchData();
			}
			$(el).form(data);
		}
	});
});


</script>