<div class="tab procView">
	<form method="POST">
	<?php echo $this->utility->tabNav($tabNav,'penawaran');?>
	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php echo $this->session->flashdata('msgSuccess')?>
		
		<table class="tableData">
			<thead>
				<tr>
					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['peserta_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=peserta_name">Peserta<i class="fa fa-sort-<?php echo ($sort['peserta_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
                    <td>Nilai Penawaran</td>
                    <td>Nilai Fee</td>
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
						<td><?php if($act=='edit'){?>
								<input type="hidden" name="id[<?php echo $value['id']?>]">
								<p> Nilai Evaluasi. <input type="text" name="nilai_evaluasi[<?php echo $value['id']?>]" value="<?php echo $value['nilai_evaluasi'];?>"></p>
								<p> Rp.<input type="text" name="idr_value[<?php echo $value['id']?>]" class="money-masked" value="<?php echo $value['idr_value'];?>"></p>
                                <?php echo $this->form->get_kurs(array('name'=>'id_kurs['.$value['id'].']'),$value['id_kurs'])?>
                                <br>
								<input type="text" name="kurs_value[<?php echo $value['id']?>]" value="<?php echo $value['kurs_value'];?>" class="money-masked" >
								<br>
								<input placeholder="Keterangan" type="text" name="remark[<?php echo $value['id']?>]" value="<?php echo $value['remark'];?>">
							<?php } else{ ?>

								<p> Nilai Evaluasi : <?php echo isset($value['nilai_evaluasi']) ? $value['nilai_evaluasi'] : '-';?></p>
								<p> Rp. <?php echo number_format($value['idr_value']);?></p>
								<p> <?php echo $value['symbol']?> <?php echo number_format($value['kurs_value']);?></p>
								<p> <?php echo ($value['remark']);?></p>

							<?php } ?>
                        </td>
                        <td>
                            <?php if($act=='edit'){?>
                                <p> <input type="text" name="fee[<?php echo $value['id']?>]" value="<?php echo $value['fee'];?>" placeholder="nilai fee dalam IDR"></p>
                            <?php } else{ ?>
								<p> <?php echo ($value['fee']);?></p>
							<?php } ?>
                        </td>
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
		<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
			<?php if($act=='edit'){?>
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			</div>
			<?php } else{ ?>
			<div class="buttonRegBox clearfix">
				<a href="<?php echo site_url('pengadaan/view/'.$id.'/penawaran/edit#tabNav') ?>" class="btnBlue"><i class="fa fa-cog"></i>&nbsp;Masukan Penawaran</a>
			</div>
			<?php } ?>
		<?php } ?>
		<div class="pageNumber">
			<?php echo $pagination ?>
		</div>
	</div>
	</form>
</div>