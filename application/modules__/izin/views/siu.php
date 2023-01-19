<div class="progressBar">
	<ul>
		<li>
			Klasifikasi Usaha
		</li><!--
		--><li class="active">
			Surat Izin Usaha
		</li><!--
		--><li>
			Pengisian Data
		</li>
	</ul>
</div>
<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
		<?php foreach($dpt[$form['id_dpt_type']] as $key => $val){?>
			<tr class="input-form">
				
				<td>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'type'),$val,(set_radio('type',$val)||($this->form->get_temp_data('type')==$val))?TRUE:FALSE)?><?php echo $siu[$val];?>
					</label>
				</td>
				
			</tr>
			<?php } ?>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Selanjutnya" class="btnBlue" name="next">
			<input type="submit" value="Kembali" class="btnBlue" name="back">
		</div>
	</form>
</div>