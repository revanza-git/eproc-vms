<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/scss/main.min.css'); ?>" type="text/css" media="screen"/>
    <!-- <link rel="stylesheet" href="assets/css/vendors/jquery-ui.css" /> -->
    <link rel="stylesheet" href="../source/vendors/font-awesome/css/all.css" />
</head>

<body>

	<div id="regForm">
		<!-- <form id="regForm"> -->
		<div class="tab" id="tab-intro">
			<div class="tab-content">
				<div class="intro-wrapper">
					<div class="intro">
						<div class="intro-icon">
							<img src="<?php echo base_url('assets/images/edit-icon.png'); ?>" alt="" style="height: 175px">
						</div>
						<div class="intro-caption">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quibusdam aliquid sit maiores vel laudantium labore beatae voluptatibus neque fuga tempore libero odio dolorem voluptatum veritatis praesentium iusto, illum quae minima.
						</div>
						<div class="intro-title">
							<button id="btnUbah">Ubah</button>
						</div>
					</div>	
				</div>
				<div class="intro-wrapper">
					<div class="intro">
						<div class="intro-icon">
							<img src="<?php echo base_url('assets/images/delete-icon.png'); ?>" alt="" style="height: 175px">
						</div>
						<div class="intro-caption">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quibusdam aliquid sit maiores vel laudantium labore beatae voluptatibus neque fuga tempore libero odio dolorem voluptatum veritatis praesentium iusto, illum quae minima.
						</div>
						<div class="intro-title">
							<button id="btnHapus">Hapus</button>
						</div>
					</div>	
				</div>
			</div>
			<div class="tab-footer"></div>
		</div>
		<div class="tab" id="formUbah">
			<div class="tab-form-header active">
				Ubah
			</div>
			<div class="tab-form-header" id="switchHapus">
				Hapus
			</div>
			<div class="tab-content">
				Hello
			</div>
			<div class="tab-footer">
			<button type="button" id="nextBtn3">Submit</button>
			</div>
		</div>
		<div class="tab" id="formBatal">
			<div class="tab-form-header" id="switchUbah">
				Ubah
			</div>
			<div class="tab-form-header active">
				Hapus
			</div>
			<div class="tab-content">
				Kuk
			</div>
			<div class="tab-footer">
			<button type="button" id="submitBtn">Submit</button>
			</div>
		</div>
		<!-- </form> -->
	</div>

	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js');?>"></script>
  	<script>
  		$(document).ready(function() {
  			$('#tab-intro').css('display','block');

  			$('#btnUbah').click(function() {
  				$('#formUbah').css('display','block');
  				$('#tab-intro').css('display','none');
  			});

  			$('#btnBatal').click(function() {
  				$('#formBatal').css('display','block');
  				$('#tab-intro').css('display','none');
  			});

  			$('#switchBatal').click(function() {
  				$('#formBatal').css('display','block');
  				$('#formUbah').css('display','none');
  			})

  			$('#switchUbah').click(function() {
  				$('#formUbah').css('display','block');
  				$('#formBatal').css('display','none');
  			})

  		})
  	</script>

</body>

</html>