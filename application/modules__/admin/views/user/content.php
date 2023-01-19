<?php echo $this->session->flashdata('msgSuccess')?>

<?php echo $this->session->flashdata('msgError')?>

<div class="btnTopGroup clearfix">

	<a href="<?php echo site_url('admin/admin_user/tambah')?>" class="btnBlue"><i class="fa fa-plus"></i>Tambah</a>

</div>

<div class="tableWrapper">

	<div class="tableHeader">

		<form method="POST">

			<?php echo $filter_list;?>

		</form>	

	</div>

	<table class="tableData">

		<thead> 

			<tr>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['role_name'] == 'asc') ? 'desc' : 'asc'; ?>&by=role_name">Role<i class="fa fa-sort-<?php echo ($sort['role_name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>&by=name">Nama User<i class="fa fa-sort-<?php echo ($sort['name'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['password'] == 'asc') ? 'desc' : 'asc'; ?>&by=password">Password<i class="fa fa-sort-<?php echo ($sort['password'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td><a href="?<?php echo $this->utility->generateLink('sort','desc')?>&sort=<?php echo ($sort['email'] == 'asc') ? 'desc' : 'asc'; ?>&by=email">Email<i class="fa fa-sort-<?php echo ($sort['email'] == 'asc') ? 'desc' : 'asc'; ?>"></i></a></td>

				<td colspan="2">Action</td>

			</tr>

		</thead>

		<tbody>

		<?php 

		if(count($akta_list)){

			foreach($akta_list as $row => $value){
			?>

				<tr>

					<td><?php echo $value['role_name']?></td>

					<td><?php echo $value['name'];?></td>

					<td><?php echo $value['password'];?></td>

					<td><?php echo $value['email'];?></td>

					<td class="actionBlock">

						<a href="<?php echo site_url('admin/admin_user/edit/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
						<?php if($value['id_role'] != 1){ ?>
						<a href="<?php echo site_url('admin/admin_user/hapus/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
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

	

</div>

<div class="pageNumber">

	<?php echo $pagination ?>

</div>