<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};
	var _xhr;
	$.ajax({
					url: '<?php echo site_url('fkpbj/formFilter')?>',
					async: false,
					dataType: 'json',
					success:function(xhr){
						_xhr = xhr;
					}
				})

	var folder = $('#tableGenerator').folder({
		url: '<?php echo site_url('fkpbj/getData/'.$id . '/' . $id_division . '/' . $id_fppbj . '/' . $year); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			_id = value[key][4].value;

			btn = [{
				icon : 'search',
				label: 'Lihat Data',
				class: 'buttonView',
				href:site_url+"fkpbj/getSingleData/"+_id
			},{
				icon : 'thumbs-up',
				label: 'Setujui Data',
				class: 'buttonApprove',
				href:site_url+"fkpbj/approve/"+_id
			},{
				icon : 'cog',
				label: 'Edit',
				class: 'buttonEdit',
				href:site_url+"fkpbj/edit/"+_id
			},{
				icon : 'trash',
				label: 'Hapus',
				class: 'buttonDelete',
				href:site_url+"fkpbj/remove/"+_id
			}];
			return btn;
		},
		callbackFunctionRightClick: function(){

			var view = $('.buttonView').modal({
				header: 'Lihat Data',
				render : function(el, data){
					_self = view;

					data.onSuccess = function(){
						$(view).data('modal').close();
						folder.data('plugin_tableGenerator').fetchData();
					};
					data.isReset = false;
					
					$(el).form(data).data('form');

				}
			});
			
			var edit = $('.buttonEdit').modal({
				render : function(el, data){
					_self = edit;

					data.onSuccess = function(){
						
						folder.data('plugin_tableGenerator').fetchData();
						
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

						folder.data('plugin_tableGenerator').fetchData();
						
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
			var status = '';
			var badge = '';
			if (value[4].value = 0) {
				status = "Menunggu";
			}
			if (value[3].value == "2" && value[5].value == "3") {
				status = 'FKPBJ ini telah update di perencanaan pengadaan B/J';
				badge = 'success';
			}
			console.log(status);
			html += '<div class="caption"><p>'+value[0].value+'</p><p>'+value[1].value+'</p><p><span class="badge is-'+badge+'">'+status+'</span></p></div>';
			
			return html;
		},

		additionFeature: function(el){
			el.prepend(insertButton(site_url+"fkpbj/insert/<?php echo $id;?>"));
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
				folder.data('plugin_tableGenerator').fetchData();
						$(add).data('modal').close();
			}
			$(el).form(data);
		}
	});
});


</script>