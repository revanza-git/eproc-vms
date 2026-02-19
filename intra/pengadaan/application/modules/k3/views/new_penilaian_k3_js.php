
	   <script type="text/javascript" src="../source/js/vendors/jquery-3.3.1.js">
	   </script>
		<script>
			$(function(){
				var sum = 0;
				
				$('.radio').click(function(){
					var _id = $(this).attr('data-id');

					// Sub Total
					sum = 0;
					$('.radio-' + _id + ':checked').each(function(){
						sum += parseFloat(this.value);
					})
					$('.sub-' + _id).html(sum);

					// Grand Total
					total = 0;
					$('.sub').each(function(){
						var _val = $(this).html();
						if(!_val) _val = 0;

						total += parseInt(_val);
					})
					//display the result of csms in number
					$('.total-keseluruhan').text(total);
					
					//display the result of csms in category
					if(total < 35){
						$('.result').text("Kategori: Tidak Lulus.");
					}else if(total >= 35 && total < 50){
						$('.result').text("Kategori: Resiko Rendah. Proses tidak perlu dilanjutkan ke evaluasi lapangan.");
					}else if(total >= 50 && total < 70){
						$('.result').text("Kategori: Resiko Menengah. Proses perlu dilanjutkan ke evaluasi lapangan.");
					}else if(total >= 70 && total <= 100){
						$('.result').text("Kategori: Resiko Tinggi. Proses perlu dilanjutkan ke evaluasi lapangan.");
					}
					
				});
				
			})
		</script>