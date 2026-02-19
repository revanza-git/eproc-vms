<div class="sideArea">
	<ul class="navMenu">
		<li <?php echo ($this->uri->segment(1)=='')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url()?>" >
				<i class="fa fa-home"></i>
				&nbsp; 
				<span>Beranda</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='administrasi')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('administrasi');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>Administrasi</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='akta')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('akta');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>Akta</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='situ')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('situ');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>SITU/SKDP</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='tdp')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('tdp');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>NIB/TDP</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='pengurus')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('pengurus');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>Pengurus Perusahaan</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='pemilik')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('pemilik');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>Pemilik Modal</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='izin')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('izin');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>Izin Usaha</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='pengalaman')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('pengalaman');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>Pengalaman</span>
			</a>
		</li>
		<li <?php echo ($this->uri->segment(1)=='agen')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('agen');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span style="word-break: break-word">Pabrikan/Keagenan/Distributor</span>
			</a>
		</li>
		<?php 
		$user = $this->session->userdata('user');
		if($this->dpt->check_iu($user['id_user'])>0){?>
		<li <?php echo ($this->uri->segment(1)=='k3')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('k3');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>Aspek K3 / CSMS</span>
			</a>
		</li>
		<?php }?>

		<?php 
		$this->load->model('dashboard/dashboard_model');
		$auction = $this->dashboard_model->get_auction();
		if($auction->num_rows() > 0){ ?>
		<li <?php echo ($this->uri->segment(1)=='auction')?'class="selectedMenu"':''; ?>>
			<a href="<?php echo site_url('auction/user/vendor_dash');?>">
				<!-- <i class="fa fa-file-o"></i> -->
				&nbsp; 
				<span>Auction</span>
			</a>
		</li>
		<?php }?>


		<?php 
			
			if($user['vendor_status']==0){
				?>
				<li class="waitBtn">
					<a href="<?php echo site_url('vendor/to_waiting_list');?>" class="waitingList">
						<i class="fa fa-briefcase"></i>
						&nbsp; 
						<span>Submit Data</span>
					</a>
				</li>
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
