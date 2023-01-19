<script type="text/javascript">
	$(function(e){
		$('.waitingList').on('click',function(e){
			e.preventDefault();
			if(confirm('Apakah anda yakin mengirim data ke admin?')){
				window.location = $(this).attr('href');
			}
		});
	})
</script>