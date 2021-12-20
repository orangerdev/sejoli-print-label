<?php ob_start(); ?>

<!-- Tag HTML -->
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
</head>
<body>
	<style>
		body{
			font-family: Tahoma, sans-serif;
		}
	</style>
	<div class="labelarea" style="width:100%; height: 100%; display:inline-block; margin-top: 0em; width: 100%;">
	<?php
	$count = 1;
	foreach ($response['orders'] as $value) {
		$receiver_destination_id   = $value->meta_data['shipping_data']['district_id'];
		$receiver_destination_city = $this->get_subdistrict_detail($receiver_destination_id);
		$shipper_origin_id   	   = $value->product->shipping['origin'];
		$shipper_origin_city 	   = $this->get_subdistrict_detail($shipper_origin_id);
	?>
		<div class="label-item" style="width: 88.5mm; height: auto; border: 2px solid #000; margin-top: 0.5em; margin-right: 0.5em; margin-bottom: 5mm; float:left; padding: 10px;">
			<table style="width: 88.5mm; font-size: 90%;">
				<thead style="text-align: left;">
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><b><?php echo carbon_get_theme_option('print_label_invoice_text').' #'.$value->ID; ?></b></td>
						<td style="width: 43.05mm"><b><?php echo __('Label Pengiriman', 'sejoli-print-label'); ?></b></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="height: 4px;"></div></td>
						<td style="width: 43.05mm"><div style="height: 4px;"></div></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>
						<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="height: 1px;"></div></td>
						<td style="width: 43.05mm"><div style="height: 1px;"></div></td>
					</tr>
				</thead>
				<tbody>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><?php echo __('Kurir', 'sejoli-print-label'); ?></td>
						<td style="width: 43.05mm"><b><?php echo $value->meta_data['shipping_data']['courier'].' - '.$value->meta_data['shipping_data']['service']; ?></b></td>
					</tr>
					<?php if(isset($value->meta_data['shipping_data']['resi_number'])): ?>
						<tr style="vertical-align: middle;">
							<td style="width: 43.05mm"><?php echo __('No. Resi', 'sejoli-print-label'); ?></td>
							<td style="width: 43.05mm"><b><?php echo $value->meta_data['shipping_data']['resi_number']; ?></b></td>
						</tr>
					<?php endif; ?>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><?php echo __('Ongkir', 'sejoli-print-label'); ?></td>
						<td style="width: 43.05mm"><b><?php echo sejolisa_price_format($value->meta_data['shipping_data']['cost']); ?></b></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><?php echo __('Berat', 'sejoli-print-label'); ?></td>
						<td style="width: 43.05mm"><b><?php echo $value->product->shipping['weight']; ?> gram</b></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><?php echo __('Pembayaran', 'sejoli-print-label'); ?></td>
						<td style="width: 43.05mm"><b><?php echo $value->payment_gateway; ?></b></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm">&nbsp;</td>
						<td style="width: 43.05mm">&nbsp;</td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><b><?php echo carbon_get_theme_option('print_label_receiver_text'); ?></b></td>
						<td style="width: 43.05mm"><b><?php echo carbon_get_theme_option('print_label_shipper_text'); ?></b></td>
					</tr>
					<tr style="vertical-align: middle;">
						<?php if(true === boolval(carbon_get_theme_option('print_label_phone_visible'))): ?>
							<td style="width: 43.05mm"><b><?php echo carbon_get_theme_option('print_label_store_name').' - '.carbon_get_theme_option('print_label_store_phone'); ?></b></td>
						<?php else: ?>
							<td style="width: 43.05mm"><b><?php echo carbon_get_theme_option('print_label_store_name'); ?></b></td>
						<?php endif; ?>
						<td style="width: 43.05mm"><b><?php echo $value->meta_data['shipping_data']['receiver'].' - '.$value->meta_data['shipping_data']['phone']; ?></b></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><?php echo carbon_get_theme_option('print_label_store_address'); ?></td>
						<td style="width: 43.05mm"><?php echo $value->meta_data['shipping_data']['address'].', '.$receiver_destination_city['type'].' '.$receiver_destination_city['city'].', '.$receiver_destination_city['subdistrict_name'].', '.$shipper_origin_city['province']; ?></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="height: 4px;"></div></td>
						<td style="width: 43.05mm"><div style="height: 4px;"></div></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>
						<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="height: 1px;"></div></td>
						<td style="width: 43.05mm"><div style="height: 1px;"></div></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><b><?php echo __('QTY', 'sejoli-print-label'); ?></b></td>
						<td style="width: 43.05mm"><b><?php echo __('PRODUK', 'sejoli-print-label'); ?></b></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><?php echo $value->quantity.' '.carbon_get_theme_option('print_label_item_text'); ?></td>
						<td style="width: 43.05mm"><?php echo $value->product_name; ?></td>
					</tr>
					<?php foreach ($value->meta_data['variants'] as $variants): ?>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm">&nbsp;</td>
						<td style="width: 43.05mm"><?php echo $variants['type'] .' : '. $variants['label']; ?></br></td>
					</tr>
			        <?php endforeach; ?>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="height: 4px;"></div></td>
						<td style="width: 43.05mm"><div style="height: 4px;"></div></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>
						<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>
					</tr>
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm"><div style="height: 1px;"></div></td>
						<td style="width: 43.05mm"><div style="height: 1px;"></div></td>
					</tr>
					<?php if(true === boolval(carbon_get_theme_option('print_label_total_price'))): ?>	
					<tr style="vertical-align: middle;">
						<td style="width: 43.05mm">&nbsp;</td>
						<td style="width: 43.05mm"><b><?php echo __('TOTAL', 'sejoli-print-label').' : '.sejolisa_price_format($value->grand_total); ?></b></td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	<?php if ($count%2 == 0): ?>
		<div style="clear:both"></div>
	<?php 
		endif; 
		$count++;
	}
	?>
	</div>
</body>
</html>

<?php
	$html = ob_get_contents();
	ob_end_clean();
?>	