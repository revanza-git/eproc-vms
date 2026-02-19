<div class="bgSh ">
	<div class="loginTitle">
		<h1>Sistem Aplikasi Kelogistikan</h1>
		<!--<p>Penyedia Barang &amp; Jasa Management System | e-Auction Management System</p>-->
	</div>
	<div class="loginBox clearfix">
		<div class="logoLogin">
			<img src="<?php echo base_url('assets/images/login-regas-logo.jpg')?>">
		</div>
		<div class="formLogin">
			<form id="loginForm" class="login-form" method="POST" action="<?php echo site_url('main/login');?>">
				<div class="form-group">
					<label>Username</label>
					<input type="text" name="username">
				</div>
				<div class="form-group">
					<label>Password</label>
					<input type="password" name="password">
				</div>
				<?php if($this->session->flashdata('error_msg')){ ?>
				<div class="form-group">
					<p class="errorMsg"><?php echo $this->session->flashdata('error_msg');?></p>
				</div>
				<?php } ?>
				<div class="form-group">
					<input type="submit" value="LOGIN" class="btnBlue">
					
				</div>
			</form>
		</div>
	</div>
</div>