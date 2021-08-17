<?php
$html .= '<html><head><meta charset="utf-8"></head><body>';
	$html .= '<div class="labelarea" style="width:100%; height: 100%; display:inline-block; margin-top: 0em; width: 100%;">';
	$count = 1;
	foreach ($response['orders'] as $value) {
		$receiver_destination_id   = $value->meta_data['shipping_data']['district_id'];
		$receiver_destination_city = $this->get_subdistrict_detail($receiver_destination_id);
		$shipper_origin_id   	   = $value->product->shipping['origin'];
		$shipper_origin_city 	   = $this->get_subdistrict_detail($shipper_origin_id);
		$html .= '<div class="label-item" style="width: 88.5mm; height: auto; border: 2px solid #000; margin-top: 0.5em; margin-right: 0.5em; margin-bottom: 5mm; float:left; padding: 10px;">';
			$html .= '<table style="width: 88.5mm">';
				$html .= '<thead text-align: left;">';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><b>'.carbon_get_theme_option('print_label_invoice_text').' #'.$value->ID.'</b></td>';
						$html .= '<td style="width: 43.05mm"><b>'.__('Label Pengiriman', 'sejoli-print-label').'</b></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="height: 4px;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="height: 4px;"></div></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="height: 1px;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="height: 1px;"></div></td>';
					$html .= '</tr>';
				$html .= '</thead>';
				$html .= '<tbody>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm">'.__('Kurir', 'sejoli-print-label').'</td>';
						$html .= '<td style="width: 43.05mm"><b>'.$value->meta_data['shipping_data']['courier'].' - '.$value->meta_data['shipping_data']['service'].'</b></td>';
					$html .= '</tr>';
					if($value->meta_data['shipping_data']['resi_number']):
						$html .= '<tr style="vertical-align: middle;">';
							$html .= '<td style="width: 43.05mm">'.__('No. Resi', 'sejoli-print-label').'</td>';
							$html .= '<td style="width: 43.05mm"><b>'.$value->meta_data['shipping_data']['resi_number'].'</b></td>';
						$html .= '</tr>';
					endif;
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm">'.__('Ongkir', 'sejoli-print-label').'</td>';
						$html .= '<td style="width: 43.05mm"><b>'.sejolisa_price_format($value->meta_data['shipping_data']['cost']).'</b></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm">'.__('Berat', 'sejoli-print-label').'</td>';
						$html .= '<td style="width: 43.05mm"><b>'.$value->product->shipping['weight'].' gram</b></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm">'.__('Pembayaran', 'sejoli-print-label').'</td>';
						$html .= '<td style="width: 43.05mm"><b>'.$value->payment_gateway.'</b></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm">&nbsp;</td>';
						$html .= '<td style="width: 43.05mm">&nbsp;</td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><b>'.carbon_get_theme_option('print_label_receiver_text').'</b></td>';
						$html .= '<td style="width: 43.05mm"><b>'.carbon_get_theme_option('print_label_shipper_text').'</b></td>';
					$html .= '</tr>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						if(true === boolval(carbon_get_theme_option('print_label_phone_visible'))){
							$html .= '<td style="width: 43.05mm"><b>'.carbon_get_theme_option('print_label_store_name').' - '.carbon_get_theme_option('print_label_store_phone').'</b></td>';
						} else {
							$html .= '<td style="width: 43.05mm"><b>'.carbon_get_theme_option('print_label_store_name').'</b></td>';
						}
						$html .= '<td style="width: 43.05mm"><b>'.$value->meta_data['shipping_data']['receiver'].' - '.$value->meta_data['shipping_data']['phone'].'</b></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm">'.carbon_get_theme_option('print_label_store_address').'</td>';
						$html .= '<td style="width: 43.05mm">'.$value->meta_data['shipping_data']['address'].', '.$receiver_destination_city['type'].' '.$receiver_destination_city['city'].', '.$receiver_destination_city['subdistrict_name'].', '.$shipper_origin_city['province'].'</td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="height: 4px;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="height: 4px;"></div></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="height: 1px;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="height: 1px;"></div></td>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><b>'.__('QTY', 'sejoli-print-label').'</b></td>';
						$html .= '<td style="width: 43.05mm"><b>'.__('PRODUK', 'sejoli-print-label').'</b></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm">'.$value->quantity.' '.carbon_get_theme_option('print_label_item_text').'</td>';
						$html .= '<td style="width: 43.05mm">'.$value->product_name.'</td>';
					$html .= '</tr>';
					foreach ($value->meta_data['variants'] as $variants) {
						$html .= '<tr style="vertical-align: middle;">';
								$html .= '<td style="width: 43.05mm">&nbsp;</td>';
								$html .= '<td style="width: 43.05mm">'.$variants['type'] .' : '. $variants['label'] . '</br></td>';
						$html .= '</tr>';
			        }
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="height: 4px;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="height: 4px;"></div></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="border-bottom: 1px dashed #999;"></div></td>';
					$html .= '</tr>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm"><div style="height: 1px;"></div></td>';
						$html .= '<td style="width: 43.05mm"><div style="height: 1px;"></div></td>';
					$html .= '<tr style="vertical-align: middle;">';
						$html .= '<td style="width: 43.05mm">&nbsp;</td>';
						$html .= '<td style="width: 43.05mm"><b>'.__('TOTAL', 'sejoli-print-label').': '.sejolisa_price_format($value->grand_total).'</b></td>';
					$html .= '</tr>';
				$html .= '</tbody>';
			$html .= '</table>';
		$html .= '</div>';
		if ($count%2 == 0)
	    {
			$html .= '<div style="clear:both"></div>';
	    }
	    $count++;
	}
	$html .= '</div>';
$html .= '</body></html>';
?>