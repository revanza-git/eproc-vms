<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'barang');?>
	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php echo $this->session->flashdata('msgSuccess')?>
	<?php if($this->session->userdata('admin')['id_role']==6||$this->session->userdata('admin')['id_role']==7){ ?>
	<div class="btnTopGroup clearfix">
		<?php 
			$show	= "";
			if(isset($list[0]['status'])){
				if ($list[0]['status']=="lump_sump" && count($list)) {
					$show="style='display:none;'";
				}
			}
		?>
		<a href="<?php echo site_url('auction/tambah_barang/'.$id);?>" class="btnBlue" <?php echo $show; ?>><i class="fa fa-plus"></i>Tambah</a>
	</div>
	<?php } ?>
		<table class="tableData">
			<thead>
				<tr>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['nama_barang'] == 'asc') ? 'desc' : 'asc'; ?>&by=nama_barang">Barang<i class="fa fa-sort-<?php echo ($sort['nama_barang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['ms_procurement_barang.id_kurs'] == 'asc') ? 'desc' : 'asc'; ?>&by=ms_procurement_barang.id_kurs">Mata Uang<i class="fa fa-sort-<?php echo ($sort['ms_procurement_barang.id_kurs'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['nilai_hps'] == 'asc') ? 'desc' : 'asc'; ?>&by=nilai_hps">Nilai Kontrak<i class="fa fa-sort-<?php echo ($sort['nilai_hps'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['volume'] == 'asc') ? 'desc' : 'asc'; ?>&by=volume">Volume<i class="fa fa-sort-<?php echo ($sort['volume'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<?php if($this->session->userdata('admin')['id_role']==6|7){ ?><td class="actionPanel">Action</td><?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($list)){
				foreach($list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['nama_barang'];?></td>
						<td><?php echo $value['kurs'];?></td>
						<td><?php echo number_format($value['nilai_hps']);?></td>
						<td><?php echo $value['volume'];?></td>
						<?php if($this->session->userdata('admin')['id_role']==6||$this->session->userdata('admin')['id_role']==7){ ?>
						<td class="actionBlock">
							<a href="<?php echo site_url('auction/edit_barang/'.$value['id'].'/'.$id)?>"><i class="fa fa-cog"></i>&nbsp;Edit</a> | 
							<a href="<?php echo site_url('auction/hapus_barang/'.$value['id'].'/'.$id)?>" class="delBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
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