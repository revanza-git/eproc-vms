<?php echo $this->session->flashdata('msgSuccess')?>
<div class="tableWrapper" style="margin-bottom: 20px">

	<div class="btnTopGroup clearfix">
		<form method="POST" enctype="multipart/form-data">
			<h2>Format BAST</h2>
			<table style="width:100%">
				<tr class="input-form">
					<td>
						<textarea id="wysiwyg" style="width:100%;"  name="text"><?php echo $text;?></textarea>
					</td>
				</tr>
			</table>
			
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan Perubahan" class="btnBlue" name="update">
			</div>
		</form>
	</div>
</div>