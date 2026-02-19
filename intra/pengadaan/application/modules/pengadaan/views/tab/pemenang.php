<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'pemenang');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
	<?php echo $this->session->flashdata('msgSuccess')?>
		<form method="POST" enctype="multipart/form-data">
			<div class="formDashboard">
			<table>
				<tr class="input-form">
					<td><label>Pemenang</label></td>
					<td><?php 
						$res = array();
						foreach ($list as $key => $value) {
							$res[$value['id']] = $value['name'];
						}
						if(count($res)>0){
							echo form_dropdown('pemenang', $res, ($this->form->get_temp_data('pemenang'))?$this->form->get_temp_data('pemenang'):$data['id_vendor'],'');
						}else{
							echo '-- Belum ada peserta yang melakukan penawaran --';
						}?>
						<?php echo form_error('pemenang'); ?>
						
					</td>
				</tr>
				<tr class="input-form">
					<td>
						<label>Nilai Kontrak: </label>
					</td>
					<td>
						 Rp. <input type="text" name="idr_kontrak" class="money-masked" value="<?php echo ($this->form->get_temp_data('idr_kontrak'))?$this->form->get_temp_data('idr_kontrak'):$data['idr_kontrak']?>">
					</td>
				</tr>
				<tr class="input-form">
					<td>
						
					</td>
					<td>
						<?php echo $this->form->get_kurs(array('name'=>'id_kurs_kontrak'),($this->form->get_temp_data('id_kurs_kontrak'))?$this->form->get_temp_data('id_kurs_kontrak'):$data['id_kurs_kontrak'])?>
						<input type="text" name="kurs_kontrak" value="<?php echo ($this->form->get_temp_data('kurs_kontrak'))?$this->form->get_temp_data('kurs_kontrak'):$data['kurs_kontrak']?>" class="money-masked" >
						<?php echo form_error('check_nilai'); ?>
					</td>
				</tr>
				<tr class="input-form">
					<td>
						<label>Nilai Fee: </label>
					</td>
					<td>
						 Rp. <input type="text" name="fee" class="money-masked" value="<?php echo ($this->form->get_temp_data('fee'))?$this->form->get_temp_data('fee'):$data['fee']?>">
					</td>
				</tr>
			</table>
			<?php echo form_error('id_surat'); ?>
			<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			</div>
			<?php } ?>
			</div>
		</form>
		
	</div>
</div>