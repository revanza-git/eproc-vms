<script>
	$('.customReportCheckBox').on('click',function(e){
		// alert('a');
		$(this).toggleClass('checked');
		if($(this).hasClass('checked')){
			$(this).find('input').attr('checked','checked');
		}else{
			$(this).find('input').removeAttr('checked');
		}
	})
</script>