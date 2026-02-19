<script src="<?php echo base_url(); ?>assets/js/highcharts.js"></script>

<script src="<?php echo base_url(); ?>assets/js/modules/exporting.js"></script>

<script>

    $(function () {

            $('#container-charts').highcharts({

                chart: {

                    plotBackgroundColor: null,

                    plotBorderWidth: null,

                    plotShadow: false,

                    type: 'pie'

                },

                title: {

                    text: 'Grafik Penyedia Barang & Jasa'

                },

                tooltip: {

                    pointFormat: 'Jumlah Penyedia Barang/Jasa: <b>{point.y}</b>'

                },

                plotOptions: {

                    pie: {

                        allowPointSelect: true,

                        cursor: 'pointer',

                        dataLabels: {

                            enabled: false

                        },

                        showInLegend: true

                    }

                },

                exporting: {

                    enabled: false

                },

                series: [{

                    name: 'DPT',

                    colorByPoint: true,

                    data:   [{

                                name: 'Daftar Tunggu (Aktif)',

                                y: <?php echo $chart['dt_aktif']->num_rows(); ?>,

                                color: '#3498db'

                            },
                            {

                                name: 'Daftar Tunggu (Tidak Aktif)',

                                y: <?php echo $chart['dt_non_aktif']->num_rows(); ?>,

                                color: '#ffd700'

                            },{

                                name: 'Daftar Hitam',

                                y: <?php echo $chart['daftar_hitam_chart']->num_rows(); ?>,

                                color: '#2c3e50'

                            }, {

                                name: 'Daftar Merah',

                                y: <?php echo $chart['daftar_merah_chart']->num_rows(); ?>,

                                color: '#c0392b'

                            }, {

                                name: 'DPT',

                                y: <?php echo $chart['dpt_chart']->num_rows(); ?>,

                                color: '#27ae60'

                            }]

                }]

            });

        });

</script>

