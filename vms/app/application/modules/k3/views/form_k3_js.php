<script type="text/javascript">
	$(function(e){
		$('.lampiran_csms').hide();
		if($('#csms_true').is(':checked')){
			$('.lampiran_csms').show();
		}
		$('.csms_radio').on('click',function(){
			if($(this).val()=='1'){
				$('.lampiran_csms').show();
			}else{
				$('.lampiran_csms').hide();
			}
		})
	})
</script>