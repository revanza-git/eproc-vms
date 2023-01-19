<?php echo $this->session->flashdata('msgSuccess')?>

<?php echo $this->session->flashdata('msgError')?>

<?php //echo ($this->session->userdata('admin')['id_role']);?>

<div class="clearfix">

	<div class="compareHolder clearfix">

		<div class="compareButton">

			<a class="filterBtn">

				<i class="fa fa-list"></i>&nbsp;<?php echo $category ?> Pembanding ( <?php echo count($compare)?> Unit )&nbsp;<i class="fa fa-angle-down"></i>

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

	<h1 style="text-transform: capitalize;">Katalog <?php echo $category?></h1>



		<?php if($this->session->userdata('admin')['id_role']==1){?>

		<a href="<?php echo site_url('katalog/tambah/'.$category);?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah</a>

		<?php }?>

			<form method="GET">

				<button type="submit" class="btnBlue" style="margin: 0 !important; height:32px; min-width:35px !important;"><i class='fa fa-search' style='line-height: 30px; font-size:17px;'></i></button>

				<input class="customSearch" type="text" name="q" placeholder="Cari disini..." value="<?php echo $this->input->get('q')?>">

			</form>

	</div>

<div class="tableWrapper">

		<div>

			<div class="itemContainer clearfix">

				<?php foreach ($katalog as $key => $value) { /*print_r($value);*/?>

				

				<div class="item">

					<div class="itemWrapper">

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

						<div class="btmItem">

							<div class="name" style="text-transform: capitalize;">

								<a href="<?php echo site_url('katalog/view/'.$category.'/'.$value['id_materials'])?>"><?php echo $value['nama']?></a>

							</div>

							<div class="price">

								<?php echo $value['kurs_name'].' '.number_format($value['last_price']); ?>

							</div>

							<?php if($this->session->userdata('admin')['id_role']==3 || $this->session->userdata('admin')['id_role']==6 || $this->session->userdata('admin')['id_role']==1){?>

							<div class="btn">

								<?php if(!isset($compare[$value['id_materials']])){ ?>

								<a href="<?php echo site_url('katalog/compare/'.$category.'/'.$value['id_materials']);?>"><i class="fa fa-clone"></i></a> &nbsp; 

								<?php } else{ ?> 

								<!-- <a href="<?php echo site_url('katalog/compare/'.$category.'/'.$value['id_materials']);?>"><i class="fa fa-clone"></i></a> &nbsp;  -->

								<?php } ?>
								<?php if($this->session->userdata('admin')['id_role']==1){ ?>
								<a href="<?php echo site_url('katalog/edit_barang/'.$category.'/'.$value['id_materials']);?>"><i class="fa fa-cog"></i></a> &nbsp; 

								<a href="<?php echo site_url('katalog/hapus_barang/'.$category.'/'.$value['id_materials']);?>" class="delBtn"><i class="fa fa-trash"></i></a>
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

</div>	