<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../../assets/styles/scss/main.min.css" />
    <!-- <link rel="stylesheet" href="assets/css/vendors/jquery-ui.css" /> -->
    <link rel="stylesheet" href="../../../assets/source/vendors/font-awesome/css/all.css" />
</head>

<body>

	<input type="text" class="nm-tg" placeholder="isi">

	<div class="nm-wrapper">
		<div class="nm-form">
			Form isian <br>
			<input type="text" class="nm-input">
			<div class="btn-group">
				<button class="is-primary">Submit</button>
				<button class="close-btn">Cancel</button>
			</div>
		</div>
		<table class="new-matrix" style="border-spacing: 5px">
			<tr>
				<th colspan="2"></th>
				<th colspan="5" style="font-size: 14px">Probability</th>
				<!-- <th>
					<button class="close">
						<span aria-hidden="true">x</span>
					</button>
				</th> -->
			</tr>
			<tr>
				<th></th>
				<th class="manusia_desc">Manusia</th>
				<th class="aset_desc">Aset / Peralatan</th>
				<th class="lingkungan_desc">Lingkungan</th>
				<th class="hukum_desc">Reputasi dan Hukum</th>
				<th>Tidak pernah terdengar di industri / perkantoran migas</th>
				<th>Pernah terdengar di industri / perkantoran migas</th>
				<th>Terjadi 1 kali dalam 5 tahun di perusahaan</th>
				<th>Terjadi 1 kali dalam 1tahun di perusahaan</th>
				<th style="padding-right: 30px;">Terjadi > 1 kali dalam 1 tahun di perusahaan</th>
			</tr>
			<tr>
				<th rowspan="5" style="font-size: 14px; padding-bottom: 15px"><div class="rotated">Severity</div></th>
				<td class="manusia_desc">Multiple Fatality/Korban meninggal lebih dari 1</td>
				<td class="aset_desc">Kerusakan Parah</td>
				<td class="lingkungan_desc">Dampak luar biasa (internasional)</td>
				<td class="hukum_desc">Dampak luar biasa (internasional)</td>
				<td class="box-parent">
					<div class="nm-box yellow">
						<span class="nm-text">5</span>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box orange n10">
						10
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box red n15">
						15
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box red n20">
						20
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box red n25">
						25
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="manusia_desc">Single Fatality/Cacat Permanen</td>
				<td class="aset_desc">Kerusakan Besar</td>
				<td class="lingkungan_desc">Dampak besar (nasional)</td>
				<td class="hukum_desc">Dampak besar (nasional)</td>
				<td class="box-parent">
					<div class="nm-box green-light n4">
						4
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box yellow n8">
						8
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box orange n12">
						12
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box red n16">
						16
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box red n20">
						20
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="manusia_desc">Cidera / sakit berat</td>
				<td class="aset_desc">Kerusakan Setempat</td>
				<td class="lingkungan_desc">Dampak besar setempat/lokal</td>
				<td class="hukum_desc">Dampak sedang</td>
				<td class="box-parent">
					<div class="nm-box green n3">
						3
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box yellow n6">
						6
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box yellow n9">
						9
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box orange n12">
						12
						</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box red n15">
						15
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="manusia_desc">Cidera / sakit sedang</td>
				<td class="aset_desc">Kerusakan Sedang</td>
				<td class="lingkungan_desc">Dampak sedang</td>
				<td class="hukum_desc">Dampak terbatas</td>
				<td class="box-parent">
					<div class="nm-box green n2">
						2
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box green-light n4">
						4
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box yellow n6">
						6
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box yellow n8">
						8
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box orange n10">
						10
					</div>
				</td>
			</tr>
			<tr>
				<td class="manusia_desc">Cidera / sakit ringan</td>
				<td class="aset_desc">Kerusakan Kecil</td>
				<td class="lingkungan_desc">Dampak ringan</td>
				<td class="hukum_desc">Dampak ringan</td>
				<td class="box-parent">
					<div class="nm-box green n1">1</div>
				</td>
				<td class="box-parent">
					<div class="nm-box green n2">2</div>
				</td>
				<td class="box-parent">
					<div class="nm-box green n3">3</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box green-light n4">4</div>
					</div>
				</td>
				<td class="box-parent">
					<div class="nm-box yellow n5">5</div>
					</div>
				</td>
			</tr>
			<!-- <tr>
				<td colspan="10">
					<div class="btn-group">
						<button>Previous</button>
						<button>Next</button>
					</div>
				</td>
			</tr> -->
		</table>
	</div>
	
	<script type="text/javascript" src="../source/js/vendors/jquery-3.6.3.min.js">
  	</script>

  	<script>
  		$(document).ready(function() {
  			$('.nm-tg').click(function() {
  				$('.new-matrix').addClass('active');
  			});
  			$('.nm-box').click(function() {
			  console.log($(this).text());
			  
			  var matrix = $(this).text();

			  $('.nm-tg').val(matrix);
		
			});
  	
  			$('.nm-box').click(function() {
  				$('.new-matrix').removeClass('active');
  			});
  		});
  	</script>

</body>

</html>