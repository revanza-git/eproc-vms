<div class="sideArea">
	<ul class="listMenu">
		<li><a href="<?php echo site_url('auction')?>"><i class="fa fa-gavel"></i></i>&nbsp; Semua Auction</a></li>
		<li><a href="<?php echo site_url('auction/langsung')?>"><i class="fa fa-gavel"></i></i>&nbsp; Auction Berlangsung</a></li>
		<li><a href="<?php echo site_url('auction/selesai')?>"><i class="fa fa-gavel"></i></i>&nbsp; Auction Selesai</a></li>
		<li><a href="<?php echo site_url('katalog/index/barang')?>"><i class="fa fa-file-text-o"></i>&nbsp; Katalog Barang</a></li>
		<li><a href="<?php echo site_url('katalog/index/jasa')?>"><i class="fa fa-file-text-o"></i>&nbsp; Katalog Jasa</a></li>
	</ul>
</div>
<div class="mainArea">
	<?php if(isset($content)){
		echo $content;
	}
	?>
</div>