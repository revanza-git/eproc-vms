<div class="logo">
	<div class="logoInner">
        <a href="<?php echo site_url()?>"><img src="<?php echo base_url('assets/images/login-regas-logo.png');?>"></a>
    </div>
</div>
<div class="backButton">
	<div class="search-bar">
        <div class="search-barInner">
            <input type="text" name="nama_pengadaan" class="input" placeholder="Search..">
            <span class="icon">
                <i class="fa fa-search"></i>
            </span>
        </div>
    </div>
	<div class="navbar-item account has-dropdown">

		<img src="<?php echo base_url('assets/images/man-avatar.png')?>" alt="" height="45px">
		
		<p> <?php echo $name?> </p>

		<span class="icon spin"><i class="fa fa-angle-down"></i></span>

		<div class="navbar-dropdown is-dropdown">

			<a href="<?php echo site_url('vendor/data_pic')?>" class="navbar-item">
				<span class="icon"><i class="fa fa-user"></i></span>

          		Data Akun
			</a>

        	<a href="<?php echo site_url('vendor/username_change')?>" class="navbar-item">
				<span class="icon"><i class="fa fa-lock"></i></span>

          		Ubah Username
			</a>

			<a href="<?php echo site_url('vendor/password_change')?>" class="navbar-item">
				<span class="icon"><i class="fa fa-lock"></i></span>

          		Pengaturan Password
			</a>


        	<a href="<?php echo site_url('main/logout')?>" class="navbar-item">

          		<span class="icon"><i class="fa fa-power-off"></i></span>

          		Logout

        	</a>

      	</div>

	</div>
	<!-- <ul class="navMenu clearfix">
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
	</ul> -->
</div>