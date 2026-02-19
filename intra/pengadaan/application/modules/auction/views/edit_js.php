<script>
	$(function(){
		// $('#nama_pengadaan').hide();
		$('#nama_perencanaan').hide();

		$('[name="pilih"]').on('change',function() {
			val = $(this).val();
			// alert(val);
			if (val == 'buat_baru') {
				$('#nama_pengadaan').show();
				$('#nama_perencanaan select').prop('selectedIndex',0);
				$('#nama_perencanaan').hide();
			} else {
				$('#nama_pengadaan input').val('');
				$('#nama_pengadaan').hide();
				$('#nama_perencanaan').show();
			}
		})
	});
</script>