<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'kontrak');?>

	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php echo $this->session->flashdata('msgSuccess')?>
	<?php if($this->session->userdata('admin')['id_role']==3){ ?>
	<div class="btnTopGroup clearfix">

		<?php if (count($list) == 0) { ?>
				<a href="<?php echo site_url('kontrak/tambah_kontrak/'.$id);?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		<?php } ?>
	</div>
	<?php } ?>

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
	
		<div style="margin-bottom: 5px" class="graphBarLinesPekerjaan" title='Klik untuk informasi lengkap' data-id='<?php echo isset($procurement_data['id']) ? $procurement_data['id'] : $id; ?>'>
			<?php 
					if (isset($graph['data']) && is_array($graph['data'])) {
			$graph_keys = array_keys($graph['data']);
			$last_key = end($graph_keys);
			foreach ($graph['data'] as $key => $graph_value) { 
			// print_r($graph_value);die;
			if ($graph_value['type'] == 6 && $graph_value['type'] == "3") {
				$width = 'calc(50% - 5px)';
			} else if ($graph_value['type'] == 6 && $graph_value['type'] != "3") {
				$width = 'calc(100% - 5px)';
			} else {
				$width = 'calc(50% - 5px)';
			}
				
				if ($graph_value['type'] == '6') {
					$class = 'active';
				} else if ($graph_value['type'] == '4') {
					$class = 'denda';
				} else if ($graph_value['type'] == '5') {
					$class = 'bast';
				}else if ($graph_value['type'] == '3') {
					$class = 'amandemen';
				} else if ($graph_value['type'] == '2') {
					$class = 'abutua';
				} else if ($graph_value['type'] == '1') {
					$class = '';
				}
			?>
				<?php if($key == $last_key && $graph_value['type'] == '5'){ ?>
					<span class="barLine <?php echo $class; ?>" style="width:<?php echo $width; ?>;" title="<?php echo isset($graph_value['label']) ? $graph_value['label'] : ''; ?>"></span>
				<?php } elseif ($key != $last_key && $graph_value['type'] == '5') { ?>
					<span class="barLine <?php echo $class; ?>" style="width:<?php echo $width; ?>;" title="<?php echo isset($graph_value['label']) ? $graph_value['label'] : ''; ?>"></span>

					<span class="barLine" style="width:calc(50% - 5px);" title=""></span>
				<?php }  else { ?>
					<span class="barLine <?php echo $class; ?>" style="width:<?php echo $width; ?>;" title="<?php echo isset($graph_value['label']) ? $graph_value['label'] : ''; ?>"></span>
				<?php } ?>

			<?php 
			} // end foreach
		} // end if isset($graph['data'])
		?>
		</div>
	</div>
	
		<table class="tableData">
			<thead>
				<tr>
					<td>Nama Perusahaan</td>

					<td>Nomor Kontrak</td>

					<td>Nilai Kontrak</td>

					<td>Tanggal Kontrak</td>

					<td>Dokumen Kontrak</td>

					<?php if($this->session->userdata('admin')['id_role']==3){ ?>
						<td class="actionPanel">Action</td>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($list)){
				foreach($list as $row => $value){
					// print_r($value);die;
				?>
					<tr>
						<td><?php echo $value['vendor_name'];?></td>
						<td><?php echo $value['no_contract'];?></td>
						<td><?php echo number_format($value['contract_price']);?></td>
						<td><?php echo default_date($value['start_contract']);?> - <?php echo default_date($value['end_contract']);?></td>
						<td>
							<?php 
								if ($value['po_file'] == '' || $value['po_file'] == null) {
									$file = '-';
								} else{
									$file = '<a href="'.site_url('lampiran/po_file/'.$value['po_file']).'" target="blank">'.$value['po_file'].'</a>';
								}
								echo $file;
							?>
						</td>
						<?php if($this->session->userdata('admin')['id_role']==3){ ?>
						<td class="actionBlock">
							<a href="<?php echo site_url('kontrak/edit_kontrak/'.$value['id'].'/'.$id)?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>

							<a style="top: -4px" href="<?php echo site_url('kontrak/hapus_kontrak/'.$value['id'].'/'.$id)?>" class="delBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
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