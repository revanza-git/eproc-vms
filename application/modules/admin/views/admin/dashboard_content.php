<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery.dataTables.css');?>">
<style>
</style>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js');?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#popup").hide();
		$("#popupCSMS").hide();
		$("#mask").hide();

	    $(".show").click(function() {
	        $("#popup").show();
			$("#mask").show();
			$("#popup #id").val($(this).data("id"));
	    });

	    $("#hide").click(function() {
	        $("#popup").hide();
			$("#mask").hide();
	    });

	    $(".showCSMS").click(function() {
	        $("#popupCSMS").show();
			$("#mask").show();
			$("#popupCSMS #idCSMS").val($(this).data("id"));
	    });

	    $("#hideCSMS").click(function() {
	        $("#popupCSMS").hide();
			$("#mask").hide();
	    });

	    $("#btn").click(function() {
	        $("#popup").hide();
			$("#mask").hide();
	    });

	    $("#btnCSMS").click(function() {
	        $("#popupCSMS").hide();
			$("#mask").hide();
	    });

	});
</script>
<style type="text/css">
	#mask{
		width: 100%;
		height: 100%;
		position: absolute;
		background: #fff;
		opacity: 0.5;
		z-index: 9999;
	}
	#popup{
		width: 250px;
		height: 130px;
		background: #fff;
		margin: auto;
		border: 2px #ddd solid;
		padding: 10px;
		top: 10%;
		left: 30%;
		position: absolute; 
		z-index: 99999;
	}
	
	#popup #text{
		width: 200px;
		height: 25px;
		border: none;
		border-bottom: 2px #c0392b solid;
		float: left;
	}
	#popup #btn{
		width: 50px;
		height: 29px;
		color: #fff;
		background: #c0392b;
		float: left;
		border: none;
		border-bottom: 2px #c0392e solid;
	}
	#popup #btn a{
		color: #fff;
	}
	#popupCSMS{
		width: 250px;
		height: 130px;
		background: #fff;
		margin: auto;
		border: 2px #ddd solid;
		padding: 10px;
		top: 10%;
		left: 30%;
		position: absolute; 
		z-index: 99999;
	}
	
	#popupCSMS #textCSMS{
		width: 200px;
		height: 25px;
		border: none;
		border-bottom: 2px #c0392b solid;
		float: left;
	}
	#popupCSMS #btnCSMS{
		width: 50px;
		height: 29px;
		color: #fff;
		background: #c0392b;
		float: left;
		border: none;
		border-bottom: 2px #c0392e solid;
	}
	#popupCSMS #btnCSMS a{
		color: #fff;
	}
</style>



<div id='mask'></div>
<div id='popup'>
    <a href="#" id="hide" style="text-align:right; display:block; width:100%;">
    	<i style="color:red; margin:5px;" class="fa fa-times"></i>
    </a>
	<h3>Nomor Sertifikat DPT</h3>
	<form method="post" enctype="multipart/form-data">
	    <input id="id" type="hidden" name="id" value="">
	    <input id="text" type="text" name="nomor" placeholder="masukkan disini...">

	    <button type="submit" id="btn" name="nomorBtn">
	    	OK
	    </button>
	</form>
</div>

<div id='popupCSMS'>
    <a href="#" id="hideCSMS" style="text-align:right; display:block; width:100%;">
    	<i style="color:red; margin:5px;" class="fa fa-times"></i>
    </a>
	<h3>Nomor Sertifikat CSMS</h3>
	<form method="post" enctype="multipart/form-data">
	    <input id="idCSMS" type="hidden" name="idCSMS" value="">
	    <input id="textCSMS" type="text" name="nomorCSMS" placeholder="masukkan disini...">

	    <button type="submit" id="btnCSMS" name="nomorBtnCSMS">
	    	OK
	    </button>
	</form>
</div>


<h1>Selamat Datang, <?php echo $this->session->userdata('admin')['role_name'];?></h1>
<div class="dataWrapper  col-24" style="height: 648px">
	<div id="container-charts">

	</div>
</div>

<?php //print_r($this->session->userdata('admin')); ?>

<?php 
	if($this->session->userdata('admin')['id_role']!=9 && $this->session->userdata('admin')['id_role']==2){
	if(count($chart['daftar_tunggu_chart']->result_array())){ ?>

	<?php if($this->session->userdata('admin')['id_role']!= 3){?>
		<div class="dataWrapper col-24" style="height: 648px">
			
			<div class="block">
			    <h4>Daftar Tunggu Penyedia Jasa</h4>
			   	<div class="tableWrapper">
					<table class="tableData">
						<thead>
							<tr>
								<td>Nama Penyedia Barang & Jasa</td>
								<td class="actionPanel">Action</td>
							</tr>
						</thead>
						<tbody>
						<?php 
						if(count($chart['daftar_tunggu_chart']->result_array())){
							foreach($chart['daftar_tunggu_chart']->result_array() as $row => $value){
							?>
								<form method="POST" action="<?php echo site_url('admin/admin_vendor/waiting_list/1')?>">
									<tr>
										<td><?php echo $value['name'];?></td>
										<td class="actionBlock">
											<?php if($this->session->userdata('admin')['id_role']!=8&&$this->session->userdata('admin')['id_role']!=3){ ?>
									<a href="<?php echo site_url('approval/administrasi/'.$value['id'])?>" class="editBtn"><i class="fa fa-pencil-square-o"></i>Cek Data</a>
									<?php } ?>
									<?php if($this->session->userdata('admin')['id_role']==8){ ?>
									<button type="submit" name="submit" class="editBtn"><i class="fa fa-check-square-o"></i>Angkat Menjadi DPT</button>
									</form><?php } ?>
										</td>
									</tr>
								</form>
							<?php
								if($row==4) break;
							}
						}else{?>
							<tr>
								<td colspan="11" class="noData">Data tidak ada</td>
							</tr>
						<?php }
						?>
						
						</tbody>
					</table>
					<div class="buttonRegBox clearfix">
						<a href="<?php echo site_url('admin/admin_vendor/waiting_list/1');?>" class="editBtn lihatData">Lihat Daftar Tunggu</a>
					</div>
				</div>
			</div>
			
		</div>
	<?php } 
	}?>
	<?php } ?>

<?php if(count($chart['daftar_merah_chart']->result_array())){ ?>
<div class="dataWrapper col-24" style="margin-top: 25px">
	<div class="block">
	    <h4>Daftar Merah</h4>
	   	<div class="tableWrapper">
			<table class="tableData">
				<thead>
					<tr>
						<td>Nama Penyedia Barang & Jasa</td>
						<td class="actionPanel">Action</td>
					</tr>
				</thead>
				<tbody>
				<?php 
				if(count($chart['daftar_merah_chart']->result_array())){
					foreach($chart['daftar_merah_chart']->result_array() as $row => $value){
					?>
						<tr>
							<td><?php echo $value['name'];?></td>
							<td class="actionBlock">
								<?php if($this->session->userdata('admin')['id_role']== 1){;?>
								<a href="<?php echo site_url('blacklist/edit/'.$value['id'].'/'.$value['id_blacklist'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
								<?php }

									if($this->session->userdata('admin')['id_role']==8){

										if($value['need_approve_bl']==1){
											if($value['is_white']==1){ ?>
											<a class="editBtn aktifkan" href="<?php echo site_url('blacklist/approve_aktif/'.$value['id'])?>" ><i class="fa fa-check-square-o"></i>Setuju Aktifkan kembali</a>
											<?php } else{ ?>
											<a class="editBtn aktifkan" href="<?php echo site_url('blacklist/approve_form/'.$value['id_vendor_bl'].'/'.$value['id_tr'].'/'.$value['id_blacklist'])?>"><i class="fa fa-check-square-o"></i>&nbsp;Setujui Masuk <?php echo $value['blacklist_val']?></a>
											<?php }
										}

									}

									if($this->session->userdata('admin')['id_role']==1 && $value['need_approve_bl'] == 0){
										?>
										<a class="editBtn aktifkan" href="<?php echo site_url('blacklist/aktif/'.$value['id'])?>" ><i class="fa fa-check-square-o"></i>Aktifkan</a>
										<?php
									}
									
								?>
							</td>
						</tr>
					<?php 
						if($row==4) break;
					}
				}else{?>
					<tr>
						<td colspan="11" class="noData">Data tidak ada</td>
					</tr>
				<?php }
				?>
				<tr>

				</tr>
				</tbody>
			</table>
			<div class="buttonRegBox clearfix">
				<a href="<?php echo site_url('blacklist/index/1');?>" class="editBtn lihatData">Lihat Daftar Merah</a>
			</div>
		</div>
	</div>
</div>	
<?php } ?>

<?php if(count($chart['dpt_chart']->result_array())){ ?>
<div class="dataWrapper col-24" style="margin-top: 25px">
	
	<div class="block">
	    <h4>DPT</h4>
	   	<div class="tableWrapper">
			<table class="tableData">
				<thead>
					<tr>
						<td>Nama Penyedia Barang & Jasa</td>
						<td class="actionPanel">Action</td>
					</tr>
				</thead>
				<tbody>
				<?php 
				if(count($chart['dpt_chart']->result_array())){
					foreach($chart['dpt_chart']->result_array() as $row => $value){
					?>
						<tr><?php //print_r($value);?>
							<td><?php echo $value['name'];?></td>
							<td class="actionBlock"><?php if($this->session->userdata('admin')['id_role']!= 2){	?>
								<a href="<?php echo site_url('vendor/dpt_print/'.$value['id_vendor'])?>" class="editBtn"><i class="fa fa-search"></i>&nbsp;Lihat Data</a>
								<?php } ?>
							</td>
						</tr>
					<?php 
						if($row==4) break;
					}
				}else{?>
					<tr>
						<td colspan="11" class="noData">Data tidak ada</td>
					</tr>
				<?php }
				?>
				<tr>

				</tr>
				</tbody>
			</table>
			<div class="buttonRegBox clearfix">
				<a href="<?php echo site_url('admin/admin_dpt');?>" class="editBtn lihatData">Lihat DPT</a>
			</div>
		</div>
	</div>
	
</div>
<?php } ?>