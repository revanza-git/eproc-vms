<?php echo $this->session->flashdata('msgSuccess')?>

<?php echo $this->session->flashdata('msgError')?>

<?php echo $this->data_process->generate_progress('verification',$id_data)?>

<form method="POST">

	<h3>Summary Data</h3>

	<div class="graphBar" style="min-width: 400px; height: 150px; margin: 0 auto">

		

	</div>

	<div class="successVer msgBlock">

		<h4><?php echo count($approval_data[1])?> data telah sesuai</h4>

		<ul>

			<?php foreach($approval_data[1] as $key =>$value){ ?>

			<li><?php echo $value;?></li>

			<?php } ?>

		</ul>

	</div>

	<div class="warnVer msgBlock">

		<ul>

			<h4><?php echo count($approval_data[0])?> data belum terverifikasi</h4>

			<?php foreach($approval_data[0] as $key =>$value){ ?>

			<li><?php echo $value;?></li>

			<?php } ?>

		</ul>

	</div>

	<div class="errorVer msgBlock">

		<ul>

			<h4><?php echo count($approval_data[3])?> data tidak sesuai</h4>

			<?php foreach($approval_data[3] as $key =>$value){ ?>

			<li><?php echo $value;?></li>

			<?php } ?>

		</ul>

	</div>

	<div class="buttonRegBox clearfix">

		<?php
		$admin = $this->session->userdata('admin');
		if ($admin['id_role'] == 1 || $admin['id_role'] == 10 || $admin['id_role'] == 3) {
			if ($admin['id_role'] == 1) {
				$label = "Angkat Menjadi DPT";
			} else {
				$label = "Kirim Ke Supervisor";
			}
		?>
			<?php
			if (!isset($need_approve) || !$need_approve) { ?>
				<?php // echo "ini";print_r($certificate_no);
				?>


				<!-- <span>Pilih tanggal pengangkatan</span> <?php //echo $this->form->calendar(array('name'=>'start_date','value'=>$this->form->get_temp_data('start_date')), false);
																?>&nbsp; -->
				<input type="hidden" id="certificate_no" name="certificate_no" value="<?php echo isset($certificate_no) ? $certificate_no : ''; ?>">
				<input type="hidden" name="simpan" value="simpan">
				<input type="submit" value="<?= $label ?>" class="btnBlue" id="approveCert">



		<?php }
		} ?>

	</div>

</form>

