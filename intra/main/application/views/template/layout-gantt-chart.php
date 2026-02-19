<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('assets/js/jQuery.Gantt-master/css/style.css'); ?>" type="text/css" rel="stylesheet">
<link href="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css" rel="stylesheet" type="text/css">
<style type="text/css">
    table th:first-child {
        width: 150px;
    }
</style>
        
        
<div class="container">
    <div class="gantt"></div>
</div>


  <!-- GA N T T  M A S T E R -->

<script src="<?php echo base_url('assets/js/jQuery.Gantt-master/js/jquery.min.js');?>"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo base_url('assets/js/jQuery.Gantt-master/js/jquery.fn.gantt.js');?>"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
<script>
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
        var offset = new Date().setHours(0, 0, 0, 0) -
            new Date(demoSource[0].values[0].from).setDate(35);
        for (var i = 0, len = demoSource.length, value; i < len; i++) {
            value = demoSource[i].values[0];
            value.from += offset;
            value.to += offset;
        }
        var _from       = new Date("2018-03-25");
        var  _to        = new Date("2018-04-30");
        var _from_      = new Date("2018-05-1");
        var  _to_       = new Date("2018-06-25");
        var _timeline = [{
                name: "Pengadaan Kambing",
                values: [{
                    from: _from,
                    to: _to,
                    label: "Step 1",
                    customClass: "ganttYellow"
                },{
                    from: _from_,
                    to: _to_,
                    label: "Step 2",
                    customClass: "ganttRed"
                },{
                    from: _from_,
                    to: _to_,
                    label: "Step 3",
                    customClass: "ganttBlue"
                }]
            }];

        // shifts dates closer to Date.now()
        var offset = new Date().setHours(0, 0, 0, 0) -
            new Date(_timeline[0].values[0].from).setDate(35);
        for (var i = 0, len = _timeline.length, value; i < len; i++) {
            value = _timeline[i].values[0];
            value.from  += offset;
            value.to    += offset;
        }

        $(".gantt").gantt({
            source          : _timeline,
            // source          : demoSource,
            navigate        : "scroll",
            scale           : "weeks",
            maxScale        : "months",
            minScale        : "days",
            itemsPerPage    : 20,
            scrollToToday   : true,
            useCookie       : false,
                onItemClick: function(data) {
                    alert("Item clicked - show some details");
                },
                onAddClick: function(dt, rowId) {
                    alert("Empty space clicked - add an item!");
                },
                onRender: function() {
                    if (window.console && typeof console.log === "function") {
                        console.log("chart rendered");
                    }
                }
        });

        $(".gantt").popover({
            selector: ".bar",
                title: function _getItemText() {
                    return this.textContent;
                },
            content: "Here's some useful information.",
            trigger: "hover",
            placement: "auto right"
        });

        prettyPrint();

    });
</script>
