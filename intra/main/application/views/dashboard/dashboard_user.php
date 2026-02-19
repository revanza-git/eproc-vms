<div class="mg-lg-12">
	<div class="block">
	</div>
	<br class="clear">
	<div class="admin-dashboard-frame1">
		<div id="box">
			<div id="box-inner">
				<div id="box-frame">
					<i class="fa fa-check"></i>
					<div id="content">
						<span id="title">data telah sesuai</span><br>
						<span id="number"><?php echo count($approval[1])?></span>
					</div>
				</div>
				<div id="box-table">
					<table>
					<?php foreach($approval[1] as $key =>$value){ ?>
						<tr>
							<td><?php echo $value?></td>
						</tr>
					<?php } ?>
					</table>
				</div>
			</div>
		</div>
		<div id="box">
			<div id="box-inner">
				<div id="box-frame">
					<i class="fa fa-times"></i>
					<div id="content">
						<span id="title">data tidak sesuai</span><br>
						<span id="number"><?php echo count($approval[2])?></span>
					</div>
				</div>
				<div id="box-table">
					<table>
						<?php foreach($approval[2] as $key =>$value){ ?>
						<tr>
							<td><?php echo $value?></td>
						</tr>
					<?php } ?>
					</table>
				</div>
			</div>
		</div>
		<div id="box">
			<div id="box-inner">
				<div id="box-frame">
					<i class="fa fa-pencil-square-o"></i>
					<div id="content">
						<span id="title">data belum terverifikasi</span><br>
						<span id="number"><?php echo count($approval[0])?></span>
					</div>
				</div>
				<div id="box-table">
					<table>
						<?php foreach($approval[0] as $key =>$value){ ?>
						<tr>
							<td><?php echo $value?></td>
						</tr>
					<?php } ?>
					</table>
				</div>
			</div>
		</div>
		
		<br class="clear">
	</div>
</div>