<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};
	var _xhr;
	$.ajax({
			url: '<?php echo site_url('fppbj/formFilter')?>',
			async: false,
			dataType: 'json',
			success:function(xhr){
				_xhr = xhr;
			}
		})
	var table = $('#tableGenerator').tableGenerator({
		url: '<?php echo site_url('fppbj/getData'); ?>',
		data: dataPost,
		
		headers: [
			{
				"key"	: "nama_pengadaan",
				"value"	: "Nama Pengadaan"
			},{
				"key"	: "idr_anggaran",
				"value"	: "Anggaran dalam Rupiah"
			},{
				"key"	: "year_anggaran",
				"value"	: "Tahun Anggaran"
			},{
				"key"	: "action",
				"value"	: "Action",
				"sort"	: false
			}
		],
		
		columnDefs : [{
			renderCell: function(data, row, key, el){
				var html = '';
				html +=editButton(site_url+"fppbj/edit/"+data[3].value, data[3].value);
				html +=deleteButton(site_url+"fppbj/remove/"+data[3].value, data[3].value);
				html +='<a href="'+site_url+"fppbj/export/"+data[3].value+'"class="button is-primary"><span class="icon"><i class="fas fa-file-export"></i></span>Export ke PDF</a>';

				return html;
			},
			target : [3]
		}],
		
		additionFeature: function(el){
			el.append(insertButton(site_url+"fppbj/insert"));
		},
		finish: function(){
			var edit = $('.buttonEdit').modal({
				render : function(el, data){
					_self = edit;

					data.onSuccess = function(){
						
						$(edit).data('modal').close();
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

						// location.reload();

						$(del).data('modal').close();

						folder.data('plugin_folderGenerator').fetchData();
						
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
				
				$(add).data('modal').close();
				table.data('plugin_tableGenerator').fetchData();
			}
			$(el).form(data);
			
			$('.modal [name="pengadaan"]').on('change', function(){
				// get parent value
				var pengadaan = $(this).val();

				// Change option on select based on parent
				if (pengadaan == "barang") {
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>Non Stock</option>");
				}else if(pengadaan == "jasa"){
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				}else{
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
				}
			})
		}
	});
});


// $(function() {
// 	if($('.badge').ha9sClass('fp3_reject') == true) {
// 		$('.fp3_reject').addClass('tooltip').append('<span class="tooltiptext reject">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto mollitia fugiat non corporis maxime vitae facere nisi quisquam praesentium! Accusamus corporis ad quidem doloremque rerum dolorem officiis maiores nisi libero!</span></span>');
// 	}
// })

function reject_notif(pesan){
	if($('.badge').ha9sClass('fppbj_reject') == true) {
		$('.fp3_reject').addClass('tooltip').append('<span class="tooltiptext reject">'+pesan+'</span></span>');
	}
}
// , 'jasa_konstruksi' => 'Jasa Konstruksi', 'jasa_konsultasi' => 'Jasa Konsultasi', 'jasa_lainnya' => 'Jasa Lainnya'
</script>