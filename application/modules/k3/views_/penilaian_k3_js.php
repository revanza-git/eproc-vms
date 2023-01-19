<script>

	$(function(e){

		

		generateNilai();

		function generateNilai(){

			var val = 0;

			$('input[type=radio]:checked').each(function(){

				val += parseFloat($(this).val());

			});

			$('#nilaik3').text(val);

			$('#score_k3').val(val);

		}

		

		$('input[type=radio][name*=evaluasi]').on('click',function(){

			generateNilai();

		})

		

		$('#penilaiank3').submit(function(){

			

			var is_check = true;

			$('.radiocsmsWrapper').each(function(){

				

				row_check = false;

				$(this).find('.radiocsms').each(function(){



					if($(this).is(':checked')){

						row_check = true;

						

					}

					

				});

				// console.log(row_check);

				if(!row_check){

					

					is_check =  false;

				}



			});



			if(!is_check) {

				alertify.alert('Masih ada data yang belum diisi !');	

				return false;

			}

		});

	});

</script>