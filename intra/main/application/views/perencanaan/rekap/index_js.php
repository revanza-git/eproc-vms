<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'year',
		sort: 'desc'
	};			

	var folder = $('#tableGenerator').folder({
		url: '<?php echo site_url('perencanaan/rekap/getData/'); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			_id 			= value[key][3].value;
			_year 			= value[key][2].value;
			urlDivision 	= '<?php echo base_url('perencanaan/rekap/year/');?>/'+_year;
			urlPerencanaan	= '<?php echo base_url('export/rekap_perencanaan/');?>/'+_year;
			urlDepartment 	= '<?php echo base_url('export/rekap_department/');?>/'+_year;

			btn = [
					{
						icon : 'search',
						label: 'Lihat Data',
						class: 'buttonView',
					},
					{
						icon : 'file-download',
						label: 'Rekap Perencanaan',
						class: 'buttonPerencanaan',
					},
				];
			return btn;
		},
		callbackFunctionRightClick: function(){
			var view = $('.buttonView').click(function(){
				$(location).attr('href',urlDivision);
			});

			// DOWNLOAD Data on PDF
			var pdf = $('.buttonPerencanaan').click(function(){
				window.open(urlPerencanaan, "_blank");
			});

			// DOWNLOAD Data on PDF
			var pdf = $('.buttonPDF').click(function(){
				window.open(buttonDepartment, "_blank");
			});
		},

		renderContent: function(el, value, key){
			html = '';
			html += '<div class="caption"><p>'+value[2].value+'</p><p><b>'+value[1].value+'</b> Item(s)</p></div>';
			console.log(folder);
			return html;
		},
		additionFeature: function(el){
		},
		finish: function(){
     		

		}
	});
});


</script>