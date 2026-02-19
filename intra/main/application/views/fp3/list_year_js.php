<script src="https://cdn.rawgit.com/igorescobar/jQuery-Mask-Plugin/1ef022ab/dist/jquery.mask.min.js"></script>
<script type="text/javascript">

$(function(){
	dataPost = {
		order: 'id',
		sort: 'desc'
	};				

	var folder = $('#tableGenerator').folder({
		url: '<?php echo site_url('fp3/getDataFP3ByYear/'.$year); ?>',
		data: dataPost,
		dataRightClick: function(key, btn, value){
			_id 		= value[key][3].value;
			year 		= '<?php echo $year ?>';
			urlDivision = '<?php echo base_url('fp3/index');?>/'+_id+'/'+'0'+'/'+year;

			btn = [{
				icon : 'search',
				label: 'Lihat Data',
				class: 'buttonView',
			}];
			return btn;
		},
		callbackFunctionRightClick: function(){
			var view = $('.buttonView').click(function(){
				$(location).attr('href',urlDivision);
			});
		},

		renderContent: function(el, value, key){
			html = '';
			html += '<div class="caption"><p>'+value[0].value+'</p><p><b>'+value[1].value+'</b> Item(s)</p></div>';
			console.log(folder);
			return html;
		},
		additionFeature: function(el){
			<?php if ($this->session->userdata('admin')['id_role'] == 5 || $this->session->userdata('admin')['id_role'] == 3) { ?>
				// el.prepend(insertStepButton(site_url+"pemaketan/insertStep/<?php echo $id;?>"));
			<?php } ?>
		},
		finish: function(){
     		

		}

	})
});

</script>