<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('badan_usaha',$id_data)?>
<div class="formDashboard">
	<h1 class="formHeader"><?php echo $dt_siu[$get_data['type']]?></h1>
	<table>
		<?php foreach($table[$get_data['type']] as $key =>$value){?>
		<tr class="input-form">
			<td><label><?php echo $value;?></label></td>
			<td>
				<?php if($key == 'izin_file'){ ?>
				<a target="blank" href="<?php echo BASE_LINK.('lampiran/izin_file/'.$get_data[$key])?>"><?= $get_data[$key] ?></a>
				<?php } else { 
				echo $get_data[$key];
				}?>
			</td>
			<tr>
		<?php }?>
	</table>
</div>
<form method="POST">
	<div class="tableWrapper">
	
		<table class="tableData">
			<thead>
				<tr>
					<td>Bidang</td>
					<td>Sub-Bidang</td>
					
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($bsb_list)){
				foreach($bsb_list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['bidang'];?></td>
						<td><?php echo $value['sub_bidang'];?></td>
						
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


</form>
