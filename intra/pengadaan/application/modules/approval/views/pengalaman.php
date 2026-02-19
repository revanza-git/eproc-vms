<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('pengalaman',$id_data)?>
<form method="POST">
	<div class="tableWrapper">
	
		<table class="tableData">
			<thead>
				<tr>
					<td>Nama Paket Pekerjaan</td>
					<!--<td>Bidang</td>
					<td>Sub Bidang</td>-->
					<td>Tanggal Kontrak</td>
					<td><i class="fa fa-exclamation-triangle" style="color:#f39c12"></i></td>
					<td><i class="fa fa-check" style="color:#27ae60"></i></td>
					<td><i class="fa fa-times" style="color: #c1392b"></i></td>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($pengalaman_list)){
				foreach($pengalaman_list as $row => $value){
				?>
					<tr>
						<td><a href="<?php echo site_url('approval/pengalaman_detail/'.$id_data.'/'.$value['id']); ?>"><?php echo $value['job_name'];?></a></td>
						<!--<td><?php echo $value['bidang'];?></td>
						<td><?php echo $value['sub_bidang'];?></td>-->
						<td><?php echo default_date($value['contract_start']);?></td>
						<td><input type="checkbox" name="pengalaman[<?php echo $value['id']?>][mandatory]" value="1" <?php echo $this->data_process->set_mandatory($value['data_status']);?>></td>
						<td class="actionBlock">
							<input type="radio" name="pengalaman[<?php echo $value['id']?>][status]" value="1" <?php echo $this->data_process->set_yes_no(1,$value['data_status']);?>>
						</td>
						<td class="actionBlock">
							<input type="radio" name="pengalaman[<?php echo $value['id']?>][status]" value="0" <?php echo $this->data_process->set_yes_no(0,$value['data_status']);?>>
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
