<?php echo $this->session->flashdata('msgSuccess') ?>
<h2 class="formHeader">Penilaian Kinerja</h2>
<div class="tableWrapper" style="margin-bottom: 20px">

    <div class="filterBtnWp">
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
                <td style="width: 280px">Tahun</td>
                <td class="actionPanel" style="width: 130px">Action</td>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($list_year)) {
                foreach ($list_year as $row => $value) {
            ?>
                    <tr>
                        <td><?php echo date('Y', strtotime($value['entry_stamp'])); ?></td>

                        <?php //if (($admin['id_user'] !== "55" || $admin['id_user'] == "56")) {
                        ?>
                        <td class="actionBlock">
                            <a href="<?php echo site_url('assessment/index/' . date('Y', strtotime($value['entry_stamp']))) ?>" class="editBtn lihatData"><i class="fa fa-search"></i>&nbsp;Lihat data</a>
                        </td>
                        <?php //}
                        ?>
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
</div>