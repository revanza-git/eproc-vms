<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.terbilang.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.jc-form.js"></script>
<script src="<?php echo base_url(); ?>assets/js/numeral.min.js"></script>

<script type="text/javascript">
	var _setTimeout = [];
	var id_lelang		= <?php echo $id_lelang; ?>;
	var id_user			= <?php echo $this->session->userdata('user')['id_user']; ?>;
	var timeLimit		= new Date("<?php echo $fill['time_limit']; ?>");
	var _counter		= <?php echo $penawaran->num_rows() / $barang->num_rows(); ?>;
	var jumlahBarang	= <?php echo $barang->num_rows(); ?>;
	var metodeLelang	= "<?php echo $fill['metode_auction']; ?>";
	var typeLelang		= "<?php echo $fill['auction_type']; ?>";
	var is_started		= <?php if($fill['is_started'])		echo "true"; else echo "false"; ?>;
	var is_finished		= <?php if($fill['is_finished'])	echo "true"; else echo "false"; ?>;
	var is_suspended	= <?php if($fill['is_suspended'])	echo "true"; else echo "false"; ?>;
	var highlighInd		= false;
	var timerInd		= false;
	var isStartDrawed	= false;
	var isAuctionDrawed	= false;
	var isSuspendDrawed	= false;
	var dataBarang		= [];
	var latestOffer		= [];
	var firstIndicator	= 0;
	$('.tooltip').tooltip({
		track: true
	})
	function hide_helper(id, is_first){
		if(is_first) id += '-first'; 

		// $("#total-info-" + id).hide();
	}
	
	function show_heleper(id, vol, is_first){
		if(is_first) id += '-first';
		
		var penawaran = $("#terbilang-" + id).val();
		if(penawaran) penawaran = penawaran.replace(/,/g , "");

		var satuan = parseInt(penawaran / vol);		
			satuan = numeral(satuan).format('0,0.00');
				
		$("#total-info-" + id)
			// .fadeIn()
			.attr('title','Jumlah Barang : '+vol+'. Harga satuan dari penawaran anda : ' + $("#kurs-" + id).find(":selected").text() + ' ' + satuan).tooltip();
			
		/*$("#total-info-" + id).draggable({
		    drag: function(event,ui){
		        $(this).tooltip();
		    }
		});*/
	}
		
	ajaxJsonFeedBack('<?php echo site_url('auction/admin/json_provider/get_barang');?>/' + id_lelang, null, function(data){
		var id_  = '';
		var vol_ = '';

		for(i=0;i<data.length;i++){
			dataBarang.push({ 'id' : data[i].id, 'name' : data[i].name, 'hps' : data[i].hps, 'vol' : data[i].vol });

			id_  = data[i].id;
			vol_ = data[i].vol;
			
			$("#total-info-" + data[i].id).hover(function(){
				
				var penawaran = $(this).val();
				if(penawaran) penawaran = penawaran.replace(/,/g , "");

				var satuan = parseInt(penawaran / vol_);		
				
				$("#total-info-" + id_).attr('title', 'Harga satuan dari penawaran anda : ' + satuan);
				console.log('Jumlah Barang : '+vol_+'. Harga satuan dari penawaran anda : ' + id_);
				
			});
		}
		
		second_check();
	});
	
	function second_check(){
		ajaxJsonFeedBack('<?php echo site_url('auction/admin/json_provider/get_user_update');?>/' + id_lelang + '/' + id_user, null, function(data){
			if(data.status.is_suspended == 1)
				hide_for_suspend();
			else if(data.status.is_started == 0)
				hide_for_start();
			else if(data.status.is_finished == 1)
				hide_for_finished();
			else if(data.status.is_started == 1){
				hide_for_auction();
				
				counter = data.time.now;
				is_started = true;
				timeLimit = new Date(data.time.limit);
			}

			if(is_started){
				update_position(data.data);
				var counter = new Date(data.time.now);
				var distance = convert_to_time(counter, timeLimit);
				$("#timer").html(distance);
			}

			if(!is_finished) _setTimeout.push(setTimeout("second_check()", 1000));
		});
	}
	
	function update_position(data){

		for(i=0;i<data.length;i++){
			// console.log(data.length);
			if(data[i].rank == 0)
				$("#updated-row-warning-" + data[i].id).html('<i class="fa fa-exclamation-triangle warnColor"></i>');
			else if(data[i].rank == 1){				
				$("#updated-row-warning-" + data[i].id).html('');
				
				if(metodeLelang	== "indikator")	
					$("#updated-row-lowest-" + data[i].id).html('<i class="auction-prize3"></i>');
				else if(metodeLelang == "posisi")
					$("#updated-row-lowest-" + data[i].id).html('<b style="font-size : 12px">1</b>');

				$(".updated-row-td-" + data[i].id).animate({ backgroundColor : '#d8f4c4' });
			}
			else if(data[i].rank > 1){
				$("#updated-row-warning-" + data[i].id).html('');
				
				if(metodeLelang	== "indikator")	
					$("#updated-row-lowest-" + data[i].id).html('<i class="auction-thumb1"></i>');
				else if(metodeLelang == "posisi")
					$("#updated-row-lowest-" + data[i].id).html('<b style="font-size : 12px">' + data[i].rank + '</b>');

				$(".updated-row-td-" + data[i].id).animate({ backgroundColor : '#f4c4c4' });
			}
		}
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
		if(distance <= 0) { timerInd = true; is_started = false; }

		if(hh < 0) return "00:00:00";
		
		if(hh < 10) hh = "0" + hh;
		if(mm < 10) mm = "0" + mm;
		if(ss < 10) ss = "0" + ss;

		return hh + ":" + mm + ":" + ss;
	}

	function highlight_timer($this, color){
		$this.animate({ color : color }, 750, function(){
			if(timerInd){ $this.css({ color : "#EDC345" }); return; }

			if(color == "red") highlight_timer($this, "white");
			else highlight_timer($this, "red");
		});
	}

	function hide_for_finished(){
		
		$("#auction-report-bar").fadeIn();
		$("#auction-blocker").fadeOut();
		$(".fixed").hide();
		$("#timer-gap")
			.attr({ vAlign : 'middle', align : 'left', height : '70px' })
			.html('<b style="font-size : 20px"><img src="' + base_url + 'asset/images/pengadaan-icon.png" align="center">History penawaran anda : </b>');
	}

	function hide_for_start(){
		if(isStartDrawed) return;
		isStartDrawed = true;
		
		var syarat = $("#syarat-body").html();
		var title  = "<?php echo $fill['name']; ?>";

		var _return = '<div style="margin-top : 25px; margin-bottom : 25px; color : #000"><b><h1>' + title + '<h1></b></div>'
						+ '<div style="font-size : 20px; text-align : left; margin-left : 20px">Syarat - syarat :</div>'
		  				+ '<div style="font-size : 18px; text-align : left">' + syarat + '</div>';

		 if(_counter <= 0) _return += '<div id="penawaran-awal">' + $("#first-offer").html() + '</div>';
	
		$("#auction-blocker").fadeIn();
		$("#auction-blocker-message-bar").html(_return);

		$('.money-masked-awal').iMask({
			type : 'number',
			decDigits : 0,
			decSymbol : '',
			groupSymbol : ','
		});

		for(i=0;i<dataBarang.length;i++)
			$("#terbilang-" + dataBarang[i].id + "-first").terbilang({ 'style' : 1, 'output_div' : "terbilang-" + dataBarang[i].id + "-container-first" });
 	}


	function hide_for_auction(){
		
		if(isAuctionDrawed) return;
		isAuctionDrawed = true;

		$("#timer-container").fadeIn();
		$("#auction-blocker").fadeOut();

		/*
		$('.money-masked').iMask({
			type : 'number',
			decDigits : 0,
			decSymbol : '',
			groupSymbol : ','
		});
		*/
		
		for(i=0;i<dataBarang.length;i++)
			$("#terbilang-" + dataBarang[i].id).terbilang({ 'style' : 1, 'output_div' : "terbilang-" + dataBarang[i].id + "-container" });

		push_last_data();
		
		$(".lock-offer").click(function(){
			var id = $(this).attr('id');
			
			id = id.replace("lock-offer-", "");

			if($(this).is(':checked'))
				$("#terbilang-" + id).val($("#updated-row-nilai-" + id).text()).attr('readonly','readonly');
			else
				$("#terbilang-" + id).val('').removeAttr('readonly').focus();
		});
	}

	function hide_for_suspend(){
		if(isSuspendDrawed) return;
		isSuspendDrawed = true;
		is_started		= false;
		is_finished		= false;
		is_suspended	= true;
		highlighInd		= false;
		timerInd		= false;
		isStartDrawed	= false;
		isAuctionDrawed	= false;
		
		var _return = '<div style="font-size : 38px; color : red">Auction di suspend sementara</div>'
						+ '<div style="font-size : 18px">Silahkan tunggu perintah selajutnya dari admin auction</div>'
		
		$("#auction-blocker").fadeIn();
		$("#timer-container").fadeOut();
		$("#auction-blocker-message-bar").html(_return);

	}

	function push_last_data(){
		var angka; 
		for(i=0;i<dataBarang.length;i++){
			angka =  $("#updated-row-nilai-" + dataBarang[i].id).html();
			if(angka) angka = angka.replace(/,/g , "");

			latestOffer[i] = angka;
		}
	}

	$("#auction-penawaran-form, #auction-penawaran-form-awal").jcForm({
		"check" : [
			{'name' : 'id_lelang', 'type' : 'function', 'func' : function(){
				var percent, penawaran;
				
				for(i=0;i<dataBarang.length;i++){
					percent = parseInt(latestOffer[i] / 100);
					percent = parseInt(percent * 18);

					penawaran = $("#terbilang-" + dataBarang[i].id).val();
					if(penawaran) penawaran = penawaran.replace(/,/g , "");

					if(penawaran < percent){
						alertify.alert("Penawaran anda untuk " + dataBarang[i].name + " terlalu rendah !");
						return false;
					}
				}
				
				return true;
			}}
		],
		"use_dialog" : false,
		"success" : function(data){
			if(data.status == "success"){
				console.log(data);
				$(".warning-message, .lowest-message").remove();
				
				var _class = "";
				var _proto = "";
				var total_barang = data.barang.length;
				
				var name,
					nilai,
					in_rate,
					percentage,
					_data,
					id,
					temp_nilai,
					temp_rate,
					temp_percentage;
				
				//if(_counter % 2 == 0) _class = "even"; else _class = "odd";
	
				_proto = '<tr id="new-born" style="display : none">';
				_proto += '<td ';
				if(total_barang > 1) _proto += 'rowspan="' + total_barang + '"';
				_proto += 'align="center">' + _counter + '</td>';
	
				for(i=0;i<total_barang;i++){
					_data		= data.barang[i];
					id			= _data.id;
					name		= _data.name;
					nilai		= _data.nilai;
					in_rate		= _data.in_rate;
					percentage	= _data.persentase;
					kurs		= _data.id_kurs;

					temp_nilai		= $("#updated-row-nilai-" + id).html();
					temp_rate		= $("#updated-row-rate-" + id).html();
					temp_percentage	= $("#updated-row-percentage-" + id).html();
					temp_counter	= _counter + 1;
						
					$("#updated-row-nilai-" + id).html(nilai);
					$("#updated-row-rate-" + id).html(in_rate);
					$("#updated-row-percentage-" + id).html(percentage);
					$("#updated-row-index").html(temp_counter);
								
					_proto += '<td>' + name + '</td>';
					_proto += '<td>' + temp_nilai + '</td>';
					_proto += '<td>' + temp_rate + '</td>';
					_proto += '<td>' + temp_percentage + '</td>';
					_proto += '<td align="center"></td>';

					if(data.is_first) $("#select-id-kurs-" + id + ", #id-kurs-" + id).val(kurs);
					if(i == total_barang) _proto += '</tr>'; else _proto += '</tr><tr class="' + _class + '">';

					if(!$("#lock-offer-" + id).is(':checked')) $("#terbilang-" + id).val('');

					if(data.is_first){ $("#rows-barang-" + id + "-first").remove(); firstIndicator++; }
				}
				
				_proto += '</tr>';
				_counter++;
				
				push_last_data();
				//$("#select-id-kurs-" + id + ", #id-kurs-" + id).val();
				
				if(!data.is_first)
					$("#updated-row").before(_proto); 
				else{
					if(jumlahBarang == firstIndicator) $("#penawaran-awal").hide();
				}
				
				console.log(jumlahBarang + ':' + firstIndicator);
				
				// $('html, body').animate({
				// 	scrollTop : parseInt($("#updated-row").offset().top - 100)
				// }, 1000);
			    
				$("#new-born")
					.show()
					.removeAttr('id');

				$('.terbilang-container').html('');
			}
			else
				alertify.alert(data.message);
		}
	});
	
	$(function(){
	
		$(window).on("scroll", function(){
			if($(this).scrollTop() > 85) $("#info-container").css({ position : 'fixed', top : '42px', right : '2px', width : '79.6%', zIndex : '2' });
			else $("#info-container").css({ position : 'absolute', top : '85px', width : '99.5%' });
		});
	});
</script>