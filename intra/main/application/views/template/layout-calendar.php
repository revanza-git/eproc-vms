<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/scss/main.min.css'); ?>" type="text/css" media="screen" />
    <!-- <link rel="stylesheet" href="assets/css/vendors/jquery-ui.css" /> -->
    <link rel="stylesheet" href="../source/vendors/font-awesome/css/all.css" />

    <link rel="stylesheet" href="<?php echo base_url('assets/js/fullcalendar/fullcalendar.min.css'); ?>" type="text/css" media="screen" />

</head>
<body oncontextmenu="return false;">
  
  <?php include 'ui-kit/_nav.php' ?>

  <section class="main-content"> 

     <?php include 'ui-kit/_context-menu.php' ?>

    <div class="wrapper">

      <!-- <div class="col col-2">
        
        <?php include 'ui-kit/_sidenav.php' ?>

      </div> -->

      <div class="col col-12">
        
        <div class="content" id="content" oncontextmenu="return false;">

          <div class="wrapper">

            <div class="col col-12">

              <?php include 'ui-kit/_breadcrumb.php' ?>

            </div>
            
            <div class="col col-12">

              <div class="container">

                <div id='calendar'></div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </section>

  <script type="text/javascript" src="<?php echo base_url('assets/js/jquery-3.6.3.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('assets/js/fullcalendar/lib/jquery-ui.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('assets/js/fullcalendar/lib/jquery.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('assets/js/fullcalendar/lib/moment.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('assets/js/fullcalendar/fullcalendar.js');?>"></script>
  <script>
    $(function() {

    // page is now ready, initialize the calendar...

    $('#calendar').fullCalendar({
      header: { center: 'month,agendaWeek' }, // buttons for switching between views

      views: {
        week: { // name of view
          titleFormat: 'YYYY, MM, DD'
          // other view-specific options here
        },
        defaultView: 'timelineDay'
      }
    });

  });
  </script>

</body>
</html>
