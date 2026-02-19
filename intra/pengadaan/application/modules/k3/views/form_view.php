<?php echo $this->session->flashdata('msgSuccess')?>
<div>
	<h2 style="text-align: center">SISTEM EVALUASI MANAJEMEN K3 KONTRAKTOR<br>CHECKLIST SISTEM RATING PRA KUALIFIKASI</h2>
	<?php 
	//echo print_r($csms_file);
	if(isset($csms_file['csms_file']['csms_file'])&&!empty($csms_file['csms_file']['csms_file'])){ ?>
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form ">
				<td><label>Lampiran Sertifikat CSMS</label></td>
				<td>: <?php  if(isset($csms_file['csms_file']['csms_file'])){?><a href="<?php echo base_url('lampiran/csms_file/'.$csms_file['csms_file']['csms_file']);?>" target="_blank">Lampiran</a><?php }else{echo "-";}?>
					
				</td>
			</tr>
			<tr class="input-form ">
				<td><label>Masa Berlaku</label></td>
				<td>: <?php  if(isset($csms_file['csms_file']['expiry_date'])){?>
					<?php echo (default_date(date('d-m-Y', strtotime($csms_file['csms_file']['expiry_date']))))?default_date(date('d-m-Y', strtotime($csms_file['csms_file']['expiry_date']))):'-';?>
					<?php }else{echo "-";} ?>
				</td>
			</tr>
			<tr class="input-form ">
				<td><label>Skor</label></td>
				<td>
					: <?php echo $csms_file['csms_file']['score']?>
				</td>
			</tr>
			<tr class="input-form ">
				<td><label>Kualifikasi</label></td>
				<td>
					: <?php echo $csms_file['csms_file']['value']; ?>
				</td>
			</tr>
		</table>
		<div class="btnTopGroup clearfix">
			
			<a href="<?php echo site_url('k3/csms_edit');?>" class="btnBlue"><i class="fa fa-cog"></i> Edit CSMS</a>
			&nbsp;
			<a href="<?php echo site_url('k3/reset_csms/');?>" class="btnBlue"><i class="fa fa-cog"></i> Reset</a>
		</div>
	</form>
	<?php }else{ ?>
		<div class="btnTopGroup clearfix">
			
			<a href="<?php echo site_url('k3/edit')?>" class="btnBlue"><i class="fa fa-gear"></i> Edit</a>
			&nbsp;
			<a href="<?php echo site_url('k3/reset_csms/');?>" class="btnBlue"><i class="fa fa-cog"></i> Reset</a>
		</div>
	<form method="POST" enctype="multipart/form-data">
		<div class="panel-group">
			<?php foreach ($quest_all as $key => $header) { ?>
			<div class="panel">
				<div class="panel-heading">
					<h3>Bagian <?php echo $key.'&nbsp;-&nbsp;'.$header['label'];?>
					<h3>
				</div>
				<?php if (isset($header['data'])) {?>
				<?php $no = 1;?>
				<?php foreach ($header['data'] as $keysq => $valuesq) {?>
				<div class="panel-body">
					<?php if (isset($valuesq['question'])) { ?>
					<h4 class="panel-title"><?php echo $key.'.'.$no.'&nbsp;-&nbsp;'.$valuesq['question'];?>
					</h4>
					<?php $no++; }?>
					
					<ol type="a" style="list-style-type: none;">
						<?php if (isset($valuesq['data'])) { ?>
						<?php foreach ($valuesq['data'] as $keyq => $valueq) {?>
						<li>
							<div class="fieldPanel">
							<!--Pertanyaan-->
							<?php foreach ($valueq as $keydata => $valuedata) { ?>
								<p><?php echo $valuedata['value']; ?></p>
								<?php//echo print_r($valuedata);?>
								<?php
									
									switch ($valuedata['type']) {
												default:
												case 'text':
													if(isset($data_k3[$keydata]['value'])){
														if($data_k3[$keydata]['value']!=''){
															echo $data_k3[$keydata]['value'];
														}else{
															echo '-';
														}
													}else{
														echo '-';
													}
													
													break;
												case 'checkbox':
												
													$checkbox = explode('|', $valuedata['label']);

													foreach($checkbox as $key => $row){

														if(isset($data_k3[$keydata]['value'])){
															if($data_k3[$keydata]['value']==''){
																echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
															}else{
																if($key == $data_k3[$keydata]['value']){
																	echo '<label><i class="fa fa-check-circle-o"></i>'. $row.'</label>';
																}else{
																	echo '<label><i class="fa fa-circle-o"></i>'. $row.'</label>';
																}
															}
															
														}else{
															echo '<label><i class="fa fa-circle-o"></i>'. $row.'</label>';
														}
													}
													break;
												case 'radio':
												
													$radio = explode('|', $valuedata['label']);

													foreach($radio as $key => $row){

														if(isset($data_k3[$keydata]['value'])){
															if($data_k3[$keydata]['value']==''){
																echo '<label><i class="fa fa-square-o"></i>'. $row.'</label>';
															}else{
																if($key == $data_k3[$keydata]['value']){
																	echo '<label><i class="fa fa-check-circle-o"></i>'. $row.'</label>';
																}else{
																	echo '<label><i class="fa fa-circle-o"></i>'. $row.'</label>';
																}
															}
															
														}else{
															echo '<label><i class="fa fa-circle-o"></i>'. $row.'</label>';
														}
													}
													break;
												case 'file':
													if(isset($data_k3[$keydata]['value'])){
														if($data_k3[$keydata]['value']!=''){
															echo '<p><a href="'.base_url('lampiran/'.$field_quest[$keydata]['label'].'/'.$data_k3[$keydata]['value']).'" target="_blank">Lampiran</a></p>';
														}else{
															echo '-';
														}
													}else{
														echo '-';
													}
												break;
											}
								?>
							<?php } ?>
							<!--Pertanyaan-->
							</div>
						</li>
						<?php } ?>
						<?php } ?>
					</ol>
					
	            </div>
	            <?php } ?>
	        	<?php } ?>
	        </div>
	        <?php } ?>
	    </div>

	</form>
	<?php } ?>
</div>