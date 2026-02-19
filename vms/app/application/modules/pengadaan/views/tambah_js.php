<script>

	$( document ).ready(function() {
        console.log( "document loaded");

		$('#evaluation_method_desc_div').hide();

		$("select").change(function () {
	     		if ($(this).val() == 'scoring') {
		        $('#evaluation_method_desc_div').show();
		        //alert($(this).val());
	     }

		
    });
});
	
</script>