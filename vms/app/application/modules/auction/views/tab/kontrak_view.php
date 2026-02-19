<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'kontrak');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
		
			<div class="formDashboard">
	<form method="POST" enctype="multipart/form-data">
		<table>
			<tr class="input-form">
				<td><label>Nama Perusahaan* :</label></td>
				<td>
					<?php echo $vendor_name;?>
				</td>
			</tr>
			
			<tr class="input-form">
				<td><label>No. SPPBJ*</label></td>
				<td>
					<?php echo $no_sppbj;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal SPPBJ*</label></td>
				<td>
					<?php echo $sppbj_date;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. SPMK*</label></td>
				<td>
					<?php echo $no_spmk;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal SPMK*</label></td>
				<td>
					<?php echo $spmk_date;?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Periode Kerja*</label></td>
				<td>
					<div> <?php echo $start_work.' - '.$end_work;?></div>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. Kontrak / PO*</label></td>
				<td>
					<div style="margin-bottom: 10px"><?php echo $no_contract;?></div>
					Lampiran : <?php if($po_file){?><a href="<?php echo base_url('lampiran/po_file/'.$po_file);?>"  target="_blank"><?php echo $po_file;?> <i class="fa fa-link"></i></a><?php } ?>
					
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai Kontrak / PO*</label></td>
				<td>
					<div style="margin-bottom: 10px">Dalam Rupiah : Rp.<?php echo $contract_price;?></div>
					<div style="margin-bottom: 10px">Dalam Mata Uang Asing : <?php echo $kurs_name.' '.$contract_price_kurs;?></div>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Periode Kontrak / PO*</label></td>
				<td>
					<div><?php echo $start_contract.' - '.$start_contract;?></div>
				</td>
			</tr>
		</table>
		<div class="buttonRegBox clearfix">
			<a href="<?php echo site_url('pengadaan/view/'.$id.'/kontrak_edit');?>" class="btnBlue">Edit</a>
		</div>
	</form>
</div>
		
		
	</div>
</div>
