<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Daftar Penyedia Barang &amp; Jasa</h2>
<div class="tableWrapper">
	<div class="tableHeader">
		<a style="background: #2f3640 !important; border-bottom: 2px #718093 solid !important;" href="<?php echo site_url('k3/export_excel');?>" class="btnBlue"><i class="fa fa-download"></i> Export</a>
		<form method="POST">
		<form method="POST">
			<?php echo $filter_list;?>
		</form>	
	</div>
	<table class="tableData">
		<thead>
			<tr>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama Penyedia Barang &amp; Jasa<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['score'] == 'asc') ? 'desc' : 'asc'; ?>&by=score">Skor Terakhir<i class="fa fa-sort-<?php echo ($sort['score'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>
				<td colspan="2">Action</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(count($vendor_list)){
			foreach($vendor_list as $row => $value){
			?>
				<tr>
					<td><?php echo $value['name'];?></td>
					<td><?php echo $value['score'];?></td>
					<td class="actionBlock">
						<?php if($value['csms_id']!=null){ ?>
 						<a href="<?php echo site_url('k3/penilaian_k3/'.$value['id'].'/edit/'.$value['csms_id'])?>" class="editBtn"><i class="fa fa-pencil-square-o"></i>&nbsp;Nilai CSMS</a> | 
						<?php }else{ ?>
						<a href="<?php echo site_url('k3/penilaian_k3/'.$value['id'])?>" class="editBtn"><i class="fa fa-pencil-square-o"></i>&nbsp;Nilai CSMS</a> | 
						<?php	}?>
						
						<a href="<?php echo site_url('k3/penilaian_view/'.$value['id'])?>"><i class="fa fa-search"></i>&nbsp;Lihat Nilai</a> |
						<a href="<?php echo site_url('k3/history_nilai/'.$value['id'])?>"><i class="fa fa-eye"></i>&nbsp;Lihat History Nilai</a>
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
