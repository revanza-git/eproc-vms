<?php $admin = $this->session->userdata('admin'); ?>
<div class="mg-lg-12">
	<div class="container">

      <div class="wrapper">
        <div class="col col-6">  
          <div class="panel">
            <h4>Data Rekap Perencanaan Tahun 
              <select style="background: none; border: none; border-bottom: 2px #3273dc solid; color: #3273dc; cursor: pointer;" id="yearGraph">
                <?php for ($i=2017; $i <= 2030; $i++) { 
                  if ($i == date('Y')) {
                    $sel = 'selected';
                  } else {
                    $sel = '';
                  }
                ?>

                  <option value="<?php echo $i; ?>" <?php echo $sel; ?>><?php echo $i; ?></option>
                <?php } ?>
              </select>
            </h4>
            <div id="graph_" style="height: auto; width: auto;">
            </div>
          </div>
        </div>

        <div class="col col-6">  
          <div class="panel">
            <div id="graph_jenis" style="height: auto; width: auto;">
            </div>
          </div>
        </div>
        
        <?php if ($admin['id_division'] != 1 && $admin['id_division'] != 5) { ?>
        <div class="col col-6">
    
          <div class="panel" style="height: 550px">

            <div class="scrollbar" id="custom-scroll" style="height: 538px">
                      
              <div class="container-title">
                <h3>Status FPPBJ</h3>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Sudah disetujui
                  <?php $width = ($fppbj_selesai->num_rows() / $total_fppbj_semua->num_rows()) * 100; ?>
                  <span><?php echo $fppbj_selesai->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-success" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui
                  <?php $width = ($fppbj_pending->num_rows() / $total_fppbj_semua->num_rows()) * 100;?>
                  <span><?php echo $fppbj_pending->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Tidak Disetujui
                  <?php $width = ($fppbj_reject->num_rows() / $total_fppbj_semua->num_rows()) * 100;?>
                  <span><?php echo $fppbj_reject->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-danger" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="container-title">
                <h3>Tinjauan</h3>
              </div>

                <div class="is-block">
                <button class="accordion-header">Disetujui <span class="badge is-success"><?php echo $fppbj_selesai->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fppbj_selesai->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>

                <!-- <?php if ($admin['id_role'] != 5) { ?>
                <button class="accordion-header">Menunggu persetujuan <span class="badge is-warning"><?php echo $pending_kadiv->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($pending_kadiv->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
                <?php } ?> -->

                <button class="accordion-header">Belum disetujui<span class="badge is-warning"><?php echo $fppbj_pending->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fppbj_pending->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>

                <button class="accordion-header">Tidak disetujui <span class="badge is-danger"><?php echo $fppbj_reject->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fppbj_reject->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
                
              </div>

            </div>

          </div>

          <div class="panel" style="height: 550px">

            <div class="scrollbar" id="custom-scroll" style="height: 538px">
                      
              <div class="container-title">
                <h3>Status FKPBJ</h3>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Sudah disetujui
                  <?php $width = ($fkpbj_success->num_rows() / $total_fkpbj->num_rows()) * 100; ?>
                  <span><?php echo $fkpbj_success->num_rows() ?>/<?php echo $total_fkpbj->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-success" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui
                  <?php $width = ($fkpbj_pending->num_rows() / $total_fkpbj->num_rows()) * 100;?>
                  <span><?php echo $fkpbj_pending->num_rows() ?>/<?php echo $total_fkpbj->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Tidak Disetujui
                  <?php $width = ($fkpbj_reject->num_rows() / $total_fkpbj->num_rows()) * 100;?>
                  <span><?php echo $fkpbj_reject->num_rows() ?>/<?php echo $total_fkpbj->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-danger" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="container-title">
                <h3>Tinjauan</h3>
              </div>

                <div class="is-block">
                <button class="accordion-header">Disetujui <span class="badge is-success"><?php echo $fkpbj_success->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fkpbj_success->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id_fppbj).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>

                <button class="accordion-header">Belum disetujui<span class="badge is-warning"><?php echo $fkpbj_pending->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fkpbj_pending->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id_fppbj).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>

                <button class="accordion-header">Tidak disetujui <span class="badge is-danger"><?php echo $fkpbj_reject->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fkpbj_reject->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id_fppbj).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
                
              </div>

            </div>

          </div>

          <div class="panel" style="height: 550px">

            <div class="scrollbar" id="custom-scroll" style="height: 538px">
                      
              <div class="container-title">
                <h3>Status FP3</h3>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Sudah disetujui
                  <?php $width = ($fp3_success->num_rows() / $total_fp3->num_rows()) * 100; ?>
                  <span><?php echo $fp3_success->num_rows() ?>/<?php echo $total_fp3->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-success" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui
                  <?php $width = ($fp3_pending->num_rows() / $total_fp3->num_rows()) * 100;?>
                  <span><?php echo $fp3_pending->num_rows() ?>/<?php echo $total_fp3->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Tidak Disetujui
                  <?php $width = ($fp3_reject->num_rows() / $total_fp3->num_rows()) * 100;?>
                  <span><?php echo $fp3_reject->num_rows() ?>/<?php echo $total_fp3->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-danger" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="container-title">
                <h3>Tinjauan</h3>
              </div>

                <div class="is-block">
                <button class="accordion-header">Disetujui <span class="badge is-success"><?php echo $fp3_success->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fp3_success->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id_fppbj).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>

                <button class="accordion-header">Belum disetujui<span class="badge is-warning"><?php echo $fp3_pending->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fp3_pending->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id_fppbj).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>

                <button class="accordion-header">Tidak disetujui <span class="badge is-danger"><?php echo $fp3_reject->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                <div class="accordion-panel">
                  <?php $no = 1;foreach ($fp3_reject->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id_fppbj).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
                
              </div>

            </div>

          </div>

        </div>
        <?php } ?>
        <?php if($admin['id_division'] == 1 || $admin['id_division'] == 5) { ?>
        <div class="col col-6">
    
          <div class="panel" style="height: 550px">

            <div class="scrollbar" id="custom-scroll" style="height: 538px">
              

            <?php if($admin['id_division'] == 1 || $admin['id_division'] == 5){ ?>

              <div class="container-title">
                <h3>Data FPPBJ</h3>
              </div>

              <div class="summary">
                <div class="summary-title">
                  FPPBJ Selesai
                  <?php $width = ($fppbj_selesai->num_rows() / $total_fppbj_semua->num_rows()) * 100; ?>
                  <span><?php echo $fppbj_selesai->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-success" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
                <button class="accordion-header">Disetujui <span class="badge is-success"><?php echo $fppbj_selesai->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no = 1; foreach ($fppbj_selesai->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui User
                  <?php $width = ($pending_kadiv->num_rows() / $total_fppbj_semua->num_rows()) * 100;?>
                  <span><?php echo $pending_kadiv->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
                <button class="accordion-header">Belum disetujui User<span class="badge is-warning"><?php echo $pending_kadiv->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no = 1; foreach ($pending_kadiv->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
              </div>
              
              <div class="summary">
                <div class="summary-title">
                  Belum disetujui HSSE
                  <?php $width = ($pending_admin_hsse->num_rows() / $total_fppbj_semua->num_rows()) * 100;?>
                  <span><?php echo $pending_admin_hsse->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
                <button class="accordion-header">Belum disetujui HSSE<span class="badge is-warning"><?php echo $pending_admin_hsse->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no=1;foreach ($pending_admin_hsse->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui Admin Pengendalian
                  <?php $width = ($pending_admin_pengendalian->num_rows() / $total_fppbj_semua->num_rows()) * 100;?>
                  <span><?php echo $pending_admin_pengendalian->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
                <button class="accordion-header">Belum disetujui Admin Pengendalian<span class="badge is-warning"><?php echo $pending_admin_pengendalian->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no=1; foreach ($pending_admin_pengendalian->result() as $key) { ?>
                   <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui Ka.Dept Procurement
                  <?php $width = ($pending_kadept_proc->num_rows() / $total_fppbj_semua->num_rows()) * 100;?>
                  <span><?php echo $pending_kadept_proc->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
                <button class="accordion-header">Belum disetujui Ka.Dept Procurement
                <span class="badge is-warning"><?php echo $pending_kadept_proc->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no=1; foreach ($pending_kadept_proc->result() as $key) { ?>
                    <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui Pejabat Pengadaan
                  <?php $width = ($total_pending_dir->num_rows() / $total_fppbj_semua->num_rows()) * 100;?>
                  <span><?php echo $total_pending_dir->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
                <button class="accordion-header">Belum disetujui Direktur
                <span class="badge is-warning"><?php echo $total_pending_dir->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no=1; foreach ($total_pending_dir->result() as $key) { ?>
                   <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Tidak Disetujui
                  <?php $width = ($fppbj_reject->num_rows() / $total_fppbj_semua->num_rows()) * 100;?>
                  <span><?php echo $fppbj_reject->num_rows() ?>/<?php echo $total_fppbj_semua->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-danger" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
                <button class="accordion-header">Tidak disetujui <span class="badge is-danger"><?php echo $fppbj_reject->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no=1;foreach ($fppbj_reject->result() as $key) { ?>
                   <p><?= $no.'. <a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>
              </div>
            <?php } ?>
              
              <?php if($admin['id_division'] == 1 && $admin['id_role'] ==7 &&  $admin['id_role'] ==8 && $admin['id_role'] ==9) { ?>
              <div class="container-title">
                <h3>Data FPPBJ Otorisasi</h3>
              </div>
              <?php if($admin['id_division'] == 1 && $admin['id_role'] == 9) {?>
              <div class="summary">
                <div class="summary-title">
                  Sudah disetujui Direktur Utama
                  <?php $width = ($done_dirut->num_rows() / $total_fppbj_dirut->num_rows()) * 100;?>
                  <span><?php echo $done_dirut->num_rows() ?>/<?php echo $total_fppbj_dirut->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-success" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui Direktur Utama
                  <?php $width = ($pending_dirut->num_rows() / $total_fppbj_dirut->num_rows()) * 100;?>
                  <span><?php echo $pending_dirut->num_rows() ?>/<?php echo $total_fppbj_dirut->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Ditolak Direktur Utama
                  <?php $width = ($reject_dirut->num_rows() / $total_fppbj_dirut->num_rows()) * 100;?>
                  <span><?php echo $reject_dirut->num_rows() ?>/<?php echo $total_fppbj_dirut->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-danger" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="container-title">
                <h3>Tinjauan</h3>
              </div>

              <div class="is-block">

                <button class="accordion-header">Disetujui <span class="badge is-success"><?php echo $done_dirut->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no = 1; foreach ($done_dirut->result() as $key) { ?>
                    <p><?= $no.'<a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php $no++;} ?>
                </div>

                <button class="accordion-header">Belum disetujui<span class="badge is-warning"><?php echo $pending_dirut->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php foreach ($pending_dirut->result() as $key) { ?>
                    <p><?= '<a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php } ?>
                </div>

                <button class="accordion-header">Tidak disetujui <span class="badge is-danger"><?php echo $reject_dirut->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php foreach ($reject_dirut->result() as $key) { ?>
                    <p><?= $key->nama_pengadaan ?></p>
                  <?php } ?>
                </div>
                
              </div>
              <?php } ?>
              
              <?php if($admin['id_division'] == 1 && $admin['id_role'] == 8) {?>
              <div class="summary">
                <div class="summary-title">
                  Sudah disetujui Direktur Keuangan
                  <?php $width = ($done_dirke->num_rows() / $total_fppbj_dirke->num_rows()) * 100;?>
                  <span><?php echo $done_dirke->num_rows() ?>/<?php echo $total_fppbj_dirke->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-success" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui Direktur Keuangan
                  <?php $width = ($pending_dirke->num_rows() / $total_fppbj_dirke->num_rows()) * 100;?>
                  <span><?php echo $pending_dirke->num_rows() ?>/<?php echo $total_fppbj_dirke->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Ditolak Direktur Keuangan
                  <?php $width = ($reject_dirke->num_rows() / $total_fppbj_dirke->num_rows()) * 100;?>
                  <span><?php echo $reject_dirke->num_rows() ?>/<?php echo $total_fppbj_dirke->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-danger" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="container-title">
                <h3>Tinjauan</h3>
              </div>

              <div class="is-block">

                <button class="accordion-header">Disetujui <span class="badge is-success"><?php echo $done_dirke->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no = 1; foreach ($done_dirke->result() as $key) { ?>
                    <p><?= '<a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php } ?>
                </div>

                <button class="accordion-header">Belum disetujui<span class="badge is-warning"><?php echo $pending_dirke->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php foreach ($pending_dirke->result() as $key) { ?>
                    <p><?= '<a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php } ?>
                </div>

                <button class="accordion-header">Tidak disetujui <span class="badge is-danger"><?php echo $reject_dirke->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php foreach ($reject_dirke->result() as $key) { ?>
                    <p><?= $key->nama_pengadaan ?></p>
                  <?php } ?>
                </div>
                
              </div>
              <?php } ?>

              <?php if($admin['id_division'] == 1 && $admin['id_role'] == 7) {?>
              <div class="summary">
                <div class="summary-title">
                  Sudah disetujui Direktur SDM
                  <?php $width = ($done_dirsdm->num_rows() / $total_fppbj_dirsdm->num_rows()) * 100;?>
                  <span><?php echo $done_dirsdm->num_rows() ?>/<?php echo $total_fppbj_dirsdm->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-success" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Belum disetujui Direktur SDM
                  <?php $width = ($pending_dirsdm->num_rows() / $total_fppbj_dirsdm->num_rows()) * 100;?>
                  <span><?php echo $pending_dirsdm->num_rows() ?>/<?php echo $total_fppbj_dirsdm->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-warning" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="summary">
                <div class="summary-title">
                  Ditolak Direktur SDM
                  <?php $width = ($reject_dirsdm->num_rows() / $total_fppbj_dirsdm->num_rows()) * 100;?>
                  <span><?php echo $reject_dirsdm->num_rows() ?>/<?php echo $total_fppbj_dirsdm->num_rows() ?></span>
                </div>
                <div class="summary-bars">
                  <span class="bar-top is-danger" style="width:<?php echo $width ?>%"></span>
                  <span class="bar-bottom"></span>
                </div>
              </div>

              <div class="container-title">
                <h3>Tinjauan</h3>
              </div>

              <div class="is-block">

                <button class="accordion-header">Disetujui <span class="badge is-success"><?php echo $done_dirsdm->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php $no = 1; foreach ($done_dirsdm->result() as $key) { ?>
                    <p><?= '<a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php } ?>
                </div>

                <button class="accordion-header">Belum disetujui<span class="badge is-warning"><?php echo $pending_dirsdm->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php foreach ($pending_dirsdm->result() as $key) { ?>
                    <p><?= '<a href="'.site_url('pemaketan/division/'.$key->id_division.'/'.$key->id).'">'.$key->nama_pengadaan.'</a>' ?></p>
                  <?php } ?>
                </div>

                <button class="accordion-header">Tidak disetujui <span class="badge is-danger"><?php echo $reject_dirsdm->num_rows() ?></span><span class="icon"><i class="fas fa-angle-down"></i></span></button>

                <div class="accordion-panel">
                  <?php foreach ($reject_dirsdm->result() as $key) { ?>
                    <p><?= $key->nama_pengadaan ?></p>
                  <?php } ?>
                </div>
                
              </div>
              <?php } ?>
            <?php } ?>
            </div>

          </div>

        </div>
        <?php } ?>
        <div class="col col-6">
          <div class="panel">
            <div class="container-title">
              <h3>Notifikasi</h3>
              <div class="badge is-primary is-noticable">
              {total_notif}
              </div> <!-- SHOW TOTAL NOTIFICATION -->
            </div>
            <div class="scrollbar" id="custom-scroll" style="height: 470px; overflow-x: auto;">
              <!-- LINE NOTIFICATION -->
              <?php foreach ($notification->result() as $key) { ?>
                <div class="notification is-warning"><p><?= $key->value ?></p><a href="<?= site_url('dashboard/delete_notif/'.$key->id) ?>" class="delete delete-notif">X</a></div>
              <?php } ?>
            </div>
          </div>
        </div>

      </div>

    </div>
</div>

