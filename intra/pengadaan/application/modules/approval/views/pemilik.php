<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('pemilik',$id_data)?>
<form method="POST">
	<div class="tableWrapper">
	
		<table class="tableData">
			<thead>
				<tr>
					<td>Nama</td>
					<td>Saham Dalam Lembar</td>
					<td>Nilai Kepemilikan Saham</td>
					<td><i class="fa fa-exclamation-triangle" style="color:#f39c12"></i></td>
					<td><i class="fa fa-check" style="color:#27ae60"></i></td>
					<td><i class="fa fa-times" style="color: #c1392b"></i></td>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($pemilik_list)){
				foreach($pemilik_list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['name'];?></td>
						<td><?php echo $value['shares'];?> lembar</td>
						<td><?php echo $value['percentage'];?></td>
						<td><input type="checkbox" name="pemilik[<?php echo $value['id']?>][mandatory]" value="1" <?php echo $this->data_process->set_mandatory($value['data_status']);?>></td>
						<td class="actionBlock">
							<input type="radio" name="pemilik[<?php echo $value['id']?>][status]" value="1" <?php echo $this->data_process->set_yes_no(1,$value['data_status']);?>>
						</td>
						<td class="actionBlock">
							<input type="radio" name="pemilik[<?php echo $value['id']?>][status]" value="0" <?php echo $this->data_process->set_yes_no(0,$value['data_status']);?>>
						</td>
					</tr>
				<?php 
				}
			}else{?>
				<tr>
					<td colspan="11" class="noData">Data tidak ada</td>
				</tr>
			<?php }
			?>
			<?php if (!empty($ubo_file)) { ?>
				<tr>
					<td colspan="6"><a target="blank" href="<?= BASE_LINK_EXTERNAL.('lampiran/ubo_file/'.$ubo_file['ubo_file']) ?>" class="btnBlue"><i class="fa fa-eye"></i> Lihat Surat UBO</a></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		
	</div>

		<?php
		$admin = $this->session->userdata('admin');
		if ($admin['id_role'] == 1 || $admin['id_role'] == 10 || $admin['id_role'] == 3) {
		?>
<div class="buttonRegBox clearfix">
	<input type="submit" value="Simpan" class="btnBlue" name="simpan">
</div>
<?php }?>
</form>
