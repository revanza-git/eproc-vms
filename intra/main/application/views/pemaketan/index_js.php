<script src="https://cdn.rawgit.com/igorescobar/jQuery-Mask-Plugin/1ef022ab/dist/jquery.mask.min.js"></script>
<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};				

	var folder = $('#tableGenerator').folder({
		url: '<?php echo site_url('pemaketan/getData/'.$year); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			_id 		= value[key][3].value;
			year 		= '<?php echo $year ?>';
			urlDivision = '<?php echo base_url('pemaketan/division');?>/'+_id+'/'+'0'+'/'+year;

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
			return html;
		},
		additionFeature: function(el){
			<?php if ($this->session->userdata('admin')['id_role'] == 5 || $this->session->userdata('admin')['id_role'] == 3) { ?>
				el.prepend(insertStepButton(site_url+"pemaketan/insertStep/<?php echo $id;?>"));
			<?php } ?>
		},
		finish: function(){
     		

		}

	});

	var resiko;
	var resikoOptions = {
		header: 'Tambah Data FPPBJ',
		render : function(el, data){

			$('#step5 .form5').append('<div class="alert" style="color: #f90606; font-size: 95%; text-align: center;"></div>');

			$('#multi-year').remove();
			$('#step2 .form4').append('<div id="multi-year"><input style="margin-left: 30%;" type="checkbox" value="1" name="is_multiyear">Multiyear Budget</div>');

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

				},
				onSuccessSubmit: function(){
					location.reload();
					$(resiko).data('modal').close();
					folder.data('plugin_folder').fetchData();
				},
				onSuccess: function(){
					$(resiko).data('modal').close();
				}
			});
		  	
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
						$('#step2 .form8').append(__line+'<div class="multiple-budget"></div><div><a id="add_budget">Tambah Tahun Anggaran</a> || <a id="min_budget">Batal Tahun Anggaran</a> </div>'+__line);

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
							$.ajax({
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

			$('[name="jenis_pengadaan"]').on('click',function() {
				
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
		var form_swakelola 		= '<div class="form blockWrapper"><fieldset class="form-group form0" for=""><label for="">Waktu*</label><select name="waktu" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Penyelesaian Pekerjaan ≤ 3 bulan</option><option value="2">Penyelesaian Pekerjaan &gt; 3 bulan s.d &lt; 6 bulan</option><option value="3">Penyelesaian Pekerjaan ≥ 6 bulan</option></select></fieldset><fieldset class="form-group form1" for=""><label for="">Biaya*</label><select name="biaya" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Biaya Pelaksanaan Pekerjaan&nbsp;≤ 50 juta</option><option value="2">Biaya Pelaksanaan Pekerjaan&nbsp;&gt; 50 juta s.d &lt; 100 juta</option><option value="3">Biaya Pelaksanaan Pekerjaan&nbsp;≥ 100 juta</option></select></fieldset><fieldset class="form-group form2" for=""><label for="">Tenaga Kerja*</label><select name="tenaga" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan memenuhi sebagai perencana dan pelaksana dan pengawas</option><option value="2">Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan memenuhi salah satu atau lebih sebagai perencana dan/atau pelaksana dan/atau pengawas</option><option value="3">Kompetensi dan/atau ketersediaan jumlah Tenaga Kerja di Perusahaan tidak memenuhi sebagai perencana dan pelaksana dan pengawas</option></select></fieldset><fieldset class="form-group form3" for=""><label for="">Bahan*</label><select name="bahan" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Bahan mudah didapatkan langsung oleh Pekerja NR</option><option value="2">Bahan dapat diadakan melalui pihak ketiga</option><option value="3">Bahan lebih efisien apabila diadakan oleh pihak ketiga</option></select></fieldset><fieldset class="form-group   form4" for=""><label for="">Peralatan*</label><select name="peralatan" id="" class="form-control "><option value="0" selected="">Pilih Dibawah Ini</option><option value="1">Ketersediaan jumlah dan kemampuan peralatan kerja memenuhi kebutuhan pekerjaan</option><option value="2">Ketersediaan jumlah dan/atau kemampuan peralatan kerja tidak memenuhi kebutuhan pekerjaan</option><option value="3">Peralatan lebih efisien apabila diadakan oleh pihak ketiga</option></select></fieldset><fieldset class="form-group   form5" for=""><div class="matrix-swakelola-wrapper"><div class="matrix-swakelola"><div class="ms-item green m1">1</div><div class="ms-item green m2">2</div><div class="ms-item green m3">3</div><div class="ms-item green m4">4</div><div class="ms-item green m5">5</div><div class="ms-item green-light m6">6</div><div class="ms-item green-light m7">7</div><div class="ms-item green-light m8">8</div><div class="ms-item green-light m9">9</div><div class="ms-item green-light m10">10</div><div class="ms-item green-light sw m11">11</div><span class="ms-line"></span><div class="ms-item yellow pk m12">12</div><div class="ms-item yellow m13">13</div><div class="ms-item red m14">14</div><div class="ms-item red m15">15</div></div></div><div class="alert" style="color: #f90606; font-size: 95%; text-align: center;"></div></fieldset><div class="form-group btn-group"><button type="button" class="button is-primary btn-back">Sebelumnya</button><button type="button" class="button is-primary btn-submit">Lanjut</button></div></div>';


				/*******************************************************
				 ******* 			DEFINE FORM TYPE			 *******
				 *******************************************************/

				// JASA && SWAKELOLA
				if (metode == 3 && pengadaan == "jasa") {
						// SHOW MENU TAB
						console.log("Masuk ke metode Swakelola Tipe Jasa");

						$('#step2 .btn-group .btn-to').attr('id','3');
						$('#step4 .btn-group .btn-to').attr('id','5');
						
						$(analisa_resiko).show();
						$(dpt).show();
						$(csms).show();
						$(analisa_swakelola).show();

						$('#step4 .btn-group .btn-next').removeClass('btn-save').text('Lanjut');

					// JASA NON SWAKELOLA
					}else if(metode != 3 && pengadaan == "jasa"){
						console.log("Masuk ke metode bukan Swakelola Tipe Jasa");
						
						// SHOW MENU TAB
						$(analisa_resiko).show();
						$(dpt).show();
						$(csms).show();
						
						// HIDE SWAKELOLA
						$(analisa_swakelola).hide();

						$('#step2 .btn-group .btn-to').attr('id', '3');
						$('#step4 .btn-group .btn-next').removeClass('btn-next').addClass('btn-to').attr('id','finish');
						
						$('#step5').hide();
					
					// BARANG && SWAKELOLA
					}else if(metode == 3 && pengadaan != "jasa"){
						console.log("Masuk ke metode Swakelola Tipe Bukan Jasa");
						
						$('#step2 .btn-to').attr('id', '4');
						$('#step4 .btn-prev').attr('id', '2');

						// HIDE ALL MENU TAB
						$(analisa_resiko).hide();
						$(dpt).show();
						$(csms).hide();
						$(analisa_swakelola).show();
						$(csms).hide();

						$('#step4 .btn-to').attr('id', '5');
						$('#step5 .btn-group .btn-submit').addClass('btn-save');
					
					// BARANG NON SWAKELOLA
					}else if(metode != 3 && pengadaan != "jasa"){
						// $('#step5').empty();
						console.log("Masuk ke metode Bukan Swakelola Tipe Barang");
						// HIDE ALL MENU TAB
						$(analisa_resiko).hide();
						$(dpt).show();
						$(csms).hide();
						$(analisa_swakelola).hide();

						$('#step2 .btn-to').attr('id', '4');
						$('#step4 .btn-prev').attr('id', '2');
						$('#step4 .btn-group .btn-next').removeClass('btn-next').addClass('btn-to').attr('id','finish');
						// $('.btn-to').attr('id', ' end');
					
					// LAIN LAIN 
					}else{
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
	$('.btn-save').on('click', function(){
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
