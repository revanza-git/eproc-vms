<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Daftar Penyedia Barang / Jasa Terdaftar</h2>
<div class="tableWrapper">
	<div class="tableHeader">
		<a style="background: #2f3640 !important; border-bottom: 2px #718093 solid !important;" href="<?php echo site_url('admin/admin_dpt/export_excel');?>" class="btnBlue"><i class="fa fa-download"></i> Export</a>
		<form method="POST">
			<?php echo $filter_list;?>
		</form>	
	</div>

	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Penyedia Barang/Jasa<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
                <td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['category'] == 'asc') ? 'desc' : 'asc'; ?>&by=category">Kategori<i class="fa fa-sort-<?php echo ($sort['category'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['npwp_code'] == 'asc') ? 'desc' : 'asc'; ?>&by=npwp_code">NPWP<i class="fa fa-sort-<?php echo ($sort['vendor_address'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['vendor_address'] == 'asc') ? 'desc' : 'asc'; ?>&by=vendor_address">Alamat<i class="fa fa-sort-<?php echo ($sort['vendor_phone'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['vendor_phone'] == 'asc') ? 'desc' : 'asc'; ?>&by=vendor_phone">Telepon<i class="fa fa-sort-<?php echo ($sort['vendor_phone'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
                <?php //if($this->session->userdata('admin')['id_role']!=9){?>
				<td >Action</td>
				<?php //}?>
				
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($vendor_list)){
			// print_r($vendor_list);die;
			foreach($vendor_list as $row => $value){
			?>
				<tr>
					<td style="text-transform: uppercase;"><a href="<?php echo site_url('/approval/administrasi/'.$value['id'])?>" class="editBtn">
 							<?php echo $value['legal'].". ".$value['name'];?>
 						</a>
 					</td>
					<td>
						<?php
							foreach ($csms_limit as $key_ => $value_) {
								# code...
								if ($value['score'] > $value_['end_score'] && $value['score'] < $value_['start_score']) {
									# code...
									echo $value_['value'];
								}
							}
						?>
					</td>
					<td><?php echo $value['npwp_code'];?></td>
					<td><?php echo $value['vendor_address'];?></td>
					<td><?php echo $value['vendor_phone'];?></td>
					
					<td class="actionBlock">
						<a href="<?php echo site_url('vendor/dpt_print/'.$value['id'])?>" class="editBtn">
							<i class="fa fa-search"></i>&nbsp;Lihat Data
 						</a> 
 						<?php if($this->session->userdata('admin')['id_role']!=9){?>

 						<?php if($this->session->userdata('admin')['id_role']==1){?>
 						| 
							<a href="#" no="<?php echo $value['certificate_no'];?>" id_vendor="<?php echo $value['id'];?>" class="editBtn certificateBtn"><i class="fa fa-cog"></i>&nbsp;Edit Nomor Sertifikat</button>
						
 						<?php }?>
 						<?php if($this->session->userdata('admin')['id_role']==1 || $this->session->userdata('admin')['id_role']==8){?>
 						| 
						<a data-id="<?php echo $value['id'];?>" href="<?php echo site_url('admin/certificate/dpt/'.$value['id'].'/')?>" class="editBtn">
							<i class="fa fa-print"></i>&nbsp;Print Sertifikat
						</a>
						<?php } ?>

						<!--  |  
						<a data-id="<?php echo $value['id'];?>" class="showCSMS" href="#" class="editBtn">
							<i class="fa fa-print"></i>&nbsp;Print CSMS
						</a>-->
						<?php }?>
					</td>				
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