<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('agen',$id_data)?>
<div class="formDashboard">
	<h1 class="formHeader">Pabrikan/Keagenan/Distributor</h1>
	<table>
		
		<tr class="input-form">
			<td><label>Nomor Surat Dari Lembaga Pemerintah yang Berwenang</label></td>
			<td>
				<?php echo $no;?>
			</td>
		<tr>
		<tr class="input-form">
			<td><label>Tanggal</label></td>
			<td>
				<?php echo $issue_date;?>
			</td>
		<tr>
		<tr class="input-form">
			<td><label>Pabrikan/Keagenan/Distributor</label></td>
			<td>
				<?php echo $type;?>
			</td>
		<tr>
		<tr class="input-form">
			<td><label>Masa Berlaku</label></td>
			<td>
				<?php echo ($value['expire_date']=='lifetime')?'Seumur Hidup':$value['expire_date'];?>
			</td>
		<tr>
		<tr class="input-form">
			<td><label>Lampiran</label></td>
			<td>
				<?php echo $agen_file;?>
			</td>
		<tr>
	</table>
</div>
<form method="POST">
	<div class="tableWrapper">
	
		<table class="tableData">
			<thead>
				<tr>
					<td>Produk</td>
					<td>Merk</td>
					<td><i class="fa fa-exclamation-triangle" style="color:#f39c12"></i></td>
					<td><i class="fa fa-check" style="color:#27ae60"></i></td>
					<td><i class="fa fa-times" style="color: #c1392b"></i></td>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($produk_list)){
				foreach($produk_list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['produk'];?></td>
						<td><?php echo $value['merk'];?></td>
						<td><input type="checkbox" name="produk[<?php echo $value['id']?>][mandatory]" value="1" <?php echo $this->data_process->set_mandatory($value['data_status']);?>></td>
						<td class="actionBlock">
							<input type="radio" name="produk[<?php echo $value['id']?>][status]" value="1" <?php echo $this->data_process->set_yes_no(1,$value['data_status']);?>>
						</td>
						<td class="actionBlock">
							<input type="radio" name="produk[<?php echo $value['id']?>][status]" value="0" <?php echo $this->data_process->set_yes_no(0,$value['data_status']);?>>
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

<?php if($this->session->userdata('admin')['id_role']==1 || $admin['id_role'] == 3){?>
<div class="buttonRegBox clearfix">
	<input type="submit" value="Simpan" class="btnBlue" name="simpan">
</div>
<?php }?>
</form>
