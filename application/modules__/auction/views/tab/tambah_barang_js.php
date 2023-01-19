
	<script>
		var obj = $("#material_name");
    	var res = $("#id_material");
    	var reset = $('.resetInput');
    	var cat = $('#cat');
    	var hps = $('#hps');
    	var kurs_hide = $('#id_kurs_hide');
    	var kurs_drop = $('.kurs_drop');
	    $(function(){
	    	
	        obj.autocomplete({
	            source: function(request, response) {
	                $.ajax({
		                url: "<?php echo site_url('katalog/search')?>",
		                data: { term: obj.val(),cat: $('.category').val(),id_procurement:<?php echo $id;?>},
		                dataType: "json",
		                type: "POST",
		                success: function(data){
		                   	response( $.map( data, function( item ) {
				                return {
				                    label: item.name,
				                    value: item.name,
				                    id: item.id,
				                    avg: item.average,
				                    kurs: item.id_kurs,
				                }
				            }));
		                }
		            });
		        },
		        select:function(event,ui){
		        	res.val(ui.item.id);
		        	obj.prop('readonly',true);
		        	kurs_hide.val(ui.item.kurs);
		        	if(ui.item.kurs==="null" || ui.item.kurs===null || ui.item.kurs==="" || typeof ui.item.kurs === "undefined"){
		        		kurs_drop.prop('disabled',false);
		        	}else{
		        		kurs_drop.prop('disabled',true).hide();
		        	}
		        	cat.hide();
		        	$('input',cat).prop('selected',false);
		        	reset.toggle();

		        },
		        minLength: 2
	        });
	       
	    });
	    function resetInput(){
        	obj.prop('readonly',false);
        	obj.val('');
        	res.val('');
        	cat.show();
        	kurs_drop.prop('disabled',false).show();
        	reset.toggle();
        }
	</script>
