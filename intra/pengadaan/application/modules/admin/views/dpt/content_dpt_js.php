<script src="<?php echo base_url(); ?>assets/js/highcharts.js"></script>

<script src="<?php echo base_url(); ?>assets/js/modules/exporting.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$("#popup").hide();
		$("#popupCSMS").hide();
		$("#mask").hide();

	    $(".show").click(function() {
	        $("#popup").show();
			$("#mask").show();
			$("#popup #id").val($(this).data("id"));
	    });

	    $("#hide").click(function() {
	        $("#popup").hide();
			$("#mask").hide();
	    });

	    $(".showCSMS").click(function() {
	        $("#popupCSMS").show();
			$("#mask").show();
			$("#popupCSMS #idCSMS").val($(this).data("id"));
	    });

	    $("#hideCSMS").click(function() {
	        $("#popupCSMS").hide();
			$("#mask").hide();
	    });

	    $("#btn").click(function() {
	        $("#popup").hide();
			$("#mask").hide();
	    });

	    $("#btnCSMS").click(function() {
	        $("#popupCSMS").hide();
			$("#mask").hide();
	    });

	});
</script>
<script type="text/javascript">
					$(function () {

						$('.certificateBtn').on('click',function(e){
							_this = $(this);
							e.preventDefault();
							alertify.prompt('Masukkan nomor sertifikat',_this.attr('no'),function(evt, value){
								$.ajax({
									method:'GET',
									url:'<?php echo site_url('admin/certificate/change_no');?>',
									data: { no:value, id:_this.attr('id_vendor')}, 
									success: function(response){
										if(response){
											location.reload();
										}else{
											location.reload();
										}
									}
								})
								return true;
								
							});
								
						});

					});
				</script>