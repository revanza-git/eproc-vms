<div class="tab procView">

	<?php echo $this->utility->tabNav($tabNav,'assessment');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		<form method="POST" enctype="multipart/form-data">
			
				<div class="panel-group">
				<?php 
				$i=1;
				foreach($assessment_question as $key => $value){ ?>
				<div class="panel">
					<div class="panel-body">
						<h4 class="panel-title"><?php echo $value['name']; ?></h4>
						<ul class="assQuest">
							<?php
							foreach($value['quest'] as $row => $val){ ?>
							<li>
								<div class="fieldPanel">
									<div class="questBox">
										<p><?php echo $i.'. '.$val['value']; ?></p>
									</div>
									<div class="questBobot">
										<span>Bobot : <?php echo $val['point'];?></span>
									</div>
									<div class="questCheck">
										<label>
										<input type="hidden" name="ass[<?php echo $val['id']; ?>]" value="0">
										<input type="checkbox" name="ass[<?php echo $val['id']; ?>]" value="<?php echo $val['point']; ?>" 
										<?php 
										if(isset($data_assessment[$val['id']])){
											if($data_assessment[$val['id']]==$val['point']){
												echo 'checked';
											}
										}
										?>
										/>
										
										</label>
									</div>
									
								</div>
							</li>
							<?php
							$i++;
							 } ?>
						</ol>
						
		            </div>
		        </div>
				<?php 
				
				} ?>
				
	              	
			</div>
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Simpan" class="btnBlue" name="simpan">
			</div>

		</form>
		
	</div>
</div>