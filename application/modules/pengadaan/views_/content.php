<?php echo $this->session->flashdata('msgSuccesss')?>
<h2 class="formHeader">Daftar Pengadaan</h2>
<div class="tableWrapper" style="margin-bottom: 20px">
	<div class="tableHeader">
			<a style="background: #2f3640 !important; border-bottom: 2px #718093 solid !important;" href="<?php echo site_url('pengadaan/export_excel');?>" class="btnBlue"><i class="fa fa-download"></i> Export</a>
		<form method="POST">
			<?php if($this->session->userdata('admin')['id_role']==3){?>
			<a href="<?php echo site_url('pengadaan/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
			<?php } echo $filter_list;?>

		</form>	
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Nama Pengadaan<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Jenis Pengadaan<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">NPWP<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>&by=pemenang">Nama Pemenang Sesuai Kontrak<i class="fa fa-sort-<?php echo ($sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Nilai<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Status<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.budget_year">Tahun<i class="fa fa-sort-<?php echo ($sort['ms_procurement.budget_year'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<?php if ($admin['id_role'] == 8) { ?>	
				<td>Last Edited</td>
				<?php }?>
				<td colspan="2">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($pengadaan_list)){
			foreach($pengadaan_list as $row => $value){
		?>
				<tr>
					<td><?php echo $value['name'];?></td>
					<td><?php echo $value['mekanisme_name'];?></td>
					<td><?php echo $value['npwp_code'];?></td>
					<td><?php echo $value['pemenang'];?></td>
					<td><?php echo "Rp. ".number_format($value['nilai']);?></td>
					<td><?php echo $value['status'];?></td>
					<td><?php echo $value['budget_year'];?></td>
					
					<?php if ($admin['id_role'] == 8) { ?>
					<td><?php echo $value['last_edited'];?></td>					
					<?php }?>

					<td class="actionBlock">
						<a href="<?php echo site_url('pengadaan/view/'.$value['id'])?>" class=""><i class="fa fa-search"></i>&nbsp;Lihat</a>
						<?php if($this->session->userdata('admin')['id_role']==3){?>
						<a href="<?php echo site_url('pengadaan/hapus_pengadaan/'.$value['id'])?>" class="editBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
						<?php }?>
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
	<div class="pageNumber">
		<?php echo $pagination ?>
	</div>
</div>
