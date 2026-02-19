<?php echo $this->session->flashdata('msgSuccess')?>

<div id="container-chart">

	

</div>



<div>

	<h2><?php echo $item["nama"];?></h2>

	<?php echo '<p>Keterangan : '.$item["remark"];?>

	<h3>Harga Satuan</h3>

    <div class="tableWrapper">

    	<div class="tableHeader">

    	

    		<?php if($this->session->userdata('admin')['id_role']==1){?>

			<a href="<?php echo site_url('katalog/tambah_harga/'.$category.'/'.$id);?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>

			<?php }?>

		</div>



		<table class="tableData">

			<thead>

				<tr>

					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['price'] == 'asc') ? 'desc' : 'asc'; ?>&by=price">Harga<i class="fa fa-sort-<?php echo ($sort['price'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['date'] == 'asc') ? 'desc' : 'asc'; ?>&by=date">Tanggal<i class="fa fa-sort-<?php echo ($sort['date'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

					<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['vendor_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=vendor_name">Nama Penyedia Barang/Jasa<i class="fa fa-sort-<?php echo ($sort['vendor_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

					<td class="actionPanel">Action</td>

				</tr>

			</thead>



			<tbody>

			<?php 

				if(count($list_harga)){

				foreach($list_harga as $key => $row){ ?>

					<tr>

						<td><?php echo $item['symbol'];?> <?php echo number_format($row['price']);?></td>

						<td><?php echo default_date($row['date']);?></td>

						<td><?php echo $row['vendor_name'];?></td>

						<td class="actionBlock">

							<?php if($this->session->userdata('admin')['id_role']==1){?>
							<a href="<?php echo site_url('katalog/edit_harga/'.$id.'/'.$row['id'].'/'.$category)?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>

							<a href="<?php echo site_url('katalog/hapus_harga/'.$category.'/'.$id.'/'.$row['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
							<?php }?>

						</td>

					</tr>

				<?php 

					}

				}else{?>

			<tr>

				<td colspan="11" class="noData">Data tidak ada</td>

			</tr>

			<?php } ?>

			</tbody>

		</table>

		

	</div>

</div>

<div class="pageNumber">

	<?php //echo $pagination ?>

</div>