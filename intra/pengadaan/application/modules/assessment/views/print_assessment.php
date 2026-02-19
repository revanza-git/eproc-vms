<script type="text/javascript">
	// window.print();
</script>
<div class="container">
	<!-- Header -->
	<div class="header">
		<table border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td width="250"><img src="<?php echo base_url('assets/images/login-regas-logo.jpg');?>" height="70"></td>
					<td valign="bottom">
						<div style="font-size : 14px">Sistem Aplikasi Kelogistikan</div>
						<div style="font-size : 11px"><i>Laporan Data Penyedia Barang/Jasa</i></div>
					</td>
					<td align="right" valign="bottom">
						<div>Dicetak pada tanggal : <?php echo date("d-m-Y h:i") ?></div>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="5"></td>
				</tr>
				<tr>
					<td colspan="3">
						<div style="border-bottom : 2px solid #000; border-top : 1px solid #000; height : 2px"></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- Header -->

	<!-- Data Vendor -->
	<div class="content">			
		<table style="border: none !important;" class="recap-table" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th colspan="3" align="left" style="border: none !important;">Data Penyedia Barang/Jasa</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(count($vendor)){
					foreach($vendor as $keyv => $valuev){
				?>
				<tr>
					<!-- <td width="15px" style="border: none !important;">1. </td> -->
					<td width="100px" style="border: none !important;">Nama</td>
					<td style="border: none !important;"><?php echo $valuev['name'];?></td>
				</tr>
				<tr>
					<!-- <td width="15px" style="border: none !important;">2. </td> -->
					<td width="100px" style="border: none !important;">NPWP</td>
					<td style="border: none !important;"><?php echo $valuev['npwp_code'];?></td>
				</tr>
				<tr>
					<!-- <td width="15px" style="border: none !important;">3. </td> -->
					<td width="100px" style="border: none !important;">Point</td>
					<td style="border: none !important;"><?php echo $valuev['point'];?></td>
				</tr>
				<tr>
					<!-- <td width="15px" style="border: none !important;">3. </td> -->
					<td width="100px" style="border: none !important;">Kategori</td>
					<td style="border: none !important;">
						<?php
							$cat ="";
							$kategori = ($valuev['point'] >= -30) ? $cat="Biru" : (($valuev['point'] <=-31 && $valuev['point'] >=-60) ? $cat="Kuning" : (($valuev['point'] <=-61 && $valuev['point'] >=-120) ? $cat="Merah" : $cat="Hitam")) ;
							echo $cat;
						?>
					</td>
				</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>			
	</div>
	<!-- / Data Vendor -->

	<!-- Data Nilai Vendor -->
	<div class="content">		
		<table class="recap-table" border="1" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th colspan="4" align="left">Data Penilaian Penyedia Barang/Jasa</th>
				</tr>
				<tr>
					<th>No.</th>
					<th>Pertanyaan</th>
					<th>Nilai</th>
					<th>Hasil</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(count($assessment_question)){
					$i=1;
					foreach($assessment_question as $key => $value){
				?>
				<tr><td colspan="4"><h2><?php echo strtoupper($value['name']); ?></h2></td><tr>
				<?php
						foreach($value['quest'] as $row => $val){ 
							$is_id = $this->session->userdata('admin')['id_role']==$val['id_role'];
				?>
				<tr>
					<td width="15px"><?php echo $i; ?></td>
					<td width="700px"><?php echo $val['value'];?></td>
					<td width="20px"><?php echo $val['point'];?></td>
					<td width="70px"><?php 
						if(isset($data_assessment[$val['id']])){
							if($data_assessment[$val['id']]!=0){
								echo 'Memenuhi';
							}else if($data_assessment[$val['id']]==0){
								echo 'Tidak Memenuhi';
							}
						}
						else{
							echo 'Belum Dinilai';
						}
						?>
					</td>
				</tr>
				<?php $i++;}}} ?>
			</tbody>
		</table>		
	</div>
	<!-- / Data Nilai Vendor -->

</div>




