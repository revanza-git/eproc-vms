<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};
	var _xhr;
	var folder;
	var initializeFolder = function(){
		folder = $('#tableGenerator').folder({
		url: '<?php echo site_url('pemaketan/getData/'); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			_id 		= value[key][2].value;
			urlDivision = '<?php echo base_url('pemaketan/division');?>/'+_id;

			btn = [{
				icon : 'search',
				label: 'Lihat Data',
				class: 'buttonView',
			}];
			return btn;
		},
		callbackFunctionRightClick: function(){
			var view = $('.buttonView').click(function(){
				$(location).attr('href',urlDivision);
			});
		},

		renderContent: function(el, value, key){
			html = '';
			html += '<div class="caption"><p>'+value[0].value+'</p><p><b>'+value[1].value+'</b> Item(s)</p></div>';
			console.log(folder);
			return html;
		},
		additionFeature: function(el){
			// el.prepend(insertButton(site_url+"pemaketan/insert/<?php echo $id;?>"));
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
	};

	$.ajax({
		url: '<?php echo site_url('pemaketan/formFilter')?>',
		dataType: 'json'
	}).done(function(xhr){
		_xhr = xhr;
		initializeFolder();
	}).fail(function(){
		_xhr = {};
		initializeFolder();
	});

	var add = $('.buttonAdd').modal({
		render : function(el, data){
			data.onSuccess = function(){
				folder.data('plugin_tableGenerator').fetchData();

						$(add).data('modal').close();
			}

			$(el).form(data);
			
			$('.modal [name="pengadaan"]').on('change', function(){
				// get parent value
				var pengadaan = $(this).val();

				// Change option on select based on parent
				if (pengadaan == "barang") {
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value=''>Pilih Salah Satu</option><option value=''>Pilih Salah Satu</option><option value=''>Pilih Salah Satu</option>");
				}else if(pengadaan == "jasa"){
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				}else{
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
				}
			});
		}
	});
});


</script>
