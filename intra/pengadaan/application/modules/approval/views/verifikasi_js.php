<script src="<?php echo base_url(); ?>assets/js/highcharts.js"></script>

<script src="<?php echo base_url(); ?>assets/js/modules/exporting.js"></script>

    <script type="text/javascript">

		$(function () {

	        $('.graphBar').highcharts({

	        	 colors: ["#2CC36B", "#F39C12", "#C0392B",],

	            chart: {

	                type: 'bar'

	            },

	            title: {

	                text: ''

	            },

	            xAxis: {

	                categories: ['Data']

	            },

	            yAxis: {

	                // gridLineWidth: 0,

	                title: {

	                    text: 'Total Data Dimasukan'

	                }

	            },

	            legend: {

	                backgroundColor: '#FFFFFF',

	                reversed: true

	            },

	            plotOptions: {

	                series: {

	                    stacking: 'normal'

	                }

	            },

	                series: [ {

	                name: 'Data Sesuai',

	                data: [<?php echo count($approval_data[1])?>]

	            },{

	                name: 'Belum Di Verifikasi',

	                data: [<?php echo count($approval_data[0])?>]

	            }, {

	                name: 'Data tidak valid',

	                data: [<?php echo count($approval_data[3])?>]

	            }]

	        });

			$('#approveCert').on('click',function(e){
				_this = $(this);
				total = <?php echo ($graphBar[0]['val'] + $graphBar[3]['val'])?>;
				if(total>0){
					return true;
				}else{
					e.preventDefault();
					alertify.prompt('Masukkan nomor sertifikat','',function(evt, val){
					$('#certificate_no').val(val);
					_this.closest('form').submit();
					return true;
					
				});
				}
				
			});
	    });
		$
    </script>

