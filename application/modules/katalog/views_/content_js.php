<script src="<?php echo base_url('assets/js/highchart/js/highcharts.js')?>"></script>
<script>

    $(function () {

        $('.filterBtn').on('click',function(e){
            var superparent = $(this).closest('.compareHolder');
            $('.compareWrapper',superparent).toggleClass('active');
        });


       $('#container-chart').highcharts({
            title: {
            
                text: 'Riwayat Harga <?php $x=1; foreach($item as $keyitem => $valueitem){ echo $valueitem["nama"]; if($x < count($item)){echo " dan ";} $x++; }?>',
                text: 'Riwayat Harga <?php echo $item['nama']?>',






                x: -20 //center
            },
            xAxis: {
                categories: [<?php foreach($chart as $key=>$row){

                    if($row['years']!=''){
                        echo $row['years'];
                        if($i<count($chart)){echo ",";}
                    }
                    
                }?>]
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
                valuePrefix: '<?php echo $item['symbol']?> '
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: '<?php echo $item["nama"]; ?>',
                data: [<?php foreach($chart as $key=>$row){

                    if($row['avg_year']!=''){
                        echo $row['avg_year'];
                        if($i<count($chart)){echo ",";}
                    }
                    
                }?>]
            }]
        });
    });
       
      
    
</script>