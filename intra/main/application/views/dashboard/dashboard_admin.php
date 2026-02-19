<?php $admin = $this->session->userdata('admin'); ?>
<div class="mg-lg-12">
  <div class="container">

      <div class="wrapper">
        <!-- Grafik -->
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
        <!-- End Grafik -->

        <!-- Data Approval FPPBJ -->
        <div class="col col-6">
    
          <div id="rekapFPPBJ">
          
          </div>

        </div>
        <!-- End Data Approval FPPBJ -->

        <!-- Data Approval FKPBJ -->
        <div class="col col-6">
    
          <div id="rekapFKPBJ">
          
          </div>

        </div>
        <!-- End Data Approval FKPBJ -->

        <!-- Data Approval FP3 -->
        <div class="col col-6">
    
          <div id="rekapFP3">
          
          </div>

        </div>
        <!-- End Data Approval FP3 -->

        <!-- Data Approval FPPBJ Baru -->
        <div class="col col-6">
    
          <div id="rekapFPPBJBaru">
          
          </div>

        </div>
        <!-- End Data Approval FPPBJ Baru -->

        <!-- Data Approval FKPBJ Baru -->
      <div class="col col-6">

        <div id="rekapFKPBJBaru">

        </div>

      </div>
      <!-- End Data Approval FKPBJ Baru -->

        <!-- Notification -->
        <div class="col col-6">
          <div class="panel">
            <div class="container-title">
              <h3>Notifikasi</h3>
              <div class="badge is-primary is-noticable">
              <?php echo $notification->num_rows(); ?>
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
        <!-- End Notification -->



      </div>

    </div>
</div>