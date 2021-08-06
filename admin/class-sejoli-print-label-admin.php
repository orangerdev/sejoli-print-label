<?php
namespace Sejoli_Print_Label;
require_once SEJOLI_PRINT_LABEL_DIR . 'vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

// wp-content/plugins/yourplugin/yourplugin.php
// Set up paths to include DOMPDF
$plugin_path = plugin_dir_path( __FILE__ );
define( 'SEJOLI_JNE_DOMPDF', $plugin_path . 'vendor/dompdf/' );

// Set up directory to save PDF
$upload_dir = wp_upload_dir();
define( 'SEJOLI_JNE_UPLOAD_DIR', $upload_dir['basedir'] . '/label-pengiriman');
define( 'SEJOLI_JNE_UPLOAD_URL', $upload_dir['baseurl'] . '/label-pengiriman');

// include( SEJOLI_JNE_DOMPDF . 'autoload.inc.php' );
use Dompdf\Dompdf;
use Dompdf\Options;


/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://sejoli.co.id
 * @since      1.0.0
 *
 * @package    Sejoli_Print_Label
 * @subpackage Sejoli_Print_Label/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sejoli_Print_Label
 * @subpackage Sejoli_Print_Label/admin
 * @author     Sejoli Team <developer@sejoli.co.id>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sejoli_Print_Label_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sejoli_Print_Label_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sejoli-print-label-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sejoli_Print_Label_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sejoli_Print_Label_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sejoli-print-label-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'sejoli_print_label', array(
			'print_shipment_label' => array(
				'ajaxurl'	=> add_query_arg(array(
						'action' => 'sejoli-print-shipment-label'
					), admin_url('admin-ajax.php')
				),
				'nonce'	=> wp_create_nonce('sejoli-print-shipment-label')
			)
        ));

	}

	/**
     * Get subdistrict detail
     * @since   1.2.0
     * @since   1.5.0       Add conditional to check if subdistrict_id is 0
     * @param   integer     $subdistrict_id     District ID
     * @return  array|null  District detail
     */
    public function get_subdistrict_detail($subdistrict_id) {

        if( 0 !== intval($subdistrict_id) ) :

            ob_start();

            require SEJOLISA_DIR . 'json/subdistrict.json';
            $json_data = ob_get_contents();
            
            ob_end_clean();

            $subdistricts        = json_decode($json_data, true);
            $key                 = array_search($subdistrict_id, array_column($subdistricts, 'subdistrict_id'));
            $current_subdistrict = $subdistricts[$key];

            return $current_subdistrict;

        endif;

        return  NULL;

    }

	public function print_shipment_label(){
		// if ( ! $html = yourplugin_generate_invoice_html( $order_id ) ) {
		//   return;
		// }
		// 
		$params = wp_parse_args( $_POST, array(
            'orders' => NULL,
            'nonce'  => NULL
        ));

        $respond = [
            'valid'   => false,
            'message' => NULL
        ];

        $html = '';

        if( wp_verify_nonce( $params['nonce'], 'sejoli-print-shipment-label') ) :

            unset( $params['nonce'] );
  
	        $response = sejolisa_get_orders(['ID' => $params['orders'] ]);

	        if(false !== $response['valid']) :

			  	$html = '';
			  	$html .= '<html><head><meta name="viewport" content="width=device-width, initial-scale=1"><meta charset="utf-8"></head><body>';
				$html .= '<div class="labelarea" style="width:100%; height: 100%; display:inline-block; margin-top: 0em; -moz-column-count: 2; -webkit-column-count: 2; column-count: 2; width: 100%;">';
				foreach ($response['orders'] as $value) {
					$receiver_destination_id   = $value->meta_data['shipping_data']['district_id'];
        			$receiver_destination_city = $this->get_subdistrict_detail($receiver_destination_id);
					$shipper_origin_id   	   = $value->product->shipping['origin'];
        			$shipper_origin_city 	   = $this->get_subdistrict_detail($shipper_origin_id);
        			$html .= '<div class="label-item" style="width: 88.5mm; height: auto; border: 2px solid #000; margin-top: 0.5em; margin-right: 0.5em; margin-bottom: 5mm; float:left; padding: 10px; ">';
						$html .= '<table style="width: 88.5mm">';
							$html .= '<thead text-align: left;">';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm"><b>INV #'.$value->ID.'</b></td>';
									$html .= '<td style="width: 43.05mm"><b>Label Pengiriman</b></td>';
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
									$html .= '<td style="width: 43.05mm">Kurir</td>';
									$html .= '<td style="width: 43.05mm"><b>'.$value->meta_data['shipping_data']['courier'].' - '.$value->meta_data['shipping_data']['service'].'</b></td>';
								$html .= '</tr>';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm">Ongkir</td>';
									$html .= '<td style="width: 43.05mm"><b>'.sejolisa_price_format($value->meta_data['shipping_data']['cost']).'</b></td>';
								$html .= '</tr>';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm">Berat</td>';
									$html .= '<td style="width: 43.05mm"><b>'.$value->product->shipping['weight'].' gram</b></td>';
								$html .= '</tr>';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm">Pembayaran</td>';
									$html .= '<td style="width: 43.05mm"><b>'.$value->payment_gateway.'</b></td>';
								$html .= '</tr>';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm">&nbsp;</td>';
									$html .= '<td style="width: 43.05mm">&nbsp;</td>';
								$html .= '</tr>';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm"><b>PENGIRIM</b></td>';
									$html .= '<td style="width: 43.05mm"><b>PENERIMA</b></td>';
								$html .= '</tr>';
								$html .= '</tr>';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm"><b>'.get_bloginfo('name').'</b></td>';
									$html .= '<td style="width: 43.05mm"><b>'.$value->meta_data['shipping_data']['receiver'].' - '.$value->meta_data['shipping_data']['phone'].'</b></td>';
								$html .= '</tr>';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm">'.$shipper_origin_city['type'].' '.$shipper_origin_city['city'].', '.$shipper_origin_city['subdistrict_name'].', '.$shipper_origin_city['province'].'</td>';
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
									$html .= '<td style="width: 43.05mm"><b>QTY</b></td>';
									$html .= '<td style="width: 43.05mm"><b>PRODUK</b></td>';
								$html .= '</tr>';
								$html .= '<tr style="vertical-align: middle;">';
									$html .= '<td style="width: 43.05mm">'.$value->quantity.'</td>';
									$html .= '<td style="width: 43.05mm">'.$value->product_name.'</td>';
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
									$html .= '<td style="width: 43.05mm">&nbsp;</td>';
									$html .= '<td style="width: 43.05mm"><b>Total: '.sejolisa_price_format($value->grand_total).'</b></td>';
								$html .= '</tr>';
							$html .= '</tbody>';
						$html .= '</table>';
					$html .= '</div>';
				}
				$html .= '</div>';
		    	$html .= '</body></html>';

			endif;

		endif;

		$options = new Options();
		$options->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($options);

		$dompdf->load_html($html);
		// $dompdf->load_html_file(plugin_dir_url( __FILE__ ) . '/label-pengiriman.php');
		$dompdf->setPaper('P', 'A4', 'portrait');
		$dompdf->render();
		$output = $dompdf->output();
		wp_mkdir_p( SEJOLI_JNE_UPLOAD_DIR );
		// $file_name = 'order-'.$order_id.'.pdf';
		$file_name = 'label-pengiriman-'.date("Y-m-d h:i:sa").'.pdf';
		$file_path = SEJOLI_JNE_UPLOAD_DIR . '/'. $file_name;
		file_put_contents( $file_path, $output);
		$invoice_url = SEJOLI_JNE_UPLOAD_URL . '/'. $file_name;
		return wp_send_json($invoice_url);
	}

	/**
     * Process Print Shipment Label
     * Hooked via wp_ajax_sejoli-print-shipment-label, priority 1
     * @since   1.0.0
     * @return  json
     */
	// public function print_shipment_label(){
	// 	$params = wp_parse_args( $_POST, array(
 //            'orders' => NULL,
 //            'nonce'  => NULL
 //        ));

 //        $respond = [
 //            'valid'   => false,
 //            'message' => NULL
 //        ];

 //        $html = '';

 //        if( wp_verify_nonce( $params['nonce'], 'sejoli-print-shipment-label') ) :

 //            unset( $params['nonce'] );
  
	//         $response = sejolisa_get_orders(['ID' => $params['orders'] ]);

	//         if(false !== $response['valid']) :
	        	
	//         	// foreach ($response['orders'] as $value) {

	//         	// }

 //        		// $html .= '<script type="text/javascript">';
 //    			$html .= 'alert("ok coy");';
 //    			// $html .= "var divToPrint = document.getElementById('labelarea');";
	// 	       	// $html .= "var popupWin = window.open('', '_blank', 'width=300,height=300');";
	// 	       	$html .= "var popupWin = window.open('application/pdf', 'about:blank');";
	// 	       	$html .= "popupWin.document.open();";
	// 	       	$html .= "popupWin.document.write('<html><body>');";
	//             // $html .= "popupWin.document.write('<h1>Div contentsd are <br>');";
 //        		$html .= "popupWin.document.write('<div class=\"labelarea\">');";
	// 				foreach ($response['orders'] as $value) {
	// 					// $html .= "popupWin.document.write('<div class=\"label-item\" style=\" width: 30%; height: 350px; border: 2px solid #000; margin-top: 0.5em; margin-right: 0.5em; float: left; padding: 10px; \">');";
	// 					// 	$html .= "popupWin.document.write('".$value->ID."');";
	// 					// 	$html .= "popupWin.document.write('<hr></hr>');";
	// 					// $html .= "popupWin.document.write('</div>');";
						
	// 					$receiver_destination_id   = $value->meta_data['shipping_data']['district_id'];
 //            			$receiver_destination_city = $this->get_subdistrict_detail($receiver_destination_id);
	// 					$shipper_origin_id   	   = $value->product->shipping['origin'];
 //            			$shipper_origin_city 	   = $this->get_subdistrict_detail($shipper_origin_id);

	// 					$html .= "popupWin.document.write('<table class=\"label-item\" style=\" width: 30%; height: 350px; border: 2px solid #000; margin-top: 0.5em; margin-right: 0.5em; float: left; padding: 10px; \">');";
	// 						$html .= "popupWin.document.write('<thead style=\"border: 1px solid #000; text-align: left;\">');";
	// 							$html .= "popupWin.document.write('<tr>');";
	// 								$html .= "popupWin.document.write('<th style=\"width: 50%\">INV #".$value->ID."</th>');";
	// 								$html .= "popupWin.document.write('<th style=\"width: 50%\">Label Pengiriman</th>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr>');";
	// 								$html .= "popupWin.document.write('<th style=\"width: 50%\"><hr></hr></th>');";
	// 								$html .= "popupWin.document.write('<th style=\"width: 50%\"><hr></hr></th>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 						$html .= "popupWin.document.write('</thead>');";
	// 						$html .= "popupWin.document.write('<tbody>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: middle;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">Kurir</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>".$value->meta_data['shipping_data']['courier']." - ".$value->meta_data['shipping_data']['service']."</b></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">Ongkir</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>".sejolisa_price_format($value->meta_data['shipping_data']['cost'])."</b></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">Berat</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>".$value->product->shipping['weight']." gram</b></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">Pembayaran</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>".$value->payment_gateway."</b></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">&nbsp;</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">&nbsp;</td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>PENGIRIM</b></td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>PENERIMA</b></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>".get_bloginfo('name')."</b></td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>".$value->meta_data['shipping_data']['receiver']." - ".$value->meta_data['shipping_data']['phone']."</b></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">".$shipper_origin_city['type']." ".$shipper_origin_city['city'].", ".$shipper_origin_city['subdistrict_name'].", ".$shipper_origin_city['province']."</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">".$value->meta_data['shipping_data']['address'].", ".$receiver_destination_city['type']." ".$receiver_destination_city['city'].", ".$receiver_destination_city['subdistrict_name'].", ".$shipper_origin_city['province']."</td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><hr></hr></td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><hr></hr></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>QTY</b></td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>PRODUK</b></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">".$value->quantity."</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">".$value->product_name."</td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">&nbsp;</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">&nbsp;</td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 							$html .= "popupWin.document.write('<tr style=\"vertical-align: unset;\">');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\">&nbsp;</td>');";
	// 								$html .= "popupWin.document.write('<td style=\"width: 50%\"><b>Total: ".sejolisa_price_format($value->grand_total)."</b></td>');";
	// 							$html .= "popupWin.document.write('</tr>');";
	// 						$html .= "popupWin.document.write('</tbody>');";
	// 					$html .= "popupWin.document.write('</table>');";
	// 				}
	// 			$html .= "popupWin.document.write('</div>');";
	//             // $html .= "popupWin.document.write(divToPrint);";
	//             $html .= "popupWin.document.write('</body></html>');";
	// 	        $html .= "popupWin.document.close();";
	// 	        // $html .= "popupWin.print();";
	       	
	//        	endif;
        
 //        endif;

 //        return wp_send_json( $html );
 //        // echo $html;
	// }

}