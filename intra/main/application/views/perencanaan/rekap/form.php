<div class="mg-lg-12">

	<div class="block">

		<?php 
			if ($action == 'Tambah') {
				$url = 'save';
			} else {
				$url = 'update';
			}
		?>

		<div>
			<form action="<?php echo base_url('perencanaan/rekap/save') ?>" method="POST" enctype="multipart/form-data" id="tableGenerator">
				
			</form>
		</div>



	</div>

</div>
