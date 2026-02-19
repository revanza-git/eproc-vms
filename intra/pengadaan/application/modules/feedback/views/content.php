<?php echo $this->session->flashdata('msgSuccess') ?>
<h2 class="formHeader">Umpan Balik Penyedia B/J</h2>
<div class="tableWrapper" style="margin-bottom: 20px">

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
				<td><a href="?<?php echo $this->utility->generateLink('sort', 'desc') ?>&sort=<?php echo ($sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>&by=pemenang">Nama Penyedia B/J<i class="fa fa-sort-<?php echo ($sort['pemenang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td class="actionPanel" style="width: 130px">Action</td>
			</tr>
		</thead>
		<tbody>
			<?php
			if (count($feedback_list)) {
				foreach ($feedback_list as $row => $value) {
			?>
					<tr>
						<td><?php echo $value['name']; ?></td>
						<td><?php echo $value['pemenang']; ?></td>
						<td class="actionBlock">
							<a href="<?= site_url('feedback/view/' . $value['id']) ?>" class="editBtn"><i class="fa fa-search"></i> Lihat Umpan Balik</a>
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