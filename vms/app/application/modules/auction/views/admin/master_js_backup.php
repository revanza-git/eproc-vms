<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.imask.js')?>"></script>
<script src="<?php echo base_url('assets/js/highchart/js/highcharts.js')?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/js/countdown/jquery.countdown.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/date.format.js')?>"></script>
<script type="text/javascript">
	var _setTimeout = [];
	var id_lelang		= "<?php echo $id_lelang; ?>";
	var is_started		= <?php if($fill['is_started']) 	echo "true"; else echo "false"; ?>;
	var is_suspended	= <?php if($fill['is_suspended']) 	echo "true"; else echo "false"; ?>;
	var is_finished		= <?php if($fill['is_finished']) 	echo "true"; else echo "false"; ?>;
	var timeLimit		= new Date("<?php echo $fill['time_limit']; ?>");
	var typeLelang		= "<?php echo $fill['auction_type']; ?>";
	var barang			= [];
	var _barang			= [];
	var peserta			= [];
	var halfSized 		= "";
	var lastData		= [];
	var hps				= [];
	var hpsIdr			= [];
	var timer1			= "";
	var timer2			= "";
	var highlighInd		= false; 
	var timerInd		= false;

	(function(){

		/**
		 * Decimal adjustment of a number.
		 *
		 * @param	{String}	type	The type of adjustment.
		 * @param	{Number}	value	The number.
		 * @param	{Integer}	exp		The exponent (the 10 logarithm of the adjustment base).
		 * @returns	{Number}			The adjusted value.
		 */
		 
		function decimalAdjust(type, value, exp) {
			// If the exp is undefined or zero...
			if (typeof exp === 'undefined' || +exp === 0) {
				return Math[type](value);
			}
			value = +value;
			exp = +exp;
			// If the value is not a number or the exp is not an integer...
			if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
				return NaN;
			}
			// Shift
			value = value.toString().split('e');
			value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
			// Shift back
			value = value.toString().split('e');
			return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
		}

		// Decimal round
		if (!Math.round10) {
			Math.round10 = function(value, exp) {
				return decimalAdjust('round', value, exp);
			};
		}
		// Decimal floor
		if (!Math.floor10) {
			Math.floor10 = function(value, exp) {
				return decimalAdjust('floor', value, exp);
			};
		}
		// Decimal ceil
		if (!Math.ceil10) {
			Math.ceil10 = function(value, exp) {
				return decimalAdjust('ceil', value, exp);
			};
		}

	})();
	
	start_drawing();
	
	//if(is_started && !is_finished)	start_drawing();
	//if(is_started && is_finished)	start_drawing();
	
	function end_auction(){
		var url = '<?php echo site_url('auction/admin/auction_progress/end_auction/');?>/' + id_lelang;
		var callback = function(data){
			if(data.status == "success"){ is_finished = true; return hide_for_finished(); }
			else if(data.status == "fail"){	is_suspended = true; return hold_for_extend(); }

			$("#timer").html("-- : -- : --");
			is_started = false;
		};
		ajaxJsonFeedBack(url, null, callback);
	}

	function extend_auction(){
		alertify.prompt('Masukkan waktu tambahan (dalam menit)','', function(evt, val){
			var url = '<?php echo site_url('auction/admin/auction_progress/extend_auction/');?>/' + id_lelang;
			var callback = function(data){
				if(data.status == 'success') location.reload();
			};
			ajaxJsonFeedBack(url, {time: val}, callback);
		});
		
	}

	function force_stop(){
		var url = '<?php echo site_url('auction/admin/auction_progress/force_stop/');?>/' + id_lelang;
		var callback = function(data){
			if(data.status == 'success') location.reload();
		};
		ajaxJsonFeedBack(url, null, callback);
	}
	
	function start_auction(){
		var url = '<?php echo site_url('auction/admin/auction_progress/start/');?>/' + id_lelang;
		var callback = function(data){
			if(data.status == 'success'){ timeLimit = new Date(data.time); start_drawing(); hide_for_auction(); }
		};
		ajaxJsonFeedBack(url, null, callback);
	}
	
	function start_drawing(){
		ajaxJsonFeedBack('<?php echo site_url('auction/admin/json_provider/get_peserta');?>/' + id_lelang, null, function(data){
			peserta = data;
	
			ajaxJsonFeedBack('<?php echo site_url('auction/admin/json_provider/get_barang');?>/' + id_lelang, null, function(data){
				barang = data;
				
				for(i=0;i<barang.length;i++){
					curr_barang = barang[i];
					hps[curr_barang.id] = curr_barang.hps;
					hpsIdr[curr_barang.id] = curr_barang.hps_in_idr;

					// console.log(hps);

					_barang[curr_barang.id] = 	{
													color:"#ff0000",
													label: {text: barang[i].name},
													value: barang[i].hps,
													width: 2,
													zIndex: 4
												};
					


					if(barang.length > 1) halfSized = ' style="width : 50%; float : left"';
						
					ajaxJsonFeedBack('<?php echo site_url('auction/admin/json_provider/get_initial_data');?>/' + id_lelang + '/' + curr_barang.id, null, function(data){ 
						$("#chart-container").append('<div id="chart-container-' + data.id + '"' + halfSized + '></div>');
						lastData = data.last;
						// console.log(data);
						create_chart(data.id, data.name, data.subtitle, peserta, data.data);
					});
				}
				// console.log(hpsIdr);

				
				if(!timerInd && !is_finished) _setTimeout.push(setTimeout('update_chart()', 1000));
			});
		});
	}
	
	function get_value(result, index, chartIndex){
		var data = [];
		var a = 0;
		var time = value = "";

		for(x=0;x<result.length;x++){
			time = new Date(result[x].x);
			value = parseInt(result[x].y);

			data.push({
				x : time,
				y : value
			});
		}
		
		return data;
	}

	
	function create_chart(id, title, subTitle, peserta, result, _hps){
		// console.log(_barang);
		
		
		barang_raw = [_barang[id]];
		// console.log(barang_raw);
		console.log(barang_raw);
		// _barang =[{
  //                   value:450,
  //                   color: '#ff0000',
  //                   width:2,
  //                   zIndex:4,
  //                   label:{text:'goal'}
  //               }];
	 	Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });
		
		$('#chart-container-' + id).highcharts({
            chart: {
                type: 'line',
                zoomType: 'x',
                animation: Highcharts.svg,
                marginRight: 10,
            },
            title: {
                text: title
            },
            subtitle: {
                text: subTitle
            },
            xAxis: {
                type: 'datetime'
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
                    text: 'Penawaran'
                },
                plotLines: barang_raw
            },
            tooltip: {
                formatter: function() {
                    var distance = parseFloat(this.y / (hpsIdr[id] / 100) - 100);
                    	distance = Math.ceil10(distance, -2);
                    
                    var color = "";
                    
                    if(distance < 0){
                        distance = parseInt(distance * -1) + "% dibawah HPS";
                        if(typeLelang == "reverse_auction") color = "green"; else color = "red";
                    }
                    else if(distance > 0){
                        distance = distance + "% diatas HPS";
                        if(typeLelang == "reverse_auction") color = "red"; else color = "green";
                    }
                    else if(distance == 0){ distance = "sama dengan HPS " + distance; color = "orange"; }
                      
                    return '<b>' + this.series.name + '</b><br/>'
                        		 + Highcharts.dateFormat('%d/%M/%Y, %H:%M:%S', this.x)
                        		 + '<br/>'
                        		 + Highcharts.numberFormat(this.y, 2) + '<br/>'
                        		 + '<b style="color : ' + color + '">' + distance + '</b>';
                   
                }
            },
            legend: {
                enabled: true
            },
            exporting: {
                enabled: false
            },
            series : (function(){
				var _data = [];
				var value;
				
				for(i=0;i<peserta.length;i++){
					value = get_value(result[i].data, i, id);
					
					_data.push({
						name : peserta[i].name,
						data : value
					});
				}
				// console.log(_data);
				return _data;
			})()  
        });
	}

	function update_chart(){
		ajaxJsonFeedBack('<?php echo site_url('auction/admin/json_provider/get_chart_update');?>/' + id_lelang, null, function(data){

			var chart, _data, value, time, counter, distance;

			counter = data.time;
			data = data.data;
			
			for(i=0;i<data.length;i++){
				chart = $('#chart-container-' + data[i].id).highcharts();
				
				_data = data[i].data;
				
				for(x=0;x<_data.length;x++){
					
					if(_data[x].data[0] != undefined){
						time = (new Date()).getTime(),
						value = parseInt(_data[x].data[0].y);

						if(lastData[i].data[x].data[0] == undefined)
							lastData[i].data[x].data[0] = { 'x' : '' };

						if(lastData[i].data[x].data[0].x != _data[x].data[0].x){
							chart.series[x].addPoint([time, value],true);
							lastData[i].data[x].data[0].x = _data[x].data[0].x;
						}
					}
				}
				
			}

			if(!timerInd) distance = convert_to_time(counter, timeLimit);
			
			$("#timer").html(distance);
			if(!timerInd) _setTimeout.push(setTimeout('update_chart()', 1000));
		});
	}

	function convert_to_time(now, limit){
		counter = new Date(now);
		counter = counter.getTime();
		limit = limit.getTime();

		distance = limit - counter;

		var msec = distance;
		var hh = Math.floor(msec / 1000 / 60 / 60);
		msec -= hh * 1000 * 60 * 60;
		var mm = Math.floor(msec / 1000 / 60);
		msec -= mm * 1000 * 60;
		var ss = Math.floor(msec / 1000);
		msec -= ss * 1000;

		if(distance <= 60000 && !highlighInd){ highlight_timer($("#timer"), "red");  highlighInd = true; }
		if(distance <= 0) { timerInd = true; end_auction(); }

		if(hh < 10) hh = "0" + hh;
		if(mm < 10) mm = "0" + mm;
		if(ss < 10) ss = "0" + ss;

		return hh + ":" + mm + ":" + ss;
	}

	function highlight_timer($this, color){
		$this.animate({ color : color }, 750, function(){
			if(timerInd) return;

			if(color == "red") highlight_timer($this, "white");
			else highlight_timer($this, "red");
		});
	}
	
	function hold_for_extend(){
		var _return = '<div style="font-size : 25px">Terdapat barang/jasa dengan harga penawaran masih berada diatas HPS/dibawah nilai limit</div>'
			+ '<div style="margin-top : 35px">'
				+ '<input style="width : 200px" onclick="extend_auction()" type="button" value="Perpanjang Waktu Auction" class="auction-button btn-blue"/>'
				+ '<input style="width : 200px; margin-left : 10px" onclick="force_stop()" type="button" value="Tutup Auction" class="auction-button btn-blue"/>'
			+ '</div>';

		$("#auction-blocker").fadeIn();
		$("#timer-container").fadeOut();
		$("#auction-blocker-message-bar").html(_return);

	}

	function show_for_start(){
		var syarat = $("#syarat-body").html();
		var title  = "<?php echo $fill['name']; ?>";

		var _return = '<div style="margin-top : 25px; margin-bottom : 25px; color : #000"><b><h1>' + title + '<h1></b></div>'
						+ '<div style="font-size : 20px; text-align : left; margin-left : 20px">Syarat - syarat :</div>'
		  				+ '<div style="font-size : 18px; text-align : left">' + syarat + '</div>'
		  				+ '<div style="font-size : 18px; text-align : center"><div style="margin-top : 15px; align : center">	<input type="button" onClick="hide_syarat()" value="Lanjutkan" class="button-submit btnBlue" style="float:none"/></div></div>';
		$("#auction-blocker-message-bar").html(_return);
		$("#timer-container").fadeOut();
		$("#auction-blocker").fadeIn();

		$("#start-auction")
			.html('<div style="margin-top : 15px; align : center"><input onclick="start_auction()" type="button" value="Mulai Auction" class="auction-button btn-blue"/></div>');
	}
	function hide_syarat(){
		$("#auction-blocker").fadeOut();
	}
	function hide_for_start(){	
		var _return = '<div>Auction belum di mulai</div>'
			+ '<div style="margin-top : 15px"><input onclick="start_auction()" type="button" value="Mulai Auction" class="auction-button btn-blue"/></div>';

		$("#timer-container").fadeOut();
		$("#auction-blocker-message-bar").html(_return);
	}

	function hide_for_finished(){	
		$("#auction-blocker").fadeOut();
		$("#timer-container").fadeOut();
		$("#auction-report-bar").fadeIn();
	}

	function hide_for_auction(){	
		$("#auction-blocker").fadeOut();
		$("#start-auction").fadeOut();
		$("#timer-container").fadeIn();
	}
	
	$(function(){
		$(window).on("scroll", function(){
			// if($(this).scrollTop() > 85) $("#timer-container").css({ position : 'fixed', top : '42px' });
			// else $("#timer-container").css({ position : 'absolute', top : '85px' });
		});
		
		if(is_suspended) hold_for_extend();
		else if(!is_started) show_for_start();
		else if(is_finished) hide_for_finished();
		else if(is_started && !is_finished) hide_for_auction();
	});
</script>
