<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'pemenang');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
			<?php echo $this->session->flashdata('msgSuccess')?>
		<form method="POST" enctype="multipart/form-data">
			<div class="formDashboard">
			<table>
				<tr class="input-form">
					<td><label>Pemenang</label></td>
					<td><?php echo $data['name']?>
					</td>
				</tr>
				<tr class="input-form">
					<td>
						<label>Nilai : </label>
					</td>
					<td>
						 Rp. <?php echo number_format($data['idr_kontrak'])?>
					</td>
				</tr>
				<tr class="input-form">
					<td>
						
					</td>
					<td>
						<?php echo $data['kurs_name_kontrak']?>
						<?php echo number_format($data['kurs_kontrak'])?>
					</td>
				</tr>
				<tr class="input-form">
					<td>
						Fee
					</td>
					<td>
						Rp. <?php echo number_format($data['fee'])?>
					</td>
				</tr>
				
			</table>
			<?php echo form_error('id_surat'); ?>
			<?php if($this->session->userdata('admin')['id_role']==3){ ?>
			<div class="buttonRegBox clearfix">
				<a href="<?php echo site_url('pengadaan/view/'.$id.'/pemenang_edit')?>" class="btnBlue">Edit</a>
			</div>
			<?php } ?>
			</div>
		</form>
		
	</div>
</div>