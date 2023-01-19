<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery.wysiwyg.css');?>" type="text/css"/>

<div class="formDashboard">
	<h1 class="formHeader">Edit Header K3</h1>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama* :</label></td>
				<td style="width: 500px;"><?php //print_r($header)?>
					<?php foreach ($header as $key => $question) { ?>
					<textarea id="wysiwyg" style="width:700px !important;" name="question"><?php echo $question; ?></textarea>
					<?php echo form_error('question');?>
					<?php } ?>
				</td>
			</tr>
		</table>
		
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="Update">
		</div>
	</form>
</div>


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