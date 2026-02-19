<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('tdp',$id_data)?>
<form method="POST">
	<div class="tableWrapper">
	
		<table class="tableData">
			<thead>
				<tr>
					<td>No</td>
					<td>Tanggal</td>
					<td>Masa Berlaku</td>
					<td>Lampiran</td>
					<td><i class="fa fa-exclamation-triangle" style="color:#f39c12"></i></td>
					<td><i class="fa fa-check" style="color:#27ae60"></i></td>
					<td><i class="fa fa-times" style="color: #c1392b"></i></td>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($tdp_list)){
				foreach($tdp_list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['no'];?></td>
						<td><?php echo (strtotime($value['issue_date']) > 0) ? default_date($value['issue_date']): "-";?></td>
						<td>
							<?php echo ($value['expiry_date'] == "lifetime") ? "Seumur Hidup": ((strtotime($value['expiry_date']) > 0 ) ? default_date($value['expiry_date']) : "-" );?>
						</td>
						<td><a href="<?php echo BASE_LINK_EXTERNAL.('lampiran/tdp_file/'.$value['tdp_file']);?>" target="_blank"><i class="fa fa-download"></i></a></td>
						<td><input type="checkbox" name="tdp[<?php echo $value['id']?>][mandatory]" value="1" <?php echo $this->data_process->set_mandatory($value['data_status']);?>></td>
						<td class="actionBlock">
							<input type="radio" name="tdp[<?php echo $value['id']?>][status]" value="1" <?php echo $this->data_process->set_yes_no(1,$value['data_status']);?>>
						</td>
						<td class="actionBlock">
							<input type="radio" name="tdp[<?php echo $value['id']?>][status]" value="0" <?php echo $this->data_process->set_yes_no(0,$value['data_status']);?>>
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
