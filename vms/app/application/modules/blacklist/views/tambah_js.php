
<script>
	$(function(){
		$('.readonly select').attr('readonly',true).attr('disabled',true);
		$('.readonly input[value="lifetime"]').attr('readonly',true).attr('disabled',true);
		
	    $(function(){
	    	 $( "#vendor_name" ).on('change',function(){
	    	 	
	    	 })
	    	var obj = $( "#vendor_name" );
	    	var parent = obj.closest('form');
	        obj.autocomplete({
	            source: function(request, response) {
	                $.ajax({
		                url: "<?php echo site_url('blacklist/autocomplete/')?>",
		                data: { term: $("#vendor_name").val()},
		                dataType: "json",
		                type: "POST",
		                success: function(data){
		                   	response( $.map( data, function( item ) {
				                return {
				                    label: item.name,
				                    value: item.name,
				                    id: item.id
				                }
				            }));
		                }
		            });
		        },
		        select:function(event,ui){
		        	$('#id_vendor').val(ui.item.id);
		        	// parent.submit();
		        },
		        minLength: 2
	        });
	    });

	})
</script>
<script>
$(function(){

	$('.addFilter').on('click',function(e){
		e.preventDefault();
		var prnt = $(this).parent();
		var kloning = prnt.clone(true,true);
		var filterForm = $(prnt).parent();
		
		$('.filterForm').append(kloning);
		$(this).remove();
	});
	$(function(){
		$('.rate').hide();
		check_kurs();
		$('.kurs').on('change',function(){
			check_kurs();
		});

	})
	function check_kurs(){
		if($('.kurs').val()!='1'){
			$('.rate').show();

		}else{
			$('.rate').hide().val('1');
		}
	}
})	
</script>

<!-- Default REMARK -->
<script type="text/javascript">
$( document ).ready(function() {

    $("#listDefault").hide();
    

    $('#remark').on('click' , function(){
	    var $remark = $('#remark').is(':checked');
	    $("#remarkArea").attr("readonly", false);
   		$("#remarkArea").val("");

    	$("#listDefault").hide();

	    if($remark){
	    	$("#listDefault").show();$("#remarkArea").val("");
	        $("#remarkArea").attr("readonly", true);
	    }
	})

		$('.option').change(function(){
		    $('#remarkArea')
		        .val($('.option:checked')
		        .map(function(){
		            return $(this).val();
		        }).get().join('\n\n'));
		});

});
</script>
<!-- / Default REMARK -->