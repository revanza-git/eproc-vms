<?php $admin = $this->session->userdata('admin');?>
<script src="https://cdn.rawgit.com/igorescobar/jQuery-Mask-Plugin/1ef022ab/dist/jquery.mask.min.js"></script>
<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};
	var _xhr;
	var folder;
	var initializeFolder = function(){
		var line 	= '';
		var parent 	= '';
		folder = $('#folder').folder({
		url: '<?php echo site_url('pemaketan/getDataDivision/'.$id_division.'/'.$id_fppbj.'/'.$year); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			_id 				= value[key][9].value;
			$('#id_fppbj').val(_id);
			_metode				= value[key][1].value;
			_is_approve			= value[key][4].value;
			is_status 			= value[key][3].value;
			pengadaan 			= value[key][10].value;
			metode_pengadaan	= value[key][11].value;
			lampiran_persetujuan= value[key][5].value;
			del 				= value[key][18].value;
			is_reject 			= value[key][7].value;
			is_approved_hse		= value[key][12].value;
			idr_anggaran		= value[key][16].value;

			urlTimeline = '<?php echo base_url('timeline/view/fppbj');?>/'+_id;
			urlpdf 		= site_url+"export/fppbj/"+_id;
			id_role = '<?php echo $this->session->userdata('admin')['id_role'] ?>'

			_fp3 = [{
					icon : 'file-signature',
					label: 'Buat File FP3',
					class: 'buttonFP3',
					href:site_url+"fp3/insert/"+_id,
				}];

			_btn_resiko = [{
					icon : 'eye',
					label: 'Lihat Data.',
					class: 'buttonViewStep',
					href:site_url+"pemaketan/get_step/"+_id
				}];

			_btn_swakelola = [{
					icon : 'eye',
					label: 'Lihat Data Swakelola',
					class: 'buttonViewSwakelola',
					href:site_url+"pemaketan/form_analisa_swakelola/"+_id
				}];

 			_btn_fkpbj = [{
 					icon : 'file',
 					label: 'Buat File FKPBJ',
 					class: 'buttonFKPBJ',
 					href:site_url+"fkpbj/add_fkpbj/"+_id
 				}];

 				btn_edit = [
 				{
 					icon : 'cog',
 					label: 'Edit',
 					class: 'buttonEdit',
 					href:site_url+"pemaketan/edit_fppbj/"+_id
 				}

 				<?php if ($id_division == $this->session->userdata('admin')['id_division']) { ?>
 					,{
 					icon : 'trash',
 					label: 'Hapus',
 					class: 'buttonDelete',
 					href:site_url+"pemaketan/remove/"+_id
 				}
 				<?php } ?>
 				];
			
			<?php if ($admin['id_role'] == 10) { ?>
					btn = [{
						icon: 'eye',
						label: 'Lihat Riwayat Approval',
						class: 'buttonViewRiwayat',
						href: site_url + "riwayat/approval/" + _id
					}];
			<?php } else { ?>
 			btn = [{
 					icon : 'eye',
 					label: 'Lihat Data',
					class: 'buttonViewStep',
					href:site_url+"pemaketan/get_step/"+_id
 				},{
 					icon : 'file-download',
 					label: 'Download Riwayat',
					class: 'buttonUnduhRiwayat',
					href:site_url+"riwayat/pengadaan/"+_id
 				},{
 					icon : 'calendar-alt',
 					label: 'Timeline',
 					class: 'buttonTimeline',
 				}
 				<?php if (($id_division == $this->session->userdata('admin')['id_division']) || $this->session->userdata('admin')['id_role'] == 6) { ?>
 				,{
 					icon : 'file-download',
 					label: 'Download PDF',
 					class: 'buttonPDF',
 					href:site_url+"pemaketan/form_download_pdf/"+_id
 				}
 				<?php } ?>
 				];
 			<?php } ?>
 		if(pengadaan == 'jasa'){

				if (metode_pengadaan == 3){
					
					if (is_status == 0 && is_reject == 0 && del == 0 && is_approved_hse < 2 && ((del == 0 && _is_approve == 3 && (idr_anggaran <= 100000000 || (idr_anggaran > 100000000 && metode_pengadaan == 3)))) ||  (del == 0 && _is_approve == 4 && idr_anggaran > 100000000) && (metode_pengadaan != 3 || metode_pengadaan != 5)){

						return btn;
					} else if (is_status == 2) {
						if (_is_approve == 3) {
							return btn;
						} else {
							<?php if ($admin['id_division'] == 1 && $admin['id_role'] == 6 || ($id_division == $this->session->userdata('admin')['id_division'])) { ?>
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							<?php } else { ?>
								return btn;
							<?php } ?>
						}
					} else if (is_status == 1) {
						return btn;
					} else {
						if (is_reject == 0) {
							return btn;
						} else {
							if (is_reject == 0) {
								return btn;
							} else {
								return btn.concat(btn_edit);
							}
						}
					}
				}
				<?php if ($admin['id_role'] == 5 || $admin['id_division'] == 1){ ?>

					else if (is_status == 0 || is_status == 1){
						if (is_reject == 0 
						&& del == 0 
						&& is_approved_hse < 2 
						&& ((del == 0 && _is_approve == 3 
						&& (idr_anggaran <= 100000000 || (idr_anggaran > 100000000 
						&& metode_pengadaan == 3)))) ||  (del == 0 
						&& _is_approve == 4 && idr_anggaran > 100000000) 
						&& (metode_pengadaan != 3 || metode_pengadaan != 5)) {
								var _btn = _btn_fkpbj.concat(btn);
		 						return _btn;
						} else {
							<?php if ($id_division == $this->session->userdata('admin')['id_division']) { ?>
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							<?php } else { ?>
								return btn;
							<?php } ?>
						}
					}
 				<?php }?>
 					else if (is_status == 2) {
 						//console.log('Ke 2');
						if (_is_approve == 3) {
							return btn;
						} else {
							<?php if ($admin['id_division'] == 1 && $admin['id_role'] == 6 || ($id_division == $this->session->userdata('admin')['id_division'])) { ?>
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							<?php } else { ?>
								return btn;
							<?php } ?>
						}
					}
	 				else {
						<?php if($admin['id_role'] == 2 || $admin['id_role'] == 5 || $admin['id_role'] == 3 || $admin['id_role'] == 4) {
		 					if ($id_divisi == $this->session->userdata('admin')['id_division'] || $id_divisi == 1) {
		 				?>

			 			if (is_reject == 0) {
							return btn;
						} else {
							return btn.concat(btn_edit);
						}

 						<?php } } else { ?>
 							return btn;
 						<?php } ?>
	 				}
	 			}else{
					if (metode_pengadaan == 3){
						
						if (is_status == 0 && is_reject == 0 && del == 0 && is_approved_hse < 2 && ((del == 0 && _is_approve == 3 && (idr_anggaran <= 100000000 || (idr_anggaran > 100000000 && metode_pengadaan == 3)))) ||  (del == 0 && _is_approve == 4 && idr_anggaran > 100000000) && (metode_pengadaan != 3 || metode_pengadaan != 5)){

							return btn;
						} else if (is_status == 2) {
							if (_is_approve == 3) {
								return btn;
							} else {
								<?php if ($admin['id_division'] == 1 && $admin['id_role'] == 6 || ($id_division == $this->session->userdata('admin')['id_division'])) { ?>
									if (is_reject == 0) {
										return btn;
									} else {
										return btn.concat(btn_edit);
									}
								<?php } else { ?>
									return btn;
								<?php } ?>
							}
						} else if (is_status == 1) {
							return btn;
						} else {
							if (is_reject == 0) {
								return btn;
							} else {
								return btn.concat(btn_edit);
							}
						}
					}
	 				<?php if ($admin['id_role'] == 5 || $admin['id_division'] == 1) { ?>
					else if (is_status == 0){
						if (is_status == 0 && is_reject == 0 && del == 0 && is_approved_hse < 2 && ((del == 0 && _is_approve == 3 && (idr_anggaran <= 100000000 || (idr_anggaran > 100000000 && metode_pengadaan == 3)))) ||  (del == 0 && _is_approve == 4 && idr_anggaran > 100000000) && (metode_pengadaan != 3 || metode_pengadaan != 5)) {
								var _btn = _btn_fkpbj.concat(btn);
		 						return _btn;
						} else {
							<?php if ($id_division == $this->session->userdata('admin')['id_division']) { ?>
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							<?php } else { ?>
								return btn;
							<?php } ?>
						}
					}
					<?php  }?>
					else if (is_status == 2) {
						if (_is_approve == 3) {
							return btn;
						} else {
							<?php if ($admin['id_division'] == 1 && $admin['id_role'] == 6 || ($id_division == $this->session->userdata('admin')['id_division'])) { ?>
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							<?php } else { ?>
								return btn;
							<?php } ?>
						}
					}
					else if (is_status == 1) {
						return btn;
					}
	 				else {
	 					<?php if($admin['id_role'] == 2 || $admin['id_role'] == 5 || $admin['id_role'] == 3 || $admin['id_role'] == 4) {
		 					if ($id_divisi == $this->session->userdata('admin')['id_division'] || $id_divisi == 1) {
		 				?>
		 				if (is_reject == 0) {
							return btn;
						} else {
							return btn.concat(btn_edit);
						}

 						<?php } } else { ?>
 							return btn;
 						<?php } ?>
	 				}
	 			}
		},
		callbackFunctionRightClick: function(){
			$('.buttonUnduhRiwayat').on('click',function() {
				href = $(this).attr('href');

				window.location.href = href;
			})
			
			$('.buttonViewRiwayat').on('click', function() {
					href = $(this).attr('href');
					window.location.href = href;
				})

				var step = $('.buttonViewStep').modal({
				header: 'Lihat Data',
				method: 'GET',
				dataType:'html',
                render : function(el, data){
                  $(el).html(data);

                  $('.close').on('click',function(){
                  	$(step).data('modal').close();
                  })

                  $('#tab1').css('display','block');

		  			$('#nextBtn2').click(function() {
		  				$('#tab2').css('display','block');
		  				$('#tab1').css('display','none');
		  			});

		  			$('#nextBtn3').click(function() {
		  				$('#tab3').css('display','block');
		  				$('#tab2').css('display','none');
		  			});

		  			$('#nextBtn4').click(function() {
		  				$('#tab4').css('display','block');
		  				$('#tab3').css('display','none');
		  			});

		  			$('#prevBtn1').click(function() {
		  				$('#tab1').css('display','block');
		  				$('#tab2').css('display','none');
		  			});

		  			$('#prevBtn2').click(function() {
		  				$('#tab2').css('display','block');
		  				$('#tab3').css('display','none');
		  			});

		  			$('#prevBtn4').click(function() {
		  				$('#tab3').css('display','block');
		  				$('#tab4').css('display','none');
		  			});

		  			$('.reject-btn-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').addClass('active');
		  			})

		  			$('.close-reject-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').removeClass('active');
		  			})

		  			$.ajax({
							url:'<?php echo site_url('pemaketan/get_pic') ?>/'+$('#id_fppbj').val(),
							method: 'get',
							success: function(xhr) {
								id_role = '<?php echo $this->session->userdata('admin')['id_role'] ?>';
								if (id_role == 2) {
									$('#form-pic').append(xhr);		
								}
							}
						})


				}
			});

			var stepAnalisa = $('.buttonViewSwakelola').modal({
				header: 'Data Analisa Swakelola',
				dataType:'html',
                render : function(el, data){
                  	$(el).html(data);

                  	$('.close').on('click',function(){
                  	$(step).data('modal').close();
                  })

                  $('#tab1').css('display','block');

		  			$('#nextBtn2').click(function() {
		  				$('#tab2').css('display','block');
		  				$('#tab1').css('display','none');
		  			});

		  			$('#nextBtn3').click(function() {
		  				$('#tab3').css('display','block');
		  				$('#tab2').css('display','none');
		  			});

		  			$('#prevBtn1').click(function() {
		  				$('#tab1').css('display','block');
		  				$('#tab2').css('display','none');
		  			});

		  			$('#prevBtn2').click(function() {
		  				$('#tab2').css('display','block');
		  				$('#tab3').css('display','none');
		  			});

		  			$('.reject-btn-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').addClass('active');
		  			})

		  			$('.close-reject-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').removeClass('active');
		  			})
                }
			});		

			// ANALISA RISIKO
			// POP UP ANALISA RISIKO
				
			// ANALISA SWAKELOLA
			$('#step5 select').on('change', function(){
				$(".matrix-box").removeClass("active");
				$(".ms-item").removeClass("active");
				// SWAKELOLA
				var waktu		= parseInt($('.modal [name="waktu"]').val());
				var biaya 		= parseInt($('.modal [name="biaya"]').val());
				var tenaga 		= parseInt($('.modal [name="tenaga"]').val());
				var bahan 		= parseInt($('.modal [name="bahan"]').val());
				var peralatan	= parseInt($('.modal [name="peralatan"]').val());

				// PARAMETER
				var swakelola	= waktu + biaya + tenaga + bahan + peralatan;
				
				var _class = '.m'+(swakelola);
				// jQuery(_class).addClass("active");
				$(_class).addClass('active');
				if (swakelola >= 12) {
					// $('#step5 .alert').empty();
					$('.form5').append('Pengadaan harus dilaksanakan dengan metode pemilihan pengadaan barang/jasa yang lain.');
					$('#step5 .btn-submit').prop('disabled',true);
					$('#step5 .btn-submit').css('display','none');
				} else{
					$('#step5 .alert').empty();
					$('#step5 .alert').remove();
					$('#step5 .btn-submit').removeAttr('disabled');
					$('#step5 .btn-submit').css('display','block');
				}
			});

			// function _close(){
			// 	$('.btn-save').on('click', function(){
			// 		location.reload();
			// 		$(resiko).data('modal').close();
			// 		folder.data('plugin_folder').fetchData();
			// 	});
			// }

			// stepButton();
			// _close();
		
			var view = $('.buttonView').modal({
				header: 'Lihat Data',
				render : function(el, data){
					_self = view;
					var anggaran_idr = '';
					var anggaran_usd = '';
					data.onSuccess = function(){
						location.reload();

					};
					
					$(el).form(data).data('form');

					pr = '';
					if (data.form[1].value == 'direct_charge') {
						pr += 'Direct Charge';
					} else if (data.form[1].value == 'services') {
						pr += 'Services';
					} else if (data.form[1].value == 'user_purchase') {
						pr += 'User Purchase';
					} else{
						pr += 'NDA';
					}
					$('.form1 span').empty();
					$('.form1 span').append(pr);

					tp = '';
					if (data.form[3].value == 'barang') {
						tp += 'Barang';
					} else{
						tp += 'Jasa';
					}
					$('.form3 span').empty();
					$('.form3 span').append(tp);

					if (data.form[10].value == 1) {
						$('.form10 span').empty()
						$('.form10 span').append('Ada')
					} else{
						$('.form10 span').empty()
						$('.form10 span').append('Tidak ada')
					}

					$.ajax({
						url:'<?php echo site_url('pemaketan/get_pic') ?>/'+data.form[18].value,
						method: 'get',
						success: function(xhr) {
							id_role = '<?php echo $this->session->userdata('admin')['id_role'] ?>';
							if (id_role == 2) {
								$('.form18').append(xhr);				
							}
						}
					})

		  			$('.close-modal-reject').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject').removeClass('active');
		  			})

		  			$('.form5 span').empty();

		  			if (data.form[5].value == 1) {
		  				metode_pengadaan = 'Pelelangan';
		  			} else if(data.form[5].value == 2){
		  				metode_pengadaan = 'Pemilihan Langsung';
		  			} else if(data.form[5].value == 3){
		  				metode_pengadaan = 'Swakelola';
					} else if(data.form[5].value == 4){
		  				metode_pengadaan = 'Penunjukkan Langsung';
		  			} else {
		  				metode_pengadaan = 'Pengadaan Langsung';
		  			}
		  			$('.form5 span').append(metode_pengadaan);

		  			var jdp = data.form[4].value;
		  			$('.form4 span').empty();
		  			if (jdp == 'stock') {
		  				vdp = 'Stock';
		  			} else if (jdp == 'non_stock') {
		  				vdp = 'Non Stock';
		  			} else if (jdp == 'jasa_konstruksi') {
		  				vdp = 'Jasa Konstruksi';
		  			} else if (jdp == 'jasa_konsultasi') {
		  				vdp = 'Jasa Konsultasi';
		  			} else {
		  				vdp = 'Jasa Lainnya';
		  			}
		  			$('.form4 span').append(vdp);

		  			var sistem_kontrak = JSON.parse(data.form[17].value);
		  			$('.form17 span').empty();
		  			sistem_kontrak.forEach(function(value) {
		  				$('.form17 span').append(value+", ");
		  			})

		  			$('.form12 span').empty();
		  			var penggolongan_penyedia = data.form[13].value;
					if (penggolongan_penyedia == 'perseorangan') {
						golongan = 'Perseorangan';
					} else if (penggolongan_penyedia == 'usaha_kecil') {
						golongan = 'Usaha Kecil (K)';
					} else if (penggolongan_penyedia == 'usaha_menengah') {
						golongan = 'Usaha Menengah (M)';
					} else if (penggolongan_penyedia == 'usaha_besar') {
						golongan = 'Usaha Besar (B)';
					} else{
						golongan = '-';
					}

					$('.form12 span').append(golongan);
				}
			});

			// DOWNLOAD Data on PDF
			var pdf = $('.buttonPDF').modal({
				header : 'Download PDF',
				render : function(el, data){
					data.onSuccess = function(){
					}
					$(el).form(data);
					$('form',el).off('submit')
				}				
			});
			
			// Link to Timeline Page
			var timeline = $('.buttonTimeline').click(function(){
				$(location).attr('href',urlTimeline);
			});

			// Create new FKPBJ based on selected procurement
			var fkpbj = $('.buttonFKPBJ').modal({
				header: 'Tambah Data FKPBJ',
				dataType: 'html',
				render : function(el, data){

					$(el).html(data);
					
					$('.money').iMask({
						type : 'number'
					});

					$('#tab1').css('display','block');

		  			$('#nextBtn2').click(function() {
		  				no_pr 					= $('#formStep [name="no_pr"]').val();
		  				tipe_pr 				= $('#formStep [name="tipe_pr"]').val();
						lampiran_pr 			= $('#formStep [name="pr_lampiran"]').val();
		  				tipe_pengadaan 			= $('#formStep [name="tipe_pengadaan"]').val();
		  				jenis_pengadaan 		= $('#formStep [name="jenis_pengadaan"]').val();
		  				idr_anggaran 			= $('#formStep [name="idr_anggaran"]').val();
		  				year_anggaran 			= $('#formStep [name="year_anggaran"]').val();
						lampiran_kak 			= $('#formStep [name="kak_lampiran"]').val();
		  				hps 					= $('#formStep [name="hps"]').val();
		  				lingkup_kerja 			= $('#formStep [name="lingkup_kerja"]').val();
		  				desc_metode_pembayaran 	= $('#formStep [name="desc_metode_pembayaran"]').val();
		  				jenis_kontrak 			= $('#formStep [name="jenis_kontrak"]').val();
		  				sistem_kontrak 			= $('#formStep [name="sistem_kontrak[]"]').val();
		  				desc_dokumen 			= $('#formStep [name="desc_dokumen"]').val();

		  				if (no_pr == '') {
		  					alert('No.PR Harus Diisi !');
		  				} else if(tipe_pr == ''){
		  					alert('Tipe PR Harus Diisi !');
						}else if(lampiran_pr == ''){
							alert('Lampiran PR Harus Diisi !');	
		  				} else if(tipe_pengadaan == ''){
		  					alert('Jenis Pengadaan Harus Diisi !');
		  				} else if(jenis_pengadaan == ''){
		  					alert('Jenis Detail Pengadaan Harus Diisi !');
		  				} else if(idr_anggaran == ''){
		  					alert('Anggaran (IDR) Harus Diisi !');
		  				} else if(year_anggaran == ''){
		  					alert('Tahun Anggaran Harus Diisi !');
		  				}else if(lampiran_kak == ''){
							alert('Lampiran KAK Harus Diisi !');	
		  				} else if(hps == ''){
		  					alert('Ketersediaan HPS Harus Diisi !');
		  				} else if(lingkup_kerja == ''){
		  					alert('Lingkup Kerja Harus Diisi !');
		  				} else if(desc_metode_pembayaran == ''){
		  					alert('Metode Pembayaran (Usulan) Harus Diisi !');
		  				} else if(jenis_kontrak == '' || jenis_kontrak == '0'){
		  					alert('Jenis Kontrak (Usulan) Harus Diisi !');
		  				} else if(sistem_kontrak == '' || sistem_kontrak == null){
		  					alert('Sistem Kontrak (Usulan) Harus Diisi !');
		  				} else if(desc_dokumen == ''){
		  					alert('Keterangan Harus Diisi !');
		  				} else {
		  					$('#tab2').css('display','block');
		  					$('#tab1').css('display','none');
		  				}
		  			});

		  			$('#nextBtn3').click(function() {
		  				$('#tab3').css('display','block');
		  				$('#tab2').css('display','none');
		  			});

		  			$('#prevBtn1').click(function() {
		  				$('#tab1').css('display','block');
		  				$('#tab2').css('display','none');
		  			});

		  			$('#prevBtn2').click(function() {
		  				$('#tab2').css('display','block');
		  				$('#tab3').css('display','none');
		  			});

		  			$('.reject-btn-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').addClass('active');
		  			})

		  			$('.close-reject-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').removeClass('active');
		  			});
					
					$('.deleteFile').on('click',function(e) {
						data = $(this).data('id');
						$('[type="file"].closeInput'+data+'').css('display','block');
						$('.fileUploadBlock.close'+data+'').empty();
						$('[type="hidden"].closeHidden'+data+'').val('');
					});

					var valCsms = $('[name="valcsms"]').val();
		  			if (valCsms == 'E' || valCsms == 'H') {
		  				csms = 1;
		  			}else if (valCsms == 'M') {
		  				csms = 2;
		  			}else {
		  				csms = 3;
		  			}

					var val_e = $('[name="jenis_pengadaan"]').val();

					$.ajax({
							url: '<?php echo site_url('main/get_dpt_type/')?>/'+val_e,
							dataType: 'json',
							complete : function(){
							},
							success: function(dpt){
								$('#tab2 .checkboxWrapper').empty();
								$('#tab2 .tab-content .form1').empty();
								$('#tab2 label').css("float", "left");

								$('#tab2 .tab-content').append('<fieldset class="form-group form1 " for=""><label for="">Usulan Non DPT</label><input type="text" class="form-control" name="usulan"></fieldset>');

								$('#tab2 .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
								dpt.forEach(function(element) {
									if (element.score == '' || element.score == null) {
										score = '';
									} else {
										score = '('+element.score+')';
									}

									$('#tab2 .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id_vendor+'" name="type[]" type="checkbox" value="'+element.id_vendor+'"/> <label for="option'+element.id_vendor+'">'+element.vendor+' '+score+'</label> </div>');
								});
							}
					});

					$('[name="jenis_pengadaan"]').on('click',function() {
						var dpt_type = $(this).val();

						$.ajax({
							url: '<?php echo site_url('main/get_dpt_type/')?>/'+dpt_type,
							dataType: 'json',
							complete : function(){
							},
							success: function(dpt){
								$('#tab2 .checkboxWrapper').empty();
								$('#tab2 .tab-content .form1').empty();
								$('#tab2 label').css("float", "left");
								$('#tab2 .tab-content').append('<fieldset class="form-group form1 " for=""><label for="">Usulan Non DPT</label><input type="text" class="form-control" name="usulan"></fieldset>');
								$('#tab2 .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
								dpt.forEach(function(element) {
									if (element.score == '' || element.score == null) {
										score = '';
									} else {
										score = '('+element.score+')';
									}

									if (element.value == 1) {
										checked = 'checked';
									}

									$('#tab2 .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id_vendor+'" name="type[]" type="checkbox" value="'+element.id_vendor+'"/> <label for="option'+element.id_vendor+'">'+element.vendor+' '+score+'</label> </div>');
								});
							}
						});
					});

					$("#searchDPT").on('keyup', function(){
						var matcher = new RegExp($(this).val(), 'gi');
						
						$('.checkboxWrapper').css('display','block').not(function(){
							return matcher.test($(this).find('.inputGroup').text())
						}).css('display','none');
					});
 
					$('.modal [name="tipe_pr"]').on('change', function(){
						// get parent value
						var tipe_pr = $(this).val();
						// alert(pengadaan);

						// Change option on select based on parent
						if (tipe_pr == "direct_charge") {
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
						}
						else if(tipe_pr == "services"){
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
						}
						else if(tipe_pr == "user_purchase"){
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='5'>Pengadaan Langsung</option>");
						}else if(tipe_pr == "nda"){
							$('.modal [name="pengadaan"]').empty();
							$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='3'>Swakelola</option>");
						}
					});

					// PENGADAAN TIPE
					$('.modal [name="tipe_pengadaan"]').on('change', function(){
						// get parent value
						var pengadaan = $(this).val();
						// alert(pengadaan);

						// Change option on select based on parent
						if (pengadaan == "barang") {
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>non Stock</option>");
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

			var fkpbj_view = $('.buttonLihatFKPBJ').modal({
				header: 'Lihat usulan FKPBJ',
				dataType: 'html',
				render : function(el, data){
					// _self = fkpbj;
					
					// data.onSuccess = function(){
					// 	$.ajax({
					// 		url: '<?php echo site_url('main/update_status/');?>',
					// 		data: {'id_fppbj':_id,'param_':2},
					// 		dataType: 'xml',
					// 		success: function(xml){
					// 			// $(fkpbj).data('modal').close();
					// 			// folder.data('plugin_folder').fetchData();
					// 			location.reload();	
					// 		}
					// 	});
					// };
					// data.isReset = false;
					$(el).html(data);

					$('#tab1').css('display','block');

		  			$('#nextBtn2').click(function() {
		  				$('#tab2').css('display','block');
		  				$('#tab1').css('display','none');
		  			});

		  			$('#nextBtn3').click(function() {
		  				$('#tab3').css('display','block');
		  				$('#tab2').css('display','none');
		  			});

		  			$('#prevBtn1').click(function() {
		  				$('#tab1').css('display','block');
		  				$('#tab2').css('display','none');
		  			});

		  			$('#prevBtn2').click(function() {
		  				$('#tab2').css('display','block');
		  				$('#tab3').css('display','none');
		  			});

		  			$('.reject-btn-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').addClass('active');
		  			})

		  			$('.close-reject-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').removeClass('active');
		  			})

					$('.modal [name="pengadaan"]').on('change', function(){
						// get parent value
						var pengadaan = $(this).val();
						// alert(pengadaan);

						// Change option on select based on parent
						if (pengadaan == "barang") {
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>Non Stock</option>");
						}else if(pengadaan == "jasa"){
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_nonkonstruksi'>Jasa non-Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
						}else{
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
						}
					});

					$.ajax({
						url: '<?php echo site_url('main/get_dpt')?>',
						// data: category,
						dataType: 'json',
						complete : function(){
						},
						success: function(dpt){
							// console.log(dpt);
							$('#tab2 .checkboxWrapper').empty();
							$('#tab2 label').css("float", "left")

							$('#tab2 .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPTFKPBJ()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
							dpt.forEach(function(element) {
								$('#tab2 .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id+'" name="type[]" type="checkbox" value="'+element.id+'"/> <label for="option'+element.id+'">'+element.name+'</label> </div>');
							});
						}
						});

						$("#searchDPT").on('keyup', function(){
							var matcher = new RegExp($(this).val(), 'gi');
							// console.log(matcher);
							// console.log($(this).val());
							$('.checkboxWrapper').css('display','block').not(function(){
								return matcher.test($(this).find('.inputGroup').text())
							}).css('display','none');
						})

						tipe = $('.form1 [name="tipe_pr"]').val()
						if (tipe == "direct_charge") {
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
						}
						else if(tipe == "services"){
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
						}
						else if(tipe == "user_purchase"){
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='5'>Pengadaan Langsung</option>");
						}
						else if(tipe_pr == "nda"){
							$('.modal [name="pengadaan"]').empty();
							$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='3'>Swakelola</option>");
						}

				jenis_pengadaan = $('.form3 [name="tipe_pengadaan"]').val()
				if (jenis_pengadaan == "barang") {
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>non Stock</option>");
				}else if(jenis_pengadaan == "jasa"){
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");

				}else{
					// $('.modal [name="jenis_pengadaan"]').empty();
					// $('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
				}
 
					$('.modal [name="tipe_pr"]').on('change', function(){
						// get parent value
						var tipe_pr = $(this).val();
						// alert(pengadaan);

						// Change option on select based on parent
						if (tipe_pr == "direct_charge") {
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
						}
						else if(tipe_pr == "services"){
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
						}
						else if(tipe_pr == "user_purchase"){
							$('.modal [name="tipe_pengadaan"]').empty();
							$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='5'>Pengadaan Langsung</option>");
						}
						else if(tipe_pr == "nda"){
							$('.modal [name="pengadaan"]').empty();
							$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
							$('.modal [name="metode_pengadaan"]').empty();
							$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='3'>Swakelola</option>");
						}
					});

					// PENGADAAN TIPE
					$('.modal [name="tipe_pengadaan"]').on('change', function(){
						// get parent value
						var pengadaan = $(this).val();
						// alert(pengadaan);

						// Change option on select based on parent
						if (pengadaan == "barang") {
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>non Stock</option>");
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

			// Create new FP3 based on selected procurement
			var fp3 = $('.buttonFP3').modal({
				header: 'Tambah Data FP3',
				render : function(el, data){
					_self = fp3;

					data.onSuccess = function(){
						$.ajax({
							url: '<?php echo site_url('main/update_status/');?>',
							data: {'id_fppbj':_id,'param_':2},
							dataType: 'xml',
							success: function(xml){
								$(fp3).data('modal').close();
								folder.data('plugin_folder').fetchData();				
							}
						});
								
					};
					data.isReset = false;
					$(el).form(data).data('form');
				}
			});

			var edit = $('.buttonEdit').modal({
				header:'Edit',
				dataType:'html',
				render : function(el, data){
					// _self = edit;

					// data.onSuccess = function(){
					// 	// $(edit).data('modal').close();
					// 	// folder.data('plugin_folder').fetchData();
					// 	location.reload()
					// };
					// data.isReset = false;
					// $(el).form(data).data('form');

					// // $('.btn-submit').on('click',function(e) {
					// // 	alert('asdas');
					// // });
					// console.log('asd');

					$(el).html(data);

				// $('.fa-trash').on('click',function(e) {
				// 	console.log('Tesashdgujafsdhasfhd');
				// 	$('.fileUploadBlock',this).empty();
				// 	$('input[type="file"]',this).show()
				// });

                  $('.close').on('click',function(){
                  	$(step).data('modal').close();
                  })
                  //-------------------- BTN Next
                  	$('#detailData').css('display','block');

		  			$('#btnToDPT').click(function() {
		  				$('#detailData').css('display','none');
		  				$('#DPT').css('display','block');
		  			});

		  			$('#btnAnalisaToDPT').click(function() {
		  				$('#Analisa').css('display','none');
		  				$('#DPT').css('display','block');
		  			});

		  			$('#btnToDPTList').click(function() {
		  				$('#DPTList').css('display','block');
		  				$('#DPT').css('display','none');
		  			});

		  			$('#btnToAnalisa').click(function() {
		  				$('#Analisa').css('display','block');
		  				$('#detailData').css('display','none');
		  			});

					$('#btnAnalisaToDPT').click(function() {
		  				$('#DPT').css('display','block');
		  				$('#Analisa').css('display','none');
		  			});

		  			$('#btnDPTListToSwakelola').click(function() {
		  				$('#Swakelola').css('display','block');
		  				$('#DPTList').css('display','none');
		  			});
		  		  //-------------------- BTN Prev
		  		  	$('#toDetailData').click(function() {
		  				$('#detailData').css('display','block');
		  				$('#DPT').css('display','none');
		  			});

		  			$('#toDPT').click(function() {
		  				$('#DPTList').css('display','none');
		  				$('#DPT').css('display','block');
		  			});

		  			$('#toDPTList').click(function() {
		  				$('#Swakelola').css('display','none');
		  				$('#DPTList').css('display','block');
		  			});

		  			$('#toAnalisa').click(function() {
		  				$('#DPT').css('display','none');
		  				$('#Analisa').css('display','block');
		  			});

		  			$('#analisaToDetailData').click(function() {
		  				$('#detailData').css('display','block');
		  				$('#Analisa').css('display','none');
		  			});

		  			$('.reject-btn-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').addClass('active');
		  			});

		  			$('.close-reject-step').on('click',function() {
		  				$('.form-keterangan-reject.modal-reject-step').removeClass('active');
		  			});

		  			//$( '.money' ).mask('999,999,999,999,999,999,999.99', {reverse: true});
		  			//$( '.money' ).mask('999,999,999,999,999,999,999.99', {reverse: true});

		  			$('.datePicker', el).datetimepicker({
						timepicker: false,
						format: 'Y-m-d',
						scrollMonth : false,
						scrollInput : false
					});

		  			var __no = $('input[name="nomor"]').val();
					var __form = '<div style="margin:0.35em 0.625em 0.75em"><label for="">Anggaran (IDR)</label><input type="text" class="form-control money" id="" value="" name="idr_anggaran[]" placeholder="" style="text-align: right;"></div><div style="margin:0.35em 0.625em 0.75em"><label for="">Anggaran (USD)</label><input type="text" class="form-control money" id="" value="" name="usd_anggaran[]" placeholder="" style="text-align: right;"></div><div style="margin:0.35em 0.625em 0.75em"><label for="">Tahun Anggaran*</label><input type="number" class="form-control" id="" value="" name="year_anggaran[]" placeholder=""></div>';
					var __line = '<hr style="display: block; color:#3273dc; border-bottom: 1px #3273dc solid; margin: 20px 0;">';

					// $('.modal .form7').append(__line+'<div class="multiple-budget"></div><div><a id="add_budget">Tambah Tahun Anggaran</a></div>'+__line);

					$('#add_budget').on('click', function(){
						__no ++;

						$('.modal .form7 .multiple-budget').append('<div id="budget-'+__no+'"><p style="color: #3273dc; font-weight: bold;">Detail Anggaran #'+__no+'</p>'+__form+'</div>');
					});
					$('#min_budget').on('click', function(){
						if (__no == 1) {
							_clear();
							$('[name="is_multiyear"]').prop("checked",false);
							$('.modal .form7').append(__form);
						} else {
							$('#budget-'+__no).remove();
						}
						
						__no--;
					});
				$('.modal [name="is_multiyear"]').on('change', function(){
					// if so....
					
					var __no = 1;
					var __form = '<div style="margin:0.35em 0.625em 0.75em"><label for="">Anggaran (IDR)</label><input type="text" class="form-control money" id="" value="" name="idr_anggaran[]" placeholder="" style="text-align: right;"></div><div style="margin:0.35em 0.625em 0.75em"><label for="">Anggaran (USD)</label><input type="text" class="form-control money" id="" value="" name="usd_anggaran[]" placeholder="" style="text-align: right;"></div><div style="margin:0.35em 0.625em 0.75em"><label for="">Tahun Anggaran*</label><input type="number" class="form-control" id="" value="" name="year_anggaran[]" placeholder=""></div>';
					var __line = '<hr style="display: block; color:#3273dc; border-bottom: 1px #3273dc solid; margin: 20px 0;">';
					
					if ($(this).is(":checked")) {
						_clear();
						$('.modal .form7').append(__line+'<div class="multiple-budget"></div><div><a id="add_budget">Tambah Tahun Anggaran</a>|| <a id="min_budget">Batal Tahun Anggaran</a></div>'+__line);
						$('.modal .form7 .multiple-budget').append('<div id="budget-'+__no+'"><p style="color: #3273dc; font-weight: bold;">Detail Anggaran #'+__no+'</p>'+__form+'</div>');
						
						$('#add_budget').on('click', function(){
							__no ++;

							$('.modal .form7 .multiple-budget').append('<div id="budget-'+__no+'"><p style="color: #3273dc; font-weight: bold;">Detail Anggaran #'+__no+'</p>'+__form+'</div>');
						});

						$('#min_budget').on('click', function(){
							if (__no == 1) {
								_clear();
								$('[name="is_multiyear"]').prop("checked",false);
								$('.modal .form7').append(__form);
							} else {
								$('#budget-'+__no).remove();
							}
							
							__no--;
						});

					}else{
						_clear();
						$('.modal .form7').append(__form);
					}
					
				});

				function _clear(){
					$('.modal .form7').empty();
					$('.modal .form8').remove();
					$('.modal .form9').remove();
				}

				$('.deleteFile').on('click',function(e) {
					data = $(this).data('id');
					$('[type="file"].closeInput'+data+'').css('display','block');
					$('.fileUploadBlock.close'+data+'').empty();
					$('[type="hidden"].closeHidden'+data+'').val('');
				});

				var val_tipe_pr 		= $('.form1a input').val();
				var val_tipe_pengadaan 	= $('.form4a input').val();
				var val_jenis_pengadaan = $('.form5a input').val();
				var val_metode_pengadaan= $('.form6a input').val();

				if (val_tipe_pr == 'direct_charge') {
					$('.modal [name="tipe_pr"]').empty();
					$('.modal [name="tipe_pr"]').append("<option value=''>Pilih Salah Satu</option><option value='direct_charge' selected>Direct Charge</option><option value='services'>Services</option><option value='user_purchase'>User Purchase</option><option value='nda'>NDA</option>");
				} else if(val_tipe_pr == 'nda'){
					$('.modal [name="tipe_pr"]').empty();
					$('.modal [name="tipe_pr"]').append("<option value=''>Pilih Salah Satu</option><option value='direct_charge'>Direct Charge</option><option value='services'>Services</option><option value='user_purchase'>User Purchase</option><option value='nda' selected>NDA</option>");
				} else if(val_tipe_pr == 'services'){
					$('.modal [name="tipe_pr"]').empty();
					$('.modal [name="tipe_pr"]').append("<option value=''>Pilih Salah Satu</option><option value='direct_charge'>Direct Charge</option><option value='services' selected>Services</option><option value='user_purchase'>User Purchase</option><option value='nda'>NDA</option>");
				} else{
					$('.modal [name="tipe_pr"]').empty();
					$('.modal [name="tipe_pr"]').append("<option value=''>Pilih Salah Satu</option><option value='direct_charge'>Direct Charge</option><option value='services'>Services</option><option value='user_purchase' selected>User Purchase</option><option value='nda'>NDA</option>");
				}

				if (val_tipe_pr == 'direct_charge' && val_tipe_pengadaan == 'barang') {
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang' selected>Barang</option>");
				} else if (val_tipe_pr == 'services' && val_tipe_pengadaan == 'jasa') {
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa' selected>Jasa</option>");
				} else if (val_tipe_pr == 'user_purchase' && val_tipe_pengadaan == 'barang') {
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang' selected>Barang</option>");
				} else if (val_tipe_pr == 'user_purchase' && val_tipe_pengadaan == 'jasa') {
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa' selected>Jasa</option>");
				} else if (val_tipe_pr == 'nda' && val_tipe_pengadaan == 'barang') {
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang' selected>Barang</option>");
				} else if (val_tipe_pr == 'nda' && val_tipe_pengadaan == 'jasa') {
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa' selected>Jasa</option>");
				}

				if (val_tipe_pr == 'direct_charge' && val_tipe_pengadaan == 'barang' && val_jenis_pengadaan == 'stock') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock' selected>Stock</option><option value='non_stock'>Non Stock</option>");
				} else if (val_tipe_pr == 'direct_charge' && val_tipe_pengadaan == 'barang' && val_jenis_pengadaan == 'non_stock') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock' selected>Non Stock</option>");
				} else if (val_tipe_pr == 'services' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_konstruksi') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi' selected>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				} else if (val_tipe_pr == 'services' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_konsultasi') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi' selected>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				} else if (val_tipe_pr == 'services' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_lainnya') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya' selected>Jasa Lainnya</option>");
				} else if (val_tipe_pr == 'user_purchase' && val_tipe_pengadaan == 'barang' && val_jenis_pengadaan == 'stock') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock' selected>Stock</option><option value='non_stock'>Non Stock</option>");
				} else if (val_tipe_pr == 'user_purchase' && val_tipe_pengadaan == 'barang' && val_jenis_pengadaan == 'non_stock') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock' selected>Non Stock</option>");
				} else if (val_tipe_pr == 'user_purchase' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_konstruksi') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi' selected>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				} else if (val_tipe_pr == 'user_purchase' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_konsultasi') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi' selected>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				} else if (val_tipe_pr == 'user_purchase' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_lainnya') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya' selected>Jasa Lainnya</option>");
				} else if (val_tipe_pr == 'nda' && val_tipe_pengadaan == 'barang' && val_jenis_pengadaan == 'stock') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock' selected>Stock</option><option value='non_stock'>Non Stock</option>");
				} else if (val_tipe_pr == 'nda' && val_tipe_pengadaan == 'barang' && val_jenis_pengadaan == 'non_stock') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock' selected>Non Stock</option>");
				} else if (val_tipe_pr == 'nda' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_konstruksi') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi' selected>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				} else if (val_tipe_pr == 'nda' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_konsultasi') {

					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi' selected>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				} else if (val_tipe_pr == 'nda' && val_tipe_pengadaan == 'jasa' && val_jenis_pengadaan == 'jasa_lainnya') {
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya' selected>Jasa Lainnya</option>");
				}

				//---------------------------------- metode pengadaan

				if (val_tipe_pr == 'direct_charge' && val_tipe_pengadaan == 'barang' &&val_metode_pengadaan == 1) {
					console.log('direct_charge barang metode 1');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1' selected>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}  

				else if (val_tipe_pr == 'direct_charge' && val_tipe_pengadaan == 'barang'  &&val_metode_pengadaan == 2) {
					console.log('direct_charge barang metode 2');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2' selected>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'direct_charge' && val_tipe_pengadaan == 'barang'  &&val_metode_pengadaan == 4) {
					console.log('direct_charge barang metode 4');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4' selected>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'direct_charge' && val_tipe_pengadaan == 'barang'  &&val_metode_pengadaan == 5) {
					console.log('direct_charge barang metode 5');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5' selected>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'services' && val_tipe_pengadaan == 'jasa'  &&val_metode_pengadaan == 1) {
					console.log('services jasa metode 1');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1' selected>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'services' && val_tipe_pengadaan == 'jasa'  &&val_metode_pengadaan == 2) {
					console.log('services jasa metode 2');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2' selected>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'services' && val_tipe_pengadaan == 'jasa'  &&val_metode_pengadaan == 4) {
					console.log('services jasa metode 4');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4' selected>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'services' && val_tipe_pengadaan == 'jasa'  &&val_metode_pengadaan == 5) {
					console.log('services jasa metode 5');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5' selected>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'user_purchase' && (val_tipe_pengadaan == 'jasa' || val_tipe_pengadaan == 'barang') && val_metode_pengadaan == 1) {
					console.log('user_purchase metode 1');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1' selected>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'user_purchase' && (val_tipe_pengadaan == 'jasa' || val_tipe_pengadaan == 'barang') && val_metode_pengadaan == 2) {
					console.log('user_purchase metode 2');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2' selected>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'user_purchase' && (val_tipe_pengadaan == 'jasa' || val_tipe_pengadaan == 'barang') && val_metode_pengadaan == 4) {
					console.log('user_purchase metode 4');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4' selected>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'user_purchase' && (val_tipe_pengadaan == 'jasa' || val_tipe_pengadaan == 'barang') && val_metode_pengadaan == 5) {
					console.log('user_purchase metode 5');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5' selected>Pengadaan Langsung</option>");
				}

				else if (val_tipe_pr == 'nda' && (val_tipe_pengadaan == 'jasa' || val_tipe_pengadaan == 'barang') && val_metode_pengadaan == 3) {
					console.log('nda metode 1');
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='3' selected>Swakelola</option>");
				}				
 
			$('.modal [name="pengadaan"]').on('change', function(){
				
				var pengadaan = $(this).val();

				// Change option on select based on parent
				if (pengadaan == "barang") {
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>Non Stock</option>");
				}else if(pengadaan == "jasa"){
					$('.modal [name="jenis_pengadaan"]').empty();
					$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_nonkonstruksi'>Jasa non-Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");
				}else{
					// $('[name="jenis_pengadaan"]').empty();
					// $('[name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
				}
			});

			jenis_pengadaan = $('.form3 [name="tipe_pengadaan"]').val()
			if (jenis_pengadaan == "barang") {
				$('.modal [name="jenis_pengadaan"]').empty();
				$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>non Stock</option>");
			}else if(jenis_pengadaan == "jasa"){
				$('.modal [name="jenis_pengadaan"]').empty();
				$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");

			}else{
				// $('[name="jenis_pengadaan"]').empty();
				// $('[name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
			}
 
			$('.modal [name="tipe_pr"]').on('change', function(){
				
				var tipe_pr = $(this).val();

				// Change option on select based on parent
				if (tipe_pr == "direct_charge") {
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option>");
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}
				else if(tipe_pr == "services"){
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa'>Pengadaan Jasa</option>");
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
				}
				else if(tipe_pr == "user_purchase"){
					$('.modal [name="tipe_pengadaan"]').empty();
					$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='5'>Pengadaan Langsung</option>");
				}else if(tipe_pr == "nda"){
					$('.modal [name="pengadaan"]').empty();
					$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
					$('.modal [name="metode_pengadaan"]').empty();
					$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='3'>Swakelola</option>");
				}
			});

					// PENGADAAN TIPE
					$('.modal [name="tipe_pengadaan"]').on('click', function(){
						// get parent value
						var pengadaan = $(this).val();
						// alert(pengadaan);

						// Change option on select based on parent
						if (pengadaan == "barang") {
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>non Stock</option>");
						}else if(pengadaan == "jasa"){
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");

						}else{
							// $('.modal [name="jenis_pengadaan"]').empty();
							// $('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
						}
					});

					//ANALISA RISIKO
					var textbox = '';
					$('.nm-tg').click(function() {
						$('.nm-wrapper').addClass('active');
						
						line 	= "."+$(this).closest('tr').attr('class'); 
						parent	= $(this).parent().parent().find('[name="'+$(this).attr("name")+'"]'); 
						// console.log(parent);
						// var textbox = parent+' [name="'+$(this).attr("name")+'"]';
						
						if ('[name="'+$(this).attr("name")+'"]' == "manusia") {
								$('.new-matrix .manusia_desc').show();
								$('.new-matrix .aset_desc').hide();
								$('.new-matrix .lingkungan_desc').hide();
								$('.new-matrix .hukum_desc').hide();
							}else if ('[name="'+$(this).attr("name")+'"]' == "asset") {
								$('.new-matrix .manusia_desc').hide();
								$('.new-matrix .aset_desc').show();
								$('.new-matrix .lingkungan_desc').hide();
								$('.new-matrix .hukum_desc').hide();
							}else if ('[name="'+$(this).attr("name")+'"]' == "lingkungan") {
								$('.new-matrix .manusia_desc').hide();
								$('.new-matrix .aset_desc').hide();
								$('.new-matrix .lingkungan_desc').show();
								$('.new-matrix .hukum_desc').hide();
							}else if ('[name="'+$(this).attr("name")+'"]' == "hukum") {
								$('.new-matrix .manusia_desc').hide();
								$('.new-matrix .aset_desc').hide();
								$('.new-matrix .lingkungan_desc').hide();
								$('.new-matrix .hukum_desc').show();
						}
					});

					if ($('#Analisa #total span span').text() == 'E') {
						analisa = 1;
					} else if ($('#Analisa #total span span').text() == 'H') {
						analisa = 1;
					} else if ($('#Analisa #total span span').text() == 'M') {
						analisa = 2;
					} else if ($('#Analisa #total span span').text() == 'L') {
						analisa = 3;
					} else{
						analisa = '';
					}
					
					var val_e = $('[name="jenis_pengadaan"]').val();

					$.ajax({
							url: '<?php echo site_url('main/get_dpt_type/')?>/'+val_e,
							// data: category,
							dataType: 'json',
							complete : function(){
							},
							success: function(dpt){
								// console.log(dpt);
								$('#DPTList .checkboxWrapper').empty();
								$('#DPTList label').css("float", "left");

								$('#DPTList .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
								dpt.forEach(function(element) {
									if (element.score == '' || element.score == null) {
										score = '';
									} else {
										score = '('+element.score+')';
									}

									$('#DPTList .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id_vendor+'" name="type[]" type="checkbox" value="'+element.id_vendor+'"/> <label for="option'+element.id_vendor+'">'+element.vendor+' '+score+'</label> </div>');
								});
							}
						});

					$('[name="jenis_pengadaan"]').on('click',function() {
						// abcd += '';
						var dpt_type = $(this).val();

						$.ajax({
							url: '<?php echo site_url('main/get_dpt_type/')?>/'+dpt_type,
							// data: category,
							dataType: 'json',
							complete : function(){
							},
							success: function(dpt){
								// console.log(dpt);
								$('#DPTList .checkboxWrapper').empty();
								$('#DPTList label').css("float", "left");

								$('#DPTList .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
								dpt.forEach(function(element) {
									if (element.score == '' || element.score == null) {
										score = '';
									} else {
										score = '('+element.score+')';
									}

									$('#DPTList .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id_vendor+'" name="type[]" type="checkbox" value="'+element.id_vendor+'"/> <label for="option'+element.id_vendor+'">'+element.vendor+' '+score+'</label> </div>');
								});
							}
						});
						// alert(abcd);
					});

					$('.nm-box').click(function() {
						var matrix = parseInt($(this).text());
						// APPEND TO INPUT
						nmBox(parent, matrix);
						// console.log(nmBox(parent, matrix))
						// GET CLASS OF THE INPUT
							var question 	= line;
							var _question 	= question+" input";
							// console.log("Line >>"+question);
							// console.log("Question >>"+_question);

							var manusia		= question+' [name="manusia[]"]';
							var asset		= question+' [name="asset[]"]';
							var lingkungan	= question+' [name="lingkungan[]"]';
							var hukum		= question+' [name="hukum[]"]';
							var catatan		= question+' .catatan';
							
							
							var _qtotal	= 0;
							// define category each question
							var _manusia	= parseInt($(manusia).val());
							var _asset		= parseInt($(asset).val());
							var _lingkungan	= parseInt($(lingkungan).val());
							var _hukum		= parseInt($(hukum).val());
							
							var cat_manusia		= setCategory(_manusia);
							var cat_asset		= setCategory(_asset);
							var cat_lingkungan	= setCategory(_lingkungan);
							var cat_hukum		= setCategory(_hukum);
												
							
								if (cat_manusia == "extreme" || cat_asset == "extreme" || cat_lingkungan == "extreme" || cat_hukum == "extreme") {
									category = '<span id="catatan" class="catatan red">E</span>';
								}else if (cat_manusia == "high" || cat_asset == "high" || cat_lingkungan == "high" || cat_hukum == "high") {
									category = '<span id="catatan" class="catatan red">H</span>';
								}else  if (cat_manusia == "medium" || cat_asset == "medium" || cat_lingkungan == "medium" || cat_hukum == "medium") {
									category = '<span id="catatan" class="catatan yellow">M</span>';
								}else if (cat_manusia == "low" || cat_asset == "low" || cat_lingkungan == "low" || cat_hukum == "low") {
									category = '<span id="catatan" class="catatan green">L</span>';
								}else{
									category = '<span id="catatan" class="catatan">?</span>';
								}
							
								$(catatan).empty();
								$(catatan).append(category);
								
								var lineTotal	= $(line+" td .catatan .catatan").text();
								var grandTotal	= getTotal();
								
								console.log("TOTAL>>"+grandTotal);

								var cat__ = 0;
								if (grandTotal.indexOf("E") >= 0) {
									$("#total").empty();
									$("#total").append('<span class="catatan red">E</span>');
									// console.log(">>>> Extreme");
									cat__="1";

								}else if (grandTotal.indexOf("H") >= 0) {
									$("#total").empty();
									$("#total").append('<span class="catatan red">H</span>');
									// console.log(">>>> High");
									cat__="1";

								}else if (grandTotal.indexOf("M") >= 0) {
									$("#total").empty();
									$("#total").append('<span class="catatan yellow">M</span>');
									// console.log(">>>> Medium");
									cat__="2";

								}else if (grandTotal.indexOf("L") >= 0) {
									$("#total").empty();
									$("#total").append('<span class="catatan green">L</span>');
									// console.log(">>>> Low");
									cat__="3";
										
								}
							
							// GET THIS DPT RECOMMENDATION+cat__
							$.ajax({
								url: '<?php echo site_url('main/get_dpt_csms/')?>/'+cat__,
								// data: category,
								dataType: 'json',
								complete : function(){
								},
								success: function(dpt){
									// console.log(dpt);
									$('#DPTList .checkboxWrapper').empty();
									$('#DPTList label').css("float", "left");

									$('#DPTList .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
									dpt.forEach(function(element) {
										if (element.score == '' || element.score == null) {
											score = '';
										} else {
											score = '('+element.score+')';
										}
										
										$('#DPTList .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id_vendor+'" name="type[]" type="checkbox" value="'+element.id_vendor+'"/> <label for="option'+element.id_vendor+'">'+element.vendor+' '+score+'</label> </div>');
									});
								}
							});

							$("#searchDPT").on('keyup', function(){
								var matcher = new RegExp($(this).val(), 'gi');
								// console.log(matcher);
								// console.log($(this).val());
								$('.checkboxWrapper').show().not(function(){
									return matcher.test($(this).find('.inputGroup label').text())
								}).hide();
							});
						// });
					
					//Close the pop up 
					$('.nm-wrapper').removeClass('active');

					// Reset Parent Value
					parent = '';
				});

				// ANALISA SWAKELOLA
					$('#Swakelola select').on('change', function(){
						$(".matrix-box").removeClass("active");
						$(".ms-item").removeClass("active");

						// SWAKELOLA
						var waktu		= parseInt($('.modal [name="waktu"]').val());
						var biaya 		= parseInt($('.modal [name="biaya"]').val());
						var tenaga 		= parseInt($('.modal [name="tenaga"]').val());
						var bahan 		= parseInt($('.modal [name="bahan"]').val());
						var peralatan	= parseInt($('.modal [name="peralatan"]').val());

						// PARAMETER
						var swakelola	= waktu + biaya + tenaga + bahan + peralatan;
						
						var _class = '.m'+(swakelola);
						// jQuery(_class).addClass("active");
						$(_class).addClass('active');
						if (swakelola >= 12) {
							$('#Swakelola .alert').empty();
							$('#Swakelola .alert').append('Pengadaan harus dilaksanakan dengan metode pemilihan pengadaan barang/jasa yang lain.');
							$('#Swakelola .btn-submit').prop('disabled',true);
							$('#Swakelola .is-primary').hide();
						} else{
							$('#Swakelola .alert').empty();
							// $('#Swakelola .alert').remove();
							$('#Swakelola .btn-submit').removeAttr('disabled');
							$('#Swakelola .is-primary').show();
						}
					});
					
					$('.nm-box').click(function() {
						$('.nm-wrapper').removeClass('active');
					});

				}
			});


			var upload = $('.buttonUpload').modal({
				render : function(el, data){
					_self = upload;

					data.onSuccess = function(){
						$(upload).data('modal').close();
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
					el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin menghapus data perencanaan pemaketan pengadaan?<span><div class="form"></div><div>');
					data.onSuccess = function(){
						// $(del).data('modal').close();
						// folder.data('plugin_folder').fetchData();
						location.reload()
					};
					data.isReset = true;
					$('.form', el).form(data).data('form');
				}
			});
		},
		renderContent: function(el, value, key){
			
			html 		= '';
			var status 	= '';
			var badge 	= '';
			var metode_pengadaan 		= value[1].value;
			var is_status 				= value[3].value;
			var is_approve 				= value[4].value;
			var _persetujuan			= value[5].value;
			var id_perencanaan_umum 	= value[6].value;
			var is_reject 				= value[7].value;
			var is_writeoff				= value[8].value;
			var _id 					= value[9].value;
			var tipe_pengadaan			= value[10].value;
			var is_approved_hse			= value[12].value;
			var keterangan 				= value[13].value;
			var note_type				= value[17].value;
			var is_planning				= value[14].value;
			var jenis_pengadaan			= value[15].value;
			var idr_anggaran			= value[16].value;
			var is_cancelled			= value[20].value;
			var desc_batal				= value[21].value;

			// console.log(value);
			// console.log(value[1].value)
			// console.log(metode_pengadaan)
			// console.log(keterangan_)
			// console.log(note_type)
			// if (keterangan_ != '' && note_type == 'reject') {
			// 	keterangan = keterangan_;
			// } else {
			// 	keterangan = '';
			// }

			idr_anggaran = parseInt(idr_anggaran);

			if (idr_anggaran >= 100000000 && idr_anggaran <= 1000000000) {
				// statement
				// console.log("sad");
			}

			if (is_status == "1") {
				var nama_pengadaan = value[19].value;
			} else {
				var nama_pengadaan = value[0].value;
			}

			// STATUS FPPBJ
			// DATA PEMAKETAN PENGADAAN DALAM STATUS FPPBJ
			if (is_status == "0" && is_approve == "0" && is_reject == "0" && is_cancelled == "0") {
				status = 'FPPBJ (Menunggu persetujuan Kadept.user)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_reject == 1 && is_approved_hse == 0 && is_approve == 0 && is_cancelled == "0") {
				status = 'FPPBJ (Direvisi Kadept.user) <span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "0" && is_approve == "1" && tipe_pengadaan == 'jasa' && (is_approved_hse == 0 ||is_approved_hse == null) && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FPPBJ (FPPBJ belum disetujui oleh HSSE)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_approve == "1" && tipe_pengadaan == 'jasa' && is_approved_hse == '2' && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FPPBJ (Direvisi Kadept.HSSE) <span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "0" && is_approve == "1" && tipe_pengadaan == 'jasa' && is_approved_hse == 1 && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FPPBJ (Menunggu persetujuan admin pengendalian)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_approve == "1" && tipe_pengadaan != 'jasa' && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FPPBJ (Menunggu persetujuan admin pengendalian)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_approve == "1" && tipe_pengadaan == 'jasa' && is_approved_hse == 1 && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FPPBJ (Direvisi Admin Pengendalian) <span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "0" && is_approve == "1" && tipe_pengadaan != 'jasa' && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FPPBJ (Direvisi Admin Pengendalian) <span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "0" && is_approve == "2" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FPPBJ (Menunggu persetujuan Kadept.Procurement)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_approve == "2" && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FPPBJ (Direvisi Kadept.Procurement) <span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "0" && is_approve == "2"  && is_reject == '0' && is_writeoff == '0' && is_cancelled == "0" && idr_anggaran <= 100000000) {
				status = 'FPPBJ (Menunggu persetujuan Kadept.Procurement)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_approve == "2" && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0" && (idr_anggaran <= 100000000)) {
				status = 'FPPBJ (Direvisi Kadept.Procurement)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "0" && is_approve == "3" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0" && idr_anggaran <= 100000000) {
				status = 'FPPBJ (Disetujui Kadept.Procurement)';
				badge = 'success';
			}
			else if (is_status == "0" && is_approve == "3" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0" && ((idr_anggaran > 100000000 && idr_anggaran <= 1000000000))) {
				status = 'FPPBJ (Menunggu persetujuan Ka.Div SDM & Umum)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_approve == "4" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0" && idr_anggaran >= 100000000 && idr_anggaran <= 1000000000) {
				status = 'FPPBJ telah di setujui Ka.Div SDM & Umum';
				badge = 'success';
			}
			else if (is_status == "0" && is_approve == "3" && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0" && idr_anggaran >= 100000000 && idr_anggaran <= 1000000000) {
				status = 'FPPBJ (Direvisi Ka.Div SDM & Umum)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "0" && is_approve == "3" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0" && (idr_anggaran > 1000000000 && idr_anggaran <= 10000000000)) {
				status = 'FPPBJ (Menunggu persetujuan Dir.Keuangan & Umum)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_approve == "4" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0" && idr_anggaran > 1000000000 && idr_anggaran <= 10000000000) {
				status = 'FPPBJ telah di setujui Dir.Keuangan & Umum';
				badge = 'success';
			}
			else if (is_status == "0" && is_approve == "3" && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0" && idr_anggaran >= 1000000000 && idr_anggaran <= 10000000000) {
				status = 'FPPBJ (Direvisi Dir.Keuangan & Umum)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "0" && is_approve == "3" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0" && idr_anggaran >= 10000000000) {
				status = 'FPPBJ (Menunggu persetujuan Dir.Utama)';
				badge = 'warning';
			}
			else if (is_status == "0" && is_approve == "4" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0" && idr_anggaran >= 1000000000) {
				status = 'FPPBJ telah di setujui Dir.Utama';
				badge = 'success';
			}
			else if (is_status == "0" && is_approve == "3" && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0" && idr_anggaran >= 1000000000) {
				status = 'FPPBJ (Direvisi Dir.Utama)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			
			// FKPBJ
			else if (is_status == '2' && is_approve == "0" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FKPBJ (FKPBJ ini dilanjutkan ke ka.Dept user)';
				badge = 'warning';
			}
			else if (is_status == '2' && is_approve == "0" && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FKPBJ (Direvisi ka.Dept user)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "2" && is_approve == "1" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FKPBJ (FKPBJ ini dilanjutkan ke Admin Procurement)';
				badge = 'warning';
			}
			else if (is_status == "2" && is_approve == "1" && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FKPBJ (Direvisi Admin Procurement)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "2" && is_approve == "2" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FKPBJ (FKPBJ ini dilanjutkan ke Kadept.Procurement)';
				badge = 'warning';
			}
			else if (is_status == "2" && is_approve == "2" && is_reject == 1 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FKPBJ (Direvisi Kadept.Procurement)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == "2" && is_approve == "3" && is_reject == 0 && is_writeoff == 0 && is_cancelled == "0") {
				status = 'FKPBJ';
				badge = 'success';
			}	
			
			// FP3		
			else if (is_status == 1 && is_approve == 0 && is_reject == 0 && is_cancelled == "0") {
					status = 'FP3 (Menunggu Ka.Dept User)';
					badge = 'warning';
			}
			else if (is_status == 1 && is_approve == 1 && is_reject == 0 && is_cancelled == "0") {
				status = 'FP3 (Menunggu Admin Pengendalian)';
				badge = 'warning';
			}
			else if (is_status == 1 && is_approve == 2 && is_reject == 0 && is_cancelled == "0") {
				status = 'FP3 (Menunggu Ka.Dept Procurement)';
				badge = 'warning';
			}
			else if (is_status == 1 && is_approve == 1 && is_reject == 1 && is_cancelled == "0") {
				status = 'FP3 (Direvisi Ka.Dept User) <span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fp3_reject tooltip';
			}
			else if (is_status == 1 && is_approve == 2 && is_reject == 1 && is_cancelled == "0") {
				status = 'FP3 (Direvisi Admin Pengendalian)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fp3_reject tooltip';
			}
			else if (is_status == 1 && is_approve == 3 && is_reject == 1 && is_cancelled == "0") {
				status = 'FP3 (Direvisi Ka.Dept Procurement)<span class="tooltiptext reject">'+keterangan+'</span>';
				badge = 'danger fp3_reject tooltip';
			}
			else if (is_status == 1 && is_approve == 3 && is_reject == 0 && is_cancelled == "0" && idr_anggaran < 100000000 ) {
				status = 'FP3 (telah disetujui Kadept.Procurement)';
				badge = 'success';
			}
			else if (is_status == 1 && is_approve == "3" && is_reject == 0 && is_cancelled == "0" && ((idr_anggaran >= 100000000 && idr_anggaran <= 1000000000))) {
				status = 'FP3 (Menunggu persetujuan Ka.Div SDM & Umum)';
				badge = 'warning';
			} else if (is_status == 1 && is_approve == "4" && is_reject == 0 && is_cancelled == "0" && idr_anggaran >= 100000000 && idr_anggaran <= 1000000000) {
				status = 'FP3 telah di setujui Ka.Div SDM & Umum';
				badge = 'success';
			} else if (is_status == 1 && is_approve == "4" && is_reject == 1 && is_cancelled == "0" && idr_anggaran >= 100000000 && idr_anggaran <= 1000000000) {
				status = 'FP3 (Direvisi Ka.Div SDM & Umum)<span class="tooltiptext reject">' + keterangan + '</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == 1 && is_approve == "3" && is_reject == 0 && is_cancelled == "0" && (idr_anggaran > 1000000000 && idr_anggaran <= 10000000000)) {
				status = 'FP3 (Menunggu persetujuan Dir.Keuangan & Umum)';
				badge = 'warning';
			} else if (is_status == 1 && is_approve == "4" && is_reject == 0 && is_cancelled == "0" && idr_anggaran > 1000000000 && idr_anggaran <= 10000000000) {
				status = 'FP3 telah di setujui Dir.Keuangan & Umum';
				badge = 'success';
			} else if (is_status == 1 && is_approve == "4" && is_reject == 1 && is_cancelled == "0" && idr_anggaran >= 1000000000 && idr_anggaran <= 10000000000) {
				status = 'FP3 (Direvisi Dir.Keuangan & Umum)<span class="tooltiptext reject">' + keterangan + '</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else if (is_status == 1 && is_approve == "3" && is_reject == 0 && is_cancelled == "0" && idr_anggaran >= 10000000000) {
				status = 'FP3 (Menunggu persetujuan Dir.Utama)';
				badge = 'warning';
			} else if (is_status == 1 && is_approve == "4" && is_reject == 0 && is_cancelled == "0" && idr_anggaran >= 1000000000) {
				console.log('Masuk Ke Kondisi Sukses >10M ');
				status = 'FP3 telah di setujui Dir.Utama';
				badge = 'success';
			} else if (is_status == 1 && is_approve == "4" && is_reject == 1 && is_cancelled == "0" && idr_anggaran >= 1000000000) {
				status = 'FP3 (Direvisi Dir.Utama)<span class="tooltiptext reject">' + keterangan + '</span>';
				badge = 'danger fppbj_reject tooltip';
			} else if (is_cancelled == "1"){
				status = 'FP3 (Batal)<span class="tooltiptext reject">' + desc_batal + '</span>';
				badge = 'danger fppbj_reject tooltip';
			}
			else{
				$('.note'+_id).empty();
			}

			html += '<div class="caption"><p>'+nama_pengadaan+'</p><p>'+value[1].value+'</p><p>'+value[2].value+'</p><p><span class="badge is-'+badge+'">'+status+'</span></p></div>';
			return html;
		},

		additionFeature: function(el){
			<?php if($admin['id_role'] == 5){ ?>
				// el.prepend(insertButton(site_url+"pemaketan/insert/<?php echo $id_division;?>"));
				<?php //if ($admin['id_division'] != 5 ) { ?>
				el.prepend(insertStepButton(site_url+"pemaketan/insertStep/<?php echo $id_division;?>"));
				<?php //} ?>
			<?php }?>
			
			// el.prepend(exportButton(site_url+"pemaketan/export/<?php echo $id_division;?>"));
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


	var resiko;
	var resikoOptions = {
		header: 'Tambah Data FPPBJ',
		render : function(el, data){

			var fw = $(el).formWizard({
				data: data,
				url: '<?php echo site_url("pemaketan/insert");?>',
				onNext: function(){
					_wizard = $(fw).data('formWizard');
					
					step = _wizard.options.data.step;
					var _step = [];
					var i = 1;
					$.each(step, function(key, value){
						_step[i] = key;
						i++;
					})
					_formWrapper = $('.wizard-content #step'+_wizard.options.currentPosition);
					_form = $('form',_wizard.options.wrapper);
					formData = new FormData( _form[0]);
					formData .append('validation', _step[_wizard.options.currentPosition]);

					console.log('Ini cureent position '+_step[_wizard.options.currentPosition]);

					if (_wizard._nextInFlight) {
						return false;
					}
					_wizard._nextInFlight = true;
					_return = false;

					$.ajax({
						url			: '<?php echo site_url('pemaketan/insertFPPBJ')?>',
						data		: formData,
						method 		: 'POST',
						processData	: false,
						contentType	: false,
						dataType	: 'json',	
						success: function(xhr){
							_formWrapper.data('form').element = _formWrapper;

							if(xhr.status=='error') {
								_formWrapper.data('form').options.errorMessage = 'Terjadi Kesalahan';
								_formWrapper.data('form').generateError(xhr.form);

								_return = false;
							}else{
								_formWrapper.data('form').removeError(_formWrapper);
								_return = true;
							}

							if (_return) {
								_wizard.options.currentPosition++;
								_wizard.reset();
							}
						},
						complete: function() {
							_wizard._nextInFlight = false;
						}
					});

					return false;
				},
				onPrev: function(){
					// Allow previous navigation
					return true;
				},
				onSubmit: function(){
					// alert('on submit');
					// location.reload();
				},
				onSuccessSubmit: function(){
					// alert('onSuccessSubmit');
					location.reload();
					$(resiko).data('modal').close();
					folder.data('plugin_folder').fetchData();
					// console.log(resiko);
				},
				onSuccess: function(){
					// alert('onSuccess');
					// location.reload();
					$(resiko).data('modal').close();
				}
			});

			$('#step5 .form5').append('<div class="alert" style="color: #f90606; font-size: 95%; text-align: center;"></div>');

			$('#multi-year').remove();
			$('#step2 .form4').append('<div id="multi-year"><input style="margin-left: 30%;" type="checkbox" value="1" name="is_multiyear">Multiyear Budget</div>');
			
			//$( '.money' ).mask('999,999,999,999,999,999,999.99', {reverse: true});
		  	//$( '.money' ).mask('999,999,999,999,999,999,999.99', {reverse: true});

		  	<?php if ($is_perencanaan == '0') { ?>
		  		$('#step2 .form0').hide();
		  	<?php } ?>
			
			//MULTIYEAR ANGGARAN
				// append checkbox to define fppbj is multiyear
				$('.modal [name="is_multiyear"]').on('change', function(){
					// if so....
					
					var __no = 1;
					var __form = '<div style="margin:0.35em 0.625em 0.75em"><label for="">Anggaran (IDR)</label><input type="text" class="form-control money" id="" value="" name="idr_anggaran[]" placeholder="" style="text-align: right;"></div><div style="margin:0.35em 0.625em 0.75em"><label for="">Anggaran (USD)</label><input type="text" class="form-control money" id="" value="" name="usd_anggaran[]" placeholder="" style="text-align: right;"></div><div style="margin:0.35em 0.625em 0.75em"><label for="">Tahun Anggaran*</label><input type="number" class="form-control" id="" value="" name="year_anggaran[]" placeholder=""></div>';
					var __line = '<hr style="display: block; color:#3273dc; border-bottom: 1px #3273dc solid; margin: 20px 0;">';
					
					if ($(this).is(":checked")) {
						_clear();
						$('#step2 .form8').append(__line+'<div class="multiple-budget"></div><div><a id="add_budget">Tambah Tahun Anggaran</a> || <a id="min_budget">Batal Tahun Anggaran</a></div>'+__line);
							$('#step2 .form8 .multiple-budget').append('<div id="budget-'+__no+'"><p style="color: #3273dc; font-weight: bold;">Detail Anggaran #'+__no+'</p>'+__form+'</div>');
						
						$('#add_budget').on('click', function(){
							__no ++;

							$('#step2 .form8 .multiple-budget').append('<div id="budget-'+__no+'"><p style="color: #3273dc; font-weight: bold;">Detail Anggaran #'+__no+'</p>'+__form+'</div>');
						});

						$('#min_budget').on('click', function(){
							if (__no == 1) {
								_clear();
								$('[name="is_multiyear"]').prop("checked",false);
								$('#step2 .form8').append(__form);
							} else {
								$('#budget-'+__no).remove();
							}
							
							__no--;
						});

					}else{
						_clear();
						$('#step2 .form8').append(__form);
					}

					function _clear(){
						$('#step2 .form8').empty();
						$('#step2 .form9').remove();
						$('#step2 .form10').remove();
					}
					
				});

			// TIPE PR
				$('.modal [name="tipe_pr"]').on('change', function(){
					// get parent value
					var tipe_pr = $(this).val();
					// alert(pengadaan);

					// Change option on select based on parent
					if (tipe_pr == "direct_charge") {
						$('.modal [name="pengadaan"]').empty();
						$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option>");
						$('.modal [name="metode_pengadaan"]').empty();
						$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
					}
					else if(tipe_pr == "services"){
						$('.modal [name="pengadaan"]').empty();
						$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa'>Pengadaan Jasa</option>");
						$('.modal [name="metode_pengadaan"]').empty();
						$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
					}
					else if(tipe_pr == "user_purchase"){
						$('.modal [name="pengadaan"]').empty();
						$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
						$('.modal [name="metode_pengadaan"]').empty();
						$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='5'>Pengadaan Langsung</option>");
					}
					else if(tipe_pr == "nda"){
						$('.modal [name="pengadaan"]').empty();
						$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
						$('.modal [name="metode_pengadaan"]').empty();
						$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='3'>Swakelola</option>");
					}
				});

			// PENGADAAN TIPE
				$('.modal [name="pengadaan"]').on('change', function(){
					// get parent value
					var pengadaan = $(this).val();
					// alert(pengadaan);

					// Change option on select based on parent
					if (pengadaan == "barang") {
						$('.modal [name="jenis_pengadaan"]').empty();
						$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>non Stock</option>");
					}else if(pengadaan == "jasa"){
						$('.modal [name="jenis_pengadaan"]').empty();
						$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");

					}else{
						// $('.modal [name="jenis_pengadaan"]').empty();
						// $('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
					}
				});
			

			// ANALISA RISIKO
				// POP UP ANALISA RISIKO
					var textbox = '';
					$('.nm-tg').click(function() {
						$('.nm-wrapper').addClass('active');
						
						line 	= "."+$(this).closest('tr').attr('class'); 
						parent	= $(this).parent().parent().find('[name="'+$(this).attr("name")+'"]'); 
						// console.log(parent);
						// var textbox = parent+' [name="'+$(this).attr("name")+'"]';
						
						if ('[name="'+$(this).attr("name")+'"]' == "manusia") {
								$('.new-matrix .manusia_desc').show();
								$('.new-matrix .aset_desc').hide();
								$('.new-matrix .lingkungan_desc').hide();
								$('.new-matrix .hukum_desc').hide();
							}else if ('[name="'+$(this).attr("name")+'"]' == "asset") {
								$('.new-matrix .manusia_desc').hide();
								$('.new-matrix .aset_desc').show();
								$('.new-matrix .lingkungan_desc').hide();
								$('.new-matrix .hukum_desc').hide();
							}else if ('[name="'+$(this).attr("name")+'"]' == "lingkungan") {
								$('.new-matrix .manusia_desc').hide();
								$('.new-matrix .aset_desc').hide();
								$('.new-matrix .lingkungan_desc').show();
								$('.new-matrix .hukum_desc').hide();
							}else if ('[name="'+$(this).attr("name")+'"]' == "hukum") {
								$('.new-matrix .manusia_desc').hide();
								$('.new-matrix .aset_desc').hide();
								$('.new-matrix .lingkungan_desc').hide();
								$('.new-matrix .hukum_desc').show();
						}
					});

					$('.nm-box').click(function() {
						var matrix = parseInt($(this).text());
						// APPEND TO INPUT
						nmBox(parent, matrix);
						// console.log(nmBox(parent, matrix))
						// GET CLASS OF THE INPUT
							var question 	= line;
							var _question 	= question+" input";
							// console.log("Line >>"+question);
							// console.log("Question >>"+_question);

							var manusia		= question+' [name="manusia[]"]';
							var asset		= question+' [name="asset[]"]';
							var lingkungan	= question+' [name="lingkungan[]"]';
							var hukum		= question+' [name="hukum[]"]';
							var catatan		= question+' .catatan';
							
							
							var _qtotal	= 0;
							// define category each question
							var _manusia	= parseInt($(manusia).val());
							var _asset		= parseInt($(asset).val());
							var _lingkungan	= parseInt($(lingkungan).val());
							var _hukum		= parseInt($(hukum).val());
							
							var cat_manusia		= setCategory(_manusia);
							var cat_asset		= setCategory(_asset);
							var cat_lingkungan	= setCategory(_lingkungan);
							var cat_hukum		= setCategory(_hukum);
												
							
								if (cat_manusia == "extreme" || cat_asset == "extreme" || cat_lingkungan == "extreme" || cat_hukum == "extreme") {
									category = '<span id="catatan" class="catatan red">E</span>';
								}else if (cat_manusia == "high" || cat_asset == "high" || cat_lingkungan == "high" || cat_hukum == "high") {
									category = '<span id="catatan" class="catatan red">H</span>';
								}else  if (cat_manusia == "medium" || cat_asset == "medium" || cat_lingkungan == "medium" || cat_hukum == "medium") {
									category = '<span id="catatan" class="catatan yellow">M</span>';
								}else if (cat_manusia == "low" || cat_asset == "low" || cat_lingkungan == "low" || cat_hukum == "low") {
									category = '<span id="catatan" class="catatan green">L</span>';
								}else{
									category = '<span id="catatan" class="catatan">?</span>';
								}
							
								$(catatan).empty();
								$(catatan).append(category);
								
								var lineTotal	= $(line+" td .catatan .catatan").text();
								var grandTotal	= getTotal();
								
								// console.log("TOTAL>>"+grandTotal);

								var cat__ = 0;
								if (grandTotal.indexOf("E") >= 0) {
										$("#total").empty();
										$("#total").append('<span class="catatan red">E</span>');
										// console.log(">>>> Extreme");
										cat__="1";

									}else if (grandTotal.indexOf("H") >= 0) {
										$("#total").empty();
										$("#total").append('<span class="catatan red">H</span>');
										// console.log(">>>> High");
										cat__="1";

									}else if (grandTotal.indexOf("M") >= 0) {
										$("#total").empty();
										$("#total").append('<span class="catatan yellow">M</span>');
										// console.log(">>>> Medium");
										cat__="2";

									}else if (grandTotal.indexOf("L") >= 0) {
										$("#total").empty();
										$("#total").append('<span class="catatan green">L</span>');
										// console.log(">>>> Low");
										cat__="3";
										
								}
							
							// GET THIS DPT RECOMMENDATION
							/*$.ajax({
								url: '<?php echo site_url('main/get_dpt_csms/')?>/'+cat__,
								// data: category,
								dataType: 'json',
								complete : function(){
								},
								success: function(dpt){
									// console.log(dpt);
									$('#step4 .checkboxWrapper').empty();
									$('#step4 label').css("float", "left")

									$('#step4 .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
									dpt.forEach(function(element) {
										if (element.score == '' || element.score == null) {
											score = '';
										} else {
											score = '('+element.score+')';
										}

										$('#step4 .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id_vendor+'" name="type[]" type="checkbox" value="'+element.id_vendor+'"/> <label for="option'+element.id_vendor+'">'+element.vendor+' '+score+'</label> </div>');
									});
								}
							});*/

							$("#searchDPT").on('keyup', function(){
								var matcher = new RegExp($(this).val(), 'gi');
								// console.log(matcher);
								// console.log($(this).val());
								$('.checkboxWrapper').show().not(function(){
									return matcher.test($(this).find('.inputGroup label').text())
								}).hide();
							});
						// });
					
					//Close the pop up 
					$('.nm-wrapper').removeClass('active');

					// Reset Parent Value
					parent = '';
				});
					
				$('.nm-box').click(function() {
					$('.nm-wrapper').removeClass('active');
				});

			// ANALISA SWAKELOLA
			$('#step5 select').on('change', function(){
				$(".matrix-box").removeClass("active");
				$(".ms-item").removeClass("active");

				// SWAKELOLA
				var waktu		= parseInt($('.modal [name="waktu"]').val());
				var biaya 		= parseInt($('.modal [name="biaya"]').val());
				var tenaga 		= parseInt($('.modal [name="tenaga"]').val());
				var bahan 		= parseInt($('.modal [name="bahan"]').val());
				var peralatan	= parseInt($('.modal [name="peralatan"]').val());

				// PARAMETER
				var swakelola	= waktu + biaya + tenaga + bahan + peralatan;
				
				var _class = '.m'+(swakelola);
				// jQuery(_class).addClass("active");
				$(_class).addClass('active');
				if (swakelola >= 12) {
					$('#step5 .alert').empty();
					$('#step5 .alert').append('Pengadaan harus dilaksanakan dengan metode pemilihan pengadaan barang/jasa yang lain.');
					$('#step5 .btn-to').prop('disabled',true);
					$('#step5 .btn-to').css('display','none');
				} else{
					$('#step5 .alert').empty();
					// $('#step5 .alert').remove();
					$('#step5 .btn-to').removeAttr('disabled');
					$('#step5 .btn-to').css('display','block');
				}
			});

			$('[name="jenis_pengadaan"]').on('change',function() {
				
				var dpt = $(this).val();

				$.ajax({
					url: '<?php echo site_url('main/get_dpt_type')?>/'+dpt,
					// data: category,
					dataType: 'json',
					complete : function(){
					},
					success: function(dpt){
						// console.log(dpt);
						$('#step4 .checkboxWrapper').empty();
						$('#step4 label').css("float", "left")

						$('#step4 .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
						dpt.forEach(function(element) {
							$('#step4 .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id_vendor+'" name="type[]" type="checkbox" value="'+element.id_vendor+'"/> <label for="option'+element.id_vendor+'">'+element.vendor+'</label> </div>');
						});
					}
				});
			});
			
			stepButton();
			// _close();
		}
	};

	$(document).off('click.pemaketanFppbj', '.buttonStep');
	$(document).on('click.pemaketanFppbj', '.buttonStep', function(e){
		if ($(this).data('modal')) {
			return;
		}
		e.preventDefault();
		resiko = this;
		window.resiko = resiko;
		window.folder = folder;
		$(this).modal(resikoOptions);
		$(this).data('modal').open();
	});

});


function nmBox(ele,matrix){
	ele.val(matrix);
}

function stepButton(){
	var analisa_resiko 		= $('#stepHeader3');
	var dpt 				= $('#stepHeader4');
	var analisa_swakelola 	= $('#stepHeader5');
	var csms 				= $('#stepHeader6');
	
	// HIDE ALL MENU TAB
	$(analisa_resiko).hide();
	$(dpt).hide();
	$(csms).hide();
	$(analisa_swakelola).hide();
	//METODE PENGADAAN
	$('.modal [name="pengadaan"], .modal [name="metode_pengadaan"]').on('change', function(){
		var metode 				= $('.modal [name="metode_pengadaan"]').val();
		var pengadaan		 	= $('.modal [name="pengadaan"]').val();
		var form_swakelola = '<div class="form blockWrapper"><fieldset class="form-group form0" for=""><label for="">Waktu*</label><select name="waktu" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Penyelesaian Pekerjaan ≤ 3 bulan</option><option value="2">Penyelesaian Pekerjaan &gt; 3 bulan s.d &lt; 6 bulan</option><option value="3">Penyelesaian Pekerjaan ≥ 6 bulan</option></select></fieldset><fieldset class="form-group form1" for=""><label for="">Biaya*</label><select name="biaya" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Biaya Pelaksanaan Pekerjaan&nbsp;≤ 50 juta</option><option value="2">Biaya Pelaksanaan Pekerjaan&nbsp;&gt; 50 juta s.d &lt; 100 juta</option><option value="3">Biaya Pelaksanaan Pekerjaan&nbsp;≥ 100 juta</option></select></fieldset><fieldset class="form-group form2" for=""><label for="">Tenaga Kerja*</label><select name="tenaga" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan memenuhi sebagai perencana dan pelaksana dan pengawas</option><option value="2">Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan memenuhi salah satu atau lebih sebagai perencana dan/atau pelaksana dan/atau pengawas</option><option value="3">Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan tidak memenuhi sebagai perencana dan pelaksana dan pengawas</option></select></fieldset><fieldset class="form-group form3" for=""><label for="">Bahan*</label><select name="bahan" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Bahan mudah didapatkan langsung oleh Pekerja NR</option><option value="2">Bahan dapat diadakan melalui pihak ketiga</option><option value="3">Bahan lebih efisien apabila diadakan oleh pihak ketiga</option></select></fieldset><fieldset class="form-group   form4" for=""><label for="">Peralatan*</label><select name="peralatan" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Ketersediaan jumlah dan kemampuan peralatan kerja memenuhi kebutuhan pekerjaan</option><option value="2">Ketersediaan jumlah dan/atau kemampuan peralatan kerja tidak memenuhi kebutuhan pekerjaan</option><option value="3">Peralatan lebih efisien apabila diadakan oleh pihak ketiga</option></select></fieldset><fieldset class="form-group   form5" for=""><div class="matrix-swakelola-wrapper"><div class="matrix-swakelola"><div class="ms-item green m1">1</div><div class="ms-item green m2">2</div><div class="ms-item green m3">3</div><div class="ms-item green m4">4</div><div class="ms-item green m5">5</div><div class="ms-item green-light m6">6</div><div class="ms-item green-light m7">7</div><div class="ms-item green-light m8">8</div><div class="ms-item green-light m9">9</div><div class="ms-item green-light m10">10</div><div class="ms-item green-light sw m11">11</div><span class="ms-line"></span><div class="ms-item yellow pk m12">12</div><div class="ms-item yellow m13">13</div><div class="ms-item red m14">14</div><div class="ms-item red m15">15</div></div></div><div class="alert" style="color: #f90606; font-size: 95%; text-align: center;"></div></fieldset><div class="form-group btn-group"><button type="button" class="button is-primary btn-prev">Sebelumnya</button><button type="button" class="button is-primary btn-submit">Lanjut</button></div></div>';


			/*******************************************************
			 ******* 			DEFINE FORM TYPE			 *******
			 *******************************************************/

			// JASA && SWAKELOLA
			if (metode == 3 && pengadaan == "jasa") {
				// SHOW MENU TAB
				console.log("Masuk ke metode Swakelola Tipe Jasa");

				$('#step2 .btn-group .btn-to').attr('id', '3');
				$('#step4 .btn-group .btn-to').attr('id', '5');
				$('#step3 .btn-prev').attr('id', '2');
				$('#step4 .btn-prev').attr('id', '3');
				$('#step5 .btn-prev').attr('id', '4');

				$(analisa_resiko).show();
				$(dpt).show();
				$(csms).show();
				$(analisa_swakelola).show();

				$('#step4 .btn-group .btn-next').removeClass('btn-save').text('Lanjut');

				// JASA NON SWAKELOLA
			} else if (metode != 3 && pengadaan == "jasa") {
				console.log("Masuk ke metode bukan Swakelola Tipe Jasa");

				// SHOW MENU TAB
				$(analisa_resiko).show();
				$(dpt).show();
				$(csms).show();

				// HIDE SWAKELOLA
				$(analisa_swakelola).hide();

				$('#step2 .btn-group .btn-to').attr('id', '3');
				$('#step4 .btn-group .btn-next').removeClass('btn-next').addClass('btn-to').attr('id', 'finish');
				$('#step3 .btn-prev').attr('id', '2');
				$('#step4 .btn-prev').attr('id', '3');

				$('#step5').hide();

				// BARANG && SWAKELOLA
			} else if (metode == 3 && pengadaan != "jasa") {
				console.log("Masuk ke metode Swakelola Tipe Bukan Jasa");

				$('#step2 .btn-to').attr('id', '4');
				$('#step4 .btn-prev').attr('id', '2');
				$('#step5 .btn-prev').attr('id', '4');

				// HIDE ALL MENU TAB
				$(analisa_resiko).hide();
				$(dpt).show();
				$(csms).hide();
				$(analisa_swakelola).show();
				$(csms).hide();

				$('#step4 .btn-to').attr('id', '5');
				$('#step5 .btn-group .btn-submit').addClass('btn-save');

				// BARANG NON SWAKELOLA
			} else if (metode != 3 && pengadaan != "jasa") {
				// $('#step5').empty();
				console.log("Masuk ke metode Bukan Swakelola Tipe Barang");
				// HIDE ALL MENU TAB
				$(analisa_resiko).hide();
				$(dpt).show();
				$(csms).hide();
				$(analisa_swakelola).hide();

				$('#step2 .btn-to').attr('id', '4');
				$('#step4 .btn-prev').attr('id', '2');
				$('#step4 .btn-group .btn-next').removeClass('btn-next').addClass('btn-to').attr('id', 'finish');
				// $('.btn-to').attr('id', ' end');

				// LAIN LAIN 
			} else {
				// $('#step5').empty();
				console.log("Masuk ke lain lain");
				// HIDE ALL MENU TAB
				$(analisa_resiko).hide();
				$(dpt).hide();
				$(csms).hide();
				$(analisa_swakelola).hide();
			}

	});
}

function setCategory(val){
	if (val >= 1 && val <= 4) {
		return 'low';
		// return '<span id="catatan" class="catatan green">L</span>';
	}else if (val >= 5 && val <= 9) {
		return 'medium';
		// return '<span id="catatan" class="catatan yellow">M</span>';		
	}else if (val >= 10 && val <= 14) {
		return 'high';
		// return '<span id="catatan" class="catatan red">H</span>';
	}else if (val >= 15 && val <= 25) {
		return 'extreme';
		// return '<span id="catatan" class="catatan red">E</span>';
	}else{
		return false;
	}
}

function _close(){
	alert('fungsi close');
	$('button.btn-save').on('click', function(){
		$(resiko).data('modal').close();
		folder.data('plugin_folder').fetchData();
		location.reload();
	});
}

function getTotal(){
	var total__ = [];
	for (let q = 1; q <= 10; q++) {
		total__.push($('.q'+q+' td .catatan .catatan').text());
	}

	return total__;
}

function filterDPT(){
	// console.log($(".inputGroup label").val());
	$("#searchDPT").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$(".inputGroup").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
}

function filterDPTFKPBJ(){
	// console.log($(".inputGroup label").val());
	$("#searchDPT").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$(".inputGroup").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
}

function reject_notif(pesan){

	var notif = '<span class="tooltiptext reject">'+pesan+'</span>';
	console.log(notif)
	if($('.badge').hasClass('fppbj_reject') == true) {
		$('.fppbj_reject').addClass('tooltip').append(notif);
	}
	
}
</script>
