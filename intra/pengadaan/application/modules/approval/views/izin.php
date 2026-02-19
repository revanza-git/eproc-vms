<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('badan_usaha',$id_data)?>
<div class="tab">
	<form method="post">
	<div class="tabNav">
		<ul>
			<?php foreach($dt_siu as $key=>$value){?>
				<li class="<?php echo ($key==$surat)?'active':'';?>"><a href="<?php echo site_url('approval/badan_usaha/'.$id_data.'/'.$key)?>"><?php echo $value;?></a></li>
			<?php 	
			}?>
		</ul>
	</div>

	<div class="tabWrapper">

		<div class="tableWrapper">
		

			<table class="tableData">
				<thead>
					<tr>
						<?php foreach($table[$surat] as $key =>$value){?>
							<td><?php echo $value;?></td>
						<?php }?>
						<td><i class="fa fa-exclamation-triangle" style="color:#f39c12"></i></td>
						<td><i class="fa fa-check" style="color:#27ae60"></i></td>
						<td><i class="fa fa-times" style="color: #c1392b"></i></td>
					</tr>
				</thead>
				<tbody>
				<?php 
				if(count($izin_list)){
					foreach($izin_list as $row => $value){
					?>
						<tr>
							<?php foreach($table[$surat] as $field =>$label){
								if($field=='izin_file'){
								?>
								<td><a href="<?php echo BASE_LINK_EXTERNAL.('lampiran/izin_file/'.$value['izin_file']);?>" target="_blank"><i class="fa fa-download"></i></a></td>
								<?php }elseif($field=='no'){?>
								<td><a href="<?php echo site_url('approval/bsb/'.$id_data.'/'.$value['id']);?>"><?php echo $value['no']?></a></td>
								<?php }elseif($field=='issue_date'){?>
								<td><?php echo default_date($value['issue_date']);?></td>
								<?php }elseif($field=='expire_date'){?>
								<td><?php echo ($value['expire_date'] == "lifetime")? "Seumur Hidup" : default_date($value['expire_date']) ;?></td>
								<?php }else{ ?>
									<td><?php echo $value[$field];?></td>
								<?php	} ?>
							<?php }?>
							<td><input type="checkbox" name="ijin_usaha[<?php echo $value['id']?>][mandatory]" value="1" <?php echo $this->data_process->set_mandatory($value['data_status']);?>></td>
							<td class="actionBlock">
								<input type="radio" name="ijin_usaha[<?php echo $value['id']?>][status]" value="1" <?php echo $this->data_process->set_yes_no(1,$value['data_status']);?>>
							</td>
							<td class="actionBlock">
								<input type="radio" name="ijin_usaha[<?php echo $value['id']?>][status]" value="0" <?php echo $this->data_process->set_yes_no(0,$value['data_status']);?>>
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
