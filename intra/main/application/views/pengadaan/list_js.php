<script type="text/javascript">

$(function(){
	dataPost = {
	};			

	var folder = $('#folderGenerator').folder({
		url: '<?php echo site_url('pengadaan/getData/'); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			year 		= value[key][1].value;
			urlDivision = '<?php echo base_url('pemaketan/index/');?>/'+year;
			urlYear = '<?php echo base_url('export_timeline/rekap_timeline/');?>/'+year;

			btn = [
			{
				icon : 'search',
				label: 'Lihat Data',
				class: 'buttonView',
			}
			,
			{
				icon : 'download',
				label: 'Export Timeline',
				class: 'buttonExport'
			}
			];
			return btn;
		},

		callbackFunctionRightClick: function(){
			var view = $('.buttonView').click(function(){
				$(location).attr('href',urlDivision);
			});

			var export_ = $('.buttonExport').click(function(){
				window.open(urlYear, "_blank");
			});
		},

		renderContent: function(el, value, key){
	
			html = '';
			html += '<div class="caption"><p>'+value[1].value+'</p><p><b>'+value[0].value+'</b> Item(s)</p></div>';
			return html;
		},

		additionFeature: function(el){
		
		},

		finish: function(){
     	
		}
	});
});

</script>