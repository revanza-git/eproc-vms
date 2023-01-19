<div class="sideArea">
	<div class="loginInfo">
		<h2>Selamat Datang, <?php echo $this->session->userdata('admin')['role_name'];?></h2>
		<!--<p class="sbuText">Welcome <?php echo $this->session->userdata('admin')['role_name'];?>, Sumber Daya Manusia dan Umum</p>-->
		<?php if ($this->uri->segment(1)=="approval") {?>
			<div class="warnVer msgBlock" style="color: #fff;">
				<ul><?php //print_r($approval_data);?>
					<h4><?php echo count($approval_data[0])?> data belum terverifikasi</h4>
					<?php 
						$link = array(
							'Akta' 							=> 'akta',
							'Akta Pendirian' 				=> 'akta',
							'Akta Perubahan' 				=> 'akta',
							'SITU/Domisili' 				=> 'situ',
							'TDP'							=> 'tdp',
							'Kepemilikan Saham'				=> 'pemilik',
							'Izin Usaha'					=> 'badan_usaha',
							'Pabrikan/Keagenan/Distributor'	=> 'agen',
							'CSMS'							=> 'k3',
							'Data Administrasi Vendor'		=> 'administrasi',
							'Pengurus'						=> 'pengurus',
							'Pengalaman'					=> 'pengalaman'
						);
					?>
					<?php foreach($approval_data[0] as $key =>$value){?>
						<?php $perubahan = ($value=='Akta Perubahan') ? $perubahan="perubahan" : $perubahan="" ;?>
						<li>
							<a style="color: #fff;" href="<?php echo site_url('approval/'.$link[$value].'/'.$this->uri->segment(3).'/'.$perubahan);?>">
								<?php echo $value;?>
							</a>	
						</li>
					<?php } ?>

					<?php if ($this->session->userdata('admin')['role_name'] != 2 ) {?>
					<!-- <li><a href="<?php echo site_url('Katalog')?>"><i class="fa fa-file-text-o"></i>&nbsp;Katalog</a></li> -->
					<?php }?>
					<!-- <li><a href="<?php echo site_url('Katalog')?>"><i class="fa fa-file-text-o"></i>&nbsp; Katalog</a></li> -->
				</ul>
			</div>
		<?php }?>
	</div>
</div>
<div class="mainArea">
	<?php if(isset($content)){
		echo $content;
	}
	?>
</div>
