<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>INPUT ADMIN</title>
	<link rel="stylesheet" href="">
</head>
<body>
	<form action="<?php echo site_url('input/save') ?>" method="post">
		<table>
			<tr>
				<td>Nama</td>
				<td>:</td>
				<td><input type="text" name="name"></td>
			</tr>
			<tr>
				<td>email</td>
				<td>:</td>
				<td><input type="email" name="email"></td>
			</tr>
			<tr>
				<td>Username</td>
				<td>:</td>
				<td><input type="text" name="username"></td>
			</tr>
			<tr>
				<td>Aplikasi Utama</td>
				<td>:</td>
				<td>
					<select name="id_app" id="">
						<option value="">Pilih Salah Satu</option>
						<option value="1">VMS</option>
						<option value="2">Perencanaan</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Role VMS</td>
				<td>:</td>
				<td>
					<select name="id_role" id="">
						<option value="">Pilih Salah Satu</option>
						<?php foreach ($role_1 as $key => $value) { ?>
							<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Role Perencanaan</td>
				<td>:</td>
				<td>
					<select name="id_role_app2" id="">
						<option value="">Pilih Salah Satu</option>
						<?php foreach ($role_2 as $key => $value) { ?>
							<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Divisi</td>
				<td>:</td>
				<td>
					<select name="id_division" id="">
						<option value="">Pilih Salah Satu</option>
						<?php foreach ($division as $key => $value) { ?>
							<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><button>Simpan</button></td>
			</tr>
		</table>
	</form>
	<br>	
	<br>
	<br>
	<table border="1">
		<tr>
			<td>Nama</td>
			<td>Email</td>
			<td>ID Role 1</td>
			<td>ID Role 2</td>
			<td>Username</td>
			<td>Password</td>
			<td>Aplikasi Utama</td>
		</tr>
		<?php foreach ($table as $key => $value) { ?>
			<tr>
				<td><?php echo $value['name'] ?></td>
				<td><?php echo $value['email'] ?></td>
				<td><?php echo $value['id_role'] ?></td>
				<td><?php echo $value['id_role_app2'] ?></td>
				<td><?php echo $value['username'] ?></td>
				<td><?php echo $value['password'] ?></td>
				<td><?php echo $value['type_app'] ?></td>
			</tr>
		<?php } ?>
	</table>
</body>
</html>