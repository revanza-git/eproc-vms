<style type="text/css">
	input[type=file]{ 
        color:transparent;
        width: 62px;
    }
    .cg__wrapper {
				display: flex;
				justify-content: space-between;
				width: 90%;
				min-height: 250px;
				padding: 0 8px;
				border-bottom: 1px solid #ddd;
				margin-left: 40px;
				margin-top: -170px;
			}

			.cg__wrapper .cg__item {
				padding: 8px;
				position: relative;
			}

			.cg__wrapper .cg__item .cg__popup {
				background-color: rgba(0, 0, 0, 0.2);
				color: #fff;
				font-size: 17px;
				text-align: center;
				padding: 8px;
				min-width: 150px;
				visibility: hidden;
				opacity: 0;
				transition: all .3s;
				position: absolute;
				bottom: -35px;
				left: 50%;
				transform: translate(-50%, 0);
			}

			.cg__wrapper .cg__item .cg__popup:before {
				content: "";
				position: absolute;
				top: 100%;
				left: 50%;
				margin-left: -5px;
				border-width: 5px;
				border-style: solid;
				border-color: rgba(0, 0, 0, 0.2) transparent transparent transparent;
			}

			.cg__wrapper .cg__item .cg__popup.active {
				visibility: visible;
				opacity: 1;
				bottom: 15px;
			}

			.cg__wrapper .cg__item .dot {
				position: absolute;
				display: block;
				height: 10px;
				width: 10px;
				border-radius: 50%;
				background-color: #d63031;
				bottom: -5px;
				left: 50%;
				transform: translate(-50%, 0);
				cursor: pointer;
			}
</style>
<div class="tab procView">

	<?php 

		if ($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10||$this->session->userdata('admin')['id_role']==9) {

			echo $this->utility->tabNav($tabNav,'progress_pengadaan');

		}?>

	<div class="tableWrapper">

	<?php echo $this->session->flashdata('msgSuccess')?>

			

			<div class="tableWrapper">
			
				<p>Grafik Progress</p>

				<div class="graphBar clearfix">

						<div class="graphBarGroup clearfix barPengadaan" style="width: 100%;">

							<table width="100%">

								<tr class="graphWrap">
								<!-- <div id="chart">
									
								</div> -->
								<div class="cg__wrapper">
									<?php 
										foreach($pengadaan as $key => $value) { ?>
										<div class="cg__item">
											<div class="cg__popup">
												<?php echo $value['value'] ?>
											</div>
											<span class="dot"></span>
										</div>
									<?php	
										}
									?>
								</div>
								<?php //if(isset($pengadaan)){?>

									<?php //foreach($pengadaan as $key => $row){ ?>

										<!-- <td class="graph" title="<?php //echo $row['value'];?> (Tanggal : <?php //echo default_date($row['date'])?>) " style="width:<?php //echo $row['percent'];?>%;background: <?php //echo $row['color'];?>"></td>
 -->
									<?php //} ?>

								<?php //} ?>

								<tr>

							</table>

						</div>





					<ul>

					<?php //if(isset($pengadaan)){ ?>

						<?php //foreach($pengadaan as $_key => $_row){ ?>

						<!-- <li style="margin-bottom: 5px; padding-left: 10px; border-left: 15px solid <?php //echo $_row['color'];?>;"><?php //echo $_row['value'];?></li> -->

						<?php //} ?>

					<?php //} ?>

					</ul>

					<p class="notifReg"><i>*Arahkan Mouse untuk melihat keterangan pada Grafik Progress</i></p>

				</div>
				<br><br><br><br><br>
				<a href="<?= site_url('pengadaan/view/'.$id_pengadaan.'/tambah_progress_pengadaan') ?>" class="btnBlue"><i class="fa fa-plus"></i> Tambah Proses Pengadaan</a>
				<br><br>
				<?php echo form_open_multipart();?>

					<div class="pengadaanStep">
					
						<table class="tableData">

						<thead>
						
							<tr>
								<td>Proses Pengadaan</td>
								<td style="text-align: center">Status</td>
								<td>Tanggal</td>
								<td class="actionPanel">Action</td>
							</tr>

						</thead>
						
						<?php foreach($step_pengadaan as $key => $val){ ?>

							<tr>
								<td><?php echo $val['value']?></td>

								<td style="text-align: center"><?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
									<input type="hidden" name="progress[<?php echo $key;?>]" value="0">
									<input type="checkbox" name="progress[<?php echo $key;?>]" value="1" <?php echo ($progress[$key]['value'])?'checked':'';?>>
									<?php }else{  
										echo ($progress[$key]['value'])?'<i class="fa fa-check-square-o"></i>':'<i class="fa fa-square-o"></i>';
									} ?>
								</td>

								<td>
									<?php 
									if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){
										if($progress[$key]['date'] > 0){
											#echo default_date($progress[$key]['date']);
											echo $this->form->calendar(array('name'=>'date['.$key.']','value'=>$progress[$key]['date']), false);
										}else{
											echo $this->form->calendar(array('name'=>'date['.$key.']'), false);
										}
										if ($key == 10) {
											# code...
												echo " - ".$this->form->calendar(array('name'=>'date_['.$key.']','value'=>$progress[$key]['date']), false);
										}
									}else{
										echo default_date($progress[$key]['date']);
									}?>
								</td>
								<td width="70px">
									<?php 
									$lampiran = explode(";", $val['lampiran']);
									$a = '<i class="fa fa-square-o"></i>';
									foreach ($lampiran as $lampiran_) {
										if ($lampiran_ != null) {
											echo "<a style='margin-right: 5px' href='".base_url('lampiran/progress_pengadaan/file/')."/".$lampiran_."' title='".$lampiran_."'><i class='fa fa-download'></i></a>";
										}else{
											echo "--";
										}
									}?>
								</td>
								<td width="162px">
									<?php if (($progress[$key]['file'] != '')) {
										echo "<a style='margin-right: 5px' href='".base_url('lampiran/progress_pengadaan/')."/".$progress[$key]['file']."' title='".$progress[$key]['file']."' target='_blank'><i class='fa fa-download'></i></a>";
									?>
										<?php if ($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10) { ?>
											<input type="file" name="file_upload[<?php echo $key;?>]">
										<?php } ?>
									<?php
									}else{
									?>
										<?php if ($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10) { ?>
											<input type="file" name="file_upload[<?php echo $key;?>]">
										<?php } ?>
									<?php }?>
									
									
								</td>

							</tr>

						<?php 
							}
						if (count($custom_step_pengadaan) > 0) {
							foreach($custom_step_pengadaan as $key => $val){
						?>
													<tr>
								<td><?php echo $val['value']?></td>

								<td style="text-align: center"><?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>
									<input type="hidden" name="progress[<?php echo $key;?>]" value="0">
									<input type="checkbox" name="progress[<?php echo $key;?>]" value="1" <?php echo ($progress[$key]['value'])?'checked':'';?>>
									<?php }else{  
										echo ($progress[$key]['value'])?'<i class="fa fa-check-square-o"></i>':'<i class="fa fa-square-o"></i>';
									} ?>
								</td>

								<td>
									<?php 
									if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){
										if($progress[$key]['date'] > 0){
											#echo default_date($progress[$key]['date']);
											echo $this->form->calendar(array('name'=>'date['.$key.']','value'=>$progress[$key]['date']), false);
										}else{
											echo $this->form->calendar(array('name'=>'date['.$key.']'), false);
										}
										if ($key == 10) {
											# code...
												echo " - ".$this->form->calendar(array('name'=>'date_['.$key.']','value'=>$progress[$key]['date']), false);
										}
									}else{
										echo default_date($progress[$key]['date']);
									}?>
								</td>
								<td width="100px">
									<?php 
									$lampiran = explode(";", $val['lampiran']);
									$a = '<i class="fa fa-square-o"></i>';
									foreach ($lampiran as $lampiran_) {
										if ($lampiran_ != null) {
											echo "<a style='margin-right: 100px' href='".base_url('lampiran/new_test/')."/".$lampiran_."' title='".$lampiran_."'><i class='fa fa-download'></i></a>";
										}else{
											echo "--";
										}
									}?>
								</td>
								<td width="200px">
									<?php if (($progress[$key]['file'] != '')) {
										echo "<a style='margin-right: 100px' href='".base_url('lampiran/new_test/')."/".$progress[$key]['file']."' title='".$progress[$key]['file']."' target='_blank'><i class='fa fa-download'></i></a>";
									?>
										<?php if ($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10) { ?>
											<input type="file" name="file_upload[<?php echo $key;?>]">
										<?php } ?>
									<?php
									}else{
									?>
										<?php if ($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10) { ?>
											<input type="file" name="file_upload[<?php echo $key;?>]">
										<?php } ?>
									<?php }?>
									
									
								</td>

							</tr>
						<?php } }?>
						</table>

						<?php if($this->session->userdata('admin')['id_role']==3||$this->session->userdata('admin')['id_role']==10){ ?>

						<div class="buttonRegBox clearfix">
						
							<input type="submit" value="Simpan" class="btnBlue" name="simpan">

						</div>

						<?php } ?>

					</div>

				</form>

			</div>



		</div>

	</div>

</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>
	$(document).ready(function() {
				$('.dot').mouseover(function() {
					$(this).siblings('.cg__popup').addClass('active');
				});
				$('.dot').mouseout(function() {
					$(this).siblings('.cg__popup').removeClass('active');
				});
			})
	
</script>
