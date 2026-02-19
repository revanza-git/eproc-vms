<?php echo $this->session->flashdata('msgSuccess') ?>
<h2 class="formHeader">Penilaian Kinerja</h2>
<div class="tableWrapper" style="margin-bottom: 20px">

	<div class="filterBtnWp">
		<!-- <a href="<?php echo site_url('assessment/export_excel'); ?>" class="btnBlue exportBtn"><i class="fa fa-download"></i> Export</a> -->
		<!-- <button class="editBtn lihatData filterBtn">Filter</button> -->
	</div>
	<div class="tableHeader">
		<!-- <a href="<?php echo site_url('assessment/export_excel'); ?>" class="btnBlue exportBtn"><i class="fa fa-download"></i> Export</a> -->
		<!-- <form method="POST">
			
			<a href="<?php echo site_url('pengadaan/tambah'); ?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		</form> -->
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td style="width: 280px"><a href="?<?php echo $this->utility->generateLink('sort', 'desc') ?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Nama Pengadaan<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort', 'desc') ?>&sort=<?php echo ($sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>&by=pemenang">Nama Pemenang Sesuai Kontrak<i class="fa fa-sort-<?php echo ($sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort', 'desc') ?>&sort=<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>&by=point">Skor Assessment<i class="fa fa-sort-<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort', 'desc') ?>&sort=<?php echo ($sort['tr_assessment.category'] == 'asc') ? 'desc' : 'asc'; ?>&by=tr_assessment.category">Kategori<i class="fa fa-sort-<?php echo ($sort['tr_assessment.category'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td class="actionPanel" style="width: 130px">Action</td>
			</tr>
		</thead>
		<tbody>
			<?php
			if (count($pengadaan_list)) {
				foreach ($pengadaan_list as $row => $value) {
			?>
					<tr>
						<td><?php echo $value['name']; ?></td>
						<td><?php echo $value['pemenang']; ?></td>
						<td><?php echo $value['point']; ?></td>
						<td><?php echo $value['category']; ?></td>
						<td class="actionBlock">
							<?php if ($value['remark'] == null) { ?>
								<a href="<?php echo site_url('evaluasi/form_feedback/' . $value['id'] . '/' . $value['id_vendor']) ?>" class="editBtn"><i class="fa fa-edit"></i>&nbsp;Kirim Umpan Balik</a>
							<?php } ?>
						</td>
					</tr>
				<?php
				}
			} else { ?>
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
<div class="filterWrapperOverlay"></div>
<div class="filterWrapper">
	<div class="filterWrapperInner">
		<form method="POST">
			<?php echo $filter_list; ?>
		</form>
	</div>
</div>