<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js');?>"></script>


<style type="text/css">
	.wysiwyg, #mceu_36, .mce-tinymce{
		width: 706px !important;
	}
</style>
<div class="formDashboard">

	<h1 class="formHeader">Edit Pertanyaan K3</h1>

	<?php foreach ($quest as $key => $value) {?>



	<?php if ($value['type'] == "text") {?>

	<div class="text">

		<form method="POST" enctype="multipart/form-data">

			<table>

				<tr class="input-form">

					<td><label>Pertanyaan* :</label></td>

					<td style="width: 500px;">

						<textarea class="wysiwyg textTP" style="width:700px !important;" name="value"><?php echo $value['value'] ?></textarea>

						<input type="hidden" value="text" name="type">

						<?php echo form_error('value');?>

					</td>

				</tr>

			</table>

			<div class="buttonRegBox clearfix">

				<input type="submit" value="Simpan" class="btnBlue" name="Update">

			</div>

		</form>

	</div>

	<?php }?>



	<?php if ($value['type'] == "radio"||$value['type'] == "checkbox") {?>

	<div class="radio">

		<form method="POST" enctype="multipart/form-data">

			<table>

				<tr class="input-form">

					<td><label>Pertanyaan* :</label></td>

					<td style="width: 500px;">

						<textarea class="wysiwyg textTP" style="width:700px !important;" name="value"><?php echo $value['value'] ?></textarea>

						<input type="hidden" value="radio" name="type">

						<?php echo form_error('value');?>

					</td>

				</tr>

				<tr class="input-form">

					<td><label>Label Pertanyaan* :</label></td>

						<?php $label = explode("|", $value['label']);?>

					<td style="width: 500px;">

						<input placeholder="label pertama" type="text" class="textTP" name="labelfield[]" value="<?php echo isset($label[0]) ? $label[0] : '';?>">

						<input placeholder="label kedua" type="text" class="textTP" name="labelfield[]" value="<?php echo isset($label[1]) ? $label[1] : '';?>">

						<?php echo form_error('labelfield[]');?>

					</td>

				</tr>

			</table>

			<div class="buttonRegBox clearfix">

				<input type="submit" value="Simpan" class="btnBlue" name="Update">

			</div>

		</form>

	</div>

	<?php }?>



	<?php if ($value['type'] == "file") {?>

	<div class="file">

		<form method="POST" enctype="multipart/form-data">

			<table>

				<tr class="input-form">

					<td><label>Pertanyaan* :</label></td>

					<td style="width: 500px;">

						<textarea class="wysiwyg textTP" style="width:700px !important;" name="value"><?php echo $value['value'] ?></textarea>

						<input type="hidden" value="file" name="type">

						<?php echo form_error('value');?>

					</td>

				</tr>

				<tr class="input-form">

					<td><label>Label Pertanyaan* :</label></td>

					<td style="width: 500px;">

						<?php $label = explode("_", $value['label']); $result = implode(" ", $label)?>

						<input placeholder="label pertama" type="text" class="textTP" name="labelfield" value="<?php echo $result; ?>">

						<?php echo form_error('labelfield');?>

					</td>

				</tr>

			</table>

			<div class="buttonRegBox clearfix">

				<input type="submit" value="Simpan" class="btnBlue" name="Update">

			</div>

		</form>

	</div>

	<?php } ?>

	<?php } ?>

	

</div>




<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js');?>"></script>

<script src="<?php echo base_url('assets/js/tinymce/tinymce.min.js');?>"></script>

<script>

	tinymce.init({

			selector:'textarea',

            fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",

			toolbar:  ["undo redo | bold italic | link image | alignleft aligncenter alignright | fontselect | fontsizeselect"],

	        style_formats: [

	            {title: 'Open Sans', inline: 'span', styles: { 'font-family':'Open Sans'}},

	            {title: 'Arial', inline: 'span', styles: { 'font-family':'arial'}},

	            {title: 'Book Antiqua', inline: 'span', styles: { 'font-family':'book antiqua'}},

	            {title: 'Comic Sans MS', inline: 'span', styles: { 'font-family':'comic sans ms,sans-serif'}},

	            {title: 'Courier New', inline: 'span', styles: { 'font-family':'courier new,courier'}},

	            {title: 'Georgia', inline: 'span', styles: { 'font-family':'georgia,palatino'}},

	            {title: 'Helvetica', inline: 'span', styles: { 'font-family':'helvetica'}},

	            {title: 'Impact', inline: 'span', styles: { 'font-family':'impact,chicago'}},

	            {title: 'Symbol', inline: 'span', styles: { 'font-family':'symbol'}},

	            {title: 'Tahoma', inline: 'span', styles: { 'font-family':'tahoma'}},

	            {title: 'Terminal', inline: 'span', styles: { 'font-family':'terminal,monaco'}},

	            {title: 'Times New Roman', inline: 'span', styles: { 'font-family':'times new roman,times'}},

	            {title: 'Verdana', inline: 'span', styles: { 'font-family':'Verdana'}}

	        ],

		}

	);

</script>

