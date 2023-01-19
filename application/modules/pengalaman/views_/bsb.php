<div class="progressBar">
	<ul>
		<li>
			Izin Usaha
		</li><!--
		--><li class="active">
			Bidang/Sub-bidang Pekerjaan
		</li><!--
		--><li>
			Pengisian Data
		</li>
	</ul>
</div>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
		
			<tr class="input-form">
				<td><label>Bidang/Sub-bidang Pekerjaan</label></td>
				<td>
					<label class="lbform">
						<?php echo form_dropdown('id_iu_bsb', $get_bsb, $this->form->get_temp_data('id_iu_bsb'));?>
					</label>
				</td>
				
			</tr>
			
			<tr>
				<td>
					<?php echo form_error('id_iu_bsb'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Selanjutnya" class="btnBlue" name="next">
			<input type="submit" value="Sebelumnya" class="btnBlue" name="back">
		</div>
	</form>
</div>