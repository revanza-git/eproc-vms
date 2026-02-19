<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'submit');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		<form method="POST" enctype="multipart/form-data">
			<div class="formDashboard">
			<?php if($status_procurement==0){
				?>
				<p class="noticeMsg">Setelah merasa yakin, Tekan tombol dibawah untuk mengirim data dan dilengkapi oleh admin kontrak.</p>
				<div class="buttonRegBox clearfix">
				<input type="submit" value="Submit" class="btnBlue" name="simpan">
			</div>
				<?php
			}elseif($status_procurement==1){?>
				<p class="noticeMsg">Pengadaan sedang menunggu di proses oleh admin kontrak</p>
			<?php } ?>
			
			</div>
		</form>
	</div>
</div>