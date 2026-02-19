<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'assessment');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px">
	
		<table class="tableData">
			<thead>
				<tr>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['peserta_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=peserta_name">Peserta<i class="fa fa-sort-<?php echo ($sort['peserta_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>&by=point">Nilai<i class="fa fa-sort-<?php echo ($sort['point'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<?php if($this->session->userdata('admin')['id_role']==3){ ?><td class="actionPanel">Action</td><?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			// echo print_r($list);
			if(count($list)>0){

				foreach($list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['peserta_name'];?></td>
						<td><?php echo (isset($value['point']))?$value['point']:'-';?></td>
						<?php if($this->session->userdata('admin')['id_role']==3){ ?>
						<td class="actionBlock">
							<a href="<?php echo site_url('pengadaan/view/'.$id.'/form_assessment/'.$value['id_vendor'])?>"><i class="fa fa-check-square-o"></i>&nbsp;Penilaian</a>
						</td>
						<?php } ?>
					</tr>
				<?php 
				}
			}else{?>
				<tr>
					<td colspan="11" class="noData">Data tidak ada</td>
				</tr>
			<?php }
			?>
			</tbody>
		</table>
		<div class="pageNumber">
			<?php echo $pagination ?>
		</div>
	</div>
</div>