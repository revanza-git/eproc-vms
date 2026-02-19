<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>

<html lang="en">

<head>

	<meta charset="utf-8">

	<title>{header}</title>

	<link rel="stylesheet" href="<?php echo base_url('assets/styles/normalize.css'); ?>" type="text/css" media="screen"/>
	<!-- <link rel="stylesheet" href="<?php echo base_url('assets/font/font-awesome/css/font-awesome.min.css'); ?>" type="text/css" media="screen"/> -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="<?php echo base_url('assets/font/font/flaticon.css'); ?>" type="text/css" media="screen"/>
	<!-- <link rel="stylesheet" href="<?php echo base_url('assets/js/fullcalendar/fullcalendar.min.css'); ?>" type="text/css" media="screen"/> -->
	<link rel="stylesheet" href="<?php echo base_url('assets/js/datepicker/jquery.datetimepicker.css'); ?>" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo base_url('assets/js/clockpicker/src/clockpicker.css'); ?>" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo base_url('assets/js/clockpicker/dist/jquery-clockpicker.css'); ?>" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo base_url('assets/js/daterangepicker/jquery.comiseo.daterangepicker.css'); ?>" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo base_url('assets/js/jquery-ui/jquery-ui.min.css'); ?>" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo base_url('assets/styles/base.css'); ?>" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo base_url('assets/styles/scss/main.min.css'); ?>" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo base_url('assets/styles/fontawesome5.6.3/css/all.css'); ?>" type="text/css" media="screen"/>
	<script>
		var base_url = "<?php echo base_url()?>";
		var site_url = "<?php echo site_url()?>";
		var csrf_token_name = "<?php echo $this->security->get_csrf_token_name(); ?>";
		var csrf_cookie_name = "<?php echo $this->config->item('csrf_cookie_name'); ?>";
		var csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";

		function getCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for (var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) === ' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
			}
			return null;
		}
	</script>

	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js');?>"></script>
	<script type="text/javascript">
		if (window.jQuery) {
			$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
				var method = (options.type || options.method || 'GET').toString().toUpperCase();
				if (method !== 'POST') return;
				var token = getCookie(csrf_cookie_name) || csrf_hash;
				if (!token) return;

				options.headers = options.headers || {};
				if (!options.headers['X-CSRF-Token']) {
					options.headers['X-CSRF-Token'] = token;
				}

				if (typeof FormData !== 'undefined' && options.data instanceof FormData) {
					if (typeof options.data.has === 'function') {
						if (!options.data.has(csrf_token_name)) {
							options.data.append(csrf_token_name, token);
						}
					} else {
						options.data.append(csrf_token_name, token);
					}
					return;
				}

				if (typeof options.data === 'string') {
					if (options.data.indexOf(csrf_token_name + '=') !== -1) return;
					options.data = options.data + (options.data.length ? '&' : '') +
						encodeURIComponent(csrf_token_name) + '=' + encodeURIComponent(token);
					return;
				}

				if (options.data == null) {
					options.data = {};
				}

				if (typeof options.data === 'object' && !options.data[csrf_token_name]) {
					options.data[csrf_token_name] = token;
				}
			});

			$(document).ajaxComplete(function(event, xhr) {
				try {
					var cookieToken = getCookie(csrf_cookie_name);
					if (cookieToken) {
						csrf_hash = cookieToken;
					}
					var nextToken = xhr.getResponseHeader('X-CSRF-Token');
					if (nextToken) {
						csrf_hash = nextToken;
					}
				} catch (e) {}
			});
		}
	</script>
	

	<script type="text/javascript" src="<?php echo base_url('assets/js/moment-with-locales.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui/jquery-ui.min.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.imask.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/tableGenerator_v2.js?v='.filemtime(FCPATH.'assets/js/tableGenerator_v2.js'));?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/folder_generator.js?v='.filemtime(FCPATH.'assets/js/folder_generator.js'));?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/filter.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/form.js?v='.filemtime(FCPATH.'assets/js/form.js'));?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/formWizard.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/modal.js?v='.filemtime(FCPATH.'assets/js/modal.js'));?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/datepicker/build/jquery.datetimepicker.full.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/countdown/jquery.countdown.js')?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/date.format.js')?>"></script>

	<script type="text/javascript" src="<?php echo base_url('assets/js/fullcalendar/fullcalendar.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/tinymce/js/tinymce/tinymce.min.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/clockpicker/src/clockpicker.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/common.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/app.js');?>"></script>

	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.number.min.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/numeral.min.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/utility.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/daterangepicker/jquery.comiseo.daterangepicker.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui/ui/jquery.ui.tooltip.min.js');?>"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.actual/1.0.19/jquery.actual.js"></script>
	<!-- Datejs-master/build/date.js -->
	<script src="https://code.highcharts.com/highcharts.js"></script>

	<script type="text/javascript" src="<?php echo base_url('assets/js/Datejs-master/build/date.js');?>"></script>


</head>

<body>

<div id="container">

	<nav class="navbar">

		<div class="navbar-brand">
			
			<div class="navbar-item logo">

				<img src="<?php echo base_url('assets/images/logo-nr.png?v='.@filemtime(FCPATH.'assets/images/logo-nr.png')); ?>">

			</div>

		</div>

		<div class="navbar-menu">

			<div class="navbar-start">
				
				<div class="navbar-item">
					<div class="search-bar">
						<input type="text" name="nama_pengadaan" class="input" placeholder="Search..">
						<span class="icon">
							<i class="fas fa-search"></i>
						</span>
					</div>
				</div>

			</div>

			<div class="navbar-end">

				<div class="navbar-item account has-dropdown">

					<?php
						$photo_profile = isset($this->session->userdata('admin')['photo_profile']) ? $this->session->userdata('admin')['photo_profile'] : '';
						$photo_profile_path = FCPATH.'assets/lampiran/photo_profile/'.$photo_profile;
						$photo_profile_url = ( ! empty($photo_profile) && file_exists($photo_profile_path))
							? base_url('assets/lampiran/photo_profile/'.$photo_profile)
							: base_url('assets/images/Man-Avatar.png');
					?>
					<img src="<?php echo $photo_profile_url; ?>" alt="" height="45px">
					
          			<p>{user}</p>

          			<span class="icon spin"><i class="fas fa-angle-down"></i></span>

          			<div class="navbar-dropdown is-dropdown">

		            	<a href="<?php echo site_url('pass_change') ?>" class="navbar-item">

		              		<span class="icon"><i class="fas fa-cog"></i></span>

		              		Edit Akun

		            	</a>

		            	<a href="<?php echo site_url('main/logout')?>" class="navbar-item">

		              		<span class="icon"><i class="fas fa-sign-out-alt"></i></span>

		              		Logout

		            	</a>

		          	</div>

				</div>

			</div>

		</div>
	</nav>

	<section class="contentWrap">
		<div class="hbox">

			<div class="sidebar">

				{sideMenu}

			</div>

			<div class="main">
				
				<div class="mainWrapper">
						<div class="sr-wrapper">
							
							
						</div>
						<button style="display: none" class="button sr-close-btn is-danger">Close all</button>
					<?php

						if($this->session->userdata('alert'))

					?>

					{breadcrumb}

					<?php

						echo $this->session->userdata('msg');

					?>

					<h1 class="page-heading">{header}</h1>

					<div class="row">
						{content}

					</div>
					
				</div>

			</div>

		</div>

	</section>

</div>

<div class="form-keterangan-reject">
	<span class="fkr-btn-close">
		<i class="fas fa-times"></i>
	</span>
	<div class="fkr-content">
		<fieldset class="form-group" for="" style="display: block;">
			<label for="keterangan">Keterangan</label>
			<textarea type="text" class="form-control fkr-textarea" id="" value="" name="" placeholder="isi keterangan penolakan"></textarea>
		</fieldset>
	</div>
	<div class="fkr-btn-group">
		<button class="is-danger" type="submit" name="reject">Reject</button>
	</div>
</div>

<div class="nm-wrapper"><div class="nm-form">Form isian <br><input type="text" class="nm-input"><div class="btn-group"><button class="is-primary">Submit</button><button class="close-btn">Cancel</button></div></div><table class="new-matrix" style="border-spacing: 5px"><tr><th colspan="2"></th><th colspan="5" style="font-size: 14px">Probability</th><!-- <th><button class="close"><span aria-hidden="true">x</span></button></th> --></tr><tr><th></th><th class="manusia_desc">Manusia</th><th class="aset_desc">Aset / Peralatan</th><th class="lingkungan_desc">Lingkungan</th><th class="hukum_desc">Reputasi dan Hukum</th><th>Tidak pernah terdengar di industri / perkantoran migas</th><th>Pernah terdengar di industri / perkantoran migas</th><th>Terjadi 1 kali dalam 5 tahun di perusahaan</th><th>Terjadi 1 kali dalam 1tahun di perusahaan</th><th style="padding-right: 30px;">Terjadi > 1 kali dalam 1 tahun di perusahaan</th></tr><tr><th rowspan="5" style="font-size: 14px; padding-bottom: 15px"><div class="rotated">Severity</div></th><td class="manusia_desc">Multiple Fatality/Korban meninggal lebih dari 1</td><td class="aset_desc">Kerusakan Parah</td><td class="lingkungan_desc">Dampak luar biasa (internasional)</td><td class="hukum_desc">Dampak luar biasa (internasional)</td><td class="box-parent"><div class="nm-box yellow"><span class="nm-text">5</span></div></td><td class="box-parent"><div class="nm-box orange n10">10</div></div></td><td class="box-parent"><div class="nm-box red n15">15</div></div></td><td class="box-parent"><div class="nm-box red n20">20</div></div></td><td class="box-parent"><div class="nm-box red n25">25</div></div></td></tr><tr><td class="manusia_desc">Single Fatality/Cacat Permanen</td><td class="aset_desc">Kerusakan Besar</td><td class="lingkungan_desc">Dampak besar (nasional)</td><td class="hukum_desc">Dampak besar (nasional)</td><td class="box-parent"><div class="nm-box green-light n4">4</div></div></td><td class="box-parent"><div class="nm-box yellow n8">8</div></div></td><td class="box-parent"><div class="nm-box orange n12">12</div></div></td><td class="box-parent"><div class="nm-box red n16">16</div></div></td><td class="box-parent"><div class="nm-box red n20">20</div></div></td></tr><tr><td class="manusia_desc">Cidera / sakit berat</td><td class="aset_desc">Kerusakan Setempat</td><td class="lingkungan_desc">Dampak besar setempat/lokal</td><td class="hukum_desc">Dampak sedang</td><td class="box-parent"><div class="nm-box green n3">3</div></div></td><td class="box-parent"><div class="nm-box yellow n6">6</div></div></td><td class="box-parent"><div class="nm-box yellow n9">9</div></div></td><td class="box-parent"><div class="nm-box orange n12">12</div></div></td><td class="box-parent"><div class="nm-box red n15">15</div></div></td></tr><tr><td class="manusia_desc">Cidera / sakit sedang</td><td class="aset_desc">Kerusakan Sedang</td><td class="lingkungan_desc">Dampak sedang</td><td class="hukum_desc">Dampak terbatas</td><td class="box-parent"><div class="nm-box green n2">2</div></td><td class="box-parent"><div class="nm-box green-light n4">4</div></td><td class="box-parent"><div class="nm-box yellow n6">6</div></td><td class="box-parent"><div class="nm-box yellow n8">8</div></td><td class="box-parent"><div class="nm-box orange n10">10</div></td></tr><tr><td class="manusia_desc">Cidera / sakit ringan</td><td class="aset_desc">Kerusakan Kecil</td><td class="lingkungan_desc">Dampak ringan</td><td class="hukum_desc">Dampak ringan</td><td class="box-parent"><div class="nm-box green n1">1</div></td><td class="box-parent"><div class="nm-box green n2">2</div></td><td class="box-parent"><div class="nm-box green n3">3</div></div></td><td class="box-parent"><div class="nm-box green-light n4">4</div></div></td><td class="box-parent"><div class="nm-box yellow n5">5</div></div></td></tr><!-- <tr><td colspan="10"><div class="btn-group"><button>Previous</button><button>Next</button></div></td></tr> --></table></div>
</body>
	{script}
	<script type="text/javascript">

	$('.button .is-danger').click(function() {
		location.reload();
	});

	$(function() {
		$('.search-bar input[name="nama_pengadaan"]').keyup(function() {
				_self = $(this);
				// _wrapper 		= $('mainWrapper').append('')
				_parent 		= _self.closest('.input');
				_resultWrapper 	= $('.sr-wrapper');
				_resultWrapper.empty();

				$('.sr-close-btn').click(function() {
					_resultWrapper.empty();
					_resultWrapper.removeClass('active');
					$('.sr-close-btn').css('display','none');
					$('.search-bar').find('.input').val('');
				})

				$.ajax({
						url : '<?php echo site_url('dashboard/search_data')?>',
						data : {
							search : _self.val()
							, [csrf_token_name] : getCookie(csrf_cookie_name)
						},
						method : 'POST',
						beforeSend : function(){
							$(_self).addClass('loading');
							_resultWrapper.empty();
						},
						success : function(xhr){
							// setTimeout(function(){
								$(_resultWrapper).addClass('active');
								$(_self).removeClass('loading');
									// html += '';
								$.each( $.parseJSON(xhr), function(key, value){
									
								console.log(value);
									_resultWrapper.append(value);
									$('.sr-close-btn').css('display','block');
								});
								// _resultWrapper.after('')
							// }, 1000);
							
						}	
					});
			})
		$('.sr-close-btn').click(function() {
			$('.sr-wrapper').removeClass('active');
			$(this).css('display','none');
		})
	})
</script>
</html>
