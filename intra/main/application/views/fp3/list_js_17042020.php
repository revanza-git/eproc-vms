<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};
	var _xhr;
	$.ajax({
		url: '<?php echo site_url('fp3/formFilter')?>',
		async: false,
		dataType: 'json',
		success:function(xhr){
			_xhr = xhr;
		}
	})
	

	var folder = $('#tableGenerator').folder({
		url: '<?php echo site_url('fp3/getData/'.$id_division.'/'.$id_fppbj.'/'.$year); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			_id 		= value[key][6].value;
			_id_fppbj 	= value[key][8].value;
			is_approved = value[key][10].value;
			id_division = value[key][14].value;
			user_logged = '<?php echo $admin['id_division'] ?>';
			metode 		= value[key][2].value;
			tipe 		= value[key][15].value;
			urlpdf 		= site_url+"export/fp3/"+_id_fppbj;

			btn = [{
					icon : 'search',
					label: 'Lihat Data',
					class: 'buttonView',
					href:site_url+"fp3/getSingleData/"+_id
				}
				,{
					icon : 'file-download',
					label: 'Download FP3',
					class: 'buttonExport',
					href:site_url+"fp3/form_download_fp3/"+_id_fppbj
				}
			];

			btn_edit = [
				{
					icon : 'cog',
					label: 'Edit',
					class: 'buttonEdit',
					href:site_url+"fp3/edit/"+_id
				},{
					icon : 'trash',
					label: 'Hapus',
					class: 'buttonDelete',
					href:site_url+"fp3/remove/"+_id
				}
			];

			if (tipe == 'jasa') {

				if (is_approved == 3) {
					return btn;
				} else {
					if (id_division == user_logged) {
						return btn.concat(btn_edit);
					} else {
						return btn;
					}
				}

			} else {

				if (is_approved == 3) {
					return btn;
				} else {
					if (id_division == user_logged) {
						return btn.concat(btn_edit);
					} else {
						return btn;
					}
				}
			}
		},
		callbackFunctionRightClick: function(){
			var pdf = $('.buttonExport').modal({
				header : 'Download PDF',
				render : function(el, data){
					data.onSuccess = function(){
						$(pdf).data('modal').close();
	     				folder.data('plugin_folder').fetchData();
	     				// location.reload();
					}
					$(el).form(data);
					$('form',el).off('submit')
				}
			});
			var lampiran = $('.buttonLampiran').modal({
				header : 'Upload Lampiran',
				render : function(el, data){
					data.onSuccess = function(){
						$(lampiran).data('modal').close();
	     				folder.data('plugin_folder').fetchData();
	     				// location.reload();
					}
					$(el).form(data);
				}
			});
			var view = $('.buttonView').modal({
				header: 'Lihat Data',
				render : function(el, data){
					_self = view;

					data.onSuccess = function(){
						$(view).data('modal').close();
						folder.data('plugin_folder').fetchData();
					};
					data.isReset = false;
					
					$(el).form(data).data('form');
					
					$('.close-modal-reject').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject').removeClass('active');
		  			})
				}
			});
			var edit = $('.buttonEdit').modal({
				header:'Edit',
				render : function(el, data){
					_self = edit;

					data.onSuccess = function(){
						// $(edit).data('modal').close();
						// folder.data('plugin_folder').fetchData();
						location.reload();
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
						// $(del).data('modal').close();
						// folder.data('plugin_folder').fetchData();
						location.reload();
					};
					data.isReset = true;
					$('.form', el).form(data).data('form');
				}
			});

			var batal = $('.buttonViewBatalkan').modal({
				header: 'Batalkan Data',
				render : function(el, data){
					_self = batal;
					el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin membatalkan data?<span><div class="form"></div><div>');
					data.onSuccess = function(){
						$(batal).data('modal').close();
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
						table.data('plugin_tableGenerator').fetchData();
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

						table.data('plugin_tableGenerator').fetchData();
						
					};
					data.isReset = true;
					$('.form', el).form(data).data('form');
				}
			});
		},
		renderContent: function(el, value, key){
			console.log(value[1].value);
			var status 			= '';
			var badge 			= '';
			var is_status 		= value[5].value;
			var is_approve 		= value[10].value;
			var is_reject 		= value[11].value;
			var keterangan 		= value[12].value;
			console.log(value)
			
			if (is_status == "3" || (is_approve == "3" && is_reject == 0)) {
					status = 'FP3';
					badge = 'success';
				}

				else if (is_approve == 2 && is_reject == 0) {
					status = 'FP3 (Menunggu Ka.Dept Procurement)';
					badge = 'warning';
				}
				else if (is_approve == 1 && is_reject == 0) {
					status = 'FP3 (Menunggu Admin Pengendalian)';
					badge = 'warning';
				}
				else if (is_approve == 0 && is_reject == 0) {
					status = 'FP3 (Menunggu Ka.Dept User)';
					badge = 'warning';
				}
				else if (is_approve == 3 && is_reject == 1) {
					status = 'FP3 (Ditolak Manager Procurement)<span class="tooltiptext reject">'+keterangan+'</span>';
					badge = 'danger fp3_reject tooltip';
				}
				else if (is_approve == 2 && is_reject == 1) {
					status = 'FP3 (Ditolak Admin Pengendalian)<span class="tooltiptext reject">'+keterangan+'</span>';
					badge = 'danger fp3_reject tooltip';
				}
				else if (is_approve == 1 && is_reject == 1) {
					status = 'FP3 (Ditolak Manager User) <span class="tooltiptext reject">'+keterangan+'</span>';
					badge = 'danger fp3_reject tooltip';
				}

				else if (is_approve == "3" && is_reject == 0) {
					status = 'FP3';
					badge = 'success';
				}
				else if (is_approve == "2" && is_reject == 0) {
					status = 'FP3 (Menunggu Ka.Dept Procurement)';
					badge = 'warning';
				}
				else if (is_approve == "1" && is_reject == 0) {
					status = 'FP3 (Menunggu Admin Pengendalian)';
					badge = 'warning';
				}
				else if (is_approve == "0" && is_reject == 0) {
					status = 'FP3 (Menunggu Ka.Dept User)';
					badge = 'warning';
				}else if (is_reject == 1) {
					status = 'FP3 (FP3 Ditolak)<span class="tooltiptext reject">'+keterangan+'</span>';
					badge = 'danger fp3_reject tooltip';
				}

				if (value[0].value == 1) {
					metode_pengadaan = 'Pelelangan';
				} else if(value[0].value == 2) {
					metode_pengadaan = 'Pemilihan Langsung';
				} else if(value[0].value == 3) {
					metode_pengadaan = 'Swakelola';
				} else if(value[0].value == 4) {
					metode_pengadaan = 'Penunjukan Langsung';
				} else {
					metode_pengadaan = 'Pengadaaan Langsung';
				}

				year_anggaran = value[13].value;
			html = '';
			html += '<div class="caption"><p>'+value[1].value+'</p><p><span class="badge is-'+badge+'">'+status+'</p></div>';

 			return html;
		},

		additionFeature: function(el){
			el.prepend(insertButton(site_url+"fp3/insert/<?php echo $id;?>"));
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
				// $(add).data('modal').close();
				// folder.data('plugin_folder').fetchData();
				location.reload();
			}

			$(el).form(data);

				// TAB FUNCTION FP3
				$('#tab-intro').css('display','block');
				$('.btn-group').css('display', 'none');
				
				$('.form1').css('display', 'none');
				$('.form2').css('display', 'none');
				$('.form3').css('display', 'none');
				$('.form4').css('display', 'none');
				$('.form5').css('display', 'none');
				$('.form6').css('display', 'none');
				$('.form7').css('display', 'none');
				$('.form8').css('display', 'none');
				$('.form9').css('display', 'none');
				$('.form10').css('display', 'none');
				$('.form11').css('display', 'none');
				$('.form12').css('display', 'none');

				$('#btnUbah').click(function() {
					$('.btn-group').css('display', 'block');
					$('#formUbah').css('display','block');
					$('#tab-intro').css('display','none');
					
					$('.form1').css('display', 'block');
					$('.form2').css('display', 'block');
					$('.form3').css('display', 'block');
					$('.form4').css('display', 'block');
					$('.form5').css('display', 'block');
					$('.form6').css('display', 'block');
					$('.form7').css('display', 'block');
					$('.form8').css('display', 'block');
					$('.form9').css('display', 'block');
					$('.form10').css('display', 'block');
					$('.form11').css('display', 'block');
					$('.form12').css('display', 'block');
				});

				$('.form1 [name="id_fppbj"]').on('click',function() {
					val_id = $(this).val();
					$.ajax({
						url:'<?php echo site_url('fp3/get_data_fppbj') ?>/'+val_id,
						method: 'post',
						dataType: 'json',
						success:function(data) {
							// var data_ = JSON.parse(data);

							if (data.metode_pengadaan == 1) {
								metode_pengadaan = 'Pelelangan';
							} else if (data.metode_pengadaan == 2) {
								metode_pengadaan = 'Pemilihan Langsung';
							} else if (data.metode_pengadaan == 3) {
								metode_pengadaan = 'Swakelola';
							} else if (data.metode_pengadaan == 4) {
								metode_pengadaan = 'Penunjukan Langsung';
							} else {
								metode_pengadaan = 'Pengadaan Langsung';
							}

							$('.form3 span').empty();
							$('.form3 span').append(metode_pengadaan);

							if (data.jwpp_start != '' && data.jwpp_end != '') {
								jwpp = defaultDate(data.jwpp_start)+' sampai '+defaultDate(data.jwpp_start);
							} else {
								jwpp = '-';
							}
							// $('.form5 span').empty();
							// $('.form5 span').append('Rp '+data.idr_anggaran);
							$('.form5 span').empty();
							$('.form5 span').append(jwpp);

							$('.form7 span').empty();
							$('.form7 span').append(data.desc_dokumen);

							if (data.lampiran_persetujuan != '' || data.lampiran_persetujuan != null) {
								link = '<a href="'+base_url+'assets/lampiran/lampiran_persetujuan/'+data.lampiran_persetujuan+'">'+data.lampiran_persetujuan+'</a>';
							} else {
								link = '-';
							}
							$('.form9 span').empty();
							$('.form9 span').append(link);
						}
					})
				})

				$('#btnHapus').click(function() {
					$('#formHapus').css('display','block');
					$('#tab-intro').css('display','none');
					$('.btn-group').css('display', 'block');

					$('.form1').css('display', 'block');
					$('.form2').css('display', 'none');
					$('.form3').css('display', 'none');
					$('.form4').css('display', 'none');
					$('.form5').css('display', 'none');
					$('.form6').css('display', 'none');
					$('.form7').css('display', 'block');
					$('.form8').css('display', 'block');
				});

				$('#switchHapus').click(function() {
					$('.btn-group').css('display', 'block');
					$('#formHapus').css('display','block');
					$('#formUbah').css('display','none');

					$('.form1').css('display', 'block');
					$('.form2').css('display', 'none');
					$('.form3').css('display', 'none');
					$('.form4').css('display', 'none');
					$('.form5').css('display', 'none');
					$('.form6').css('display', 'none');
				})

				$('#switchUbah').click(function() {
					$('.btn-group').css('display', 'block');
					$('#formUbah').css('display','block');
					$('#formHapus').css('display','none');
					
					$('.form1').css('display', 'block');
					$('.form2').css('display', 'block');
					$('.form3').css('display', 'block');
					$('.form4').css('display', 'block');
					$('.form5').css('display', 'block');
					$('.form6').css('display', 'block');
					$('.form7').css('display', 'block');

				})

		}
	});
});

// $(function() {
// 	if($('.badge').hasClass('fp3_reject') == true) {
// 		$('.fp3_reject').addClass('tooltip').append('<span class="tooltiptext reject">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto mollitia fugiat non corporis maxime vitae facere nisi quisquam praesentium! Accusamus corporis ad quidem doloremque rerum dolorem officiis maiores nisi libero!</span></span>');
// 	}
// })


</script>