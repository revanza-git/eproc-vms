<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <?php include 'ui-kit/link.php' ?>

</head>

<body class="bg-image">

	<section class="main-content"> 

	    <div class="wrapper is-centered">

	      <div class="col col-8">
	      	
			<div class="initialize-after">
				
				<div class="form-login">
					<div class="group-start">
						<h1>Manajemen Penyedia Barang/Jasa</h1>
						<p>Vendor Management System | e-Auction Management System</p>
					</div>
					<div class="group-end">
						<div class="panel">
							<div class="field">
								<div class="title">
									<img src="<?= base_url().'assets/images/img/Logo PGN A member of Pertamina_Full Color.png' ?>" alt="" style="height: 95px">
								</div>
								<div class="control is-centered">
									<input type="text" class="input" placeholder="Username">
								</div>
								<div class="control is-centered">
									<input type="text" class="input" placeholder="Password">
								</div>
								<div class="control is-centered">
									<button onclick="location.href='<?= base_url('template_test/show/beranda') ?>'" class="button is-expand is-primary">
										Login
									</button>
								</div>
								<div class="control is-centered">
									<p><a href="#">Lupa Password?</a></p>
								</div>
								<div class="control signup-link is-centered">
									<p>atau <a href="<?= base_url().'template_test/show/layout-signup' ?>">Daftar Vendor Baru</a></p>
								</div>
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

