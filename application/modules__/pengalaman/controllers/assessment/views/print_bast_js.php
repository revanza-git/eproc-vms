<script src="<?php echo base_url('assets/js/tinymce/tinymce.min.js');?>"></script>
<script>
	tinymce.init({
			selector:'textarea',
			plugins:'table print',
            fontsize_formats: "8pt 10pt 11pt 12pt 14pt 18pt 24pt 36pt",
			toolbar:  ["undo redo | print | bold italic | link image | alignleft aligncenter alignright | fontselect | fontsizeselect |table"],
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
	        content_css : "<?php echo base_url('assets/css/print_bast.css')?>",

		}
	);
</script>