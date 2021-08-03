<?php
namespace Sejoli_Print_Label;
require_once SEJOLI_PRINT_LABEL_DIR . 'vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

// wp-content/plugins/yourplugin/yourplugin.php
// Set up paths to include DOMPDF
$plugin_path = plugin_dir_path( __FILE__ );
define( 'YOURPLUGIN_DOMPDF', $plugin_path . 'vendor/dompdf/' );

// Set up directory to save PDF
$upload_dir = wp_upload_dir();
define( 'YOURPLUGIN_UPLOAD_DIR', $upload_dir['basedir'] . '/label-pengiriman');
define( 'YOURPLUGIN_UPLOAD_URL', $upload_dir['baseurl'] . '/label-pengiriman');

// include( YOURPLUGIN_DOMPDF . 'autoload.inc.php' );
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
			  	$html .= '<html><head><meta name="viewport" content="width=device-width, initial-scale=1"></head><body>';
				$html .= '<div class="labelarea" style="margin-top: 2em; -moz-column-count: 2; -webkit-column-count: 2; column-count: 2; width: 100%;">';
				foreach ($response['orders'] as $value) {
					$html .= '<div class="label-item" style="overflow: hidden; height: 300px; width: 300px; padding: 10px; margin: 0.5em 0; -webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid-column; display:inline-block; border: 2px solid #000;">';
						$html .= $value->ID;
						$html .= '<hr></hr>';
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
		$dompdf->setPaper('P', 'A4','fr', false, 'ISO-8859-15', 'portrait');
		$dompdf->render();
		$output = $dompdf->output();
		wp_mkdir_p( YOURPLUGIN_UPLOAD_DIR );
		// $file_name = 'order-'.$order_id.'.pdf';
		$file_name = 'label-pengiriman-'.date("Y-m-d h:i:sa").'.pdf';
		$file_path = YOURPLUGIN_UPLOAD_DIR . '/'. $file_name;
		file_put_contents( $file_path, $output);
		$invoice_url = YOURPLUGIN_UPLOAD_URL . '/'. $file_name;
		return wp_send_json($invoice_url);
	}

	// /**
 //     * Process Print Shipment Label
 //     * Hooked via wp_ajax_sejoli-print-shipment-label, priority 1
 //     * @since   1.0.0
 //     * @return  json
 //     */
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
	// 					$html .= "popupWin.document.write('<div class=\"label-item\" style=\" width: 30%; height: 350px; border: 2px solid #000; margin-top: 0.5em; margin-right: 0.5em; float: left; padding: 10px; \">');";
	// 						$html .= "popupWin.document.write('".$value->ID."');";
	// 						$html .= "popupWin.document.write('<hr></hr>');";
	// 					$html .= "popupWin.document.write('</div>');";
	// 				}
	// 			$html .= "popupWin.document.write('</div>');";
	//             // $html .= "popupWin.document.write(divToPrint);";
	//             $html .= "popupWin.document.write('</body></html>');";
	// 	        $html .= "popupWin.document.close();";
	// 	        $html .= "popupWin.print();";
	       	
	//        	endif;
        
 //        endif;

 //        return wp_send_json( $html );
 //        // echo $html;
	// }

}