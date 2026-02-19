<div id="edit" class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'tatacara');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px">
	<?php if($this->session->userdata('admin')['id_role']==6|7){ ?>
	<div class="btnTopGroup clearfix"><?php echo $this->session->flashdata('msgSuccess')?>
		<form method="POST" enctype="multipart/form-data">
			<h2>Tata Cara</h2>
			<table>
				<tr class="input-form">
					<td><label>Metode Auction</label></td>
					<td>
						<label class="lbform">
							:&nbsp;<?php echo ($metode_auction=='posisi')?'Posisi/Ranking':'Indikator';?>
						</label>
					</td>
				</tr>
				<tr class="input-form">
					<td><label>Metode Penawaran</label></td>
					<td>
						<label class="lbform">
							:&nbsp;<?php echo ($metode_penawaran=='lump_sum')?'Lump Sum':'Harga Satuan';?>
						</label>
					</td>
				</tr>
			</table>
			
			<div class="buttonRegBox clearfix">
				<input type="submit" value="Edit" class="btnBlue" name="edit">
			</div>
		</form>
	</div>
	</div>
	<?php } ?>
</div>