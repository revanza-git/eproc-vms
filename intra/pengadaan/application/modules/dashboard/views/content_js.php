<script src="<?php echo base_url(); ?>assets/js/highcharts.js"></script>
<script src="<?php echo base_url(); ?>assets/js/modules/exporting.js"></script>
    <script type="text/javascript">
		$(function () {
	        $('#container').highcharts({
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
	                name: 'OK',
	                data: [<?php echo count($approval_data[1])?>]
	            },{
	                name: 'Belum Di Verifikasi',
	                data: [<?php echo count($approval_data[0])?>]
	            }, {
	                name: 'Data tidak valid',
	                data: [<?php echo count($approval_data[3])?>]
	            }]
	        });
	    });
    </script>
