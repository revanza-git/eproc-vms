<!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css"> -->
<link href="<?php echo base_url('assets/js/jQuery.Gantt-master/css/style.css'); ?>" type="text/css" rel="stylesheet">
<!-- <link href="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css" rel="stylesheet" type="text/css"> -->
<style type="text/css">
    table th:first-child {
        width: 150px;
    }
    .data{
        margin-left: 75px;
    }
    .data th, .data td{
        padding: 10px 20px; 
        text-align: left;
    }
    table th:first-child {
        width: 200px;
    }
</style>

    <ul class="info-wrapper">
        <li class="info-row">
            <div class="title">
                Nama Pengadaan <span>:</span>
            </div>
            <div class="info">
                <?php echo $proc['nama_pengadaan'];?>
            </div>
        </li>
        <li class="info-row">
            <div class="title">
                Jenis Pengadaan <span>:</span>
            </div>
            <div class="info">
                <?php echo ucfirst(str_replace('_', ' ', $proc['jenis_pengadaan']));?>
            </div>
        </li>
        <li class="info-row">
            <div class="title">
                Metode Pengadaan <span>:</span>
            </div>
            <div class="info">
                <?php echo $proc['metode_pengadaan'];?>
            </div>
        </li>
        <li class="info-row">
            <div class="title">
                Tahun Anggaran <span>:</span>
            </div>
            <div class="info">
                <?php echo $proc['year_anggaran'];?>
            </div>
        </li>
    </ul>

    <div class="timeline-legend">
        <div class="legend-item">
            <span class="yellow"></span>
            Persiapan & Permohonan Pengadaan (PP1)
        </div>
        <div class="legend-item">
            <span class="red"></span>
            Proses Pengadaan (PP2)
        </div>
        <div class="legend-item">
            <span class="blue"></span>
            Pelaksanaan Pekerjaan (PP3)
        </div>
    </div>

    <div class="timeline-container">
        <div class="gantt"></div>
    </div>

    <div class="tree-wrapper">
        <div class="timeline2">
          <ul> 
          <?php foreach ($proc['detail'] as $key => $value){
                $status = '';
                if ($value['type'] == "fkpbj") {
                    $status = "Edit By : ".$value['user']."</br>File Upload : <a href='".base_url('assets/lampiran/temp/')."/".$value['file']."'>klik disini</a><br>Keterangan: <br>".$value['desc'];
                }else if($value['type'] == "fp3"){
                    $status = 'Telah melakukan fp3';
                }
            ?>
            <li>
              <div class="active">
                <time><a class="buttonView" href="<?php echo site_url($value['type'].'/getSingleData/'.$id) ?>" style="color:white;"><?php echo strtoupper($value['type']);?></a></time> 
                <?php echo $status;?>
              </div>
                <span class="time">
                    <span><?php echo date('M', strtotime($value['entry_stamp']));?> <sup><?php echo date('d', strtotime($value['entry_stamp']));?></sup></span>
                    <br><span><?php echo date('Y', strtotime($value['entry_stamp']));?></span>
                </span>
            </li>
          <?php }?>
          </ul>
        </div>
    </div>

    <?php 
        $encode_jwp      = json_encode(array('start'=>$proc['jwp_start'],'end'=>$proc['jwp_end']));
        $encode_jwpp     = json_encode(array('start'=>$proc['jwpp_start'],'end'=>$proc['jwpp_end']));
        $proc_jwp        = json_decode($encode_jwp);
        $proc_jwpp       = json_decode($encode_jwpp);
        if ($proc_jwp->start != '0000-00-00' && $proc_jwp->end != '0000-00-00') {
            $end_date = $proc_jwp->end;
        } else{
            $end_date = $proc_jwpp->end;
        }
        $start_date = $proc_jwpp->start;
        // print_r($start_date);
        // echo ">>".$start_date.">> TANGGAL >>".date('Y-m-d', strtotime('-11 days', strtotime($start_date)));
    ?>
  <!-- GA N T T  M A S T E R -->

    <script src="<?php echo base_url('assets/js/jQuery.Gantt-master/js/jquery.min.js');?>"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="<?php echo base_url('assets/js/jQuery.Gantt-master/js/jquery.fn.gantt.js');?>"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>

    <!-- 2019 example -->
    <script>
        $(document).ready(function() {
            var from_ = new Date.parse('1-Mar-2019');
            var to_ = new Date.parse('1-Dec-2019');
            $(".gantt2").gantt({
                source: [{
                    name: "Example",
                    desc: "Lorem ipsum dolor sit amet.",
                    values: [{
                        to: to_,
                        from: from_,
                        desc: "Something",
                        label: "Example Value",
                        customClass: "ganttRed"
                    }],
                }],
                scale: "weeks",
                minScale: "days",
                maxScale: "months",
                onItemClick: function(data) {
                    alert("Item clicked - show some details");
                },
                onAddClick: function(dt, rowId) {
                    alert("Empty space clicked - add an item!");
                },
                onRender: function() {
                    console.log("chart rendered");
                }
            });
        })
    </script>

    <!-- actual timeline coding -->
    <script>
        function days_between(date1, date2) {

    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24;

    // Convert both dates to milliseconds
    var date1_ms = date1.getTime();
    var date2_ms = date2.getTime();

    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1_ms - date2_ms);

    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY);

    }

    $(function() {
        // "use strict";
        var demoSource = [{
            name: "Sprint 0",
            desc: "Analysis",
            values: [{
                from: 1320192000000,
                to: 1322401600000,
                label: "Requirement Gathering",
                customClass: "ganttRed"
            }]
            },{
                desc: "Scoping",
                values: [{
                    from: 1322611200000,
                    to: 1323302400000,
                    label: "Scoping",
                    customClass: "ganttRed"
                }]
            },{
                name: "Sprint 1",
                desc: "Development",
                values: [{
                    from: 1323802400000,
                    to: 1325685200000,
                    label: "Development",
                    customClass: "ganttGreen"
                }]
            },{
                name: " ",
                desc: "Showcasing",
                values: [{
                    from: 1325685200000,
                    to: 1325685200000,
                    label: "Showcasing",
                    customClass: "ganttBlue"
                }]
            },{
                name: "Sprint 2",
                desc: "Development",
                values: [{
                    from: 1325695200000,
                    to: 1328785200000,
                    label: "Development",
                    customClass: "ganttGreen"
                }]
            },{
                desc: "Showcasing",
                values: [{
                    from: 1328785200000,
                    to: 1328905200000,
                    label: "Showcasing",
                    customClass: "ganttBlue"
                }]
            },{
                name: "Release Stage",
                desc: "Training",
                values: [{
                    from: 1330011200000,
                    to: 1336611200000,
                    label: "Training",
                    customClass: "ganttOrange"
                }]
            },{
                desc: "Deployment",
                values: [{
                    from: 1336611200000,
                    to: 1338711200000,
                    label: "Deployment",
                    customClass: "ganttOrange"
                }]
            },{
                desc: "Warranty Period",
                values: [{
                    from: 1336611200000,
                    to: 1349711200000,
                    label: "Warranty Period",
                    customClass: "ganttOrange"
            }]
        }];

        // shifts dates closer to Date.now()
        var offset = new Date().setHours(0, 0, 0, 0) - new Date(demoSource[0].values[0].from).setDate(35);
        for (var i = 0, len = demoSource.length, value; i < len; i++) {
            value = demoSource[i].values[0];
            value.from += offset;
            value.to += offset;
        }
        var  _start     = new Date("<?php echo $start_date;?>");
        var  _end       = new Date("<?php echo $end_date;?>");

        // total days of project
        var total_days  = days_between(_start, _end);

        // total days of procurement days based on procurement method
        var proc_days =0; 
		<?php 
		$metode = trim($proc['metode_pengadaan']);
		if ($metode == "Pelelangan") {
            $metode_day = 60; //60 hari
        }else if ($metode == "Pengadaan Langsung") {
            $metode_day = 10;// 10 hari
        }else if ($metode == "Pemilihan Langsung"){
            $metode_day = 45; //45 hari
        }else if ($metode == "Swakelola"){
            $metode_day = 0;
        }else if ($metode == "Penunjukan Langsung") {
            $metode_day = 20;// 20 hari
        }else{
            //$metode_day = 1;
        }
		$start_yellow = $metode_day + 14;
		$end_yellow = $metode_day + 1;
		?>
		console.log('Metode day : '+<?php echo $metode_day; ?>);
        // total days of procurement
        var red          = new Date("<?php echo date('Y-m-d', strtotime('-'.$metode_day.'days', strtotime($start_date)));?>");
        var red_         = new Date("<?php echo date('Y-m-d', strtotime('-1 days', strtotime($start_date)));?>");
        
        // start notification 2 weeks before procurement
        var yellow       = new Date("<?php echo date('Y-m-d', strtotime('-'.$start_yellow.'days', strtotime($start_date)));;?>");
        var yellow_      = new Date("<?php echo date('Y-m-d', strtotime('-'.$end_yellow.'days', strtotime($start_date)));?>");

        var from_   = new Date.parse(_start);
        var to_     = new Date.parse(_end);

        console.log("total days : "+total_days);
        console.log("start : "+from_);
        console.log("end : "+to_);
        console.log("red start : "+red);
        console.log("red end : "+red_);
        console.log("yellow start : "+yellow);
        console.log("yellow end : "+yellow_);

        var _timeline = [{
                name: "<?php echo $proc['nama_pengadaan'];?>",
                values: [{
                    from: from_,
                    to: to_,
                    label: " ",
                    customClass: "ganttBlue"
                },{
                    from: red,
                    to: red_,
                    label: " ",
                    customClass: "ganttRed"
                },{
                    from: yellow,
                    to: yellow_,
                    label: " ",
                    customClass: "ganttOrange"
                }]
        }];

        // shifts dates closer to Date.now()
        /*var offset = new Date().setHours(0, 0, 0, 0) -
            new Date(_timeline[0].values[0].from).setDate(35);
        for (var i = 0, len = _timeline.length, value; i < len; i++) {
            value = _timeline[i].values[0];
            value.from  += offset;
            value.to    += offset;
        }*/

        // Why not try this ?!
        var from_ = new Date.parse('1-Mar-2019');
        var to_ = new Date.parse('1-Dec-2019');

        $(".gantt").gantt({
            source          : _timeline,
            // source          : demoSource,
            navigate        : "scroll",
            scale           : "days",
            maxScale        : "months",
            minScale        : "hours",
            itemsPerPage    : 10,
            scrollToToday   : false,
            useCookie       : false,
                onItemClick: function(data) {
                    // alert("Item clicked - show some details");
                },
                onAddClick: function(dt, rowId) {
                    // alert("Empty space clicked - add an item!");
                },
                onRender: function() {
                    if (window.console && typeof console.log === "function") {
                        console.log("chart rendered");
                    }
                }
        });

        // $(".gantt").popover({
        //     selector: ".bar",
        //         title: function _getItemText() {
        //             return this.textContent;
        //         },
        //     content: "Here's some useful information.",
        //     trigger: "hover",
        //     placement: "auto right"
        // });

        prettyPrint();

    });
</script>
<script>
    (function() {
             
              'use strict';

              // define variables
              var timelines= document.querySelectorAll('.timeline2');
               
                function debounce(func, wait, immediate) {
                    var timeout;
                    return function() {
                        var context = this, args = arguments;
                        var later = function() {
                            timeout = null;
                            if (!immediate) func.apply(context, args);
                        };
                        var callNow = immediate && !timeout;
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                        if (callNow) func.apply(context, args);
                    };
                }
                function callbackFunc() {
                      var h,timeline, li,rect,parent_rect,i,items;
                    for(h=0;h<timelines.length;h++){
                          timeline=timelines[h];
                            parent_rect=timeline.getBoundingClientRect();
                           items = timeline.querySelectorAll(".timeline2 li");
                        for (  i = 0; i < items.length; i++) {
                            /*
                          if (isElementInViewport(items[i])) {
                            items[i].classList.add("in-view");                 
                          }
                          */
                            li=items[i];
                            rect = li.getBoundingClientRect();  
                             
                            if( (rect.bottom<=(parent_rect.top+(rect.height/2) ) ) || (rect.top >=(parent_rect.bottom-(rect.height/2)) ) ){
                                //debugger;
                                //li.style['background']='red';
                                li.classList.remove("in-view");
                                
                            }else{
                                //li.style['background']='white';
                                li.classList.add("in-view");
                            }
                             
                        }
                    }
                }
                var updateLayout =debounce(function(e) {

                    // Does all the layout updating here
                    callbackFunc();
                    
                }, 500); // Maximum run of once per 500 milliseconds

                // listen for events
                window.addEventListener("load", callbackFunc);
                window.addEventListener("resize", updateLayout);
                window.addEventListener("scroll", callbackFunc);
                for(var h=0;h<timelines.length;h++){
                        var  timeline=timelines[h];
                    timeline.addEventListener("scroll",callbackFunc );
                }
                
             })();
</script>
<script type="text/javascript">
    $(function() {
        var edit = $('.buttonView').modal({
            header: 'Info Data',
            render : function(el, data){
                _self = edit;

                data.onSuccess = function(){
                    // $(edit).data('modal').close();
                    // folder.data('plugin_folder').fetchData();
                    location.reload()
                };
                data.isReset = false;
                $(el).form(data).data('form');
            }
        });
    })
</script>