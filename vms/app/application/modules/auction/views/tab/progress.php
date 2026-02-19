<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'submit');?>
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		<?php 	if($this->session->userdata('admin')['id_role']==4){ 
					if($status_procurement==4){ 
			?>
		<div class="btnTopGroup clearfix">
			<a href="<?php echo site_url('pengadaan/view/'.$id_pengadaan.'/tambah_progress');?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		</div>
		<?php 
					}
				} ?>
		<?php if(isset($graph['supposed']['percentage'])||isset($graph['realization']['percentage'])){ ?>
		<p>Grafik Progress</p>
		<div class="graphBar clearfix">

			<div class="graphBarGroup clearfix">
				<?php if(isset($graph['supposed']['percentage'])){ ?>
				<div class="graphWrap">
				<?php foreach($graph['supposed']['percentage'] as $key => $row){
						?>
						<div class="graph" style="width: <?php echo $row;?>%;background: <?php echo 'rgba('.$graph['supposed']['color'][$key].',0.5)';?>;border-left-color: <?php echo 'rgba('.$graph['supposed']['color'][$key].',1)'?>;border-right-color: <?php echo 'rgba('.$graph['supposed']['color'][$key].',1)';?>"></div>
						<?php
					}?>
				</div>
				<?php } 
				 if(isset($graph['realization']['percentage'])){ ?>
				<div class="graphWrapLine">
					<?php foreach($graph['realization']['percentage'] as $key => $row){
						?>
						<div class="graph" style="width: <?php echo $row;?>%;background: <?php echo 'rgba('.$graph['realization']['color'][$key].',1)'?>;border-left-color: <?php echo 'rgba('.$graph['realization']['color'][$key].',1)'?>;border-right-color: <?php echo 'rgba('.$graph['realization']['color'][$key].',1)'?>"></div>
						<?php
					}?>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		
		
		<table class="tableData">
			<thead>
				<tr>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['step_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=step_name">Tahap Pengadaan<i class="fa fa-sort-<?php echo ($sort['step_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['supposed'] == 'asc') ? 'desc' : 'asc'; ?>&by=supposed">Waktu yang ditetapkan<i class="fa fa-sort-<?php echo ($sort['supposed'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['realization'] == 'asc') ? 'desc' : 'asc'; ?>&by=realization">Waktu Pekerjaan<i class="fa fa-sort-<?php echo ($sort['realization'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					
				</tr>
			</thead>
			<tbody>
			<?php 
			$total_supposed = 0;
			$total_realization = 0;

			if(count($contract)){
				$key = 0;
				foreach($contract as $row => $value){
				?>
					<tr>
						<td>
						<div class="colorBox" style="background: <?php echo 'rgba('.implode(',',$this->config->item('color')[$key]).',1)'?>;">
						</div>
						<?php echo $value['step_name'];?></td>
						<td><?php echo $value['supposed'];?> Hari Kerja</td>
						<td><?php echo $value['realization'];?> Hari Kerja</td>
					</tr>
				<?php 

				$total_supposed+=$value['supposed'];
				$total_realization+=$value['realization'];
				$key++;
				}
			}else{?>
				<tr>
					<td colspan="4" class="noData">Data tidak ada</td>
				</tr>
			<?php }
			?>
				<tr>
					<td >Total :</td>
					<td ><?php echo $total_supposed; ?> Hari Kerja</td>
					<td ><?php echo $total_realization; ?> Hari Kerja</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>