<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <?php include 'ui-kit/link.php' ?>

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
            
            <!-- <div class="col col-12">

              <div class="container">

                <div class="container-title">

                  <h1>Auction</h1>

                </div>

                <div class="col col-6">

                  <div class="is-block">

                    <?php include 'ui-kit/_display-info.php' ?>

                  </div>

                </div> -->

                <div class="is-block">

                  <?php include 'ui-kit/_tabs.php' ?>

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
