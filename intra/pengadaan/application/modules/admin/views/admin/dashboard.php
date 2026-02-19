<div class="sideArea">
    <div class="loginInfo">
        <!-- <h2>Selamat Datang, <?php echo $this->session->userdata('admin')['role_name']; ?></h2> -->
        <ul class="navMenu">
            <li><a href="<?php echo site_url() ?>"><i class="fa fa-home"></i>&nbsp;Beranda</a></li>

            <?php if ($id_role == 1 || $id_role == 3 || $id_role == 8 || $id_role == 10) { ?>
                <li><a href="#">Penyedia Barang / Jasa</a><i class="fa fa-chevron-down li-icon"></i>
                    <ul class="navMenuDD">
                        <?php if ($id_role != 8 && $id_role != 3 && $id_role != 10) { ?>
                            <li><a href="<?php echo site_url('vendor/tambah') ?>">Tambah Penyedia B/J</a>
                            <?php } ?>
                            <?php if ($id_role != 10) { ?>
                            <li><a href="<?php echo site_url('admin/admin_vendor/daftar') ?>">Daftar Penyedia B/J</a></li>
                        <?php } ?>
                        <li><a href="<?php echo site_url('admin/admin_dpt/') ?>">DPT</a></li>
                        <?php if ($id_role == 8) { ?>
                            <li><a href="<?php echo site_url('admin/admin_vendor/waiting_list/1') ?>">Daftar Tunggu (Aktif)</a></li>
                            <li><a href="<?php echo site_url('admin/admin_vendor/waiting_list/0') ?>">Daftar Tunggu (Tidak Aktif)</a></li>
                        <?php } else { ?>
                            <li><a href="<?php echo site_url('admin/admin_vendor/waiting_list/0') ?>">Daftar Tunggu</a></li>
                        <?php } ?>
                        <?php if ($id_role != 10) { ?>
                            <li><a href="<?php echo site_url('blacklist/whitelist') ?>">Daftar Putih</a></li>
                            <li><a href="<?php echo site_url('blacklist/index/1') ?>">Daftar Merah</a></li>
                            <li><a href="<?php echo site_url('blacklist/index/2') ?>">Daftar Hitam</a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

            <?php if ($id_role == 3 || $id_role == 8 || $id_role == 10) { ?>
                <li><a href="<?php echo site_url('pengadaan') ?>">Daftar Pengadaan B/J</a></li>
            <?php } ?>

            <?php if ($id_role != 10) { ?>
                <li><a href="<?php echo site_url('kontrak') ?>">Kontrak</a></li>
            <?php } ?>

            <?php if ($id_role != 10) { ?>
                <li><a href="<?php echo site_url('assessment/select_year') ?>">Penilaian Kinerja</a></li>
            <?php } ?>

            <?php if ($id_role == 9) { ?>
                <li><a href="<?php echo site_url('pengadaan') ?>">Daftar Pengadaan B/J</a></li>
            <?php } ?>

            <?php if ($id_role != 2 && $id_role != 6 && $id_role != 10) { ?>
                <li>
                    <a href="<?php echo site_url('admin/custom_report') ?>">Laporan Pengadaan B/J</a>
                    <!-- <a href="#">Reporting</a>  -->
                    <!-- <i class="fa fa-chevron-down li-icon"></i>
            <ul class="navMenuDD">
                <li><a href="<?php echo site_url('admin/custom_report') ?>">Custom Report</a></li>
            </ul> -->
                </li>
            <?php } ?>

            <?php //if ($id_role == 3 || $id_role == 8) { ?>
                <li><a href="<?php echo site_url('k3/get_vendor_group') ?>">Penilaian CSMS</a></li>
            <?php //} ?>
			
			<?php if ($id_role == 3) { ?>
                <li><a href="<?php echo site_url('feedback') ?>">Umpan Balik Penyedia B/J</a></li>
            <?php } ?>

            <?php if ($id_role == 1) { ?>
                <li><a href="#">Master</a> <i class="fa fa-chevron-down li-icon"></i>
                    <ul class="navMenuDD">
                        <!-- <li><a href="<?php echo site_url('admin/admin_user') ?>">User</a></li> -->
                        <li><a href="<?php echo site_url('admin/admin_badan_hukum') ?>">Badan Hukum</a></li>
                        <li><a href="<?php echo site_url('admin/admin_bidang') ?>">Bidang</a></li>
                        <li><a href="<?php echo site_url('admin/admin_sub_bidang') ?>">Sub Bidang</a></li>
                        <li><a href="<?php echo site_url('admin/admin_kurs') ?>">Kurs</a></li>
                        <li><a href="<?php echo site_url('admin/admin_k3') ?>">K3</a></li>
                        <li><a href="<?php echo site_url('admin/admin_k3_passing_grade') ?>">K3 Passing Grade</a></li>
                        <li><a href="<?php echo site_url('admin/admin_assessment') ?>">Penilaian Kinerja</a></li>
                        <li><a href="<?php echo site_url('admin/admin_evaluasi') ?>">Evaluasi</a></li>
                        <li><a href="<?php echo site_url('admin/admin_pernyataan') ?>">Surat Pernyataan</a></li>
                    </ul>
                </li>
            <?php } ?>


            <?php if ($id_role != 8) { ?>
                <!-- <li><a href="<?php echo site_url('admin/admin_dpt') ?>">DPT</a></li> -->
            <?php } ?>

            <?php if ($id_role == 1 || $id_role == 3 || $id_role == 8) { ?>
                <!-- <li><a href="#">Daftar Merah/Hitam/Putih</a><i class="fa fa-chevron-down li-icon"></i>
            <ul class="navMenuDD">
                <li><a href="<?php echo site_url('blacklist/whitelist') ?>">Daftar Putih</a></li>
                <li><a href="<?php echo site_url('blacklist/index/1') ?>">Daftar Merah</a></li>
                <li><a href="<?php echo site_url('blacklist/index/2') ?>">Daftar Hitam</a></li>
            </ul>
        </li> -->
            <?php } ?>

            <?php if ($id_role != 2) { ?>
                <?php if ($id_role != 10) { ?>
                    <li><a href="#">Katalog</a><i class="fa fa-chevron-down li-icon"></i>
                        <ul class="navMenuDD">

                            <li><a href="<?php echo site_url('katalog/index/barang') ?>">Barang</a>
                            <li><a href="<?php echo site_url('katalog/index/jasa') ?>">Jasa</a>

                        </ul>
                    </li>
                <?php } ?>
                <li><a href="<?php echo site_url('admin/app') ?>">Ke aplikasi Perencanaan Pengadaan</a><i class="fa fa-sign-out li-icon"></i></li>
            <?php } ?>
            <!-- <div class="search-bar">
            <input type="text" name="nama_pengadaan" class="input" placeholder="Search..">
            <span class="icon">
                <i class="fas fa-search"></i>
            </span>
        </div> -->
            <li class="last" style="display: none"><a href="<?php echo site_url('admin/logout') ?>"><i class="fa fa-power-off"></i>&nbsp;Keluar</a></li>
        </ul>
        <!--<p class="sbuText">Welcome <?php echo $this->session->userdata('admin')['role_name']; ?>, Sumber Daya Manusia dan Umum</p>-->
        <?php if ($this->uri->segment(1) == "approval") { ?>
            <div class="warnVer msgBlock" style="color: #fff;">
                <ul><?php //print_r($approval_data);
                    ?>
                    <h4><?php echo count($approval_data[0]) ?> data belum terverifikasi</h4>
                    <?php
                    $link = array(
                        'Akta'                             => 'akta',
                        'Akta Pendirian'                 => 'akta',
                        'Akta Perubahan'                 => 'akta',
                        'SITU/Domisili'                 => 'situ',
                        'TDP'                            => 'tdp',
                        'Kepemilikan Saham'                => 'pemilik',
                        'Izin Usaha'                    => 'badan_usaha',
                        'Pabrikan/Keagenan/Distributor'    => 'agen',
                        'CSMS'                            => 'k3',
                        'Data Administrasi Vendor'        => 'administrasi',
                        'Pengurus'                        => 'pengurus',
                        'Pengalaman'                    => 'pengalaman'
                    );
                    ?>
                    <?php foreach ($approval_data[0] as $key => $value) { ?>
                        <?php $perubahan = ($value == 'Akta Perubahan') ? $perubahan = "perubahan" : $perubahan = ""; ?>
                        <li>
                            <a style="color: #fff;" href="<?php echo site_url('approval/' . $link[$value] . '/' . $this->uri->segment(3) . '/' . $perubahan); ?>">
                                <?php echo $value; ?>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($this->session->userdata('admin')['role_name'] != 2) { ?>
                        <!-- <li><a href="<?php echo site_url('Katalog') ?>"><i class="fa fa-file-text-o"></i>&nbsp;Katalog</a></li> -->
                    <?php } ?>
                    <!-- <li><a href="<?php echo site_url('Katalog') ?>"><i class="fa fa-file-text-o"></i>&nbsp; Katalog</a></li> -->
                </ul>
            </div>
        <?php } ?>
    </div>
</div>
<div class="mainArea">
    <?php if (isset($content)) {
        echo $content;
    }
    ?>
</div>