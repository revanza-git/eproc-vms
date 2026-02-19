<div class="tab procView">
	<?php echo $this->utility->tabNav($tabNav,'kontrak');?>
	
	<div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
			<?php echo $this->session->flashdata('msgSuccess')?>
			<div class="formDashboard">

		<table>
			<tr class="input-form">
				<td><label>Nama Perusahaan </label></td>
				<td>: <?php echo (isset($legal_name)) ? $legal_name : '';?> <?php echo (isset($vendor_name)) ? $vendor_name : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. SPPBJ</label></td>
				<td>: <?php echo (isset($no_sppbj)) ? $no_sppbj : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal SPPBJ</label></td>
				<td>: <?php echo (isset($sppbj_date)) ? default_date($sppbj_date) : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>No. SPMK</label></td>
				<td>: <?php echo (isset($no_spmk)) ? $no_spmk : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal SPMK</label></td>
				<td>: <?php echo (isset($spmk_date)) ? default_date($spmk_date) : '-';?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Tanggal Kontrak</label></td>
				<td>: 
				<?php echo (isset($contract_date)) ? default_date($contract_date) : '-';?>
				
				</td>
			</tr>
			<!--<tr class="input-form">
				<td><label>Periode Kerja</label></td>
				<td><div>: <?php echo ((isset($start_work)) ? default_date($start_work) : 'Tidak Diketahui').' Sampai '.((isset($end_work)) ? default_date($end_work) : 'Tidak Diketahui');?></div>
				</td>
			</tr>-->
			<tr class="input-form">
				<td><label>No. Kontrak / PO</label></td>
				<td>
					<div style="margin-bottom: 10px">: <?php echo (isset($no_contract)) ? $no_contract : '-'?></div>
					: Lampiran : <?php 
					if(isset($po_file)){
						if($po_file){ ?>
						<a href="<?php echo base_url('lampiran/po_file/'.$po_file);?>"  target="_blank"><?php echo $po_file;?> <i class="fa fa-link"></i></a>
						<?php } 
						} else{
							echo 'Belum Terlampir';
						}?>
				</td>
			</tr>
			<tr class="input-form">
				<td><label>Nilai Kontrak / PO</label></td>
				<td>
					<div style="margin-bottom: 10px">: Dalam Rupiah : Rp.<?php echo number_format((isset($contract_price)) ? $contract_price : '-');?></div>
					<div style="margin-bottom: 10px">: Dalam Mata Uang Asing : <?php echo $kurs_name.' '.number_format((isset($kurs_name)&&isset($contract_price_kurs)) ? $contract_price_kurs : '-'); ?></div>
				</td>
			</tr>
			
			<tr class="input-form">
				<td><label>Jangka Waktu Pelaksanaan Pekerjaan</label></td>
				<td>
					<div>: <?php echo ((isset($start_work)) ? default_date($start_work) : 'Tidak Diketahui').' Sampai '.((isset($end_work)) ? default_date($end_work) : 'Tidak Diketahui');?></div>
				</td>
			</tr>

			<tr class="input-form">
				<td><label>Jangka Waktu Kontrak</label></td>
				<td>
					<div>: <?php echo ((isset($start_contract)) ? default_date($start_contract) : 'Tidak Diketahui').' Sampai '.((isset($end_contract)) ? default_date($end_contract) : 'Tidak Diketahui');?></div>
				</td>
			</tr>
		</table>
			<?php if($this->session->userdata('admin')['id_role']==3){ ?>
		<div class="buttonRegBox clearfix">
			<a href="<?php echo site_url('pengadaan/view/'.$id.'/kontrak_edit#tabNav');?>" class="btnBlue">Edit</a>
		</div>
		<?php } ?>

</div>
		
		
	</div>
</div>