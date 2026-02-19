<?php echo $this->session->flashdata('msgSuccess')?>
<div>
	<h2 style="text-align: center">FORM EVALUASI PRA KUALIFIKASI<br>
RINGKASAN HASIL EVALUASI
	</h2>
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
				Skor : <?php echo $get_csms['score'];?>
			</td>
		</tr>
		<tr>
			<td>
				Kategori : <?php echo $get_csms['value'];?>
			</td>
		</tr>
	</table>

	<form method="POST" enctype="multipart/form-data">
	
		<div class="panel-group">
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
						<td>
							Subtotal
						</td>
						<td>
							Faktor
						</td>
						<td>
							Total
						</td>
					</tr>
				</thead>
				<tbody>
			<?php 
			$total = 0;
			foreach($evaluasi as $key_ms => $value_ms){
				
				if(isset($value_ms)){ ?>

					<tr class="doubleBorder">
						<td><b>Bagian <?php echo $key_ms;?> - <?php echo $ms_quest[$key_ms]?></b></td>
						<td colspan="7"></td>
					</tr>
					<?php 
					$subtotal = 0;
					$total_data = count($value_ms);
					foreach($value_ms as $key_ev => $val_ev){ ?>
					
					<tr class="evalQuest">
						<td class="textQuestLv1"><?php echo $evaluasi_list[$key_ev]['name'];?></td>
						<?php echo $this->utility->generate_checked_k3($key_ev,$evaluasi_list,isset($value_k3[$key_ev]) ? $value_k3[$key_ev] : NULL); ?>
					</tr>
					<?php 
					
					foreach($val_ev as $key_quest => $val_quest){
						?>
						<tr class="borderQuest">
							<td class="textQuestLv2">
							<?php foreach($val_quest as $key_answer => $val_answer){
								 ?>
									<?php echo $val_answer['value'];?>
									<p><i>Jawaban : <?php 

										//if(isset($data_k3[$key_answer]['value'])) { echo $data_k3[$key_answer]['value'];}else{echo '-';} 

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
							<?php echo $this->utility->generate_checked_k3($key_ev,$evaluasi_list,isset($value_k3[$key_ev]) ? $value_k3[$key_ev] : NULL,TRUE); ?>
						</tr>

						<?php 
						
					} 

					$subtotal += isset($value_k3[$key_ev]) ? $value_k3[$key_ev] : NULL;

					}?>
					<tr class="subTotalQuest">
						<td colspan="5">
							Subtotal
						</td>
						<td>
							<?php echo $subtotal;?>
						</td>
						<td>
							X <?php echo ($total_data==1) ? 1 :'1/'.$total_data;?>
						</td>
						<td>
							<?php 
								$total_sub = round($subtotal / $total_data,2);
								$total +=$total_sub;
								echo $total_sub;
							?>
						</td>
					</tr>
				<?php }else{ 

					$subtotal = 0;
					$total_data = count($value_ms);
					foreach($value_ms as $key_ev => $val_ev){ ?>
					<tr class="doubleBorder">
						<td class="radioQuest"><b>Bagian <?php echo $key_ms;?> - <?php echo $ms_quest[$key_ms]?></b></td>
						<?php echo $this->utility->generate_checked_k3($key_ev,$evaluasi_list,$value_k3[$key_ev]); ?>
					</tr>

					<?php 
					
					foreach($val_ev as $key_quest => $val_quest){ ?>
						<tr class="borderQuest">
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
							<?php echo $this->utility->generate_checked_k3($key_ev,$evaluasi_list,$value_k3[$key_ev],TRUE); ?>
						</tr>
					<?php 

						}
						// echo $subtotal;  
						$subtotal += $value_k3[$key_ev];
					}
					?>
					<tr class="subTotalQuest">
						<td colspan="5">
							Subtotal
						</td>
						<td>
							<?php echo $subtotal;?>
						</td>
						<td>
							X <?php echo ($total_data==1) ? 1 :'1/'.$total_data;?>
						</td>
						<td>
							<?php 
								$total_sub = round($subtotal / $total_data,2);
								$total += $total_sub;
								echo $total_sub;
							?>
						</td>
					</tr>
					<?php
				}
				
			} ?>
					<tr class="totalAllQuest">
						<td colspan="5"><p>Nilai numerik di samping ini adalah rating pemberatan yang dihitung di atas. Totalnya mewakili angka keseluruhan untuk kontraktor.</p></td>
						<td colspan="2"><b>Total</b></td>
						<td><b><?php echo $get_csms['score'];?></b></td>
					</tr>
					<tr class="totalAllQuest">
						<td colspan="5"></td>
						<td colspan="2"><b>Kategori</b></td>

						
						<td><b><?php echo $get_csms['value'];?></b></td>
					</tr>				</tbody>
            </table>

		</div>

		<div class="buttonRegBox clearfix">
			<a href="<?php echo site_url('k3/penilaian_k3/'.$id.'/edit/'.$get_csms['id']);?>" class="btnBlue"><i class="fa fa-cog"></i> Edit</a>
		</div>
	</form>
</div>