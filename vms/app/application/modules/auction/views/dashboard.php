<div class="sideArea">
	<ul class="navMenu">
		<li><a href="<?php echo site_url('auction')?>"><i style="margin-right: 12px;" class="fa fa-gavel"></i></i>Semua Auction</a></li>
		<li><a href="<?php echo site_url('auction/langsung')?>"><i style="margin-right: 12px;" class="fa fa-gavel"></i></i>Auction Berlangsung</a></li>
		<li><a href="<?php echo site_url('auction/selesai')?>"><i style="margin-right: 12px;" class="fa fa-gavel"></i></i>Auction Selesai</a></li>
		<li><a href="<?php echo site_url('katalog/index/barang')?>"><i style="margin-right: 12px;" class="fa fa-file-text-o"></i>Katalog Barang</a></li>
		<li><a href="<?php echo site_url('katalog/index/jasa')?>"><i style="margin-right: 12px;" class="fa fa-file-text-o"></i>Katalog Jasa</a></li>
	</ul>
</div>
<div class="mainArea" style="height: 100vh">
	<?php if(isset($content)){
		echo $content;
	}
	?>
</div>