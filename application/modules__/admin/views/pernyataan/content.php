<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('msgError')?>
<div class="tableWrapper" style="margin-bottom: 20px">
	<?php foreach ($pernyataan as $key => $value) {?>
	<div class="btnTopGroup clearfix">
		<form method="POST" enctype="multipart/form-data">
			<h2>Surat Pernyataan</h2>
			<table style="width:100%">
				<tr class="input-form">
					<td>
						<input type="hidden" value="<?php echo $value['id'];?>" name="id">
						<textarea id="wysiwyg" style="width:100%;height: 500px" name="value"><?php echo $value['value'];?></textarea>
					</td>
				</tr>
			</table>
			
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan Perubahan" class="btnBlue" name="update">
			</div>
		</form>
	</div>
</div>
<?php } ?>