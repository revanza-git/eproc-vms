<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <?php include 'ui-kit/link.php' ?>

</head>
<body >
  
  <?php include 'ui-kit/_nav.php' ?>

  <section class="main-content"> 

     <?php include 'ui-kit/_context-menu.php' ?>

    <div class="wrapper">


        
        <?php include 'ui-kit/_sidenav.php' ?>

        
        <div class="content" id="content" oncontextmenu="return false;">

          <div class="wrapper">

            <div class="col col-12">

              <?php include 'ui-kit/_breadcrumb.php' ?>

            </div>
            
            <div class="col col-12">

              <div class="container">

                 <?php include 'ui-kit/_modal.php' ?>

              </div>

            </div>

          </div>

        </div>


    </div>

  </section>

  <?php include 'ui-kit/script.php' ?>

</body>
</html>
