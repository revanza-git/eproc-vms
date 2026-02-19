<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'barang');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px">
	
	<?php echo $this->session->flashdata('msgSuccess')?>
	<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
	<div class="btnTopGroup clearfix">
	<a href="<?php echo site_url('pengadaan/tambah_barang/'.$id);?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a>
	</div>
	<?php } ?>
		<table class="tableData">
			<thead>
				<tr>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['nama_barang'] == 'asc') ? 'desc' : 'asc'; ?>&by=nama_barang">Barang/Jasa<i class="fa fa-sort-<?php echo ($sort['nama_barang'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['id_kurs'] == 'asc') ? 'desc' : 'asc'; ?>&by=id_kurs">Mata Uang<i class="fa fa-sort-<?php echo ($sort['id_kurs'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['nilai_hps'] == 'asc') ? 'desc' : 'asc'; ?>&by=nilai_hps">Harga Satuan<i class="fa fa-sort-<?php echo ($sort['nilai_hps'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<?php if($this->session->userdata('admin')['id_role']==6|3){ ?><td class="actionPanel">Action</td><?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($list)){
				foreach($list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['nama_barang'];?></td>
						<td><?php echo $value['symbol'];?></td>
						<td><?php echo number_format($value['nilai_hps']);?></td>
						<?php if($this->session->userdata('admin')['id_role']==6||$this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
						<td class="actionBlock">
							<a href="<?php echo site_url('pengadaan/edit_barang/'.$value['id'].'/'.$id)?>"><i class="fa fa-cog"></i>&nbsp;Ubah</a> | 
							<a href="<?php echo site_url('pengadaan/hapus_barang/'.$value['id'].'/'.$id)?>" class="delBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
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