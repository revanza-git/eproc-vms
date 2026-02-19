<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="<?php echo base_url('assets/font/fontawesome-free-5.3.1-web/css/all.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/normalize.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('assets/font/font-awesome/css/font-awesome.min.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/base.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/scss/main.min.css'); ?>" type="text/css" media="screen"/>

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

                <div class="col col-12">

                  <div class="container-title">

                    <h1>Layout File</h1>

                    <div class="group-button-start">

                      Menampilkan 

                      <select>

                        <option value="">25</option>

                        <option value="">50</option>

                        <option value="">75</option>

                        <option value="">100</option>

                      </select>

                      Entry

                    </div>

                    <div class="group-button-end">

                      <button class="button is-primary is-wide has-dropdown">Add</button>

                      <ul class="dropdown-menu is-dropdown">
                        <li>
                          <a href="">New file</a>
                        </li>
                        <li>
                          <a href="">New Akta</a>
                        </li>
                        <li>
                          <a href="">New Vendor</a>
                        </li>
                      </ul>

                      <button onclick="switchView('box')" class="button is-rounded"><span class="icon"><i class="fas fa-th"></i></span></button>

                      <button onclick="switchView('list')" class="button is-rounded"><span class="icon"><i class="fas fa-list"></i></span></button>

                    </div>

                  </div>

                </div>

                <ul>

                  <li class="col-12 header">

                    <div class="view view-list">

                      <div class="view-item" oncontextmenu="return false;">

                        <img src="<?php echo base_url('assets/images/img/folder.png')?>" alt="">

                        <img src="<?php echo base_url('assets/images/img/down-arrow-inside-a-circle.png')?>" alt="">

                        <div class="caption">

                          <p>Header Title</p>

                          <p>Header Caption</p>

                          <p>Header Caption</p>
                          
                        </div>

                      </div>

                    </div>

                  </li>

                  <?php

                    for($i = 1; $i <= 10; $i++) {

                  ?>

                  <li class="col-12">

                    <div class="view view-list">

                      <div class="view-item" oncontextmenu="return false;">

                        <img src="<?php echo base_url('assets/images/img/folder.png')?>" alt="">

                        <img src="<?php echo base_url('assets/images/img/down-arrow-inside-a-circle.png')?>" alt="">

                        <div class="caption">

                          <p>Title Here</p>

                          <p>Caption Here</p>

                          <p>Caption Here</p>
                          
                        </div>

                      </div>

                    </div>

                  </li>

                  <?php 

                    }

                  ?>

                </ul>

                <div class="container-footer">

                  <ul class="pagination">

                    <li class="disabled">

                      <a href="">

                        <span class="icon"><i class="fas fa-arrow-left"></i></span>

                      </a>

                    </li>

                    <li><a href="">1</a></li>

                    <li><a href="">2</a></li>

                    <li><a href="">3</a></li>

                    <li><a href="">4</a></li>

                    <li><a href="">5</a></li>

                    <li>

                      <a href="">

                        <span class="icon"><i class="fas fa-arrow-right"></i></span>

                      </a>

                    </li>

                  </ul>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </section>

  <?php include 'ui-kit/script.php' ?>

</body>
</html>
