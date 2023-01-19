<div class="sideArea">
	<ul class="listMenu">
		<li <?php echo ($this->uri->segment(1)=='')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url()?>" ><i class="fa fa-home"></i>&nbsp; Beranda</a></li>
		<li <?php echo ($this->uri->segment(1)=='administrasi')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('administrasi');?>"><i class="fa fa-file-o"></i>&nbsp; Administrasi</a></li>
		<li <?php echo ($this->uri->segment(1)=='akta')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('akta');?>"><i class="fa fa-file-o"></i>&nbsp; Akta</a></li>
		<li <?php echo ($this->uri->segment(1)=='situ')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('situ');?>"><i class="fa fa-file-o"></i>&nbsp; SITU/SKDP</a></li>
		<li <?php echo ($this->uri->segment(1)=='tdp')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('tdp');?>"><i class="fa fa-file-o"></i>&nbsp; TDP</a></li>
		<li <?php echo ($this->uri->segment(1)=='pengurus')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('pengurus');?>"><i class="fa fa-file-o"></i>&nbsp; Pengurus Perusahaan</a></li>
		<li <?php echo ($this->uri->segment(1)=='pemilik')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('pemilik');?>"><i class="fa fa-file-o"></i>&nbsp; Pemilik Modal</a></li>
		<li <?php echo ($this->uri->segment(1)=='izin')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('izin');?>"><i class="fa fa-file-o"></i>&nbsp; Izin Usaha</a></li>
		<li <?php echo ($this->uri->segment(1)=='pengalaman')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('pengalaman');?>"><i class="fa fa-file-o"></i>&nbsp; Pengalaman</a></li>
		<li <?php echo ($this->uri->segment(1)=='agen')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('agen');?>"><i class="fa fa-file-o"></i>&nbsp; Pabrikan/Keagenan/Distributor</a></li>
		<?php 
		$user = $this->session->userdata('user');
		if($this->dpt->check_iu($user['id_user'])>0){?>
		<li <?php echo ($this->uri->segment(1)=='k3')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('k3');?>"><i class="fa fa-file-o"></i>&nbsp; Aspek K3</a></li>
		<?php }?>

		<?php 
		$this->load->model('dashboard/dashboard_model');
		$auction = $this->dashboard_model->get_auction();
		if($auction->num_rows() > 0){ ?>
		<li <?php echo ($this->uri->segment(1)=='auction')?'class="selectedMenu"':''; ?>><a href="<?php echo site_url('auction/user/vendor_dash');?>"><i class="fa fa-file-o"></i>&nbsp; Auction</a></li>
		<?php }?>


		<?php 
			
			if($user['vendor_status']==0){
				?>
				<li class="waitBtn"><a href="<?php echo site_url('vendor/to_waiting_list');?>" class="waitingList"><i class="fa fa-briefcase"></i>&nbsp; Submit Data</a></li>
				<?php
			}
		?>

		

	</ul>
</div>
<div class="mainArea">
	<?php if(isset($content)){
		echo $content;
	}
	?>
</div>
