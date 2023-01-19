<?php echo $this->session->flashdata('msgSuccess')?>
<div class="tableWrapper formAss" style="margin-bottom: 20px;padding-left: 20px;">
	<h1 class="formHeader">Penilaian Penyedia Barang &amp; Jasa</h1>
	<form method="POST" enctype="multipart/form-data" id="assForm">
		
			<div class="panel-group">
				<div class="panel">
					<!-- Klasifikasi Warna -->
					<ul id="klasifikasiWarna">
						<li style="border-left: 10px #e74c3c solid;" >Logistik</li>
						<li style="border-left: 10px #27ae60 solid;" >HSE</li>
						<li style="border-left: 10px #2980b9 solid;" >User</li>
					</ul>
					<!-- Klasifikasi Warna -->
				<?php 
				$i=1;
				foreach($assessment_question as $key => $value){ ?>
				<?php 
					$hide="";
					foreach ($role as $keyr => $valuer) {
						if($valuer['id_role']==2 && $key==3){
							$hide="style='display: none;'";
						}elseif($valuer['id_role']==9 && $key==3){
							$hide="style='display: none;'";
						}
					}
				?>
					<div class="panel-body" <?php echo $hide;?> >
						<h4 class="panel-title"><?php echo $value['name']; ?></h4>
						<ul class="assQuest">
							<?php
							foreach($value['quest'] as $row => $val){
								$is_id = $this->session->userdata('admin')['id_role']==$val['id_role'];
							?>

							<?php 
								$class="";
								if ( $val['id_role']==2) {
									$class = "hseForm";
								}elseif($val['id_role']==3){
									$class = "logistikForm";
								}else{
									$class = "userForm";
								}
								$border = "style='border-right:5px solid #27ae60;'"
							?>
							<li <?php echo 'class="'.$class.'"'; if($this->session->userdata('admin')['id_role']==2&&$val['id']==7){ echo $border;}?>>
								<div class="fieldPanel <?php echo ($is_id||($this->session->userdata('admin')['id_role']==2&&$val['id']==7))?'':'questGrey';?>">
									<div class="questBox">
										<p><?php echo $i.'. '.$val['value']; ?></p>
									</div>
									<div class="questBobot">
										<span>Poin : <?php echo $val['point'];?></span>
									</div>
									<div class="questCheck">
										<label>
											<?php if($this->session->userdata('admin')['id_role']==3){ ?>
											<input type="hidden" value="<?php echo $data_assessment[$val['id']] ?>" name="ass[<?php echo $val['id']; ?>]">
											<?php 
											}
											if($this->session->userdata('admin')['id_role']==2&&$val['id']==7){ 
												?>

												<input type="checkbox" value="1" name="is_approve[<?php echo $val['id']; ?>]" <?php echo ($data_approve[$val['id']]==1)?'checked':'';?> class="mandatoryCheck">Menyetujui
											<?php }else{
												if($is_id){ 
												?>
												<select name="ass[<?php echo $val['id']; ?>]" class="selectAss">
													<option value="">Belum Dinilai</option>
													<option value="<?php echo $val['point']; ?>" <?php echo ($data_assessment[$val['id']]==$val['point'])?'selected':'';?>>Memenuhi</option>
													<option value="0" <?php echo ($data_assessment[$val['id']]=='0')?'selected':'';?> >Tidak Memenuhi</option>
												</select>
												<?php }else{ 

													if(isset($data_assessment[$val['id']])&&$data_assessment[$val['id']]!=''){
														if($data_assessment[$val['id']]!='0'){
															echo 'Memenuhi';
														}else if($data_assessment[$val['id']]=='0'){
															echo 'Tidak Memenuhi';
														}
													}
													else{
														echo 'Belum Dinilai';
													}
												}
												if($val['id']==7){
													if($data_approve[$val['id']]==1){ ?>
														<p><i class="fa fa-check-square-o"></i>&nbsp; Disetujui User HSE</p>
													<?php }else{ ?>
														<p><i class="fa fa-square-o"></i>&nbsp; Belum disetujui User HSE</p>
													<?php }
												}
											}	 ?>
										</label>
										<!--
										<input type="hidden" name="ass[<?php echo $val['id']; ?>]" value="0">
										<?php if($is_id){ ?>
										<input type="checkbox" name="ass[<?php echo $val['id']; ?>]" value="<?php echo $val['point']; ?>" 

										<?php 
										if(isset($data_assessment[$val['id']])){
											if($data_assessment[$val['id']]==$val['point']){
												echo 'checked';
											}
										} ?>
										/>
										<?php }else{
											if(isset($data_assessment[$val['id']])){
												if($data_assessment[$val['id']]==$val['point']){
													echo '<i class="fa fa-check"></i>';
												}else{
													echo '<i class="fa fa-minus"></i>';
												}
											} else{
													echo '<i class="fa fa-minus"></i>';
												}
										} ?>
										-->
										

									</div>
								</div>
							</li>
							<?php
							$i++;
							 } ?>
						</ul>
						
		            </div>
		       
				<?php 
				
				} ?>
			 </div>
              	
		</div>
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>

	</form>
	
</div>