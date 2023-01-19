<script>
$(function(){

	$('.addFilterGroup').on('click',function(e){
		e.preventDefault();
		var formCp = $(this).closest('.groupFieldWrap');
		var formCl = formCp.children('input:first-child,select:first-child').clone().val('');
		$(formCl).insertBefore(this);
	});
	$('.addFilterGroupDate').on('click',function(e){
		e.preventDefault();
		var formCp = $(this).closest('.groupFieldWrap');
		var formCl = formCp.children('.dateWrap:first-child').clone();
		$(formCl).insertBefore(this);
	});
	$('.groupFormHeader').on('click',function(){
		$('.groupFormContent').hide();
		$(this).siblings('.groupFormContent').slideToggle();
	}
	);
})
	
</script>