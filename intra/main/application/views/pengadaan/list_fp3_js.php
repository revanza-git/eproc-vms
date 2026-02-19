<script type="text/javascript">
	$(function() {
		dataPost = {
			// order: 'id',
			// sort: 'desc'
		};

		var id_division = '<?= $admin['id_division']; ?>';

		var folder = $('#folderGenerator').folder({
			url: '<?php echo site_url('pengadaan/getDataFP3/'); ?>',
			data: dataPost,
			dataRightClick: function(key, btn, value) {
				_id = value[key][2].value;
				urlDivision = '<?php echo base_url('fp3/fp3ByYear/'); ?>/' + _id;
				urlYear = '<?php echo base_url('export_timeline/rekap_timeline/'); ?>/' + _id;

				btn = [{
						icon: 'search',
						label: 'Lihat Data',
						class: 'buttonView',
					},
					// {
					// 	icon : 'download',
					// 	label: 'Export Timeline',
					// 	class: 'buttonExport'
					// }
				];
				return btn;
			},
			callbackFunctionRightClick: function() {
				var view = $('.buttonView').click(function() {
					$(location).attr('href', urlDivision);
				});

				var export_ = $('.buttonExport').click(function() {
					window.open(urlYear, "_blank");
				});
			},

			renderContent: function(el, value, key) {
				console.log(value);
				html = '';
				html += '<div class="caption"><p>' + value[2].value + '</p><p><b>' + value[1].value + '</b> Item(s)</p></div>';
				// console.log(folder);
				return html;
			},
			additionFeature: function(el) {
				<?php if ($admin['id_role'] == 5 || $admin['id_role'] == 3 || $admin['id_role'] == 6) { ?>
					el.prepend(insertButton(site_url + "fp3/insert/<?php echo $id; ?>"));
				<?php } ?>
			},
			finish: function() {

			}

		});
		var add = $('.buttonAdd').modal({
			render: function(el, data) {
				data.onSuccess = function() {
					// $(add).data('modal').close();
					// folder.data('plugin_folder').fetchData();
					location.reload();
				}

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
								$('[name="fp3_type"]').val('batal');
							}
						}
					})
				})
			}
		});
	});
</script>