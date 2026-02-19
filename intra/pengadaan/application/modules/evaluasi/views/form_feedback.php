<div class="formDashboard">
	<h1 class="formHeader">Form Feedback</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Pesan*</label></td>
				<td>
					<textarea name="remark"></textarea>
					<?php echo form_error('remark'); ?>
				</td>
			</tr>
		</table>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Simpan">
		</div>
	</form>
</div>