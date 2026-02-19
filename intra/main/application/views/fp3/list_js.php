<script type="text/javascript">
	<?php $admin = $this->session->userdata('admin'); ?>
	$(function() {
		dataPost = {
			order: 'id',
			sort: 'desc'
		};
		var _xhr;
		$.ajax({
			url: '<?php echo site_url('fp3/formFilter') ?>',
			async: false,
			dataType: 'json',
			success: function(xhr) {
				_xhr = xhr;
			}
		})


		var folder = $('#tableGenerator').folder({
			url: '<?php echo site_url('fp3/getData/' . $id_division . '/' . $id_fppbj . '/' . $year); ?>',
			data: dataPost,
			dataRightClick: function(key, btn, value) {
				_id = value[key][6].value;
				_id_fppbj = value[key][8].value;
				is_approved = value[key][10].value;
				id_division = value[key][14].value;
				user_logged = '<?php echo $admin['id_division'] ?>';
				metode = value[key][2].value;
				tipe = value[key][15].value;
				urlpdf = site_url + "export/fp3/" + _id_fppbj;
				status_fp3 = value[key][7].value;
				id_role = '<?= $admin['id_role'] ?>'

				var status = '';
				var badge = '';
				var is_status = value[key][5].value;
				var is_approve = value[key][10].value;
				var is_reject = value[key][11].value;
				var keterangan = value[key][12].value;
				var idr_anggaran = parseInt(value[key][16].value);
				var year_anggaran = value[key][13].value;

				if (value[key][2].value == 1) {
					metode_pengadaan = 'Pelelangan';
				} else if (value[key][2].value == 2) {
					metode_pengadaan = 'Pemilihan Langsung';
				} else if (value[key][2].value == 3) {
					metode_pengadaan = 'Swakelola';
				} else if (value[key][2].value == 4) {
					metode_pengadaan = 'Penunjukan Langsung';
				} else {
					metode_pengadaan = 'Pengadaaan Langsung';
				}
				//&& (metode_pengadaan == 'Penunjukan Langsung')
				was_approved_kadept = is_approve == 3 && is_reject == 0 && idr_anggaran <= 100000000;
				was_approved_sdm = is_approve == "4" && is_reject == 0 && idr_anggaran >= 100000000 && idr_anggaran <= 1000000000; //&& (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan === 'Pelelangan')
				was_approved_dirke = is_approve == "4" && is_reject == 0 && idr_anggaran >= 1000000000; //&& (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan === 'Pelelangan')
				was_approved_dirut = is_approve == "4" && is_reject == 0 && idr_anggaran >= 10000000000; //&& (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan === 'Pelelangan')

				btn = [{
					icon: 'search',
					label: 'Lihat Data',
					class: 'buttonView',
					href: site_url + "fp3/getSingleData/" + _id
				}, {
					icon: 'file-download',
					label: 'Download FP3',
					class: 'buttonExport',
					href: site_url + "fp3/form_download_fp3/" + _id_fppbj
				}, {
					icon: 'file-download',
					label: 'Riwayat Pengadaan',
					class: 'buttonRiwayatPengadaan',
					href: site_url + "riwayat/pengadaan/" + _id_fppbj
				}];

				_btn_fkpbj = [{
					icon: 'file',
					label: 'Buat File FKPBJ',
					class: 'buttonFKPBJ',
					href: site_url + "fkpbj/add_fkpbj/" + _id_fppbj
				}];

				btn_edit = [{
					icon: 'cog',
					label: 'Edit',
					class: 'buttonEdit',
					href: site_url + "fp3/edit/" + _id
				}
				/*{
					icon: 'trash',
					label: 'Hapus',
					class: 'buttonDelete',
					href: site_url + "fp3/remove/" + _id
				}*/
				];

				if (tipe == 'jasa') {
					//console.log('ke 1');
					if (metode == 3) {
						//console.log('ke 2');
						if ((was_approved_kadept) || (was_approved_sdm) || (was_approved_dirke) || (was_approved_dirut)) {
							//console.log('ke 3');
							return btn;
						} else {
							//console.log('ke 4');
							if (id_division == user_logged) {
								//console.log('ke 5');
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							} else {
								//console.log('ke 6');
								return btn;
							}
						}
					} else {
						//console.log('ke 7');
						if ((was_approved_kadept) || (was_approved_sdm) || (was_approved_dirke) || (was_approved_dirut)) {
							console.log('ke 8');
							if (status_fp3 == 'ubah' && (id_role == 5 || id_role == 6 || id_role == 3)) {
								//console.log('ke 9');
								return btn.concat(_btn_fkpbj);
							} else {
								//console.log('ke 10');
								return btn;
							}
						} else {
							if (id_division == user_logged) {
								//console.log('ke 11');
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							} else {
								//console.log('ke 12');
								return btn;
							}
						}
					}

				} else {
					//console.log('ke 13');
					if (metode == 3) {
						console.log('ke 14');
						if ((was_approved_kadept) || (was_approved_sdm) || (was_approved_dirke) || (was_approved_dirut)) {
							//console.log('ke 15');
							return btn;
						} else {
							//console.log('ke 16');
							if (id_division == user_logged) {
								//console.log('ke 17');
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							} else {
								//console.log('ke 18');
								return btn;
							}
						}
					} else {
						//console.log('ke 19');
						if ((was_approved_kadept) || (was_approved_sdm) || (was_approved_dirke) || (was_approved_dirut)) {
							//console.log('ke 20');
							if (status_fp3 == 'ubah' && (id_role == 5 || id_role == 6 || id_role == 3)) {
								//console.log('ke 21');
								return btn.concat(_btn_fkpbj);
							} else {
								//console.log('ke 22');
								return btn;
							}
						} else {
							//console.log('ke 23');
							if (id_division == user_logged) {
								//console.log('ke 24');
								if (is_reject == 0) {
									return btn;
								} else {
									return btn.concat(btn_edit);
								}
							} else {
								//console.log('ke 25');
								return btn;
							}
						}
					}
				}
			},
			callbackFunctionRightClick: function() {
				$('.buttonRiwayatPengadaan').on('click', function() {
					url = $(this).attr('href');
					window.location.href = url;
				})
				var pdf = $('.buttonExport').modal({
					header: 'Download PDF',
					render: function(el, data) {
						data.onSuccess = function() {
							$(pdf).data('modal').close();
							folder.data('plugin_folder').fetchData();
							// location.reload();
						}
						$(el).form(data);
						$('form', el).off('submit')
					}
				});
				var lampiran = $('.buttonLampiran').modal({
					header: 'Upload Lampiran',
					render: function(el, data) {
						data.onSuccess = function() {
							$(lampiran).data('modal').close();
							folder.data('plugin_folder').fetchData();
							// location.reload();
						}
						$(el).form(data);
					}
				});
				var view = $('.buttonView').modal({
					header: 'Lihat Data',
					render: function(el, data) {
						_self = view;

						data.onSuccess = function() {
							$(view).data('modal').close();
							folder.data('plugin_folder').fetchData();
						};
						data.isReset = false;

						$(el).form(data).data('form');
						
						var fp3_type = data.form[0].value;

						if (fp3_type == 'hapus') {
							for (let i = 2; i <= 14; i++) {
								$('.form' + i).hide();
							}
						} else {
							for (let i = 2; i <= 14; i++) {
								$('.form' + i).show();
							}
							$('.form15').hide();
						}
						
						if (data.form[11].value != '' || data.form[11].value != null) {
							link = '<a href="' + base_url + 'assets/lampiran/kak_lampiran/' + data.form[11].value + '" target="blank">' + data.form[11].value + '</a>';
						} else {
							link = '-';
						}

						$('.form11 span').html(link);

						if (data.form[13].value != '' || data.form[13].value != null) {
							link_pr = '<a href="' + base_url + 'assets/lampiran/pr_lampiran/' + data.form[13].value + '" target="blank">' + data.form[13].value + '</a>';
						} else {
							link_pr = '-';
						}

						$('.form13 span').html(link_pr);

						$('.close-modal-reject').on('click', function() {
							$('.form-keterangan-reject.modal-reject').removeClass('active');
						})
					}
				});

				var edit = $('.buttonEdit').modal({
					header: 'Edit',
					render: function(el, data) {
						console.log(data);
						_self = edit;

						data.onSuccess = function() {
							// console.log("tes");
							$(edit).data('modal').close();
							folder.data('plugin_folder').fetchData();
							//location.reload();
						};
						data.isReset = false;

						$(el).form(data).data('form');
						
						status_fp3 = $('[name="status"]').val();

						if (status_fp3 == 'ubah') {
							for (let i = 2; i < 8; i++) {
								$('.form'+i).show();
							}
							$('.form8').hide();
						} else {
							for (let i = 2; i < 8; i++) {
								$('.form'+i).hide();
							}
							$('.form8').show();
						}
					}
				});

				var del = $('.buttonDelete').modal({
					header: 'Hapus Data',
					render: function(el, data) {
						_self = edit;
						el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin menghapus data?<span><div class="form"></div><div>');
						data.onSuccess = function() {
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
					render: function(el, data) {
						_self = batal;
						el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin membatalkan data?<span><div class="form"></div><div>');
						data.onSuccess = function() {
							$(batal).data('modal').close();
							folder.data('plugin_folder').fetchData();
						};
						data.isReset = true;
						$('.form', el).form(data).data('form');
					}
				});

				var aktif = $('.buttonAktifkan').modal({
					header: 'Aktifkan Data?',
					render: function(el, data) {
						_self = edit;
						el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin mengaktifkan data?<span><div class="form"></div><div>');
						data.onSuccess = function() {

							$(aktif).data('modal').close();
							table.data('plugin_tableGenerator').fetchData();
						};
						data.isReset = true;
						$('.form', el).form(data).data('form');
					}
				});
				var batal = $('.buttonBatalkan').modal({
					header: 'Batalkan Data?',
					render: function(el, data) {
						_self = edit;
						el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin membatalkan data?<span><div class="form"></div><div>');
						data.onSuccess = function() {

							$(batal).data('modal').close();

							table.data('plugin_tableGenerator').fetchData();

						};
						data.isReset = true;
						$('.form', el).form(data).data('form');
					}
				});
				var fkpbj = $('.buttonFKPBJ').modal({
					header: 'Tambah Data FKPBJ',
					dataType: 'html',
					render: function(el, data) {

						$(el).html(data);

						$('#tab1').css('display', 'block');

						$('#nextBtn2').click(function() {
							no_pr = $('#formStep [name="no_pr"]').val();
							tipe_pr = $('#formStep [name="tipe_pr"]').val();
							tipe_pengadaan = $('#formStep [name="tipe_pengadaan"]').val();
							jenis_pengadaan = $('#formStep [name="jenis_pengadaan"]').val();
							idr_anggaran = $('#formStep [name="idr_anggaran"]').val();
							year_anggaran = $('#formStep [name="year_anggaran"]').val();
							hps = $('#formStep [name="hps"]').val();
							lingkup_kerja = $('#formStep [name="lingkup_kerja"]').val();
							desc_metode_pembayaran = $('#formStep [name="desc_metode_pembayaran"]').val();
							jenis_kontrak = $('#formStep [name="jenis_kontrak"]').val();
							sistem_kontrak = $('#formStep [name="sistem_kontrak[]"]').val();
							desc_dokumen = $('#formStep [name="desc_dokumen"]').val();
							// alert(year_anggaran);

							if (no_pr == '') {
								alert('No.PR Harus Diisi !');
							} else if (tipe_pr == '') {
								alert('Tipe PR Harus Diisi !');
							} else if (tipe_pengadaan == '') {
								alert('Jenis Pengadaan Harus Diisi !');
							} else if (jenis_pengadaan == '') {
								alert('Jenis Detail Pengadaan Harus Diisi !');
							} else if (idr_anggaran == '') {
								alert('Anggaran (IDR) Harus Diisi !');
							} else if (year_anggaran == '') {
								alert('Tahun Anggaran Harus Diisi !');
							} else if (hps == '') {
								alert('Ketersediaan HPS Harus Diisi !');
							} else if (lingkup_kerja == '') {
								alert('Lingkup Kerja Harus Diisi !');
							} else if (desc_metode_pembayaran == '') {
								alert('Metode Pembayaran (Usulan) Harus Diisi !');
							} else if (jenis_kontrak == '' || jenis_kontrak == '0') {
								alert('Jenis Kontrak (Usulan) Harus Diisi !');
							} else if (sistem_kontrak == '' || sistem_kontrak == null) {
								alert('Sistem Kontrak (Usulan) Harus Diisi !');
							} else if (desc_dokumen == '') {
								alert('Keterangan Harus Diisi !');
							} else {
								$('#tab2').css('display', 'block');
								$('#tab1').css('display', 'none');
							}
						});

						$('#nextBtn3').click(function() {
							$('#tab3').css('display', 'block');
							$('#tab2').css('display', 'none');
						});

						$('#prevBtn1').click(function() {
							$('#tab1').css('display', 'block');
							$('#tab2').css('display', 'none');
						});

						$('#prevBtn2').click(function() {
							$('#tab2').css('display', 'block');
							$('#tab3').css('display', 'none');
						});

						$('.reject-btn-step').on('click', function() {
							$('.form-keterangan-reject.modal-reject-step').addClass('active');
						})

						$('.close-reject-step').on('click', function() {
							$('.form-keterangan-reject.modal-reject-step').removeClass('active');
						});

						$('.deleteFile').on('click', function(e) {
							data = $(this).data('id');
							//alert(data);
							$('[type="file"].closeInput' + data + '').css('display', 'block');
							$('.fileUploadBlock.close' + data + '').empty();
							$('[type="hidden"].closeHidden' + data + '').val('');
						});

						var valCsms = $('[name="valcsms"]').val();
						if (valCsms == 'E' || valCsms == 'H') {
							csms = 1;
						} else if (valCsms == 'M') {
							csms = 2;
						} else {
							csms = 3;
						}
						/*else{
		  				csms = '';
		  			}*/
						// $.ajax({
						// 	url: '<?php echo site_url('main/get_dpt_csms') ?>/'+csms,
						// 	// data: category,
						// 	dataType: 'json',
						// 	complete : function(){
						// 	},
						// 	success: function(dpt){
						// 		// console.log(dpt);
						// 		$('#tab2 .checkboxWrapper').empty();
						// 		$('#tab2 label').css("float", "left");

						// 		$('#tab2 .tab-content').append('<fieldset class="form-group form0 " for=""><label for="">Usulan Non DPT</label><input type="text" class="form-control" name="usulan"></fieldset>');

						// 		$('#tab2 .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPTFKPBJ()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');

						// 		dpt.forEach(function(element) {
						// 			$('#tab2 .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option'+element.id_vendor+'" name="type[]" type="checkbox" value="'+element.id_vendor+'"/> <label for="option'+element.id_vendor+'">'+element.vendor+'</label> </div>');
						// 		});
						// 	}
						// });

						var val_e = $('[name="jenis_pengadaan"]').val();

						$.ajax({
							url: '<?php echo site_url('main/get_dpt_type/') ?>/' + val_e,
							// data: category,
							dataType: 'json',
							complete: function() {},
							success: function(dpt) {
								// console.log(dpt);
								// alert('ke sini 1');
								$('#tab2 .checkboxWrapper').empty();
								$('#tab2 .tab-content .form1').empty();
								$('#tab2 label').css("float", "left");

								$('#tab2 .tab-content').append('<fieldset class="form-group form1 " for=""><label for="">Usulan Non DPT</label><input type="text" class="form-control" name="usulan"></fieldset>');

								$('#tab2 .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
								dpt.forEach(function(element) {
									if (element.score == '' || element.score == null) {
										score = '';
									} else {
										score = '(' + element.score + ')';
									}

									$('#tab2 .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option' + element.id_vendor + '" name="type[]" type="checkbox" value="' + element.id_vendor + '"/> <label for="option' + element.id_vendor + '">' + element.vendor + ' ' + score + '</label> </div>');
								});
							}
						});

						$('[name="jenis_pengadaan"]').on('click', function() {
							// abcd += '';
							// alert(id_pengadaan);
							var dpt_type = $(this).val();

							$.ajax({
								url: '<?php echo site_url('main/get_dpt_type/') ?>/' + dpt_type,
								// data: category,
								dataType: 'json',
								complete: function() {},
								success: function(dpt) {
									// console.log(dpt);
									$('#tab2 .checkboxWrapper').empty();
									$('#tab2 .tab-content .form1').empty();
									$('#tab2 label').css("float", "left");

									$('#tab2 .tab-content').append('<fieldset class="form-group form1 " for=""><label for="">Usulan Non DPT</label><input type="text" class="form-control" name="usulan"></fieldset>');

									$('#tab2 .checkboxWrapper').append('<div class="search-recomendation"><input id="searchDPT" type="text" onkeyup="filterDPT()" class="sc" placeholder="Cari DPT"/><span class="icon"><i class="fas fa-search"></i></span></div>');
									dpt.forEach(function(element) {
										if (element.score == '' || element.score == null) {
											score = '';
										} else {
											score = '(' + element.score + ')';
										}

										if (element.value == 1) {
											checked = 'checked';
										}

										$('#tab2 .checkboxWrapper').append('<div class="inputGroup" id="inputGroup"> <input id="option' + element.id_vendor + '" name="type[]" type="checkbox" value="' + element.id_vendor + '"/> <label for="option' + element.id_vendor + '">' + element.vendor + ' ' + score + '</label> </div>');
									});
								}
							});
							// alert(abcd);
						});

						$("#searchDPT").on('keyup', function() {
							var matcher = new RegExp($(this).val(), 'gi');
							// console.log(matcher);
							// console.log($(this).val());
							$('.checkboxWrapper').css('display', 'block').not(function() {
								return matcher.test($(this).find('.inputGroup').text())
							}).css('display', 'none');
						});

						// tipe = $('.form1 [name="tipe_pr"]').val()
						// if (tipe == "direct_charge") {
						// 		$('.modal [name="tipe_pengadaan"]').empty();
						// 		$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option>");
						// 		$('.modal [name="metode_pengadaan"]').empty();
						// 		$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
						// 	}
						// 	else if(tipe == "services"){
						// 		$('.modal [name="tipe_pengadaan"]').empty();
						// 		$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa'>Pengadaan Jasa</option>");
						// 		$('.modal [name="metode_pengadaan"]').empty();
						// 		$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
						// 	}
						// 	else if(tipe == "user_purchase"){
						// 		$('.modal [name="tipe_pengadaan"]').empty();
						// 		$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
						// 		$('.modal [name="metode_pengadaan"]').empty();
						// 		$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='5'>Pengadaan Langsung</option>");
						// 	}
						// 	else if(tipe == "nda"){
						// 		$('.modal [name="pengadaan"]').empty();
						// 		$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
						// 		$('.modal [name="metode_pengadaan"]').empty();
						// 		$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='3'>Swakelola</option>");
						// }

						/*jenis_pengadaan = $('.form3 [name="tipe_pengadaan"]').val()
						if (jenis_pengadaan == "barang") {
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>non Stock</option>");
						}else if(jenis_pengadaan == "jasa"){
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");

						}else{
							$('.modal [name="jenis_pengadaan"]').empty();
							$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
						}*/

						$('.modal [name="tipe_pr"]').on('change', function() {
							// get parent value
							var tipe_pr = $(this).val();
							// alert(pengadaan);

							// Change option on select based on parent
							if (tipe_pr == "direct_charge") {
								$('.modal [name="tipe_pengadaan"]').empty();
								$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option>");
								$('.modal [name="metode_pengadaan"]').empty();
								$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
							} else if (tipe_pr == "services") {
								$('.modal [name="tipe_pengadaan"]').empty();
								$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='jasa'>Pengadaan Jasa</option>");
								$('.modal [name="metode_pengadaan"]').empty();
								$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='1'>Pelelangan</option><option value='2'>Pemilihan Langsung</option><option value='4'>Penunjukan Langsung</option><option value='5'>Pengadaan Langsung</option>");
							} else if (tipe_pr == "user_purchase") {
								$('.modal [name="tipe_pengadaan"]').empty();
								$('.modal [name="tipe_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
								$('.modal [name="metode_pengadaan"]').empty();
								$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='5'>Pengadaan Langsung</option>");
							} else if (tipe_pr == "nda") {
								$('.modal [name="pengadaan"]').empty();
								$('.modal [name="pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='barang'>Pengadaan Barang</option><option value='jasa'>Pengadaan Jasa</option>");
								$('.modal [name="metode_pengadaan"]').empty();
								$('.modal [name="metode_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='3'>Swakelola</option>");
							}
						});

						// PENGADAAN TIPE
						$('.modal [name="tipe_pengadaan"]').on('change', function() {
							// get parent value
							var pengadaan = $(this).val();
							// alert(pengadaan);

							// Change option on select based on parent
							if (pengadaan == "barang") {
								$('.modal [name="jenis_pengadaan"]').empty();
								$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock'>non Stock</option>");
							} else if (pengadaan == "jasa") {
								$('.modal [name="jenis_pengadaan"]').empty();
								$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Dibawah Ini</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>");

							} else {
								$('.modal [name="jenis_pengadaan"]').empty();
								$('.modal [name="jenis_pengadaan"]').append("<option value=''>Pilih Jenis Pengadaan Diatas</option>");
							}
						});

					}
				});
			},
			renderContent: function(el, value, key) {
				// console.log(value[16].value);
				// console.log(value);
				var status = '';
				var badge = '';
				var is_status = value[5].value;
				var is_approve = value[10].value;
				var is_reject = value[11].value;
				var keterangan = value[12].value;
				var idr_anggaran = parseInt(value[16].value);
				var year_anggaran = value[13].value;
				var pejabat = value[17].value; 
				var is_cancelled = value[18].value;
				var desc_batal = value[19].value;
				var status = value[7].value;

				if (value[2].value == 1) {
					metode_pengadaan = 'Pelelangan';
				} else if (value[2].value == 2) {
					metode_pengadaan = 'Pemilihan Langsung';
				} else if (value[2].value == 3) {
					metode_pengadaan = 'Swakelola';
				} else if (value[2].value == 4) {
					metode_pengadaan = 'Penunjukan Langsung';
				} else {
					metode_pengadaan = 'Pengadaan Langsung';
				}
				// console.log(value)			

				//pending status
				if (is_approve == 0 && is_reject == 0 && is_cancelled == 0 && status == "ubah") {
					status = 'FP3 (Menunggu Ka.Dept User)';
					badge = 'warning';
				} else if (is_approve == 1 && is_reject == 0 && is_cancelled == 0 && status == "ubah") {
					status = 'FP3 (Menunggu Admin Pengendalian)';
					badge = 'warning';
				} else if (is_approve == 2 && is_reject == 0 && is_cancelled == 0 && status == "ubah") {
					status = 'FP3 (Menunggu Ka.Dept Procurement)';
					badge = 'warning';
				}

				//reject status
				else if (is_approve == 1 && is_reject == 1 && is_cancelled == 0 && status == "ubah") {
					status = 'FP3 (Direvisi Ka.Dept User) <span class="tooltiptext reject">' + keterangan + '</span>';
					badge = 'danger fp3_reject tooltip';
				} else if (is_approve == 2 && is_reject == 1 && is_cancelled == 0 && status == "ubah") {
					status = 'FP3 (Direvisi Admin Pengendalian)<span class="tooltiptext reject">' + keterangan + '</span>';
					badge = 'danger fp3_reject tooltip';
				} else if (is_approve == 3 && is_reject == 1 && is_cancelled == 0 && status == "ubah") {
					status = 'FP3 (Direvisi Ka.Dept Procurement)<span class="tooltiptext reject">' + keterangan + '</span>';
					badge = 'danger fp3_reject tooltip';
				}

				//cancelled status
				else if(is_cancelled == 1 || status == "batal"){
					status = 'FP3 (Batal) <span class="tooltiptext reject">' + desc_batal + '</span>';
					badge = 'danger fp3_reject tooltip';
				}

				//approve <= 100 juta && (metode_pengadaan == 'Penunjukan Langsung')
				else if (is_approve == 3 && is_reject == 0 && is_cancelled == 0 && idr_anggaran <= 100000000) {
					status = 'FP3 (telah disetujui Kadept.Procurement)';
					badge = 'success';
				}

				//approve > 100 juta < 1M
				else if (is_approve == "3" && is_reject == 0 && is_cancelled == 0 && ((idr_anggaran > 100000000 && idr_anggaran <= 1000000000) && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung'))) {
					//console.log('Masuk Ke Kondisi Sukses >100Jt < 1M ');
					status = 'FP3 (Menunggu persetujuan Ka.Div SDM & Umum)';
					badge = 'warning';
				} else if (is_approve == "4" && is_reject == 0 && is_cancelled == 0 && idr_anggaran >= 100000000 && idr_anggaran <= 1000000000 && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung')) {
					//console.log('Masuk Ke Kondisi Sukses >100Jt < 1M ');
					status = 'FP3 telah di setujui Ka.Div SDM & Umum';
					badge = 'success';
				} else if (is_approve == "4" && is_reject == 1 && is_cancelled == 0 && idr_anggaran >= 100000000 && idr_anggaran <= 1000000000 && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung')) {
					status = 'FP3 (Di revisi Ka.Div SDM & Umum)<span class="tooltiptext reject">' + keterangan + '</span>';
					badge = 'danger fppbj_reject tooltip';
				}

				//approve > 1 M < 10M
				else if (is_approve == "3" && is_reject == 0 && is_cancelled == 0 && (idr_anggaran > 1000000000 && idr_anggaran <= 10000000000) && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung')) {
					status = 'FP3 (Menunggu persetujuan Dir.Keuangan & Umum)';
					badge = 'warning';
				} else if (is_approve == "4" && is_reject == 0 && is_cancelled == 0 && idr_anggaran > 1000000000 && idr_anggaran <= 10000000000 && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung')) {
					status = 'FP3 telah di setujui Dir.Keuangan & Umum';
					badge = 'success';
				} else if (is_approve == "4" && is_reject == 1 && is_cancelled == 0 && idr_anggaran >= 1000000000 && idr_anggaran <= 10000000000 && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung')) {
					status = 'FP3 (Di revisi Dir.Keuangan & Umum)<span class="tooltiptext reject">' + keterangan + '</span>';
					badge = 'danger fppbj_reject tooltip';
				}

				//approve > 10M
				else if (is_approve == "3" && is_reject == 0 && is_cancelled == 0 && idr_anggaran >= 10000000000 && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung')) {
					status = 'FP3 (Menunggu persetujuan Dir.Utama)';
					badge = 'warning';
				} else if (is_approve == "4" && is_reject == 0 && is_cancelled == 0 && idr_anggaran >= 1000000000 && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung')) {
					console.log('Masuk Ke Kondisi Sukses >10M ');
					status = 'FP3 telah di setujui Dir.Utama';
					badge = 'success';
				} else if (is_approve == "4" && is_reject == 1 && is_cancelled == 0 && idr_anggaran >= 1000000000 && (metode_pengadaan == 'Penunjukan Langsung' || metode_pengadaan == 'Pemilihan Langsung' || metode_pengadaan == 'Pelelangan' || metode_pengadaan == 'Pengadaan Langsung')) {
					status = 'FP3 (Di revisi Dir.Utama)<span class="tooltiptext reject">' + keterangan + '</span>';
					badge = 'danger fppbj_reject tooltip';
				}

				//success status
				// else if (is_approve == "3" && is_reject == 0) {
				// 	status = 'FP3';
				// 	badge = 'success';
				// }

				// else if (is_approve == "1" && is_reject == 0) {
				// 	status = 'FP3 (Menunggu Admin Pengendalian)';
				// 	badge = 'warning';
				// }
				// else if (is_approve == "2" && is_reject == 0) {
				// 	status = 'FP3 (Menunggu Ka.Dept Procurement)';
				// 	badge = 'warning';
				// }

				// else if (is_approve == "0" && is_reject == 0) {
				// 	status = 'FP3 (Menunggu Ka.Dept User)';
				// 	badge = 'warning';
				// }else if (is_reject == 1) {
				// 	status = 'FP3 (FP3 Direvisi)<span class="tooltiptext reject">'+keterangan+'</span>';
				// 	badge = 'danger fp3_reject tooltip';
				// }

				html = '';
				html += '<div class="caption"><p>' + value[0].value + '</p><p><span class="badge is-' + badge + '">' + status + '</p></div>';

				return html;
			},
			additionFeature: function(el) {
				el.prepend(insertButton(site_url + "fp3/insert/<?php echo $id; ?>/<?php echo $year; ?>"));
			},
			finish: function() {},
			filter: {
				wrapper: $('.contentWrap'),
				data: {
					data: _xhr
				}
			}
		});
		var add = $('.buttonAdd').modal({
			render: function(el, data) {
				data.onSuccess = function() {
					// $(add).data('modal').close();
					// folder.data('plugin_folder').fetchData();
					location.reload();
				}
				
				var id_division = '<?= $admin['id_division'] ?>'

				$(el).form(data);

				// TAB FUNCTION FP3
				$('#tab-intro').css('display', 'block');
				$('.btn-group').css('display', 'none');

				for (let i = 1; i <= 15; i++) {
					$('.form' + i).css('display', 'none');
				}

				$('#btnUbah').click(function() {
					$('.btn-group').css('display', 'block');
					$('#formUbah').css('display', 'block');
					$('#tab-intro').css('display', 'none');

					for (let i = 1; i <= 14; i++) {
						$('.form' + i).css('display', 'block');
					}
					$('.form15').css('display', 'none');
					$('[name="fp3_type"]').val('ubah');
				});

				$('#btnBatal').click(function() {
					$('#formBatal').css('display', 'block');
					$('#tab-intro').css('display', 'none');
					$('.btn-group').css('display', 'block');

					$('.form1').css('display', 'block');
					for (let i = 2; i <= 14; i++) {
						$('.form' + i).css('display', 'none');
					}
					$('.form15').css('display', 'block');
					$('[name="fp3_type"]').val('batal');
				});

				$('#switchBatal').click(function() {
					$('.btn-group').css('display', 'block');
					$('#formBatal').css('display', 'block');
					$('#formUbah').css('display', 'none');

					$('.form1').css('display', 'block');
					for (let i = 2; i <= 14; i++) {
						$('.form' + i).css('display', 'none');
					}
					$('.form15').css('display', 'block');
					$('[name="fp3_type"]').val('batal');
				})

				$('#switchUbah').click(function() {
					$('.btn-group').css('display', 'block');
					$('#formUbah').css('display', 'block');
					$('#formBatal').css('display', 'none');

					for (let i = 1; i <= 14; i++) {
						$('.form' + i).css('display', 'block');
					}
					$('.form15').css('display', 'none');
					$('[name="fp3_type"]').val('ubah');
				})
				
				$('.form1 [name="id_fppbj"]').on('change', function() {
					val_id = $(this).val();
					fp3_type = $('[name="fp3_type"]').val();
					$.ajax({
						url: '<?php echo site_url('fp3/get_data_fppbj') ?>/' + val_id,
						method: 'post',
						dataType: 'json',
						success: function(data) {
							if (fp3_type == 'ubah') {
								if (id_division == "1" && data.id_division != "1") {
									for (let i = 0; i <= 15; i++) {
										if (i == 0 || i == 1 || i == 5 || i == 6) {
											$('.form' + i).show();
										} else {
											$('.form' + i).hide();
										}
									}
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
									$('.form5 span').html(metode_pengadaan);
								} else {
									for (let i = 1; i <= 14; i++) {
										$('.form' + i).css('display', 'block');
									}
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
									$('.form3 span').append(data.no_pr);

									$('.form5 span').empty();
									$('.form5 span').append(metode_pengadaan);

									if (data.jwpp_start != '' && data.jwpp_end != '') {
										jwpp = defaultDate(data.jwpp_start) + ' sampai ' + defaultDate(data.jwpp_end);
									} else {
										jwpp = '-';
									}
									// $('.form5 span').empty();
									// $('.form5 span').append('Rp '+data.idr_anggaran);
									$('.form7 span').empty();
									$('.form7 span').append(jwpp);

									$('.form9 span').empty();
									$('.form9 span').append(data.desc_dokumen);

									if (data.kak_lampiran != '' || data.kak_lampiran != null) {
										link = '<a href="' + base_url + 'assets/lampiran/kak_lampiran/' + data.kak_lampiran + '" target="blank">' + data.kak_lampiran + '</a>';
									} else {
										link = '-';
									}
									// $('.form11 span').empty();
									$('.form11 span').html(link);

									if (data.pr_lampiran != '' || data.pr_lampiran != null) {
										link_pr = '<a href="' + base_url + 'assets/lampiran/pr_lampiran/' + data.pr_lampiran + '" target="blank">' + data.pr_lampiran + '</a>';
									} else {
										link_pr = '-';
									}

									$('.form13 span').html(link_pr);
								}
							} else {
								$('.btn-group').css('display', 'block');
								$('#formBatal').css('display', 'block');
								$('#formUbah').css('display', 'none');

								$('.form1').css('display', 'block');
								for (let i = 2; i <= 14; i++) {
									$('.form' + i).css('display', 'none');
								}
								$('.form15').css('display', 'block');
								$('[name="fp3_type"]').val('hapus');
							}
						}
					})
				})

			}
		});
		// var add = $('.buttonAdd').modal({
		// 	render: function(el, data) {
		// 		data.onSuccess = function() {
		// 			// $(add).data('modal').close();
		// 			// folder.data('plugin_folder').fetchData();
		// 			location.reload();
		// 		}

		// 		$(el).form(data);

		// 		// TAB FUNCTION FP3
		// 		$('#tab-intro').css('display', 'block');
		// 		$('.btn-group').css('display', 'none');

		// 		for (let i = 1; i <= 14; i++) {
		// 			$('.form' + i).css('display', 'none');
		// 		}

		// 		$('#btnUbah').click(function() {
		// 			$('.btn-group').css('display', 'block');
		// 			$('#formUbah').css('display', 'block');
		// 			$('#tab-intro').css('display', 'none');

		// 			for (let i = 1; i <= 14; i++) {
		// 				$('.form' + i).css('display', 'block');
		// 			}
		// 			$('.form13').css('display', 'none');
		// 		});

		// 		$('.form1 [name="id_fppbj"]').on('change', function() {
		// 			val_id = $(this).val();
		// 			$.ajax({
		// 				url: '<?php echo site_url('fp3/get_data_fppbj') ?>/' + val_id,
		// 				method: 'post',
		// 				dataType: 'json',
		// 				success: function(data) {
		// 					// var data_ = JSON.parse(data);

		// 					if (data.metode_pengadaan == 1) {
		// 						metode_pengadaan = 'Pelelangan';
		// 					} else if (data.metode_pengadaan == 2) {
		// 						metode_pengadaan = 'Pemilihan Langsung';
		// 					} else if (data.metode_pengadaan == 3) {
		// 						metode_pengadaan = 'Swakelola';
		// 					} else if (data.metode_pengadaan == 4) {
		// 						metode_pengadaan = 'Penunjukan Langsung';
		// 					} else {
		// 						metode_pengadaan = 'Pengadaan Langsung';
		// 					}

		// 					$('.form3 span').empty();
		// 					$('.form3 span').append(data.no_pr);

		// 					$('.form5 span').empty();
		// 					$('.form5 span').append(metode_pengadaan);

		// 					if (data.jwpp_start != '' && data.jwpp_end != '') {
		// 						jwpp = defaultDate(data.jwpp_start) + ' sampai ' + defaultDate(data.jwpp_end);
		// 					} else {
		// 						jwpp = '-';
		// 					}
		// 					// $('.form5 span').empty();
		// 					// $('.form5 span').append('Rp '+data.idr_anggaran);
		// 					$('.form5 span').empty();
		// 					$('.form5 span').append(jwpp);

		// 					$('.form7 span').empty();
		// 					$('.form7 span').append(data.desc_dokumen);

		// 					if (data.kak_lampiran != '' || data.kak_lampiran != null) {
		// 						link = '<a href="' + base_url + 'assets/lampiran/kak_lampiran/' + data.kak_lampiran + '" target="blank">' + data.kak_lampiran + '</a>';
		// 					} else {
		// 						link = '-';
		// 					}
		// 					// $('.form11 span').empty();
		// 					$('.form9 span').html(link);

		// 					if (data.pr_lampiran != '' || data.pr_lampiran != null) {
		// 						link_pr = '<a href="' + base_url + 'assets/lampiran/pr_lampiran/' + data.pr_lampiran + '" target="blank">' + data.pr_lampiran + '</a>';
		// 					} else {
		// 						link_pr = '-';
		// 					}

		// 					$('.form11 span').html(link_pr);
		// 				}
		// 			})
		// 		})

		// 		$('#btnHapus').click(function() {
		// 			$('#formHapus').css('display', 'block');
		// 			$('#tab-intro').css('display', 'none');
		// 			$('.btn-group').css('display', 'block');

		// 			$('.form1').css('display', 'block');
		// 			$('.form2').css('display', 'none');
		// 			$('.form3').css('display', 'none');
		// 			$('.form4').css('display', 'none');
		// 			$('.form5').css('display', 'none');
		// 			$('.form6').css('display', 'none');
		// 			$('.form9').css('display', 'none');
		// 			$('.form10').css('display', 'none');
		// 			$('.form11').css('display', 'none');
		// 			$('.form12').css('display', 'none');
		// 			$('.form13').css('display', 'block');
		// 		});

		// 		$('#switchHapus').click(function() {
		// 			$('.btn-group').css('display', 'block');
		// 			$('#formHapus').css('display', 'block');
		// 			$('#formUbah').css('display', 'none');

		// 			$('.form1').css('display', 'block');
		// 			$('.form2').css('display', 'none');
		// 			$('.form3').css('display', 'none');
		// 			$('.form4').css('display', 'none');
		// 			$('.form5').css('display', 'none');
		// 			$('.form6').css('display', 'none');
		// 			$('.form7').css('display', 'none');
		// 			$('.form8').css('display', 'none');
		// 			$('.form9').css('display', 'none');
		// 			$('.form10').css('display', 'none');
		// 			$('.form11').css('display', 'none');
		// 			$('.form12').css('display', 'none');
		// 			$('.form13').css('display', 'block');
		// 		})

		// 		$('#switchUbah').click(function() {
		// 			$('.btn-group').css('display', 'block');
		// 			$('#formUbah').css('display', 'block');
		// 			$('#formHapus').css('display', 'none');

		// 			$('.form1').css('display', 'block');
		// 			$('.form2').css('display', 'block');
		// 			$('.form3').css('display', 'block');
		// 			$('.form4').css('display', 'block');
		// 			$('.form5').css('display', 'block');
		// 			$('.form6').css('display', 'block');
		// 			$('.form7').css('display', 'block');
		// 			$('.form8').css('display', 'block');
		// 			$('.form9').css('display', 'block');
		// 			$('.form10').css('display', 'block');
		// 			$('.form11').css('display', 'block');
		// 			$('.form12').css('display', 'block');
		// 			$('.form13').css('display', 'none');
		// 		})

		// 	}
		// });
	});

	function filterDPT() {
		// console.log($(".inputGroup label").val());
		$("#searchDPT").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$(".inputGroup").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	}

	function filterDPTFKPBJ() {
		// console.log($(".inputGroup label").val());
		$("#searchDPT").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$(".inputGroup").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	}
	// $(function() {
	// 	if($('.badge').hasClass('fp3_reject') == true) {
	// 		$('.fp3_reject').addClass('tooltip').append('<span class="tooltiptext reject">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto mollitia fugiat non corporis maxime vitae facere nisi quisquam praesentium! Accusamus corporis ad quidem doloremque rerum dolorem officiis maiores nisi libero!</span></span>');
	// 	}
	// })
</script>