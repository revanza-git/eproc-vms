<?php 
	if($this->utility->check_administrasi()>0){
		?>
	<p class="noticeMsg">Harap melengkapi data administrasi Penyedia Barang &amp; Jasa.<br>Pilih menu Administrasi di samping atau klik <a href="<?php echo site_url('administrasi');?>">disini</a></p>
		<?php
	}
?>
<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->session->flashdata('klasifikasi')?>
<div class="btnTopGroup clearfix">
<a href="<?php echo site_url('izin/formBidang/'.$id_bsb);?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
</div>
<div class="tableWrapper">
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['id_bidang'] == 'asc') ? 'desc' : 'asc'; ?>&by=id_bidang">Bidang<i class="fa fa-sort-<?php echo ($sort['id_bidang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['id_sub_bidang'] == 'asc') ? 'desc' : 'asc'; ?>&by=id_sub_bidang">Sub Bidang<i class="fa fa-sort-<?php echo ($sort['id_sub_bidang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($bsb_list)){
			foreach($bsb_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['bidang_name'];?></td>
					<td><?php echo $value['sub_bidang_name'];?></td>
					<td class="actionBlock">
						<a href="<?php echo site_url('izin/formEditBidang/'.$id_bsb.'/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<a style='top: -4px' href="<?php echo site_url('izin/bsb_hapus/'.$id_bsb.'/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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
<div class="pageNumber">
	<?php echo $pagination ?>
</div>