<div class="bgSh ">
	<div class="loginTitle">
		<h1>Selamat Datang, <?php echo $name ?></h1>
		
	</div>
	<div class="loginBox clearfix">
		<p style="text-align: center">Anda akan diarahkan menuju dashboard, jika tidak klik <a href="<?php echo site_url('dashboard')?>">disini</a></p>
	</div>
</div>
<script type="text/javascript">
	setTimeout(function(){
	  	window.location = '<?php echo site_url('dashboard')?>';
	}, 3000);
</script>