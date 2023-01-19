<?php echo $this->session->flashdata('msgSuccess')?>
<h2 class="formHeader">Assessment</h2>
<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
	
	<form method="POST" enctype="multipart/form-data">
		
			<div class="panel-group">
			
			<div class="panel">
				<?php 
			$i=1;
			foreach($assessment as $key => $value){ ?>
				<div class="panel-body">
					<h4 class="panel-title"><?php echo $value['name']; ?>
						<span style="font-size: 12px; font-weight:normal;">
							<!-- &nbsp;<i class="fa fa-minus"></i>&nbsp; -->
							<a href="<?php echo site_url('admin/admin_assessment/edit_group/'.$value['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
							<a style="top: -4px" href="<?php echo site_url('admin/admin_assessment/hapus_group/'.$value['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
						</span>
					</h4><!-- group title -->
					
					<ul class="assQuest">
						<?php
						foreach($value['data_quest'] as $row => $val){ ?>
						<li>
							<div class="fieldPanel">
								<div class="questBox">
									<p><?php echo $i.'. '.$val['value']; ?></p>
								</div>
								<div class="questBobot">
									<span>Bobot : <?php echo $val['point'];?></span>
								</div>
								<div class="questRole">
									<span><?php echo $val['role'];?></span>
								</div>
								<div class="questCheck">
									<!--<a href="<?php echo site_url('admin/admin_assessment/move/up/'.$val['id'])?>" class="editBtn"><i class="fa fa-long-arrow-up"></i></a>
									<a href="<?php echo site_url('admin/admin_assessment/move/down/'.$val['id'])?>" class="editBtn"><i class="fa fa-long-arrow-down"></i></a>-->
									<a href="<?php echo site_url('admin/admin_assessment/edit_assessment/'.$val['id'])?>" class="editBtn"><i class="fa fa-cog"></i>Edit</a>
									<a href="<?php echo site_url('admin/admin_assessment/hapus_assessment/'.$val['id'])?>" class="delBtn"><i class="fa fa-trash"></i>Hapus</a>
								</div>
							</div>
						</li>
						<?php
						$i++;
						 } ?>
					</ul>
					<div class="tambahBtn">
						<a href="<?php echo site_url('admin/admin_assessment/tambah_assessment/'.$value['id'])?>"><i class="fa fa-plus-square-o"></i>&nbsp; Tambah Pertanyaan</a>
					</div>
	            </div>
	            <?php 
			
			} ?>
			
	            <div class="tambahBtn">
					<a href="<?php echo site_url('admin/admin_assessment/tambah_group')?>"><i class="fa fa-plus-square-o"></i>&nbsp; Tambah Group</a>
				</div>
	        </div>
			
              	
		</div>
		

	</form>
	
</div>
