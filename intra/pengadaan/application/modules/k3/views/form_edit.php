<div>

	<h2 style="text-align: center">SISTEM EVALUASI MANAJEMEN K3 KONTRAKTOR<br>

CHECKLIST SISTEM RATING PRA KUALIFIKASI

</h2>

	<form method="POST" enctype="multipart/form-data">



		<div class="panel-group">

			<?php foreach($quest as $key => $value){ ?>

			<div class="panel">

				<div class="panel-heading">

					<h3>Bagian <?php echo $key.' - '.$ms_quest[$key];?></h3>

				</div>

				<div class="panel-body">

					<?php foreach($value as $k => $field){

						

						if($k!=''){ 

							$sValue = (count($field)) ? '.'.$sub_quest[$k]['id_order'].' ' : ' ';

						?>

						<h4 class="panel-title"><?php echo $key.$sValue.$sub_quest[$k]['question']; ?></h4>

						<?php } ?>

						<ol type="a">

							<?php foreach ($field as $q_key => $q_list) { ?>

								<li>

									<div class="fieldPanel">

									<?php foreach($q_list as $key_label => $q_data){

										?>

										<p><?php echo $q_data['value'];?></p>

										<?php

										switch ($q_data['type']) {

											default:

											case 'text':

												echo '<textarea name="quest['.$q_data['id'].']" rows="8">'. $data_k3[$key_label]['value'].'</textarea>';

												break;

											case 'checkbox':

												$checkbox = explode('|', $q_data['label']);

												foreach($checkbox as $key => $row){

														if(isset($data_k3[$key_label]['value'])){

															if($data_k3[$key_label]['value']==''){

																echo '<label><input type="checkbox" name="quest['.$q_data['id'].']" value="'. $key.'" >'. $row.'</label>';

															}else{

																if($key == $data_k3[$key_label]['value']){

																	echo '<label><input type="checkbox" name="quest['.$q_data['id'].']" value="'. $key.'" checked>'. $row.'</label>';

																}else{

																	echo '<label><input type="checkbox" name="quest['.$q_data['id'].']" value="'. $key.'" >'. $row.'</label>';

																}

															}

														}else{

															echo '<label><input type="checkbox" name="quest['.$q_data['id'].']" value="'. $key.'" >'. $row.'</label>';

														}

													}

												

												break;

											case 'radio':

												$radio = explode('|', $q_data['label']);

												foreach($radio as $key => $row){

														if(isset($data_k3[$key_label]['value'])){

															if($data_k3[$key_label]['value']==''){

																echo '<label><input type="radio" name="quest['.$q_data['id'].']" value="'. $key.'" >'. $row.'</label>';

															}else{

																if($key == $data_k3[$key_label]['value']){

																	echo '<label><input type="radio" name="quest['.$q_data['id'].']" value="'. $key.'" checked>'. $row.'</label>';

																}else{

																	echo '<label><input type="radio" name="quest['.$q_data['id'].']" value="'. $key.'" >'. $row.'</label>';

																}

															}

														}else{

															echo '<label><input type="radio" name="quest['.$q_data['id'].']" value="'. $key.'" >'. $row.'</label>';

														}

													}

												

												break;

											case 'file':

												if(isset($data_k3[$key_label]['value'])){	

													
														echo $this->form->file(array('label'=>$q_data['label'], 'value'=>$data_k3[$key_label]['value'],'name'=>'quest['.$q_data["id"].']'));

														echo form_error('quest[$q_data["id"]]');

													// }

												}else{

													echo $this->form->file(array('value'=>$quest[$q_data['id']],'name'=>'quest['.$q_data["id"].']'));

													echo form_error('quest[$q_data["id"]]');

												}

											break;

										}

									}?>

									</div>

								</li>

								

							<?php } ?>

						</ol>

					<?php } ?>

					

	              	

	            </div>

	        </div>

			<?php } ?>

              	

		</div>

		<div class="buttonRegBox clearfix">

			<input type="submit" value="Simpan" class="btnBlue" name="simpan">

		</div>

	</form>

</div>