<!DOCTYPE html>
<html lang="en">
<head>
    <title>{header}</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/scss/catalog-main.css'); ?>" />
    <!-- <link rel="stylesheet" href="assets/css/vendors/jquery-ui.css" /> -->
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/fontawesome5.6.3/css/all.css'); ?>" type="text/css" media="screen"/>
</head>
<body>

	  <?php include "_nav.php" ?>

    <section class="header" style="background-image: url('<?php echo base_url("assets/images/img/header-bg.jpg") ?>')">

      <div class="overlay"></div>

      <div class="header-title">

        Katalog <span>Produk</span>

        <div class="span-line"></div>

      </div>

    </section>

    <section class="container">

      <div class="wrapper">

        <div class="content-left">

          <div class="box-list">

            <div class="box-header">

              Kategori <br> <span>Produk</span>

            </div>

            <div class="box-container">

              <div class="list" style="background-image: url('<?php echo base_url("assets/images/img/header-bg.jpg") ?>')">

                <div class="list-overlay">

                  List Here

                </div>

                <span class="icon"><i class="fas fa-angle-right"></i></span>

              </div>

              <div class="list">

                <div class="list-overlay">

                  List Here

                </div>

                <span class="icon"><i class="fas fa-angle-right"></i></span>

              </div>

              <div class="list">

                <div class="list-overlay">

                  List Here

                </div>

                <span class="icon"><i class="fas fa-angle-right"></i></span>

              </div>

              <div class="list">

                <div class="list-overlay">

                  List Here

                </div>

                <span class="icon"><i class="fas fa-angle-right"></i></span>

              </div>

            </div>

          </div>

        </div>

        <div class="content-right">

            <!-- <div class="list-header">

              Kategori Produk

            </div> -->

            <div class="card-wrapper">

              <?php for ($i=1; $i <= 12 ; $i++) { ?>

                <div class="card" style="background-image: url('<?php echo base_url("assets/images/boots-png/s-1.png") ?>')" onclick="location.href='permata-detail-page.php'">

                  <div class="card-header"></div>

                  <span class="icon shop"><i class="fas fa-shopping-cart"></i></span>

                  <div class="card-title">

                    Here Title

                    <span>Sub Title</span>

                    <span class="price">Rp. 30.000</span>

                  </div>

                </div>
                
              <?php } ?>

            </div>

        </div>

      </div>

    </section>

    <section class="footer" style="background-image: url('<?php echo base_url("assets/images/img/footer-bg.jpg") ?>')">

      <div class="overlay"></div>

      <div class="footer-info">

        <div class="footer-left">

          <div class="info-header" style="height: 45px">

            <!-- <img src="../source/img/logo.jpg" alt=""> -->

          </div>

          <p>

            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sunt, voluptatum enim excepturi dolores, quam fuga architecto quae quis laboriosam provident aliquid corporis repellendus quisquam cupiditate. Veritatis nobis necessitatibus vel exercitationem.

          </p>

          <a href="#" class="link">

            <span class="icon"><i class="fas fa-globe-asia"></i></span>

            <span class="hover hover-1">http://www.loremipsum.com</span>

          </a>

        </div>

        <div class="footer-right">

          <div class="footer-box-page">

            <span class="box-title">Akun <span>Saya</span></span>

            <ul>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Personal Info</span> 

                </a>

              </li>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Order Saya</span> 

                </a>

              </li>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Keranjang Belanja</span> 

                </a>

              </li>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Wishlist</span> 

                </a>

              </li>

            </ul>

          </div>

          <div class="footer-box-page">

            <span class="box-title">Permat<span>apd</span> </span>

            <ul>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Tentang Kami</span> 

                </a>

              </li>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Jasa Kami</span> 

                </a>

              </li>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Benefit</span> 

                </a>

              </li>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Layanan</span>

                </a>

              </li>

              <li>

                <a href="#">

                  <span class="icon"><i class="fas fa-angle-right"></i></span>

                  <span class="hover hover-1">Kontak Kami</span> 

                </a>

              </li>

            </ul>

          </div>

        </div>

      </div>
      
    </section>

    <section class="signature">

      <div class="left">

        brought to you by :

        <img src="../source/img/pgn_mas.png" alt="">

      </div>

      <div class="center">

        operated by :

        <img src="../source/img/kpusahatama.png" alt="">

      </div>

      <div class="right">

        E-Commerce Platform by :

        <img src="../source/img/dekodr.png" alt="">

      </div>

    </section>

  <script type="text/javascript" src="../source/js/vendors/jquery-3.6.3.min.js">
  </script>
  <script type="text/javascript" src="../source/js/vendors/jquery-ui.js">
  </script>
  <script type="text/javascript" src="../source/js/app.js"></script>
   

</body>
</html>