<div class="tab procView">
	<form method="POST">
	<?php echo $this->utility->tabNav($tabNav,'negosiasi');?>
	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php echo $this->session->flashdata('msgSuccess')?>
		
		<table class="tableData">
			<thead>
				<tr>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['peserta_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=peserta_name">Peserta<i class="fa fa-sort-<?php echo ($sort['peserta_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
                    <td>Negosiasi Ke-</td>
                    <td>Nilai Negosiasi</td>                    
			  		<td>Nilai Fee</td>
					<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?><td class="actionPanel">Action</td><?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			# print_r($list);
			if(count($list) > 0){
				foreach($list as $peserta_name => $values){ 
					foreach ($values as $row => $value) {
			?>
					<tr>
						<td><?php echo $peserta_name;?></td>
						<td>ke-<?php echo $row+1;?></td>
						<td><?php if($act=='edit'){?>
								Negosiasi: <input type="text" name="negosiasi[<?php echo $value['id_vendor']?>]" value="<?php echo $value['value'];?>" class="money-masked" >
								<Br>
								Keterangan: <input type="text" name="remark[<?php echo $value['id_vendor']?>]" value="<?php echo $value['remark'];?>"  >
                                <input type="hidden" name="id_proc" value="<?php echo $value['id_proc'];?>">
								<br>
							<?php } else{ ?>
								Negosiasi: <p> Rp. <?php echo number_format($value['value']);?></p>
								Remark: <p> <?php echo ($value['remark']);?></p>
							<?php } ?>
						</td>
						<td>
                            <?php if($act=='edit'){?>
                                <p> <input type="text" name="fee[<?php echo $value['id_vendor']?>]" value="<?php echo $value['fee'];?>" placeholder="nilai fee dalam IDR"></p>
                            <?php } else{ ?>
								<p> <?php echo ($value['fee']);?></p>
							<?php } ?>
                        </td>
						<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
						<td class="actionBlock">
							<a href="<?php echo site_url('pengadaan/hapus_negosiasi/'.$value['id'].'/'.$id)?>" class="delBtn"><i class="fa fa-trash"></i>&nbsp;Hapus</a>
						</td>
						<?php } ?>
					</tr>
				<?php 
				}
			}
			}else{?>
				<tr>
					<td colspan="11" class="noData">Data tidak ada</td>
				</tr>
			<?php }
			?>
			</tbody>
		</table>
		<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
			<?php if($act=='edit'){?>
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			</div>
			<?php } else{ ?>
			<div class="buttonRegBox clearfix">
				<a href="<?php echo site_url('pengadaan/tambah_negosiasi/'.$id) ?>" class="btnBlue"><i class="fa fa-cog"></i>&nbsp;Masukan negosiasi</a>
			</div>
			<?php } ?>
		<?php } ?>
		<div class="pageNumber">
			<?php echo $pagination ?>
		</div>
	</div>
	</form>
</div>