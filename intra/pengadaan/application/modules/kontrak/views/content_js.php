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

	
	$('body').on('click','.suggestionList li',function(e){
		// alert($(this).attr('data-id'));
		$('.suggestionId').val($(this).attr('data-id'));
		$('.suggestionInput').val($(this).html());
		$('.suggestionList').hide();
	})
	$('body').on('click', function(e) {
	    if (!$(e.target).closest('.suggestion li, .suggestion input').length) {
	        $('.suggestionList').hide();
	    };
	});

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
			                url: "<?php echo site_url('pengadaan/search_kandidat/'.(isset($id_pengadaan) ? $id_pengadaan : (isset($id) ? $id : '')))?>",
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
			        	$('#vendor_name').val(ui.item.value);
			        	parent.submit();
			        },
			        minLength: 2
		        });
		    });

		})
	
		// $('.editBtn').on('click',function(e){
		// 	_this = $(this);
		// 	e.preventDefault();
		// 	alertify.confirm('Apakah anda yakin?',function(evt, val){
		// 		window.location.href = _this.attr('href');
		// 	});
		// });

		$( document ).ready(function() {
	        console.log( "document loaded" );

			// $('#evaluation_method_desc').hide();
			// var Privileges = jQuery('#evaluation_method');
			// var select = this.value;
			// Privileges.change(function () {
			//     if ($(this).val() == 'scoring') {
			//         $('#evaluation_method_desc').show();
			//     }
			//     else $('.resources').hide(); // hide div if value is not "custom"
			// });
	    });
	
})
	
</script>