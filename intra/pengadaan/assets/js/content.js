$('#npwp').iMask({

		type : 'fixed',

		mask : '99.999.999.9-999.999',

	});

$('.npwp-code').iMask({

	type : 'fixed',

	mask : '99.999.999.9-999.999',

});

	$('.money-masked').iMask({

		type : 'number'

	});

	function changeCal_date( field ){

		field = field.replace(/\|/g, "\\\|");

		field = field.replace(/\[/g, "\\\[");

		field = field.replace(/\]/g, "\\\]");

		var value = $("#"+field+"_date-year").val() + "-" + $("#"+field+"_date-month").val()+ "-" + $("#"+field+"_date-date").val();

		console.log(value);

		$("input#"+field).val(value);  

	}

	function changeCal_date_filter( field ){

		var parent = field.closest('.dekodr-calendar');

		

		var value = $('.dekodr-calendar-year',parent).val() + "-" + $('.dekodr-calendar-month',parent).val()+ "-" + $('.dekodr-calendar-day',parent).val();

		

		$('.dekodr-calendar-hidden',parent).val(value);  

	}

	function lifetime_date(field){

		if($("#nppkp_date-lifetime").is(":checked")){

			$("#nppkp_date").val("lifetime");

			$("#nppkp_date-date-container").slideUp();

		} else {

			$("#nppkp_date-date-container").slideDown();

			$("#nppkp_date-year").val("2015");

			$("#nppkp_date-month").val("09");

			$("#nppkp_date-date").val("03");

			changeCal_nppkp_date()

		}

	}



	$(function(){

		$(function(){

			wrapper = $('.dekodr-calendar-lifetime');

			if($('.dekodr-calendar-checkbox-input',wrapper).is(':checked')){

				$('.dekodr-calendar',wrapper).hide();

			}

			$('.dekodr-calendar-checkbox-input',wrapper).on('change',function(){

				$('.dekodr-calendar',wrapper).toggle();

			})

		});

		$('.btnNote').on('click',function(e){

			e.stopPropagation();

			$('.noteFormWrap').toggle();

		});

		$('.noteFormWrap').on('click',function(e){

			e.stopPropagation();

			$('.noteFormWrap').show();

		});

		$(document).on('click',function(e){

			

			$('.noteFormWrap').hide();

		})

		$('.filterBtn').on('click',function(e){

			var parentFilter = $(this).closest('.filter');

			$('.groupFilterArea,.filterArea',parentFilter).slideToggle();

		});

		$('.addFilter').on('click',function(e){

			e.preventDefault();

			var prnt = $(this).parent();

			var kloning = prnt.clone(true,true);

			var filterForm = $(prnt).parent();

			

			$('.filterForm').append(kloning);

			$(this).remove();

		});



		

		$('.groupFormHeader').on('click',function(){

			

			$(this).siblings('.groupFormContent').slideToggle();

		}

		);

		

		$('.removeFilterGroup').on('click',function(e){

			e.preventDefault();

			var formCp = $(this).closest('.groupFieldWrap');



			$('input, select',formCp).last().remove();

		})

		$('.removeFilterGroupDate').on('click',function(e){

			e.preventDefault();

			var formCp = $(this).closest('.groupFieldWrap');

			$('.dateWrap .groupFieldBlock',formCp).last().remove();

		})

		$('.removeFilterNumberRange').on('click',function(e){
			e.preventDefault();
			var formCp = $(this).closest('.groupFieldWrap');
			$('.dateWrap .groupFieldBlock',formCp).last().remove();
		})



		/*Filter function*/

		$('.addFilterGroup').on('click',function(e){
			console.log('asdasdas');
			e.preventDefault();

			var formCp = $(this).closest('.groupFieldInput');

			var formCl = formCp.children('.hiddenFilter').clone();



			$(formCl).removeClass('hiddenFilter').attr('name',formCp.attr('name')).insertBefore(this);

		});



		$('.addFilterGroupDate').on('click',function(e){

			e.preventDefault();



			var formCp = $(this).closest('.groupFieldWrap');

			var count = $('.dateWrap .groupFieldBlock',formCp).length + 1;

			var formHeader = $(this).closest('.groupFieldInput');



			var formCl = formCp.children('.hiddenFilter').clone();



			var first_child = formCl.find('.dekodr-calendar').first();

			var last_child = formCl.find('.dekodr-calendar').last();

			$('.dekodr-calendar-hidden',first_child).attr('name',formHeader.attr('name') + '[start_date]['+count+']').attr('id',formHeader.attr('name') + '[start_date]['+count+']');

			$('.dekodr-calendar-hidden',last_child).attr('name',formHeader.attr('name') + '[end_date]['+count+']').attr('id',formHeader.attr('name') + '[end_date]['+count+']');


			$(formCl).removeClass('hiddenFilter');

			$('.dateWrap',formCp).append(formCl);

		});

		$('.addFilterNumberRange').on('click',function(e){

			e.preventDefault();

			var formCp = $(this).closest('.groupFieldWrap');
			var count = $('.dateWrap .groupFieldBlock',formCp).length + 1;
			var formHeader = $(this).closest('.groupFieldInput');

			var formCl = formCp.children('.hiddenFilter').clone();

			var first_child = formCl.find('.dekodr-range-number').first();
			var last_child = formCl.find('.dekodr-range-number').last();
			$('input',first_child).attr('name',formHeader.attr('name') + '[start_value]['+count+']').attr('id',formHeader.attr('name') + '[start_value]['+count+']');
			$('input',last_child).attr('name',formHeader.attr('name') + '[end_value]['+count+']').attr('id',formHeader.attr('name') + '[end_value]['+count+']');


			
			$(formCl).removeClass('hiddenFilter');
			$('.dateWrap',formCp).append(formCl);
		});

		$('.close').on('click',function(){

			var conf = confirm('Apakah anda ingin menghapus pemberitahuan ini?');

			if(conf){

				var url = $(this).data('url');

				$.ajax({

					method	: 	'POST',

					url		: 	url,

					success	: 	function(data){

						if(data == 'success'){

							location.reload();

						}

					}

				});

			}

		})



	})

	$('.delBtn').on('click',function(e){
		_this = $(this);
		e.preventDefault();
		alertify.confirm('Apakah anda yakin ingin menghapus data?').set({
			onok:function(evt, value){
				window.location.href = _this.attr('href');
			}
		});
		// if(confirm('Apakah anda yakin ingin menghapus data?'))
		// {
		// 	window.location.href = $(this).attr('href');
		// }
		/*confirm(
		'Apakah Anda yakin ingin menghapus data?',
		  function(data){
		    console.log('Exemplo: '+data)
		  },
		  {
		    return : true,
		    title : '',
		    yes : 'Ya',
		    no : 'Batal'
		  }
		)*/
	})

	$('.waitingList').on('click',function(e){

		e.preventDefault();

		if(confirm('Apakah anda yakin mengirim data ke Admin?'))

		{

			window.location.href = $(this).attr('href');

		}

	})

	$('.graph').tooltip({

		track: true,

		content: function() {

		    return $( this ).attr( "title" );

		}

	});

