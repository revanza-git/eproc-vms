<?php echo $this->session->flashdata('msgSuccesss')?>
<h2 class="formHeader">Daftar Pengadaan</h2>
<div class="tableWrapper" style="margin-bottom: 20px">

	
	<div class="tableHeader">
		<a href="<?php echo site_url('pengadaan/export_excel');?>" class="btnBlue exportBtn" style="margin-top: -8px; margin-right: 10px"><i class="fa fa-download"></i> Export</a>
	</div>
	
	<div class="filterBtnWp">
		<div class="btns">
			<?php if($this->session->userdata('admin')['id_role']==3 || $this->session->userdata('admin')['id_role']==10){?>
				<a href="<?php echo site_url('pengadaan/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
			<?php } ?>
			<?php //if ($admin['id_role'] != 3) { ?>
				<!-- <a href="<?php echo site_url('pengadaan/import_pengadaan');?>" class="btnBlue exportBtn" style="margin-top: -8px; margin-right: 10px"><i class="fa fa-download"></i> Tambah Pengadaan Dari Perencanaan</a> -->
			<?php //} ?>
		</div>
		
		<button class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<div class="tableHeader">
			<!-- <a href="<?php echo site_url('pengadaan/export_excel');?>" class="btnBlue exportBtn" style="margin-top: -8px; margin-right: 10px"><i class="fa fa-download"></i> Export</a>
			<a href="<?php echo site_url('pengadaan/tambah');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a> -->
		
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Nama Pengadaan<i class="fa fa-sort-<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: 120px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Jenis Pengadaan<i class="fa fa-sort-<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: auto"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">NPWP<i class="fa fa-sort-<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: 150px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo (isset($sort['pemenang']) && $sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>&by=pemenang">Nama Pemenang Sesuai Kontrak<i class="fa fa-sort-<?php echo (isset($sort['pemenang']) && $sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: auto"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Nilai<i class="fa fa-sort-<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: 50px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Status<i class="fa fa-sort-<?php echo (isset($sort['ms_procurement.name']) && $sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td style="width: 50px"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo (isset($sort['ms_procurement.budget_year']) && $sort['ms_procurement.budget_year'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.budget_year">Tahun<i class="fa fa-sort-<?php echo (isset($sort['ms_procurement.budget_year']) && $sort['ms_procurement.budget_year'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<?php if ($admin['id_role'] == 8) { ?>	
				<td>Last Edited</td>
				<?php }?>
				<td class="actionPanel" style="width: 130px">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($pengadaan_list)){
			foreach($pengadaan_list as $row => $value){
							if (!isset($value['symbol']) || $value['symbol'] == null || $value['symbol'] == 'IDR') {
				$symbol = 'Rp. ';
			} else {
				$symbol = $value['symbol'].' ';
			}
		?>
				<tr>
					<td><?php echo $value['name'];?></td>
					<td><?php echo $value['mekanisme_name'];?></td>
					<td><?php echo $value['npwp_code'];?></td>
					<td><?php echo $value['pemenang'];?></td>
					<?php if (isset($value['contract_price']) && $value['contract_price'] != '') { ?>
					<td><?php echo $symbol.number_format($value['contract_price']);?></td>	
					<?php } else {?>
					<td><?php echo $symbol.number_format(isset($value['nilai']) ? $value['nilai'] : 0).' (Nilai HPS)';?></td>
					<?php } ?>
					<td><?php echo isset($value['status']) ? $value['status'] : '-';?></td>
					<td><?php echo $value['budget_year'];?></td>
					
					<?php if ($admin['id_role'] == 8) { ?>
					<td><?php echo $value['last_edited'];?></td>					
					<?php }?>

					<td class="actionBlock">
						<a href="<?php echo site_url('pengadaan/view/'.$value['id'])?>" class="editBtn lihatData"><i class="fa fa-search"></i>&nbsp;Lihat data</a>
						<?php if($this->session->userdata('admin')['id_role']==3 || $this->session->userdata('admin')['id_role']==10){?>
						<a href="<?php echo site_url('pengadaan/hapus_pengadaan/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
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
	<div class="filterWrapperOverlay"></div>
	<div class="filterWrapper">
		<div class="filterWrapperInner">
			<form method="POST">
				<?php echo $filter_list;?>
			</form>
		</div>
	</div>
</div>