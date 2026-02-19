<?php echo $this->session->flashdata('msgSuccess')?>

<?php echo $this->session->flashdata('msgError')?>

<?php //echo ($this->session->userdata('admin')['id_role']);?>

<div class="clearfix">

	<div class="compareHolder clearfix">

		<div class="compareButton">

			<a class="filterBtn">

				<i class="fa fa-list"></i>&nbsp;<?php echo $category ?> Pembanding ( <?php echo (is_array($compare) || $compare instanceof Countable) ? count($compare) : 0 ?> Unit )&nbsp;<i class="fa fa-angle-down"></i>

			</a>

		</div>

		<div class="compareWrapper">

			

			<div class="compareList">

			<?php if (!empty($barang_compare)){?>

				<ul>

					

						<?php foreach($barang_compare as $key => $row) { /*print_r($row);*/?>

						<!-- <a href="<?php echo site_url('katalog/view/'.$category.'/'.$row['id'])?>"> -->

						<li>

							<div class="clearfix">

								<div class="image">

									<img src="<?php 

									if($row['gambar_barang']!=''){

										echo base_url('lampiran/gambar_barang/'.$row['gambar_barang']);

									}else{

										echo base_url('assets/images/default-img.png');

									}

									?>">

								</div>

								<div class="compareCaption">

									<div class="compareTitle">

										<?php echo $row['nama'] ?>

										<a href="<?php echo site_url('katalog/remove_compare/'.$category.'/'.$row['id']);?>">

											<i style="color:red;" class="fa fa-times"></i>

										</a>

									

									</div>

									<!--<div class="comparePrice">

										 

									</div>-->

								</div>

							</div>

						</li>

						<!-- </a> -->

						<?php } ?>

				</ul>



				<div class="compareBtn clearfix" style="text-align:center;">

					<a href="<?php echo site_url('katalog/lihat_perbandingan/'.$category);?>">Lihat Perbandingan</a>

				</div>

						

					<?php }else{?>

				<ul>

						<li>

							<div class="clearfix">

								<div class="image">

								</div>

								<div class="compareCaption">

									<div class="compareTitle" style="text-align:center;">

										Tidak ada item dipilih

									</div>

								</div>

							</div>

						</li>

					<?php }?>

				</ul>

			</div>

		</div>

	</div>

</div>

	<div class="tableHeader">

	<h1 style="width: 100%; text-transform: capitalize;">Katalog <?php echo $category?></h1>



		

			<form method="GET" style="display: flex; align-items: center; justify-content: flex-end">

				<input style="border-radius: 4px 0 0 4px; border: 1.4px solid #3273dc" class="customSearch" type="text" name="q" placeholder="Cari disini..." value="<?php echo $this->input->get('q')?>">
				<button type="submit" class="btnBlue iconOnly" style="margin: 0 !important; padding-top: 1px; padding-bottom: 1px; border-radius: 0 4px 4px 0; bottom: -2px"><i class='fa fa-search' style='line-height: 30px; font-size:17px;'></i></button>

			</form>
			<?php if($this->session->userdata('admin')['id_role']==1){?>
			
			<div class="filterBtnWp">
				<a style="/*width : 200px; top: 4px;*/ margin-left: 140px" href="<?php echo site_url('katalog/tambah/'.$category);?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>
				<button class="editBtn lihatData filterBtn">Filter</button>
			</div>
			<?php }?>
	</div>

<div class="tableWrapper">

		<div>

			<div class="itemContainer clearfix">

				<?php foreach ($katalog as $key => $value) { /*print_r($value);*/?>

				

				<div class="item">

					<div class="itemWrapper" style="padding: 15px 10px; height: 360px; position: relative; border-radius: 8px; box-shadow: 0 2px 3px rgba(0,0,0,0.2);">

						<div class="topItem">

							<div class="image">

								<img src="<?php 

								if($value['gambar_barang']!=''){

									echo base_url('lampiran/gambar_barang/'.$value['gambar_barang']);

								}else{

									echo base_url('assets/images/default-img.png');

								}

								?>">

							</div>

						</div>

						<div class="btmItem" style="padding: 10px">

							<div class="name" style="text-transform: capitalize;">

								<a href="<?php echo site_url('katalog/view/'.$category.'/'.$value['id_materials'])?>"><?php echo $value['nama']?></a>

							</div>

							<div class="price">

								<?php
									if ($value['for'] == '1') {
										echo "<b>Divisi Operasi</b>"; 
									} else if ($value['for'] == '10') {
										echo "<b>Divisi Reliability&Quality</b>"; 
									} else if ($value['for'] == '18') {
										echo "<b>Departemen Layanan Umum</b>"; 
									} else if ($value['for'] == '15') {
										echo "<b>Seksi Sistem Informasi</b>"; 
									} else if ($value['for'] == '3') {
										echo "<b>Departemen Sekretaris Perusahaan</b>"; 
									} else if ($value['for'] == '5') {
										echo "<b>Departemen HSSE</b>"; 
									} else if ($value['for'] == '6') {
										echo "<b>Jasa Konsultasi</b>"; 
									} else if ($value['for'] == '7') {
										echo "<b>Jasa Konstruksi</b>"; 
									} else if ($value['for'] == '8') {
										echo "<b>Jasa Lainnya</b>"; 
									} 
								?>

							</div>

							<div class="price">

								<?php echo $value['kurs_name'].' '.number_format($value['last_price']); ?>

							</div>

							<?php if($this->session->userdata('admin')['id_role']==3 || $this->session->userdata('admin')['id_role']==6 || $this->session->userdata('admin')['id_role']==1){?>

							<div class="btn" style="position: absolute; bottom: 0; right: 10px;">

								<?php if(!isset($compare[$value['id_materials']])){ ?>

								<a href="<?php echo site_url('katalog/compare/'.$category.'/'.$value['id_materials']);?>"><i class="fa fa-clone"></i></a> &nbsp; 

								<?php } else{ ?> 

								<!-- <a href="<?php echo site_url('katalog/compare/'.$category.'/'.$value['id_materials']);?>"><i class="fa fa-clone"></i></a> &nbsp;  -->

								<?php } ?>
								<?php if($this->session->userdata('admin')['id_role']==1){ ?>
								<a href="<?php echo site_url('katalog/edit_barang/'.$category.'/'.$value['id_materials']);?>"><i class="fa fa-cog"></i></a> &nbsp; 

								<a href="<?php echo site_url('katalog/hapus_barang/'.$category.'/'.$value['id_materials']);?>" class="delBtn iconOnly"><i class="fa fa-trash"></i></a>
								<?php } ?>
							</div>

							<?php }else{?>

							<div class="btn">

								<?php if(!isset($compare[$value['id_materials']])){ ?>

								<a href="<?php echo site_url('katalog/compare/'.$category.'/'.$value['id_materials']);?>"><i class="fa fa-clone"></i></a> &nbsp; 

								<?php } ?>

							</div>

							<?php } ?>

						</div>

						<!-- <form method="POST">

							<input type="checkbox" name="idCompare" value="<?php echo $value['id_materials']; ?>">Pilih Untuk Compare

							<input type="submit" name="btnCompare" value="Compare">

						</form>-->

					</div>

				</div>

				

				<?php } ?>



			</div>

			<div class='pageNumber'>

				<?php echo $pagination; ?>

			</div>


		</div>

	</div>
<div class="filterWrapperOverlay"></div>
<div class="filterWrapper">
	<form method="POST">
		<?php echo $filter_list;?>
	</form>
</div>
</div>
