 <div class="logo">
    <a href="<?php echo site_url()?>"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>"></a>
</div>
<div class="backButton">
    <ul class="navMenu">
        <li><a href="<?php echo site_url()?>"><i class="fa fa-home"></i>&nbsp;Beranda</a></li>
        <?php if($id_role==3||$id_role==9){ ?>
        <li><a href="<?php echo site_url('pengadaan')?>">Pengadaan</a></li>
        <?php }?>
        <?php if($id_role==8){ ?>
        <li><a href="#">Pengadaan</a>
            <ul class="navMenuDD">
                <li><a href="<?php echo site_url('pengadaan')?>">Pengadaan</a></li>
                <li><a href="<?php echo site_url('admin/custom_report_')?>">Progress Pengadaan</a></li>
            </ul>
        </li>
        <?php }?>
        <?php if($id_role==3){ ?>
        <li><a href="<?php echo site_url('k3/get_vendor_group')?>">Penilaian CSMS</a></li>
        <?php }?>
        <?php if($id_role==1){ ?>
        <li><a href="#">Master</a>
            <ul class="navMenuDD">
                <li><a href="<?php echo site_url('admin/admin_user')?>">User</a></li>
                <li><a href="<?php echo site_url('admin/admin_badan_hukum')?>">Badan Hukum</a></li>
                <li><a href="<?php echo site_url('admin/admin_bidang')?>">Bidang</a></li>
                <li><a href="<?php echo site_url('admin/admin_sub_bidang')?>">Sub Bidang</a></li>
                <li><a href="<?php echo site_url('admin/admin_kurs')?>">Kurs</a></li>
                <li><a href="<?php echo site_url('admin/admin_k3')?>">K3</a></li>
                <li><a href="<?php echo site_url('admin/admin_k3_passing_grade')?>">K3 Passing Grade</a></li>
                <li><a href="<?php echo site_url('admin/admin_assessment')?>">Assessment</a></li>
                <li><a href="<?php echo site_url('admin/admin_evaluasi')?>">Evaluasi</a></li>
                <li><a href="<?php echo site_url('admin/admin_pernyataan')?>">Surat Pernyataan</a></li>
            </ul>
        </li>
        
        <?php } ?>
        <?php if($id_role!=2&&$id_role!=6&&$id_role!=8){?>
        <li><a href="#">Reporting</a>
            <ul class="navMenuDD">
                <li><a href="<?php echo site_url('admin/custom_report')?>">Custom Report</a></li>
            </ul>
        </li>
        <?php } ?>
        <?php if($id_role==2||$id_role==3||$id_role==9){ ?>
        <li><a href="<?php echo site_url('assessment')?>">Assessment</a></li>
        <?php } ?>


        <?php if($id_role!=8){ ?>
        <li><a href="<?php echo site_url('admin/admin_dpt')?>">DPT</a></li>
        <?php } ?>

        <?php if($id_role==1||$id_role==3||$id_role==8){ ?>
        <li><a href="#">Daftar Merah/Hitam/Putih</a>
            <ul class="navMenuDD">
                <li><a href="<?php echo site_url('blacklist/whitelist')?>">Daftar Putih</a></li>
                <li><a href="<?php echo site_url('blacklist/index/1')?>">Daftar Merah</a></li>
                <li><a href="<?php echo site_url('blacklist/index/2')?>">Daftar Hitam</a></li>
            </ul>
        </li>
        <?php } ?>
        <?php if($id_role==1 || $id_role==3){ ?>
        <li><a href="#">Penyedia Barang &amp; Jasa</a>
            <ul class="navMenuDD">
                <?php if($id_role!=8 && $id_role!=3){ ?>
                <li><a href="<?php echo site_url('vendor/tambah')?>">Tambah Penyedia Barang &amp; Jasa</a>
                <?php }?>
                <li><a href="<?php echo site_url('admin/admin_vendor/daftar')?>">Daftar Penyedia Barang &amp; Jasa</a>
                <li><a href="<?php echo site_url('admin/admin_vendor/waiting_list/0')?>">Daftar Tunggu</a></li>
            </ul>
        </li>
        <?php }?>
        <?php if($id_role==8){ ?>
        <li><a href="#">Penyedia Barang &amp; Jasa</a>
            <ul class="navMenuDD">
                <li><a href="<?php echo site_url('admin/admin_dpt/')?>">Daftar DPT</a></li>
                <li><a href="<?php echo site_url('admin/admin_vendor/waiting_list/1')?>">Daftar Tunggu (Aktif)</a></li>
                <li><a href="<?php echo site_url('admin/admin_vendor/waiting_list/0')?>">Daftar Tunggu (Tidak Aktif)</a></li>
            </ul>
        </li>
        <?php }?>
        <?php if($id_role!=2){ ?>
        <li><a href="#">Katalog</a>
            <ul class="navMenuDD">
                
                <li><a href="<?php echo site_url('katalog/index/barang')?>">Barang</a>
                <li><a href="<?php echo site_url('katalog/index/jasa')?>">Jasa</a>

            </ul>
        </li>
        <?php }?> 
		<li class="last"><a href="<?php echo site_url('admin/app')?>"><i class="fa fa-sign-in"></i>&nbsp;Ke aplikasi Perencanaan</a></li>
        <li class="last"><a href="<?php echo site_url('admin/logout')?>"><i class="fa fa-power-off"></i>&nbsp;Keluar</a></li>
    </ul>
    <!--
    <ul class="ddMenu clearfix">
        <li class="fl"><a href="<?php echo site_url()?>">Welcome, <?php echo $name?></a>
        </li>
        <li class="fl"><a href="#"><i class="fa fa-cog"></i>Akun</a></li>
        <li><a href="<?php echo site_url('main/logout')?>"><i class="fa fa-power-off"></i>&nbsp;Keluar</a></li>
    </ul>
    -->
</div>