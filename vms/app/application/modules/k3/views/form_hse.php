<div class="progressBar">
	<ul>
		<li>
			CSMS
		</li>
		<li class="active">
			HSE Plan
		</li>
	</ul>
</div>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td>Upload HSE Plan</td>
			</tr>
			<tr class="input-form">
				<td>
					<input type="file" name="hse_file">
					<?php echo form_error('hse_file'); ?>
				</td>
			</tr>
			
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Lanjut" class="btnBlue" name="next">
		</div>
	</form>
</div>