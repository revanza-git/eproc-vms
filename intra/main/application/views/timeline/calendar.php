<!DOCTYPE html>
<html lang="en">
<head>
    <title>Starter Kit</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="<?php echo base_url('assets/js/jQuery.Gantt-master/css/style.css'); ?>" type="text/css" rel="stylesheet">
	<meta name="viewport" content="maximum-scale=1,width=device-width,initial-scale=1,user-scalable=0">

</head>

<body>

	<div class="gantt"></div>
	
	<script src="<?php echo base_url('assets/js/jQuery.Gantt-master/js/jquery.min.js');?>"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="<?php echo base_url('assets/js/jQuery.Gantt-master/js/jquery.fn.gantt.js');?>"></script>
    <script src="<?php echo base_url('assets/js/Datejs-master/build/date.js');?>"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
	
	<script>
		$(document).ready(function() {
			var from_ = new Date.parse('1-Mar-2019');
			var to_ = new Date.parse('1-Dec-2019');
			$(".gantt").gantt({
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
				minScale: "weeks",
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

</body>
</html>