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
	});
</script>
<style type="text/css">
	#mask{
		width: 100%;
		height: 100%;
		position: absolute;
		background: #fff;
		opacity: 0.5;
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

<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Daftar Tunggu Penyedia Barang/Jasa</h2>
<div class="tableWrapper">
	<div class="tableHeader">
	<a style="background: #2f3640 !important; border-bottom: 2px #718093 solid !important;" href="<?php echo site_url('admin/admin_vendor/export_excel_waiting_list');?>" class="btnBlue"><i class="fa fa-download"></i> Export</a>
		<form method="POST">
			<?php echo $filter_list;?>
		</form>	
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['legal_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=legal_name">Badan Usaha<i class="fa fa-sort-<?php echo ($sort['legal_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Badan Usaha<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['last_update'] == 'asc') ? 'desc' : 'asc'; ?>&by=last_update">Aktivitas Terakhir<i class="fa fa-sort-<?php echo ($sort['last_update'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<?php if($this->session->userdata('admin')['id_role']==8){ ?>
				<td>Tanggal Pengangkatan</td>
				<?php } ?>
				<td class="actionPanel">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($list)){
			//print_r($list);
			foreach($list as $row => $value){
			?>
			
				
		
				<tr>
					
					<td><?php echo $value['legal_name'];?></td>
					<td><a href="<?php echo site_url('approval/administrasi/'.$value['id'])?>"><?php echo $value['name'];?></a></td>
					<td><?php if(isset($value['last_update'])){echo default_date($value['last_update'])." - ";echo date("H:i:s", strtotime($value['last_update']));}else{ echo "-" ;}?></td>
					<?php if($this->session->userdata('admin')['id_role']==8){ if($value['need_approve'] == 1){?>

					<td>
						<form action="<?php echo site_url('approval/approve/'.$value['id'])?>" method="POST">
							<?php echo $this->form->calendar(array('name'=>'start_date'.$value['id'],'value'=>$this->form->get_temp_data('start_date')), false);?>
					</td>
					<?php } else {?>
	<td> - </td>
<?php }} ?>
					<td class="actionBlock">
						<?php if($this->session->userdata('admin')['id_role']!=8&&$this->session->userdata('admin')['id_role']!=3){ ?>
						<a href="<?php echo site_url('approval/administrasi/'.$value['id'])?>" class="editBtn"><i class="fa fa-pencil-square-o"></i>Cek Data</a>
						<?php } ?>
						<?php if($this->session->userdata('admin')['id_role']==8){ 
if($value['need_approve'] == 1) { ?>
						<button type="submit" name="submit" class="editBtn"><i class="fa fa-check-square-o"></i>Angkat Menjadi DPT</button>
						</form>
						<?php } else{ ?>
						<a href="<?php echo site_url('approval/administrasi/'.$value['id'])?>" class="editBtn"><i class="fa fa-pencil-square-o"></i>Cek Data</a>

<?php } } ?>
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
	
</div>
<div class="pageNumber">
	<?php echo $pagination ?>
</div>