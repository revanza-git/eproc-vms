<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Sistem Aplikasi Kelogistikan - PT Nusantara Regas</title>

		

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/normalize.css" type="text/css"/>

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css" type="text/css"/>

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.css" type="text/css"/>

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome-ie7.css" type="text/css"/>

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/font/auction/flaticon.css" type="text/css"/>

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/font/flaticon.css" type="text/css"/>

		<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery.wysiwyg.css');?>" type="text/css"/>

		<link rel="stylesheet" href="<?php echo base_url('assets/js/jquery-ui/css/ui-lightness/jquery-ui-1.10.0.custom.css');?>" type="text/css"/>
		<link rel="stylesheet" href="<?php echo base_url('assets/css/alertify/alertify.css');?>" type="text/css"/>
		<link rel="stylesheet" href="<?php echo base_url('assets/css/alertify/default.css');?>" type="text/css"/>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.imask.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/alertify.js"></script>


	<script src="<?php echo base_url('assets/js/jquery-ui/development-bundle/ui/jquery.ui.core.js')?>"></script>

	<script src="<?php echo base_url('assets/js/jquery-ui/development-bundle/ui/jquery.ui.widget.js'); ?>"></script>

	<script src="<?php echo base_url('assets/js/jquery-ui/development-bundle/ui/jquery.ui.position.js'); ?>"></script>

	<script src="<?php echo base_url('assets/js/jquery-ui/development-bundle/ui/jquery.ui.menu.js'); ?>"></script>

	<script src="<?php echo base_url('assets/js/jquery-ui/development-bundle/ui/jquery.ui.autocomplete.js'); ?>"></script>

	<script src="<?php echo base_url('assets/js/jquery-ui/development-bundle/ui/jquery.ui.position.js'); ?>"></script>

	<script src="<?php echo base_url('assets/js/jquery-ui/development-bundle/ui/jquery.ui.tooltip.js'); ?>"></script>

	

	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/utility.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/content.js"></script>


		<!--[if lt IE 9]>

		<script src="<?php echo base_url(); ?>assets/js/html5.min.js"></script>

		<![endif]-->

	</head>

	<body>

		<div class="wrap">

			<div class="wrapInner">

				<div class="header">

					<div class="container clearfix">

						<?php  if(isset($header)){echo $header;}?>

					</div>

				</div>

				<div class="main">

					<div class="mainInner">

						<div class="container clearfix">

							<?php if(isset($content)){echo $content;} ?>

						</div>

					</div>

				</div>

				<div class="footer">

					<p class="copyright">© PT Nusantara Regas <?php echo date('Y');?></p>

					<p class="systemVer">System Version 1.0</p>

				</div>

			</div>

		</div>

	</body>

	<script type="text/javascript">

		var base_url = "<?php echo base_url(); ?>";

		var _appGlobal = {

			'base_url' : base_url

		};



		$(document).ready(function() {
			$('.sideArea .navMenu li').click(function(){
				$(this).toggleClass('active');
			});

			$('.has-dropdown').click(function(){
				$(this).toggleClass('active');
			});

			$('.filterBtn').click(function(){
				$('.filterWrapper').addClass('active');
				$('.filterWrapperOverlay').addClass('active');
			});

			$('.filterWrapperOverlay').click(function(){
				$('.filterWrapper').removeClass('active');
				$(this).removeClass('active');
			});

			$('.filterBtnClose').click(function(){
				$('.filterWrapper').removeClass('active');
				$('.filterWrapperOverlay').removeClass('active');
			});
		});  

	</script>

	

	
	
	



	<?php 

	    if(isset($script))

	    {

	        echo $script;

	    }

	?>

</html>
