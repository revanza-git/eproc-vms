<style>
	#auction-blocker ol{
		list-style-type: decimal;
	}
	#auction-blocker ul{
		list-style-type: decimal;
	}
	#auction-blocker li{
		list-style-type: decimal;
		padding: 0 5px 3px 0;
		text-align: justify;
	}
</style>


<p id="auction-report-bar" class="msgSuccess text-center">
	<i class="fa fa-check-square-o"></i>Auction telah selesai.
	<b style="color : #617ac6; cursor : pointer">
		<a href="<?php echo site_url('auction/report/index/external/'.$id_lelang.'/'.$this->session->userdata('user')['id_user'])?>" target="_blank">Klik disini</a>
	</b> untuk melihat report.
</p>
<p id="auction-report-bar" class="msgSuccess text-center">
	<i class="fa fa-check-square-o"></i>Auction telah selesai.
	<b style="color : #617ac6; cursor : pointer">
		<a href="<?php echo site_url('auction/report/index/external/'.$id_lelang.'/'.$this->session->userdata('user')['id_user'])?>" target="_blank">Klik disini</a>
	</b> untuk melihat report.
</p>
<div class="lelang">
	<div class="col-14">
		<div class="panel">
			<div class="panel-heading">
				<h4><i class="auction-hammers4"></i><?php echo $fill['name']; ?></h4>
			</div>
			<table class="table table-borderless content-group-sm">
				<tbody>
					<tr>
						<td>
							Mata Uang
						</td>
						<td class="text-right">
							<?php echo $fill['rate']; ?>
						</td>
					</tr>
					<tr>
						<td>
							Metode Penawaran
						</td>
						<td class="text-right">
							<?php 
								if($fill['metode_penawaran'] == "lump_sum") 
									echo "Lump Sum";
								if($fill['metode_penawaran'] == "harga_satuan") 
									echo "Harga Satuan";
							?>
						</td>
					</tr>
					<tr>
						<td>
							Durasi Lelang
						</td>
						<td class="text-right">
							<?php echo $fill['auction_duration']?> Menit
						</td>
					</tr>
					<tr>
						<td>
							Tipe Auction
						</td>
						<td class="text-right">
							<?php 
								if($fill['auction_type'] == "forward_auction") 
									echo "Forward Auction";
								if($fill['auction_type'] == "reverse_auction") 
									echo "Reverse Auction";
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="panel-body">
				<div class="noticeMedia">
					<ul>
						<li class="media">
							<div class="media-left">
								<i class="fa fa-exclamation-triangle warnColor"></i>
							</div>
							<div class="media-body">
								Penawaran anda masih diatas HPS/dibawah nilai limit
							</div>
						</li>
						<li class="media">
							<div class="media-left">
								<i class="auction-prize3"></i>
							</div>
							<div class="media-body">
								Penawaran anda adalah yang <?php echo $limit; ?>
							</div>
						</li>
						<li class="media">
							<div class="media-left">
								<i class="auction-thumb1"></i>
							</div>
							<div class="media-body">
								Penawaran anda bukan yang <?php echo $limit; ?>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="panel " id="timer-container">
			<div class="panel-heading">
				<h4><i class="fa fa-clock-o"></i>&nbsp;Waktu tersisa</h4>
			</div>
			<div class="panel-body">
				<div id="timer">-- : -- : --</div>
			</div>
		</div>
	</div>
	<div class="col-34">
		<div class="panel " id="timer-container">
			<div class="panel-heading">
				<h4>Penawaran</h4>
			</div>
			<div class="panel-body">
				<?php if($kurs_info->num_rows()){ ?>
				<div class="headerBid">
					<ul>
						<?php foreach($kurs_info->result() as $data){ ?>
						<li>
							<i class="fa fa-money"></i> <?php echo $data->name; ?> : IDR <b class="gr-text"><?php echo number_format($data->rate, 2); ?></b>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
				<div class="bodyBid tableWrapper">
					
						<table cellpadding="0" cellspacing="0" border="0" class="auction-dash-table tableData" width="100%">
						<form id="auction-penawaran-form" method="post" action="<?php echo site_url('auction/user/vendor_dash/save_penawaran/'); ?>">
							<thead>
								<tr>
									<th>No.</th>
									<th>Nama Barang</th>
									<th>Penawaran Harga</th>
									<th>Penawaran Harga<br/>(dalam IDR)</th>
									<th><?php echo $persentase; ?> (%)</th>
									<th width="80">
										<?php if($fill['metode_auction'] == "posisi") echo "Posisi/Ranking"; else echo "Indikator"; ?>
									</th>
								</tr>
							</thead>

							<tbody>
								<?php 
									
									/* 1 until n-1 */
								
									$y = 1;
									$count = 0;
									$detail = $penawaran->result_array();
									$total = count($detail) / $barang->num_rows();
									$mark_last = '';
									$useRowspan = '';
									$class="";
									if($barang->num_rows() > 1) $useRowspan = ' rowspan="'.$barang->num_rows().'"';
									
									for($i=0;$i<$total;$i++){ 
										if($count == ($penawaran->num_rows() - $barang->num_rows())) $mark_last = ' id="updated-row"';
										
										//if($y % 2 == 0) $class = "even"; else $class = "odd";
										
										$index = $y;
										if(($fill['is_started'] or $fill['is_suspended']) and $mark_last)
											$index = '<div id="updated-row-index">'.$index.'</div>';
										
										echo '<tr'.$mark_last.'>';
											echo '<td'.$useRowspan.' align="center">'.$index.'</td>';
										
											$x = 0; 
											foreach($barang->result() as $data){ 
												
												$nilai		= number_format($detail[$count]['nilai']);
												$in_rate	= number_format($detail[$count]['in_rate']);
												$percentage	= $this->vdm->cek_percentage($id_lelang, $data->id, $detail[$count]['in_rate'], $detail[$count]['id']);
												$indicator	= '';
												$td_class	= '';
												
												if(($fill['is_started'] or $fill['is_suspended']) and $mark_last){
													$nilai		= '<div id="updated-row-nilai-'.$data->id.'">'.$nilai.'</div>';
													$in_rate	= '<div id="updated-row-rate-'.$data->id.'">'.$in_rate.'</div>';
													$percentage	= '<div id="updated-row-percentage-'.$data->id.'"><span class="gr-text">'.$percentage.'</span></div>';
													$indicator	= '<div id="updated-row-warning-'.$data->id.'"></div>'
																  .'<div id="updated-row-lowest-'.$data->id.'"></div>';
													
													$td_class	= ' class="updated-row-td-'.$data->id.'"';
												}
												
												echo '<td'.$td_class.'>'.$data->nama_barang.'</td>';
												echo '<td'.$td_class.'>'.$nilai.'</td>';
												echo '<td'.$td_class.'>'.$in_rate.'</td>';
												echo '<td'.$td_class.'><span class="gr-text">'.$percentage.'</span></td>';
												echo '<td'.$td_class.' align="center">'.$indicator.'</td>';
												
												if($x == $barang->num_rows()) 
													echo "</tr>"; else echo '</tr><tr class="'.$class.'">';
												
												$count++;
											}	
										$y++;
									}
									
									
									/* n-1 */
						
									if(!$fill['is_started'] and $y <= 1){
										echo '<tr id="updated-row">';
											echo '<td'.$useRowspan.' align="center"><div id="updated-row-index">'.$y.'</div></td>';
											
											$x = 0; 
											foreach($barang->result() as $data){ 
												echo '<td class="updated-row-td-'.$data->id.'">'.$data->nama_barang.'</td>';
												echo '<td class="updated-row-td-'.$data->id.'"><div id="updated-row-nilai-'.$data->id.'"></div></td>';
												echo '<td class="updated-row-td-'.$data->id.'"><div id="updated-row-rate-'.$data->id.'"></div></td>';
												echo '<td class="updated-row-td-'.$data->id.'"><div id="updated-row-percentage-'.$data->id.'"></div></td>';
												echo '<td class="updated-row-td-'.$data->id.'" align="center">';
													echo '<div id="updated-row-warning-'.$data->id.'"></div>';
													echo '<div id="updated-row-lowest-'.$data->id.'"></div>';
												echo '</td>';
												
												if($x == $barang->num_rows()) 
													echo "</tr>"; else echo '</tr><tr class="'.$class.'">';
											}
									}
								?>
								
								<tr class="fixed">
									<td <?php if($barang->num_rows() > 1) echo 'rowspan="'.$barang->num_rows().'"'; ?> align="center"></td>
									<?php $x = 0; 
									foreach($barang->result() as $data){ ?>
									<td><?php echo $data->nama_barang; ?></td>
									<td colspan="4">
										<div class="wrapperBid">
											<div>
												<?php $select = $this->vdm->get_user_currency($id_lelang, 'ASC', $data->id); 
												// echo $data->id;
												// print_r($select);
												?>
												<!-- <input type="hidden" id="id-kurs-<?php echo $data->id; ?>" name="id_kurs[<?php echo $data->id; ?>]" value="<?php echo $select['id_kurs']; ?>"/> -->
												<select id="select-id-kurs-<?php echo $data->id; ?>" name="id_kurs[<?php echo $data->id; ?>]">
												<?php 
													
													foreach($kurs->result() as $_kurs){
														echo '<option value="'.$_kurs->id.'"';
														if($_kurs->id == $select['id_kurs']) echo ' selected="selected"';
														echo '>'.$_kurs->symbol.'</option>';
													} 
												?>
												</select>
											</div>
											<div>
												<!--<div><label><input type="checkbox" class="lock-offer" id="lock-offer-<?php echo $data->id?>"/>Kunci penawaran terakhir</label></div>

-->												<input id="terbilang-<?php echo $data->id; ?>" class="auction-dash-prototype-input money-masked" name="id_barang[<?php echo $data->id; ?>]" type="text" size="50" value=""/>
												<div style="max-width : 300px;" class="terbilangWrap">
													<table cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td style="border : none; font-size : 10px" valign="top"><i>Terbilang</i></td>
															<td style="border : none; font-size : 10px" valign="top" width="20" align="center">:</td>
															<td style="border : none; font-size : 10px" valign="top">
																<b class="terbilang-container" id="terbilang-<?php echo $data->id; ?>-container"></b>
															</td>
														</tr>
													</table>
												</div>
											</div>
											<div>
												<div style="position : relative; z-index : 0">
													<i class="fa fa-info-circle tooltip" style="float : left" id="total-info-<?php echo $data->id; ?>" onmouseout="hide_helper('<?php echo $data->id; ?>')" onmouseover="show_heleper('<?php echo $data->id; ?>', <?php echo $data->volume; ?>,false)"></i>
													<!--<div id="total-info-<?php echo $data->id; ?>" style="float : left; width : 200px; word-wrap: display : none; position : absolute; margin-left : 35px; margin-top : -5px; background : #fff; border-radius : 10px; box-shadow : 0px 0px 5px #3b4663; border : 1px solid #a9b1c7; padding : 10px">
														sd
													</div>-->
												</div>
											</div>
										</div>
									</td>
									<?php if($x == $barang->num_rows()) echo "</tr>"; else echo '</tr><tr class="fixed">'; ?>
									<?php } ?>
								</tr>
								<tr class="fixed">
									<td style="text-align:center;border-bottom: none;" colspan="6">
										<input id="id_lelang" type="hidden" value="<?php echo $id_lelang; ?>" name="id_lelang"/>
										<input type="submit" value="Kirim Penawaran" class="button-submit btnBlue" style="float:none"/>
									</td>
								</tr>
							</tbody>
							</form>
						</table>
					
				</div>
			</div>
		</div>
	</div>
	
</div>
		
<div id="auction-blocker">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
		<tr>
			<td width="100%" height="100%" align="center" valign="middle">
				<div id="auction-blocker-message-bar"></div>
			</td>
		</tr>
	</table>
</div>


<div id="auction-blocker" style="display : none">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
		<tr>
			<td width="100%" height="100%" align="center" valign="middle">
				<div id="auction-blocker-message-bar"></div>
			</td>
		</tr>
	</table>
</div>
<div id="syarat-body" style="display : none"><?php echo $syarat; ?></div>
<div id="first-offer" style="display : none">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td height="30"></td>
		</tr>
		<tr>
			<td align="center">
				<b style="font-size : 20px">Masukan nilai penawaran awal anda :</b>
			</td>
		</tr>
		<tr>
			<td height="30"></td>
		</tr>
		<tr>
			<td align="center">
				<form id="auction-penawaran-form-awal" method="post" action="<?php echo $action; ?>">
					<table cellpadding="0" cellspacing="0" border="0" class="auction-dash-table">
						<thead>
							<tr>
								<th>Nama Barang</th>
								<th>Penawaran Harga Awal</th>
							</tr>
						</thead>
						<tbody>
							<?php $x = 0; foreach($barang->result() as $data){ ?>
							<tr id="rows-barang-<?php echo $data->id; ?>-first" class="fixed">
								<td><?php echo $data->nama_barang; ?></td>
								<td>
									<table cellpadding="4" cellspacing="0" border="0">
										<tr>
											<td valign="top" style="border : none">
												<select id="kurs-<?php echo $data->id; ?>-first" name="id_kurs[<?php echo $data->id; ?>]">
												<?php 
													$select	= $this->vdm->get_default_currency($data->id);
													foreach($kurs->result() as $_kurs){
														echo '<option value="'.$_kurs->id.'"';
														if($_kurs->id == $select['id']) echo ' selected="selected"';
														echo '>'.$_kurs->symbol.'</option>';
													} 
												?>
												</select>
											</td>
											<td align="left" style="border : none">
	
												<input id="terbilang-<?php echo $data->id; ?>-first" class="auction-dash-prototype-input money-masked-awal" name="id_barang[<?php echo $data->id; ?>]" style="width : 250px" type="text" value=""/>
												<div style="max-width : 300px;" class="terbilangWrap">
													<table cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td style="border : none; font-size : 10px" valign="top"><i>Terbilang</i></td>
															<td style="border : none; font-size : 10px" valign="top" width="20" align="center">:</td>
															<td style="border : none; font-size : 10px" valign="top">
																<b style="max-width : 250px" class="terbilang-container" id="terbilang-<?php echo $data->id; ?>-container-first"></b>
															</td>
														</tr>
													</table>
												</div>
	
											</td>
											<td style="border : none">
												<div style="position : relative; z-index : 0">
													<i class="fa fa-info-circle tooltip" id="total-info-<?php echo $data->id; ?>-first" style="float : left" onmouseout="hide_helper('<?php echo $data->id; ?>', true)" onmouseover="show_heleper('<?php echo $data->id; ?>', <?php echo $data->volume; ?>, true)"></i>
													<!--<div id="total-info-<?php echo $data->id; ?>-first" style="float : left; width : 200px; display : none; position : absolute; margin-left : 35px; margin-top : -5px; background : #fff; border-radius : 10px; box-shadow : 0px 0px 5px #3b4663; border : 1px solid #a9b1c7; padding : 10px">
														sd
													</div>-->
												</div>
											</td>
										</tr>
									</table>
									
								</td>
							</tr>	
							<?php } ?>
							
							<tr class="fixed">
								<td style="text-align: center" colspan="2">
									<input id="id_lelang" type="hidden" value="<?php echo $id_lelang; ?>" name="id_lelang"/>
									<input type="hidden" value="1" name="is_first"/>
									<input type="submit" value="Kirim Penawaran"  class="button-submit btnBlue" style="float: none"/>
								</td>
							</tr>
						</tbody>
					</table>
				</form>
				
			</td>
		</tr>
	</table>
</div>