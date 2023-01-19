<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'pemenang');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		<form method="POST" enctype="multipart/form-data">
			<div class="formDashboard">
			<table style="width: 100%;" class="winTable">
				<tr>
					<td>
						<?php foreach ($list as $key => $value) {
							?>
							<label class="<?php echo ($value['is_winner']==1?'winner':'');?>">
								<?php if($this->session->userdata('admin')['id_role']==3){ ?><input type="radio" name="pemenang" value="<?php echo $value['id']?>" <?php echo ($value['is_winner']==1?'checked':'')?>><?php } ?>
								</div>&nbsp;<?php echo $value['name']?><?php echo ($value['is_winner']==1?'<i class="fa fa-trophy"></i>':'')?></label>
							<?php
						}
						?>
						
					</td>
				</tr>
			</table>
			<?php echo form_error('id_surat'); ?>
			<?php if($this->session->userdata('admin')['id_role']==3){ ?>
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			</div>
			<?php } ?>
			</div>
		</form>
		
	</div>
</div>