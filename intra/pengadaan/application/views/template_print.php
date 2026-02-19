<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Sistem Aplikasi Kelogistikan - PT Nusantara Regas</title>
		
		<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/normalize.css" type="text/css"/> -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/print.css?v=<?php echo @filemtime(FCPATH . 'assets/css/print.css'); ?>" type="text/css"/>
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.css" type="text/css"/>
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome-ie7.css" type="text/css"/>
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/font/flaticon.css" type="text/css"/>
		
		<!--[if lt IE 9]>
		<script src="<?php echo base_url(); ?>assets/js/html5.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="wrap">
			<div class="wrapInner">
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
	</script>
	
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.imask.js"></script>
<script>
	$('#npwp').iMask({
		type : 'fixed',
		mask : '99.999.999.9-999.999',
	});
	$('.money-masked').iMask({
		type : 'number'
	});
	function changeCal_date( field ){
		field = field.replace(/\|/g, "\\\|");
		field = field.replace(/\[/g, "\\\[");
		field = field.replace(/\]/g, "\\\]");
		var value = $("#"+field+"_date-year").val() + "-" + $("#"+field+"_date-month").val()+ "-" + $("#"+field+"_date-date").val();
		
		$("input#"+field).val(value);  
	}
	function lifetime_date(field){
		if($("#nppkp_date-lifetime").is(":checked")){
			$("#nppkp_date").val("lifetime");
			$("#nppkp_date-date-container").slideUp();
		} else {
			$("#nppkp_date-date-container").slideDown();
			$("#nppkp_date-year").val("2015");
			$("#nppkp_date-month").val("09");
			$("#nppkp_date-date").val("03");
			changeCal_nppkp_date()
		}
	}
</script>
<script>
	$('.delBtn').on('click',function(e){
		e.preventDefault();
		if(confirm('Apakah anda yakin ingin menghapus data?'))
		{
			window.location.href = $(this).attr('href');
		}
	})
</script>
	<?php 
	    if(isset($script))
	    {
	        echo $script;
	    }
	?>
</html>
