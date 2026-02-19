<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?= base_url().'assets/styles/scss/main.min.css' ?>" />
    <!-- <link rel="stylesheet" href="assets/css/vendors/jquery-ui.css" /> -->
    <link rel="stylesheet" href="../source/vendors/font-awesome/css/all.css" />
</head>
<body oncontextmenu="return false;">
  
  <?php include 'ui-kit/_nav.php' ?>

  <section class="main-content"> 

     <?php include 'ui-kit/_context-menu.php' ?>

    <div class="wrapper">

      <div class="col col-2">
        
        <?php include 'ui-kit/_sidenav.php' ?>

      </div>

      <div class="col col-10">
        
        <div class="content" id="content" oncontextmenu="return false;">

          <div class="wrapper">

            <div class="col col-12">

              <?php include 'ui-kit/_breadcrumb.php' ?>

            </div>
            
            <div class="col col-12">

              <div class="container">

                <div class="wrapper">

                  <div class="col col-6">
    
                    <div class="panel">
                      
                      <div class="container-title">
                        <h3>Summary Data</h3>
                      </div>

                      <div class="summary">
                        <div class="summary-title">
                          Accepted
                          <span>5/15</span>
                        </div>
                        <div class="summary-bars">
                          <span class="bar-top is-success"></span>
                          <span class="bar-bottom"></span>
                        </div>
                      </div>

                      <div class="summary">
                        <div class="summary-title">
                          Pending
                          <span>5/15</span>
                        </div>
                        <div class="summary-bars">
                          <span class="bar-top is-warning"></span>
                          <span class="bar-bottom"></span>
                        </div>
                      </div>

                      <div class="summary">
                        <div class="summary-title">
                          Denied
                          <span>5/15</span>
                        </div>
                        <div class="summary-bars">
                          <span class="bar-top is-danger"></span>
                          <span class="bar-bottom"></span>
                        </div>
                      </div>

                      <div class="container-title">
                        <h3>Overview</h3>
                      </div>

                      <div class="is-block">
                        <button class="accordion-header">Data Terverifikasi <span class="badge is-success">5</span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                        <div class="accordion-panel">
                          <p>Akta</p>
                          <p>Akta</p>
                          <p>TDP</p>
                          <p>TDP</p>
                          <p>Pengajuan</p>
                        </div>
                        <button class="accordion-header">Belum Terverifikasi <span class="badge is-warning">5</span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                        <div class="accordion-panel">
                          <p>Akta</p>
                          <p>Akta</p>
                          <p>TDP</p>
                          <p>TDP</p>
                          <p>Pengajuan</p>
                        </div>
                        <button class="accordion-header">Data tidak valid <span class="badge is-danger">5</span><span class="icon"><i class="fas fa-angle-down"></i></span></button>
                        <div class="accordion-panel">
                          <p>Akta</p>
                          <p>Akta</p>
                          <p>TDP</p>
                          <p>TDP</p>
                          <p>Pengajuan</p>
                        </div>
                      </div>

                    </div>

                  </div>

                  <div class="col col-6">

                    <div class="panel">

                      <div class="container-title">
                        <h3>Notifikasi</h3>
                        <div class="badge is-primary is-noticable">10</div>
                      </div>

                      <div class="scrollbar" id="custom-scroll" style="height: 470px; overflow-x: auto;">

                      <?php for($i=0; $i<=10; $i++) { ?>

                      <div class="notification is-warning">

                        <p>
                          
                          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem, quidem, quibusdam. Ducimus eligendi repudiandae natus error eveniet consectetur veniam in et, nobis hic, laudantium non incidunt deserunt atque praesentium temporibus!

                        </p>

                        <button class="delete">X</button>

                      </div>

                      <?php } ?>

                      </div>

                    </div>
                    
                  </div>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </section>

  <script type="text/javascript" src="<?= base_url().'assets/js/jquery-3.6.3.min.js' ?>"></script>
  <script type="text/javascript" src="../source/js/vendors/jquery-ui.js">
  </script>
  <script type="text/javascript" src="../source/js/app.js"></script>
</body>
</html>
