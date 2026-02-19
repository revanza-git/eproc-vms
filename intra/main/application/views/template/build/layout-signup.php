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
  
  <nav class="navbar">
    
    <div class="navbar-brand">

      <div class="navbar-item logo">

        <img src="<?php echo base_url('assets/images/img/Logo PGN A member of Pertamina_Full Color.png')?>">

      </div>

    </div>

    <div class="navbar-menu">
      
      <div class="navbar-end">
        
        <div class="navbar-item">
          
          <button onclick="location.href='layout-login.php'" class="button is-white">Back</button>

        </div>

      </div>

    </div>

  </nav>

  <section class="main-content"> 

    <div class="wrapper">

      <div class="col col-12" style="margin-top: 3.5rem">
        
        <div class="notification is-white">
          <h1>Form Isian Admin</h1>
          <div class="info">
            <span></span>
              <p>*Yellow field is mandatory</p>
            <span></span>
              <p>*white field is optional</p>
            <span></span>
              <p>*grey field is read only</p>
          </div>
        </div>

      </div>

      <div class="container">
        
        <div class="wrapper">
          
          <div class="col col-6">

            <!-- <div class="scrollbar" id="custom-scroll" style="height: 550px; overflow-x: auto;"> -->

            <div class="form-start">

              <div class="form blockWrapper">

                <form action="">

                  <fieldset class="form-group">
                    <label for="">Lokasi <span class="english-caption">(Location)</span></label>
                    <select name="" id="" class="form-control">
                      <option value="">Logistic</option>
                      <option value="">General</option>
                      <option value="">Legal</option>
                    </select>
                  </fieldset>

                  <fieldset class="form-group">
                    <label for="">Badan Hukum <span class="english-caption">(legal entity)</span></label>
                    <select name="" id="" class="form-control">
                      <option value="">PT</option>
                      <option value="">CV</option>
                      <option value="">Firman</option>
                    </select>
                  </fieldset>

                  <fieldset class="form-group">
                    <label for="">Nama Badan Usaha</label>
                    <input type="text" class="form-control" placeholder="">
                  </fieldset>

                  <fieldset class="form-group">
                    <label for="">NPWP <span class="english-caption">(Taxpayer Registration Number)</span></label>
                    <input type="text" class="form-control" placeholder="">
                  </fieldset>

                  <fieldset class="form-group">
                    <label for="">Tanggal <span class="english-caption">(date)</span></label>
                    <div class="wrapper">
                      <input type="text" class="form-control datePicker ">
                    </div>
                  </fieldset>

                </form>

              </div>
              
              <!-- <div class="field display-info">

                <label for="" class="label">
                  Lokasi
                  <p>(Location)</p>
                </label>
                <div class="control">
                  <div class="select">
                    <select name="" id="">
                      <option value="">Logistic</option>
                      <option value="">General</option>
                      <option value="">Legal</option>
                    </select>
                  </div>
                </div>

                <label for="" class="label">Badan Hukum</label>
                <div class="control">
                  <div class="select">
                    <select name="" id="">
                      <option value="">PT</option>
                      <option value="">CV</option>
                      <option value="">Firman</option>
                    </select>
                  </div>
                </div>

                <label for="" class="label">Nama Badan Usaha</label>
                <div class="control">
                  <input type="text" class="input input-mandatory" placeholder="example of mandatory">
                </div>

                <label for="" class="label">NPWP</label>
                <div class="control">
                  <input type="text" class="input input-disabled" placeholder="example of read-only" disabled="">
                </div>

                <label for="" class="label">
                  Tanggal
                  <p>(date)</p>
                </label>
                <div class="control">
                  <div class="select date-picker">
                    <select name="" id="">
                      <option value="">18</option>
                      <option value="">Option 2</option>
                      <option value="">Option 3</option>
                    </select>
                    <select name="" id="">
                      <option value="">September</option>
                      <option value="">Option 2</option>
                      <option value="">Option 3</option>
                    </select>
                    <select name="" id="">
                      <option value="">2018</option>
                      <option value="">Option 2</option>
                      <option value="">Option 3</option>
                    </select>
                  </div>
                </div>

                <label for="" class="label">
                  Lampiran
                  <p>(attachment)</p>
                </label>
                <div class="control">
                  <input type="file"
                           id="avatar" name="avatar"
                           accept="image/png, image/jpeg" />
                </div>

                <label for="" class="label">Status</label>
                <div class="control">
                  <input type="radio" id="" 
                               name="" value="" />
                        <p>Pusat</p>
                  <input type="radio" id="" 
                               name="" value="" />
                        <p>Cabang</p>
                </div>

                <label for="" class="label">
                  Alamat
                  <p>(address)</p>
                </label>
                <div class="control">
                  <textarea name="" id="" cols="30" rows="6"></textarea>
                </div>

              </div> -->

            </div>
            
          </div>

          <div class="col col-6">

            <div class="form-end">
              
              <div class="field display-info">

                <label for="" class="label">
                  Kota/Kab
                  <p>(city)</p>
                </label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label">
                  Provinsi
                  <p>(province)</p>
                </label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label">
                  Negara
                  <p>(country)</p>
                </label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label">
                  Kode Pos
                  <p>(post code)</p>
                </label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label">
                  No. Telp
                  <p>(phone number)</p>
                </label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label">Fax</label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label">Email</label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label">Website</label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label">Captcha</label>
                <div class="control">
                  <input type="text" class="input" placeholder="">
                </div>

                <label for="" class="label"></label>
                <div class="control is-grouped">
                  <button onclick="location.href='<?= base_url('template_test/show/layout-login') ?>'" class="button is-link is-wide">Cancel</button>
                  <button onclick="location.href='<?= base_url('template_test/show/confirmation-page') ?>'" class="button is-primary is-wide">Next</button>
                </div>

              </div>

            </div>
            
          </div>

        </div>

      </div>

    </div>

  </section>

  <script>
      var base_url = "<?php echo base_url()?>";
      var site_url = "<?php echo site_url()?>";
  </script>
  <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.imask.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('assets/js/form.js');?>"></script>

</body>
</html>
