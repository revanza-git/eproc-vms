<div class="progressBar">
	<ul>
		<li class="active">
			Izin Usaha
		</li><!--
		--><li>
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
				<td><label>Izin Usaha</label></td>
				<td>
					<label class="lbform">
						<?php echo form_dropdown('id_ijin_usaha', $get_siu, $this->form->get_temp_data('id_ijin_usaha'));?>
					</label>
				</td>
				
			</tr>
			
			<tr>
				<td>
					<?php echo form_error('id_ijin_usaha'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Selanjutnya" class="btnBlue" name="next">
		</div>
	</form>
</div>