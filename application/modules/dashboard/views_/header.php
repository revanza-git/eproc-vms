<div class="logo">
	<a href="<?php echo site_url()?>"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>"></a>
</div>
<div class="backButton">
	<ul class="navMenu clearfix">
		<li><a href="<?php echo site_url()?>">Welcome, <?php echo $name?></a>
		</li>
		<li><a href="#"><i class="fa fa-cog"></i>&nbsp;Pengaturan Akun</a>
			<ul class="navMenuDD">
				<li><a href="<?php echo site_url('vendor/data_pic')?>"><i class="fa fa-user"></i>&nbsp;Data Akun</a>
				</li><li><a href="<?php echo site_url('vendor/username_change')?>"><i class="fa fa-lock"></i>&nbsp;Ubah Username</a>
				</li><li><a href="<?php echo site_url('vendor/password_change')?>"><i class="fa fa-lock"></i>&nbsp;Pengaturan Password</a>
				</li>
			</ul>
		</li>
		<li><a href="<?php echo site_url('main/logout')?>"><i class="fa fa-power-off"></i>&nbsp;Keluar</a></li>
	</ul>
</div>