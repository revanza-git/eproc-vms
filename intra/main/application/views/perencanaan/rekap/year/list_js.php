<?php $admin = $this->session->userdata('admin');?>
<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};
	var _xhr;
	$.ajax({
			url: '<?php echo site_url('perencanaan/rekap/formFilter')?>',
			async: false,
			dataType: 'json',
			success:function(xhr){
				_xhr = xhr;
			}
		});
				

	var folder = $('#tableGenerator').folder({
		url: '<?php echo site_url('perencanaan/rekap/getDataYear/'.$year); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			console.log(value);
			_id 		= value[key][3].value;
			_year 		= value[key][2].value;
			urlTimeline = '<?php echo base_url('timeline/view/fppbj');?>/'+_id;
			urlpdf 		= site_url+"export/fppbj/"+_id;
			/*{
					icon : 'search',
					label: 'Lihat Data FPPBJ',
					class: 'buttonViewFppbj',
					// href:site_url+"perencanaan/rekap/getSingleData/"+_id
					href:site_url+"perencanaan/rekap/getDataFppbj/"+_id
				},*/
			btn = [{
					icon : 'eye',
					label: 'Lihat Data',
					class: 'buttonView',
					// href:site_url+"perencanaan/rekap/getSingleData/"+_id
					href:site_url+"pemaketan/get_step/"+_id
				},
				{
					icon : 'file-download',
					label: 'Download PDF',
					class: 'buttonPDF',
				}
				<?php if ($admin['id_role'] == 6) {?>
				,{
					icon : 'file',
					label: 'Buat File FKPBJ',
					class: 'buttonFKPBJ',
					href:site_url+"fkpbj/add/"+_id
				}
				<?php //} else if ($admin['id_role'] == 6) {?>
				,{
					icon : 'file-signature',
					label: 'Buat File FP3',
					class: 'buttonFP3',
					href:site_url+"fp3/insert/"+_id
				}
				<?php }?>
				,{
					icon : 'calendar-alt',
					label: 'Timeline',
					class: 'buttonTimeline',
				},
				{
					icon : 'trash',
					label: 'Hapus',
					class: 'buttonDelete',
					href:site_url+"perencanaan/rekap/remove/"+_id
			}];

			return btn;
		},
		callbackFunctionRightClick: function(){
			// View Data Only
			var view = $('.buttonView').modal({
				header: 'Lihat Data',
				dataType:'html',
                render : function(el, data){
                  // var data = JSON.parse(data);
                  // form = '';
                  console.log(data);
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

		  			console.log($('.form11 input').val());
		  			//+'/'+$('.form31 input').val()
		  			$.ajax({
						url:'<?php echo site_url('pemaketan/get_pic') ?>/'+$('.form11 input').val(),
						method: 'post',
						async:false,
						success: function(xhr) {
							id_role = '<?php echo $this->session->userdata('admin')['id_role'] ?>';
							if (id_role == 2) {
								$('#form-pic').append(xhr);		
							}
						}
					});
				}
			});

			var viewFppbj = $('.buttonViewFppbj').modal({
				header: 'Lihat Data',
				render : function(el, data){
					_self = viewFppbj;

					data.onSuccess = function(){
						$(viewFppbj).data('modal').close();
						folder.data('plugin_folder').fetchData();
					};
					data.isReset = false;
					
					$(el).form(data).data('form');
				}
			});

			// DOWNLOAD Data on PDF
			var pdf = $('.buttonPDF').click(function(){
				// $(location).attr('href',urlpdf);
				window.open(urlpdf, "_blank");
			});
			
			// Link to Timeline Page
			var timeline = $('.buttonTimeline').click(function(){
				$(location).attr('href',urlTimeline);
			});

			// Create new FKPBJ based on selected procurement
			var fkpbj = $('.buttonFKPBJ').modal({
				header: 'Tambah Data FKPBJ',
				render : function(el, data){
					_self = fkpbj;

					data.onSuccess = function(){
						$.ajax({
							url: '<?php echo site_url('main/update_status/');?>',
							data: {'id_fppbj':_id,'param_':1},
							dataType: 'xml',
							success: function(xml){
								$(fkpbj).data('modal').close();
								folder.data('plugin_tableGenerator').fetchData();	
							}
						});
					};
					data.isReset = false;
					$(el).form(data).data('form');


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
								folder.data('plugin_tableGenerator').fetchData();				
							}
						});
								
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
					el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin menghapus data perencanaan pemaketan pengadaan?<span><div class="form"></div><div>');
					data.onSuccess = function(){
						$(del).data('modal').close();
						folder.data('plugin_tableGenerator').fetchData();
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

			console.log(value);
			// DATA PEMAKETAN PENGADAAN DALAM STATUS FPPBJ

			html += '<div class="caption"><p>'+value[1].value+'</p><p>'+value[0].value+'</p><p>'+value[2].value+'</p></div>';
			// console.log(folder);
			return html;
		},

		additionFeature: function(el){
			<?php if ($this->session->userdata('admin')['id_role'] == 3 || $this->session->userdata('admin')['id_role'] == 1) { ?>
				
				<?php if ($this->session->userdata('admin')['id_role'] == 3) : ?>//empty($is_close)
					// el.prepend(insertButton(site_url + "perencanaan/rekap/form/Tambah/<?php echo $year; ?>"));
					el.prepend("<a href='" + site_url + "perencanaan/rekap/form/Tambah/<?php echo $year; ?>' class='button is-primary'>Tutup Perencanaan</a>");
				<?php endif ?>
				el.prepend(exportButton(site_url + "export/filter/<?php echo $year ?>"));
			<?php } ?>
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


	var add = $('.buttonAdd_').modal({
				header: 'Perencanaan Umum dan Ketentuan',
				render : function(el, data){
					data.onSuccess = function(){
						$(add).data('modal').close();
						folder.data('plugin_tableGenerator').fetchData();
					}
					$(el).form(data);

					
						tinymce.init({
							selector: '.tinymce',
            				branding: false
						});
				}
	});

	var filter = $('.buttonExport').modal({
				header: 'Filter Rekap Perencanaan',
				render : function(el, data){
					data.onSuccess = function(){
						$(filter).data('modal').close();
						folder.data('plugin_tableGenerator').fetchData();
					}
					$(el).form(data);

					$('form',el).off('submit');
						tinymce.init({
							selector: '.tinymce',
            				branding: false
						});
				}
	});
	

});


</script>