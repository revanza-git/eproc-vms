<div class="tab procView">
	<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==9){ 
	 echo $this->utility->tabNav($tabNav,'progress_pengerjaan');
	 } ?>
	<div class="tableWrapper">
		<div class="tab procView">
		
			<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
				<?php echo $this->session->flashdata('msgSuccess')?>
								
				<p>Grafik Progress</p>
				<div class="graphBar clearfix">
					<?php 

						 if(isset($realization)){ ?>
						<div class="graphWrapLine" style="width: <?php echo $realization['range'];?>%;">
								<div class="graph" title="Tanggal <?php echo default_date($realization['date'])?>. <br>Dalam Masa <?php echo $realization['step']?> pada hari ke-<?php echo $realization['now']?>. "  style="width: 100%;background-color: #2ecc71;"></div>
						</div>
						<?php } ?>
						
						<?php 
						/*Buat graph progress*/
						if(isset($graph['data'])){ ?>
							<div class="graphWrap">
								<?php 
								$i = 0;
								$amandemen = 0;
								foreach($graph['data'] as $key => $row){ ?>
									<div class="graph <?php echo($row['gap'])?'shading':'' ?>" title="<?php echo $row['label'];?> " style="width: <?php echo ($row['value']/$graph['total_day']*100);?>%;background-color: 

										<?php  if($row['type']==3){
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
										?>">
									</div>
								<?php

									
								}
								
								?>
							</div>
						<?php } 

						?>

				</div>
				
				
				<table class="tableData">
					<thead>
						<tr>
							<td>Tahap Pekerjaan</td>
							<td>Waktu yang ditetapkan</td>
							<?php if($this->session->userdata('admin')['id_role']==3){ 
								?>
								<td>Action</td>
								<?php
							} ?>
							
						</tr>
					</thead>
					<tbody>
					<?php 
					
					// print_r($contract);
					if(count($contract)){
						$key = 0;
						$amandemen = 0;
						foreach($contract as $row => $value){
						?>
							<tr>
								<td>
									<?php echo $value['step_name'];?>
								</td>
								<td>
									<div class="colorBox" style="background-color: <?php echo ($value['type']==3)? $color_amandemen[$amandemen] : $color[$key]; ?>;">
									</div>
									<?php if($key==1){ ?>
									<div class="colorBox shading" style="border: 1px solid #cdcdcd">
									</div>
									<?php } ?>
									<?php echo get_range_date($value['end_date'],$value['start_date']);?> Hari Kalendar
								</td>
								
								<?php if($this->session->userdata('admin')['id_role']==3){ ?>
								<td>
									<?php if($value['type']==3){ ?>
									<a href="<?php echo site_url('pengadaan/view/'.$id.'/amandemen/'.$value['id']);?>"><i class="fa fa-cog"></i> Edit</a> | 
									<a href="<?php echo site_url('pengadaan/hapus_amandemen/'.$id.'/'.$value['id']);?>"><i class="fa fa-trash"></i> Hapus</a>
									<?php 
									
									} ?>
								</td>
								<?php } ?>
							</tr>
						<?php 
							if($value['type']==3) $amandemen++;
							$key++;
						}
						if($denda_day>0){
						?>
						<tr>
							<td>Denda</td>
							<td>
								<div class="colorBox" style="background-color: #c0392b;">
									</div>
								<?php echo $denda_day;?> Hari Kalendar
							</td>
							<td>
								<?php echo $denda_price;?>
							</td>
						</tr>
						<?php } 
					}else{

						?>
						<tr>
							<td colspan="3" class="noData">Data tidak ada</td>
						</tr>
					<?php }
					?>	
						
					</tbody>
				</table>
				<?php 
				if($this->session->userdata('admin')['id_role']==3&&$kontrak_data['end_actual']==null){ ?>
				<div class="buttonRegBox clearfix">
				
					<a href="<?php echo site_url('pengadaan/view/'.$id_pengadaan.'/denda');?>" class="btnBlue"><i class="fa fa-exclamation-triangle"></i> Denda</a>
					<?php if($denda_price!='') { ?>
					<a href="<?php echo site_url('pengadaan/view/'.$id_pengadaan.'/reset_denda');?>" class="btnBlue"><i class="fa fa-exclamation-triangle"></i> Reset Denda</a>
					<?php } ?>
					<?php if($denda_price=='') { ?>
					<a href="<?php echo site_url('pengadaan/view/'.$id_pengadaan.'/amandemen');?>" class="btnBlue"> Amandemen</a>
					<?php } ?>
					<a href="<?php echo site_url('pengadaan/stop_pengadaan/'.$id_pengadaan);?>" class="btnBlue"> Selesaikan Pengadaan</a>
				</div><?php } ?>
			</div>
		</div>
	</div>
</div>