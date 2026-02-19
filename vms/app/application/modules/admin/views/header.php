 <div class="logo">
    <div class="logoInner">
        <a href="<?php echo site_url()?>"><img src="<?php echo base_url('assets/images/logo-nr.png?v='.@filemtime(FCPATH.'assets/images/logo-nr.png'));?>"></a>
    </div>
</div>
<div class="backButton">
    <div class="search-bar">
        <div class="search-barInner">
            <input type="text" name="nama_pengadaan" class="input" placeholder="Search.jjj.">
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


            <a href="<?php echo site_url('main/logout')?>" class="navbar-item">

                <span class="icon"><i class="fa fa-power-off"></i></span>

                Logout

            </a>

        </div>

    </div>
    
</div>
