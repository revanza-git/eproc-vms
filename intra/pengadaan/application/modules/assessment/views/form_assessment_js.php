<script>

	$(function(e){

		

		generateNilai();

		function generateNilai(){

			var val = 0;

			$('.selectAssd').each(function(){

				val += parseFloat($(this).val());

			});

			

		}

		

		$('input[type=radio][name*=evaluasi]').on('click',function(){

			generateNilai();

		})

		

		$('#assForm').submit(function(){

			

			var is_check = true;

			$('.selectAss').each(function(){

				

				if($(this).val()==''){

					

					is_check = false;

					

				}

					

			});



			if(!is_check) {

				alertify.alert('Masih ada data yang belum diisi !');	

				return false;

			}



			$('.mandatoryCheck').each(function(){

				

				if( ! $(this).is(':checked') ){

					

					is_check = false;

					

				}

					

			});

			if(!is_check) {

				alertify.alert('Masih ada data yang belum diisi !');	

				return false;

			}

		});



	});

	function toggleHiddenInput(checkbox, pointValue) {
		var hiddenInput = checkbox.nextElementSibling;
		if (checkbox.checked) {
			hiddenInput.value = pointValue;
		} else {
			hiddenInput.value = '';
		}
	}

</script>