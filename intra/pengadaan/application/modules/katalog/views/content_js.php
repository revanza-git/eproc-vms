<script src="<?php echo base_url('assets/js/highchart/js/highcharts.js')?>"></script>
<script>

    $(function () {

        $('.filterBtn').on('click',function(e){
            var superparent = $(this).closest('.compareHolder');
            $('.compareWrapper',superparent).toggleClass('active');
        });


               $('#container-chart').highcharts({
            title: {
            
                text: 'Riwayat Harga <?php if(isset($item) && is_array($item)) { $x=1; foreach($item as $keyitem => $valueitem){ if(is_array($valueitem) && isset($valueitem["nama"])) { echo $valueitem["nama"]; if($x < count($item)){echo " dan ";} } $x++; } } else { echo "Data tidak tersedia"; } ?>',






                x: -20 //center
            },
            xAxis: {
                categories: [<?php if(isset($chart) && is_array($chart)) { $i=0; foreach($chart as $key=>$row){

                    if($row['years']!=''){
                        echo $row['years'];
                        if($i<count($chart)-1){echo ",";}
                    }
                    $i++;
                } } ?>]
            },
            yAxis: {
                labels: {
                    formatter: function () {
                        if (this.value.toFixed(0) >= 1000000000000) {
                            return this.value.toFixed(0) / 1000000000000 + 'T';
                        } else if (this.value.toFixed(0) >= 1000000000) {
                            return this.value.toFixed(0) / 1000000000 + 'M';
                        } else if (this.value.toFixed(0) >= 1000000) {
                            return this.value.toFixed(0) / 1000000 + 'Jt';
                        } else if(this.value.toFixed(0) >= 1000){
                            return this.value.toFixed(0) / 1000 + 'Rb';
                        }else if(this.value.toFixed(0)<0){
                            return '';
                        }else{
                                return this.value.toFixed(0);
                        }
                    }
                },
                title: {
                    text: ''
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valuePrefix: '<?php echo (isset($item) && is_array($item) && isset($item['symbol'])) ? $item['symbol'] : ''; ?> '
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: '<?php echo (isset($item) && is_array($item) && isset($item["nama"])) ? $item["nama"] : "Data"; ?>',
                data: [<?php if(isset($chart) && is_array($chart)) { $i=0; foreach($chart as $key=>$row){

                    if($row['avg_year']!=''){
                        echo $row['avg_year'];
                        if($i<count($chart)-1){echo ",";}
                    }
                    $i++;
                } } ?>]
            }]
        });
    });
       
      
    
</script>