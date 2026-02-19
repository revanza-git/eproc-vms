<script type="text/javascript">

var year = $('#yearGraph').val();
        getGraph(year);
        getApprovalFPPBJ(year);
        getApprovalFKPBJ(year);
        getApprovalFP3(year);
        getApprovalFPPBJBaru(year);
        getApprovalFKPBJBaru(year);

$('#yearGraph').on('change', function(){
    year = $(this).val();
    getGraph(year);
    getApprovalFPPBJ(year);
    getApprovalFKPBJ(year);
    getApprovalFP3(year);
    getApprovalFPPBJBaru(year);
    getApprovalFKPBJBaru(year);
});


var del = $('.delete-notif').modal({
    header:'Hapus Notifikasi',
    render: function(el,data) {
        el.html('<div class="blockWrapper"><span>Apakah anda yakin ingin menghapus notifikasi?<span><div class="form"></div><div>');
        data.onSuccess = function(){
            location.reload();
        }
        data.isReset = true;
        $('.form', el).form(data).data('form');
    }
})




// CALLING THE GRAPHIC
function getGraph(year){
    console.log(year);
    $.ajax({
        url:'<?php echo site_url('main/rekapPerencanaanGraph') ?>/'+year,
        method: 'get',
        async:false,
        dataType : 'json',
        success: function(__graph) {
            __graph = __graph;

            Highcharts.chart('graph_', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Data Rekap Perencanaan'
                    },
                    xAxis: {
                        categories: ['Perencanaan', 'Aktual']
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    tooltip: {
                        shared: true
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal'
                        }
                    },
                    series: [{
                        name: 'Perencanaan',
                        data: [__graph.plan]
                    }, {
                        name: 'Aktual Perencanaan (' + __graph.percent_act + '%)',
                        data: ['', __graph.act]
                    }, {
                        name: 'Aktual Diluar Perencanaan (' + __graph.percent_act_out + '%)',
                        data: ['', __graph.act_out]
                    }]
                });

            var id_division = '<?= $admin['id_division'] ?>';

                if (id_division == 1) {
                    label_pelelangan = '<a id="link_pelelangan">Pelelangan</a>';
                    label_pemilihan = '<a id="link_pemilihan">Pemilihan Langsung</a>';
                    label_swakelola = '<a id="link_swakelola">Swakelola</a>';
                    label_penunjukan = '<a id="link_penunjukan">Penunjukan Langsung</a>';
                    label_pengadaan = '<a id="link_pengadaan">Pengadaan Langsung</a>';
                } else {
                    label_pelelangan = 'Pelelangan';
                    label_pemilihan = 'Pemilihan Langsung';
                    label_swakelola = 'Swakelola';
                    label_penunjukan = 'Penunjukan Langsung';
                    label_pengadaan = 'Pengadaan Langsung';
                }

                Highcharts.chart('graph_jenis', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Data Aktual Pengadaan'
                    },
                    subtitle: {
                    },
                    xAxis: {
                        type: 'category',
                        labels: {
                            rotation: -45,
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Metode Pengadaan'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    tooltip: {
                        pointFormat: 'Jumlah: <b>{point.y:.1f}</b>'
                    },
                    series: [{
                        name: 'Population',
                        data: [
                            [label_pelelangan, __graph.pelelangan],
                            [label_pemilihan, __graph.pemilihan_langsung],
                            [label_swakelola, __graph.swakelola],
                            [label_penunjukan, __graph.penunjukan_langsung],
                            [label_pengadaan, __graph.pengadaan_langsung]
                        ],
                        dataLabels: {
                            enabled: false,
                            rotation: -90,
                            color: '#FFFFFF',
                            align: 'right',
                            format: '{point.y:.1f}', // one decimal
                            y: 10, // 10 pixels down from the top
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    }]
                });
        }
    })
}

//---------- FPPBJ
function getApprovalFPPBJ(year){
    console.log(year);
    $.ajax({
        url:'<?php echo site_url('main/rekapFPPBJ') ?>/'+year,
        method: 'get',
        async:false,
        success: function(data) {
            $('#rekapFPPBJ').empty();
            $('#rekapFPPBJ').append(data);

            $("#rekapFPPBJ .accordion-header").click(function() {
                for (i = 0; i < $(this).length; i++) {
                    $(this).toggleClass('active');
                    $(this).find('.spin').toggleClass('spin-effect');
                    $(this).next('.accordion-panel').toggleClass('active');
                }
            });
        }
    })
}
//---------- END FPPBJ

//---------- FKPBJ
function getApprovalFKPBJ(year){
    console.log(year);
    $.ajax({
        url:'<?php echo site_url('main/rekapFKPBJ') ?>/'+year,
        method: 'get',
        async:false,
        success: function(data) {
            $('#rekapFKPBJ').empty();
            $('#rekapFKPBJ').append(data);

            $("#rekapFKPBJ .accordion-header").click(function() {
                // alert('asd');
                for (i = 0; i < $(this).length; i++) {
                    $(this).toggleClass('active');
                    $(this).find('.spin').toggleClass('spin-effect');
                    $(this).next('.accordion-panel').toggleClass('active');
                }
            });
        }
    })
}
//---------- END FKPBJ

//---------- FP3
function getApprovalFP3(year){
    console.log(year);
    $.ajax({
        url:'<?php echo site_url('main/rekapFP3') ?>/'+year,
        method: 'get',
        async:false,
        success: function(data) {
            $('#rekapFP3').empty();
            $('#rekapFP3').append(data);

            $("#rekapFP3 .accordion-header").click(function() {
                for (i = 0; i < $(this).length; i++) {
                    $(this).toggleClass('active');
                    $(this).find('.spin').toggleClass('spin-effect');
                    $(this).next('.accordion-panel').toggleClass('active');
                }
            });
        }
    })
}
//---------- END FP3

//---------- FPPBJ Baru
function getApprovalFPPBJBaru(year){
    console.log(year);
    $.ajax({
        url:'<?php echo site_url('main/rekapFPPBJBaru') ?>/'+year,
        method: 'get',
        async:false,
        success: function(data) {
            $('#rekapFPPBJBaru').empty();
            $('#rekapFPPBJBaru').append(data);

            $("#rekapFPPBJBaru .accordion-header").click(function() {
                for (i = 0; i < $(this).length; i++) {
                    $(this).toggleClass('active');
                    $(this).find('.spin').toggleClass('spin-effect');
                    $(this).next('.accordion-panel').toggleClass('active');
                }
            });
        }
    })
}
//---------- END FPPBJ Baru

//---------- FkPBJ Baru
function getApprovalFKPBJBaru(year) {
    console.log(year);
    $.ajax({
        url: '<?php echo site_url('main/rekapFKPBJBaru') ?>/' + year,
        method: 'get',
        async: false,
        success: function(data) {
            $('#rekapFKPBJBaru').empty();
            $('#rekapFKPBJBaru').append(data);

            $("#rekapFKPBJBaru .accordion-header").click(function() {
                for (i = 0; i < $(this).length; i++) {
                    $(this).toggleClass('active');
                    $(this).find('.spin').toggleClass('spin-effect');
                    $(this).next('.accordion-panel').toggleClass('active');
                }
            });
        }
    })
}
//---------- END FkPBJ Baru

$('#graph_jenis').on('click', '#link_pelelangan', function() {
	window.location.href = site_url + "dashboard/detail_graph/1/" + year;
});

$('#graph_jenis').on('click', '#link_pemilihan', function() {
	window.location.href = site_url + "dashboard/detail_graph/2/" + year;
});

$('#graph_jenis').on('click', '#link_swakelola', function() {
	window.location.href = site_url + "dashboard/detail_graph/3/" + year;
});

$('#graph_jenis').on('click', '#link_penunjukan', function() {
	window.location.href = site_url + "dashboard/detail_graph/4/" + year;
});

$('#graph_jenis').on('click', '#link_pengadaan', function() {
	window.location.href = site_url + "dashboard/detail_graph/5/" + year;
});
</script>
