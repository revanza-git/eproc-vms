<div>
	<h2 style="text-align: center">SISTEM EVALUASI MANAJEMEN K3 KONTRAKTOR<br>CHECKLIST SISTEM RATING PRA KUALIFIKASI</h2>

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
								<?php
									switch ($valuedata['type']) {
										default:
										case 'text':
											?>
											<textarea name="quest[<?php echo $valuedata['id']?>]" rows=8></textarea>
											<?php 
											break;
										case 'checkbox':
											$checkbox = explode('|', $valuedata['label']);
											?>
											<input type="hidden" name="quest[<?php echo $valuedata['id']?>]" value="">
											<?php
											foreach($checkbox as $keys => $rows){ ?>
												<label><input type="checkbox" name="quest[<?php echo $valuedata['id']?>]" value="<?php echo $keys;?>"><?php echo $rows;?></label>
											<?php }
											break;
										case 'radio':
											$checkbox = explode('|', $valuedata['label']);
											?>
											<input type="hidden" name="quest[<?php echo $valuedata['id']?>]" value="">
											<?php
											foreach($checkbox as $keys => $rows){ ?>
												<label><input type="radio" name="quest[<?php echo $valuedata['id']?>]" value="<?php echo $keys;?>"><?php echo $rows;?></label>
											<?php }
											break;
										case 'file':
											?>	
												<?php echo $this->form->file(array('value'=>$quest[$valuedata['id']],'name'=>'quest['.$valuedata["id"].']'));?>
												<?php echo form_error('quest[$valuedata["id"]]'); ?>
											<?php
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
		<div class="buttonRegBox clearfix">
			<input type="submit" value="Simpan" class="btnBlue" name="simpan">
		</div>
	</form>
</div>