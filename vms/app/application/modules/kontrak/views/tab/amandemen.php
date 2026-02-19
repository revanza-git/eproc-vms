<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'amandemen');?>

	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php echo $this->session->flashdata('msgSuccess')?>
	<?php if ($count_contract > 0) { ?>
		<?php if($this->session->userdata('admin')['id_role']==3){ ?>
		<div class="btnTopGroup clearfix">
		<a href="<?php echo site_url('kontrak/tambah_amandemen/'.$id);?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		</div>
		<legend>
		<p>Keterangan :</p>
		<p><font color="#27ae60">&#8718; Kontrak</font></p>
		<p><font color="#7f8c8d">&#8718; Jangka Waktu Pelaksanaan Pekerjaan</font></p>
		<p><font color="#ddd">&#8718; </font>Jarak</p>
		<p><font color="#54a0ff">&#8718; Amandemen</font></p>
		<p><font color="#7B68EE	">&#8718; Bast</font></p>
		<p><font color="#e74c3c">&#8718; Denda</font></p>
	</legend>
	<div class="graphBarWp">
	
		<div style="margin-bottom: 5px" class="graphBarLinesPekerjaan" title='Klik untuk informasi lengkap' data-id='<?php echo $value['id']; ?>'>
			<?php 
			
			$last_key = end(array_keys($graph['data']));
			foreach ($graph['data'] as $key => $value) { 
				// print_r($value);die;
				if ($value['type'] == 6 && in_array("3", $value['type'])) {
					$width = 'calc(50% - 5px)';
				} else if ($value['type'] == 6 && !in_array("3", $value['type'])) {
					$width = 'calc(100% - 5px)';
				} else {
					$width = 'calc(50% - 5px)';
				}
				
				if ($value['type'] == '6') {
					$class = 'active';
				} else if ($value['type'] == '4') {
					$class = 'denda';
				} else if ($value['type'] == '5') {
					$class = 'bast';
				}else if ($value['type'] == '3') {
					$class = 'amandemen';
				} else if ($value['type'] == '2') {
					$class = 'abutua';
				} else if ($value['type'] == '1') {
					$class = '';
				}
			?>
				<?php if($key == $last_key && $value['type'] == '5'){ ?>
					<span class="barLine <?php echo $class; ?>" style="width:<?php echo $width; ?>;" title="<?php echo $value['label']; ?>"></span>
				<?php } elseif ($key != $last_key && $value['type'] == '5') { ?>
					<span class="barLine <?php echo $class; ?>" style="width:<?php echo $width; ?>;" title="<?php echo $value['label']; ?>"></span>

					<span class="barLine" style="width:calc(50% - 5px);" title=""></span>
				<?php }  else { ?>
					<span class="barLine <?php echo $class; ?>" style="width:<?php echo $width; ?>;" title="<?php echo $value['label']; ?>"></span>
				<?php } ?>

			<?php } ?>			
				
		</div>
	</div>
		<?php } ?>
	<?php } else { ?>
		<p class="noticeMsg">Isi kontrak terlebih dahulu!</p>
	<?php } ?>
		<table class="tableData">
			<thead>
				<tr>
					<td>Nomor Amandemen</td>

					<td>Tanggal Amandemen</td>

					<td>Dokumen Amandemen</td>
					<?php if($this->session->userdata('admin')['id_role']==3){ ?>
						<td class="actionPanel">Action</td>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($list)){
				foreach($list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['no'];?></td>
						<td><?php echo default_date($value['start_date']);?> - <?php echo default_date($value['end_date']);?></td>
						<td>
							<?php 
								if ($value['amandemen_file'] == '' || $value['amandemen_file'] == null) {
									$file = '-';
								} else{
									$file = '<a href="'.site_url('lampiran/amandemen_file/'.$value['amandemen_file']).'" target="blank">'.$value['amandemen_file'].'</a>';
								}
								echo $file;
							?>
						</td>
						<?php if($this->session->userdata('admin')['id_role']==3){ ?>
						<td class="actionBlock">
							<a href="<?php echo site_url('kontrak/edit_amandemen/'.$value['id'].'/'.$id)?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>

							<a style="top: -4px" href="<?php echo site_url('kontrak/hapus_amandemen/'.$value['id'].'/'.$id)?>" class="delBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
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