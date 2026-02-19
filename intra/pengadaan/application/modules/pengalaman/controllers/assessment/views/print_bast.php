<?php echo $this->session->flashdata('msgSuccess')?>
<div class="tableWrapper formAss" style="margin-bottom: 20px;padding-left: 20px;">
	<h1 class="formHeader">Cetak BAST</h1>
	<form method="POST" enctype="multipart/form-data" id="assForm">
		<textarea name="text">
			<?php echo $text ?>
		</textarea>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Cetak" class="btnBlue" name="simpan">
		</div>



	</form>

	

</div>