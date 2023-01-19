<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.imask.js"></script>
<script>
	$('#npwp').iMask({
		type : 'fixed',
		mask : '99.999.999.9-999.999',
	});
	function changeCal_date( field ){
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
			changeCal_date()
		}
	}
</script>