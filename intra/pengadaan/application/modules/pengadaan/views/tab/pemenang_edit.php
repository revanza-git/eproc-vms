<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'pemenang');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		<form method="POST" enctype="multipart/form-data">
			<div class="formDashboard">
			<table>
				<tr class="input-form">
					<td><label>Pemenang</label></td>
					<td><?php 
						$res = array();
						foreach ($list as $key => $value) {
							$res[$value['id_vendor']] = $value['name'];
						}
						echo form_dropdown('pemenang', $res, ($this->form->get_temp_data('pemenang'))?$this->form->get_temp_data('pemenang'):$data['name'],'');?>
						<?php echo form_error('pemenang'); ?>
						
							<!--
							<label class="<?php echo ($value['is_winner']==1?'winner':'');?>">
								<?php if($this->session->userdata('admin')['id_role']==3){ ?><input type="radio" name="pemenang" value="<?php echo $value['id']?>" <?php echo ($value['is_winner']==1?'checked':'')?>><?php } ?>
								</div>&nbsp;<?php echo ($value['is_winner']==1?'<i class="fa fa-trophy"></i>':'')?></label>
							-->
					</td>
				</tr>
				<tr class="input-form">
					<td>
						<label>Nilai : </label>
					</td>
					<td>
						 Rp.<input type="text" name="idr_value" class="money-masked" value="<?php echo ($this->form->get_temp_data('idr_value'))?$this->form->get_temp_data('idr_value'):$data['idr_value']?>">
					</td>
				</tr>
				<tr class="input-form">
					<td>
						
					</td>
					<td>
						<?php echo $this->form->get_kurs(array('name'=>'id_kurs'),($this->form->get_temp_data('id_kurs'))?$this->form->get_temp_data('id_kurs'):$data['id_kurs'])?>
						<input type="text" name="kurs_value" value="<?php echo ($this->form->get_temp_data('kurs_value'))?$this->form->get_temp_data('kurs_value'):$data['kurs_value']?>" class="money-masked" >
						<?php echo form_error('check_nilai'); ?>
					</td>
				</tr>
				
			</table>
			<?php echo form_error('id_surat'); ?>
			<?php if($this->session->userdata('admin')['id_role']==3){ ?>
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			</div>
			<?php } ?>
			</div>
		</form>
		
	</div>
</div>