<div class="progressBar">
	<ul>
		<li class="active">
			Klasifikasi Usaha
		</li><!--
		--><li>
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
		<?php foreach($get_dpt_type as $val){?>
			<tr class="input-form">
				<td>
					<label class="lbform">
						<?php echo form_radio(array('name'=>'id_dpt_type'),$val['id'],(set_radio('id_dpt_type',$val['id'])||($this->form->get_temp_data('id_dpt_type')==$val['id']))?TRUE:FALSE)?><?php echo $val['name'];?>
					</label>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td>
					<?php echo form_error('id_dpt_type'); ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Selanjutnya" class="btnBlue" name="next">
		</div>
	</form>
</div>