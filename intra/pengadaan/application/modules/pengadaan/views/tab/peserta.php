<div class="tab procView">
	<form>
	<?php echo $this->utility->tabNav($tabNav,'peserta');?>
	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php echo $this->session->flashdata('msgSuccess')?>
	
	<?php if($this->pm->get_bsb_procurement($id)->num_rows()>0){ ?>
		<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
		<div class="btnTopGroup clearfix">
			<a href="<?php echo site_url('pengadaan/tambah_peserta/'.$id);?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
		</div>
		<?php } ?>
	<?php }else{ ?>
		<p class="noticeMsg">Pilih Bidang & Sub Bidang terlebih dahulu</p>
	<?php } ?>
		<table class="tableData">
			<thead>
				<tr>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['peserta_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=peserta_name">Peserta<i class="fa fa-sort-<?php echo ($sort['peserta_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
					<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?><td class="actionPanel">Action</td><?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			if(count($list)){
				foreach($list as $row => $value){
				?>
					<tr>
						<td><?php echo $value['peserta_name'];?></td>
						
						<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
						<td class="actionBlock">
							<a href="<?php echo site_url('pengadaan/hapus_peserta/'.$value['id'].'/'.$id)?>" class="delBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
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
	</form>
</div>