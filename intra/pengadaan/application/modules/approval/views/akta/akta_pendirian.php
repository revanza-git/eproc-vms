<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('akta',$id_data)?>
<div class="tab">
	<form method="post">
	<div class="tabNav">
		<ul>
			<li  class="active">
				<a href="<?php echo site_url('approval/akta/'.$id_data.'/pendirian')?>">Akta Pendirian Perusahaan</a>
			</li><!--
			--><li>
				<a href="<?php echo site_url('approval/akta/'.$id_data.'/perubahan')?>">Akta Perubahan Terakhir</a>
			</li>
		</ul>
	</div>
	<div class="tabWrapper">
		<div class="tableWrapper">
		
			<table class="tableData">
				<thead>
					<tr>
						<td>No Akta</td>
						<td>Notaris</td>
						<td>Tanggal</td>
						<td>Lampiran Akta</td>
						<td>Lembaga Pengesah</td>
						<td>No. Pengesahan</td>
						<td>Tanggal Pengesahan</td>
						<td>Lampiran Pengesahan Akta</td>
						<td><i class="fa fa-exclamation-triangle" style="color:#f39c12"></i></td>
						<td><i class="fa fa-check" style="color:#27ae60"></i></td>
						<td><i class="fa fa-times" style="color: #c1392b"></i></td>
					</tr>
				</thead>
				<tbody>
				<?php 
				if(count($akta_list)){
					foreach($akta_list as $row => $value){
					?>
						<tr>
							<td><?php echo $value['no'];?></td>
							<td><?php echo $value['notaris'];?></td>
							<!-- <td><?php echo default_date(date('d-m-Y', strtotime($value['issue_date'])));?></td> -->
							<td><?php echo  special_date($value['issue_date']);?></td>
							<td><a href="<?php echo BASE_LINK_EXTERNAL.('lampiran/akta_file/'.$value['akta_file']);?>" target="_blank"><i class="fa fa-download"></i></a></td>
							<td><?php echo $value['authorize_by'];?></td>
							<td><?php echo $value['authorize_no'];?></td>
							<td><?php echo  special_date($value['authorize_date']);?></td>
							<!-- <td><?php echo default_date(date('d-m-Y', strtotime($value['authorize_date'])));?></td> -->
							<td><a href="<?php echo BASE_LINK_EXTERNAL.('lampiran/authorize_file/'.$value['authorize_file']);?>" target="_blank"><i class="fa fa-download"></i></a></td>
							<td><input type="checkbox" name="akta[<?php echo $value['id']?>][mandatory]" value="1" <?php echo $this->data_process->set_mandatory($value['data_status']);?>></td>
							<td class="actionBlock">
								<input type="radio" name="akta[<?php echo $value['id']?>][status]" value="1" <?php echo $this->data_process->set_yes_no(1,$value['data_status']);?>>
							</td>
							<td class="actionBlock">
								<input type="radio" name="akta[<?php echo $value['id']?>][status]" value="0" <?php echo $this->data_process->set_yes_no(0,$value['data_status']);?>>
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
</div>