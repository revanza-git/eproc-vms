<?php echo $this->session->flashdata('msgSuccess')?>
<?php echo $this->data_process->generate_progress('k3',$id_data)?>
<div>
	<table class="tableData" style="margin-bottom: 20px">
		<tr>
			<td>
				Vendor : <?php echo $legal_name.' '.$name;?>
			</td>
			
		</tr>
		<?php if(isset($get_csms['csms_file'])){ ?>
		<tr>
			<td>
				Lampiran Sertifikat CSMS : <?php echo (isset($get_csms['csms_file'])) ? '<a href="'.base_url('lampiran/k3_files/')."/".$get_csms['csms_file'].'">Lampiran</a>': '-';?>
			</td>
		</tr>
		<tr>
			<td>
				Masa Berlaku : <?php echo default_date($get_csms['expiry_date']);?>
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
		<?php if(!isset($get_csms['csms_file'])){ ?>
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
		<?php } ?>

		<?php if($this->session->userdata('admin')['id_role']==1){?>
		<div class="clearfix" style="text-align: right">
			<div style="margin-bottom: 20px">Tanggal terbit CSMS : <?php echo $this->form->calendar(array('name'=>'start_date','value'=>($this->form->get_temp_data('start_date'))?$this->form->get_temp_data('start_date'):$get_csms['start_date']), false);?></div>
			<label class="nephritisAtt">
				<input type="radio" name="status" value="1" <?php echo $this->data_process->set_yes_no(1,$score_k3['data_status']);?>>&nbsp;<i class="fa fa-check"></i>&nbsp;OK
			</label>
			<label class="pomegranateAtt">
				<input type="radio" name="status" value="0" <?php echo $this->data_process->set_yes_no(0,$score_k3['data_status']);?>>&nbsp;<i class="fa fa-times"></i>&nbsp;Not OK
			</label>
		</div>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>
		<?php }?>
	</form>
</div>