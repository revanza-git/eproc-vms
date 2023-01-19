<p id="auction-report-bar" class="msgSuccess text-center">
	<i class="fa fa-check-square-o"></i>Auction telah selesai.
	<b style="color : #617ac6; cursor : pointer">
		<a href="<?php echo site_url('auction/report/index/internal/'.$id_lelang)?>" target="_blank">Klik disini</a>
	</b> untuk melihat report.
</p>
<p id="auction-report-bar" class="msgSuccess text-center">
	<i class="fa fa-check-square-o"></i>Auction telah selesai.
	<b style="color : #617ac6; cursor : pointer">
		<a href="<?php echo site_url('auction/report/index/internal/'.$id_lelang)?>" target="_blank">Klik disini</a>
	</b> untuk melihat report.
</p>
<div class="lelang">
	<div class="col-14">
		<div class="panel">
			<div class="panel-heading">
				<h4><i class="auction-hammers4"></i><?php echo $fill['name']; ?></h4>
			</div>
			<table class="table table-borderless content-group-sm">
				<tbody>
					<tr>
						<td>
							Mata Uang
						</td>
						<td class="text-right">
							<?php echo $fill['rate']; ?>
						</td>
					</tr>
					<tr>
						<td>
							Metode Penawaran
						</td>
						<td class="text-right">
							<?php 
								if($fill['metode_penawaran'] == "lump_sum") 
									echo "Lump Sum";
								if($fill['metode_penawaran'] == "harga_satuan") 
									echo "Harga Satuan";
							?>
						</td>
					</tr>
					<tr>
						<td>
							Durasi Lelang
						</td>
						<td class="text-right">
							<?php echo $fill['auction_duration']?> Menit
						</td>
					</tr>
					<tr>
						<td>
							Tipe Auction
						</td>
						<td class="text-right">
							<?php 
								if($fill['auction_type'] == "forward_auction") 
									echo "Forward Auction";
								if($fill['auction_type'] == "reverse_auction") 
									echo "Reverse Auction";
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="start-auction" class="panel-body">
				
			</div>
		</div>
		<div class="panel " id="timer-container">
			<div class="panel-heading">
				<h4><i class="fa fa-clock-o"></i>&nbsp;Waktu tersisa</h4>
			</div>
			<div class="panel-body">
				<div id="timer">-- : -- : --</div>
			</div>
		</div>
	</div>
	<div class="col-34">
		<div id="chart-container" class="panel"></div>
	</div>
	
</div>
		
<div id="auction-blocker">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
		<tr>
			<td width="100%" height="100%" align="center" valign="middle">
				<div id="auction-blocker-message-bar"></div>
			</td>
		</tr>
	</table>
</div>
<div id="syarat-body" style="display : none"><?php echo $syarat; ?></div>