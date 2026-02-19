<div>
	<h2 style="text-align: center">FORM EVALUASI PRA KUALIFIKASI<br>
RINGKASAN HASIL EVALUASI
	</h2>
	<?php if(isset($csms_file['csms_file'])){ ?>
	<table class="tableData">
		<tr>
			<td>
				Penyedia Barang &amp; Jasa : <?php echo $vendor['legal_name'].' '.$vendor['name'];?>
			</td>
			
		</tr>
		<?php if(isset($get_csms['csms_file'])){ ?>
		<tr>
			<td>
				Lampiran Sertifikat CSMS : <?php echo (isset($get_csms['csms_file'])) ? '<a href="'.base_url('lampiran/csms_file/'.$get_csms['csms_file']).'">Lampiran</a>': '-';?>
			</td>
		</tr>
		<tr>
			<td>
				Masa Berlaku : <?php echo ($get_csms['expiry_date'] == "lifetime") ? "Seumur Hidup" :( (strtotime($get_csms['expiry_date']) > 0) ? default_date($get_csms['expiry_date']) : "-");?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td>
				Skor : <?php echo $get_csms['value'];?>
			</td>
		</tr>
	</table>
	<?php } else{ ?>
	<form method="POST" enctype="multipart/form-data" id="penilaiank3">
	
		<div class="panel-group">
			<p class="noticeMsg">
				Lingkari nomor yang paling baik mewakili evaluasi ini berdasarkan kriteria tujuan rating yang terlampir. <br>
				Untuk kriteria evaluasi, dapat dilihat pada <a href="<?php echo base_url('lampiran/LAMPIRAN CSMS-FORM-3A.pdf');?>" target="_target"><i class="fa fa-download"></i>&nbsp;Lampiran Kriteria Evaluasi Pra Kualifikasi </a> berikut. 
			</p>
			<table class="scoreTable">
				<thead>
					<tr>
						<td>
						</td>
						<td>
							A
						</td>
						<td>
							B
						</td>
						<td>
							C
						</td>
						<td>
							D
						</td>
						
					</tr>
				</thead>
				<tbody>
			<?php foreach($evaluasi as $key_ms => $value_ms){ 

				if(count($value_ms)>1){ ?>

					<tr class="doubleBorder">
						<td><b>Bagian <?php echo $key_ms;?> - <?php echo $ms_quest[$key_ms]?></b></td>
						<td colspan="4"></td>
					</tr>

					<?php foreach($value_ms as $key_ev => $val_ev){ ?>
					
					<tr class="evalQuest radiocsmsWrapper">
						<td class="textQuestLv1"><?php echo $evaluasi_list[$key_ev]['name'];?></td>
						
						<?php echo $this->utility->generate_radio_k3($key_ev,$evaluasi_list,isset($value_k3[$key_ev]) ? $value_k3[$key_ev] : NULL,FALSE, $act); ?>
						
					</tr>

					<?php foreach($val_ev as $key_quest => $val_quest){
						?>
						<tr class="borderQuest ">
							<td class="textQuestLv2">
							<?php foreach($val_quest as $key_answer => $val_answer){
								 ?>
									<?php echo $val_answer['value'];?>
									<p><i>Jawaban : <?php 

									// if(isset($data_k3[$key_answer]['value'])) { echo $data_k3[$key_answer]['value'];}else{echo '-';} 
									switch ($val_answer['type']) {
												default:
												case 'text':
													if(isset($data_k3[$val_answer['id']]['value'])){
														if($data_k3[$val_answer['id']]['value']!=''){
															echo $data_k3[$val_answer['id']]['value'];
														}else{
															echo '-';
														}
													}else{
														echo '-';
													}
													
													break;
												case 'checkbox':
												
													$checkbox = explode('|', $val_answer['label']);

													foreach($checkbox as $key => $row){

														if(isset($data_k3[$val_answer['id']]['value'])){
															if($data_k3[$val_answer['id']]['value']==''){
																echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
															}else{
																if($key == $data_k3[$val_answer['id']]['value']){
																	echo '<label><i class="fa fa-check-square-o"></i>'. $row.'</label>';
																}else{
																	echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
																}
															}
															
														}else{
															echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
														}
													}
													break;
												case 'radio':
												
													$radio = explode('|', $val_answer['label']);

													foreach($radio as $key => $row){

														if(isset($data_k3[$val_answer['id']]['value'])){
															if($data_k3[$val_answer['id']]['value']==''){
																echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
															}else{
																if($key == $data_k3[$val_answer['id']]['value']){
																	echo '<label><i class="fa fa-check-square-o"></i>'. $row.'</label>';
																}else{
																	echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
																}
															}
															
														}else{
															echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
														}
													}
													break;
												case 'file':
													if(isset($data_k3[$val_answer['id']]['value'])){
														if($data_k3[$val_answer['id']]['value']!=''){
															echo '<p><a href="'.base_url('lampiran/'.$field_quest[$val_answer['id']]['label'].'/'.$data_k3[$val_answer['id']]['value']).'" target="_blank">Lampiran</a></p>';
														}else{
															echo '-';
														}
													}else{
														echo '-';
													}
												break;
											}
									?></i></p>

							<?php 	
							}?>
							</td>
							<?php echo $this->utility->generate_radio_k3($key_ev,$evaluasi_list,isset($value_k3[$key_ev]) ? $value_k3[$key_ev] : NULL,TRUE,$act); ?>
						</tr>

						<?php 
					}  
					?>

					<?php 
					}
				}else{ 
					foreach($value_ms as $key_ev => $val_ev){ ?>
					<tr class="doubleBorder radiocsmsWrapper">
						<td class="radioQuest"><b>Bagian <?php echo $key_ms;?> - <?php echo $ms_quest[$key_ms]?></b></td>
						<?php echo $this->utility->generate_radio_k3($key_ev,$evaluasi_list,isset($value_k3[$key_ev]) ? $value_k3[$key_ev] : NULL,FALSE,$act); ?>
					</tr>

					<?php foreach($val_ev as $key_quest => $val_quest){ ?>
						<tr class="borderQuest">
							<td class="textQuestLv2">
						<?php foreach($val_quest as $key_answer => $val_answer){
						
							?>
							
								<?php echo $val_answer['value'];?>
								<p><i>Jawaban : <?php 
								// echo $val_answer['id'];
								// if(isset($data_k3[$key_answer]['value'])) { echo $data_k3[$key_answer]['value'];}else{echo '-';}
									switch ($val_answer['type']) {
												default:
												case 'text':
													if(isset($data_k3[$val_answer['id']]['value'])){
														// echo $data_k3[$key_answer]['value'];
														if($data_k3[$val_answer['id']]['value']!=''){
															echo $data_k3[$val_answer['id']]['value'];
														}else{
															echo '-';
														}
													}else{
														echo '-';
													}
													
													break;
												case 'checkbox':
												
													$checkbox = explode('|', $val_answer['label']);

													foreach($checkbox as $key => $row){

														if(isset($data_k3[$val_answer['id']]['value'])){
															if($data_k3[$val_answer['id']]['value']==''){
																echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
															}else{
																if($key == $data_k3[$val_answer['id']]['value']){
																	echo '<label><i class="fa fa-check-square-o"></i>'. $row.'</label>';
																}else{
																	echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
																}
															}
															
														}else{
															echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
														}
													}
													break;
												case 'radio':
												
													$radio = explode('|', $val_answer['label']);

													foreach($radio as $key => $row){

														if(isset($data_k3[$val_answer['id']]['value'])){
															if($data_k3[$val_answer['id']]['value']==''){
																echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
															}else{
																if($key == $data_k3[$val_answer['id']]['value']){
																	echo '<label><i class="fa fa-check-square-o"></i>'. $row.'</label>';
																}else{
																	echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
																}
															}
															
														}else{
															echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
														}
													}
													break;
												case 'file':
													if(isset($data_k3[$val_answer['id']]['value'])){
														if($data_k3[$val_answer['id']]['value']!=''){
															echo '<p><a href="'.base_url('lampiran/'.$field_quest[$val_answer['id']]['label'].'/'.$data_k3[$val_answer['id']]['value']).'" target="_blank">Lampiran</a></p>';
														}else{
															echo '-';
														}
													}else{
														echo '-';
													}
												break;
											}
								 ?></i></p>
								
								
							
					<?php 	
						} ?>
							</td>
							<?php echo $this->utility->generate_radio_k3($key_ev,$evaluasi_list,isset($value_k3[$key_ev]) ? $value_k3[$key_ev] : NULL,TRUE,$act); ?>
						</tr>
					<?php }  
					?>

					<?php 
						
					}
				}
			} ?>
					<!--<tr>
						<td style="text-align: right">Total : </td>
						<td colspan="4" style="text-align: center"><span id="nilaik3"></span></td>
						<input type="hidden" name="score" id="score_k3">
					</tr>-->
				</tbody>
            </table>

            
		</div>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>
	</form>
	<?php } ?>
</div>