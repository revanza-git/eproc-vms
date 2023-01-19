<h2 class="formHeader">Laporan Pengadaan B/J</h2>
<?php if ($this->session->userdata('admin')['id_role']==1) {?>
<div class="customReport clearfix">
	<form action="<?php echo base_url()."admin/custom_report/view/"; ?>" method="POST">
		<div class="customReportWrapper clearfix">
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[name]"/><span>Nama Paket Pengadaan</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[hps_value]"/><span>Nilai HPS</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[budget_source]"/><span>Sumber Anggaran</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[pejabat_pengadaan]"/><span>Pejabat Pengadaan</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[budget_year]"/><span>Tahun Anggaran</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[budget_holder_name]"/><span>Budget Holder</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[budget_spender_name]"/><span>Metode Pengadaan</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[mekanisme]"/><span>Metode Evaluasi</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[barang]"/><span>Barang/Jasa</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[peserta]"/><span>Peserta</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[pemenang]"/><span>Pemenang</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[nilai_kontrak]"/><span>Nilai Kontrak</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[efisiensi]"/><span>Efisiensi</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[kontrak_period]"/><span>Kontrak/PO</span></div>
			<div class="customReportCheckBox">
				<input type="checkbox" name="field[proses_pengadaan]"/><span>Proses Pengadaan</span></div>
			<!-- <div class="customReportCheckBox">
				<td height="30"></div> -->
		</div>
		<div>
			<input type="submit" class="btnBlue"/>
		</div>
	</form>
</div>
<?php } ?>


<!-- <form method="POST">
<?php echo $filter_list;?>
</form> -->
<div class="tableWrapper">
	<div class="filterBtnWp">
		<!-- <a href="<?php echo site_url('admin/admin_user/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a> -->
		<button style="margin-bottom: 17px" class="editBtn lihatData filterBtn">Filter</button>
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td width="200"><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement.name">Nama Pengadaan<i class="fa fa-sort-<?php echo ($sort['ms_procurement.name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td>Progress Pengadaan</td>
			</tr>
		</thead>
		<tbody>
		
		<?php if(count($pengadaan)>0){
			// print_r($pengadaan);die;
			foreach($pengadaan as $row => $value){
				// print_r($value);die;
				$prog = end($value['progress']);
				$p = 0;
				foreach ($value['progress'] as $k => $v) {
					$p += $v['percent'];
				}
			?>	
				<tr>
					<td rowspan="2"><?php echo $value['name'];?></td>
					<td style="width: 400px; overflow: hidden">
						<?php if (!empty($prog)) { ?>
							<div class="graphBarWp">
								<div class="graphBarLines" title='Klik untuk informasi lengkap' data-id='<?php echo $value['id']; ?>'>
									<span class="barLine"></span>
									<span class="barLine active" style="width: <?php echo $p ?>% ;"></span>
									<span class="barLineText"><?php echo $prog['value'].' '.default_date($prog['date']) ?></span>
								</div>
							</div>
						<?php } ?>
						<!-- <div class="graphBar clearfix">
							<div class="graphBarGroup clearfix barPengadaan" style="width: 100%;">
								<table width="100%">
									<tr class="graphWrap">
									<?php if(isset($value['progress'])){ ?>
										<?php foreach($value['progress'] as $key => $row){ ?>
											<td class="graph" title="<?php echo $row['value'];?> (Tanggal : <?php echo default_date($row['date'])?>) " style="width:<?php echo $row['percent'];?>%;background-color: <?php echo $row['color'];?>"></td>
										<?php } ?>
									<?php } ?>
									</tr>
								</table>
							</div>
						</div> -->
					</td>
				</tr>
				<tr>
					<!-- <td>
						<div class="graphBar clearfix">

							<?php 
							// echo print_r($value['data']);

							if(isset($value['graph']['data'])||isset($value['graph']['realization'])){

								 if(isset($value['graph']['realization'])){ ?>

								<div class="graphWrapLine" style="width: <?php echo $value['graph']['realization']['range'];?>%;">
									<div class="graph" title="Tanggal <?php echo default_date($value['graph']['realization']['date'])?>. Dalam masa <?php echo $value['graph']['realization']['step']?> pada hari ke-<?php echo $value['graph']['realization']['now']?>."  style="width: 100%;background-color: #2ecc71;"></div>
								</div>

								<?php }

								/*Buat graph progress*/

								if(isset($value['graph']['data'])){ ?>

									<div class="graphWrap">
										<?php 
										$i = 0;
										$amandemen = 0;
										foreach($value['graph']['data'] as $key => $row){ 
											?>
											<div class="graph <?php echo ($row['gap'])?'shading':'' ?>" title="<?php echo $row['label'];?> " style="width: <?php echo ($row['value']/ $value['graph']['total_day']*100);?>%;background-color: 
											<?php  
												if($row['type']==3){
											 		echo $color_amandemen[$amandemen];
											 		$amandemen++;
												}elseif($row['type']==4){
													echo '#8A9FD5';
												}elseif($row['type']==5){
													echo '#c0392b';
												}else{
													echo  $color[$i];
													$i++;
												} 
											?>"></div>
										<?php
											
										}
										
										?>
									</div>

								<?php } 

							} ?>
						</div>
					</td> -->
					
				</tr>
			<?php 
			}
		}else{?>
			<tr>
				<td colspan="11" class="noData">Data tidak ada</td>
			</tr>
		<?php }?>



		</tbody>
	</table>	
</div>

<div class="pageNumber">
	<?php echo $pagination ?>
</div>

<div class="filterWrapperOverlay"></div>
<!-- FILTER WRAPPER -->
<div class="filterWrapper">
	<div class="filterWrapperInner">
		<form method="POST">
			<?php echo $filter_list;?>
		</form>
	</div>
</div>
<!-- TIMELINE WRAPPER -->
<div class="timelineWrapper">
	<div class="timelineWrapperInner">
		<div class="timelineHeader">
			<h2>Progress Pengadaan</h2> 
			<span class="editBtn filterBtnClose iconOnly">
				<i class="fa fa-times"></i>
			</span>
		</div>
		<div class="timelineContent" id="progressStep">
			
		</div>
	</div>
</div>