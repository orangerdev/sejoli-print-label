<?php
namespace Sejoli_Print_Label;

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
				'ajaxurl' => add_query_arg(array(
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

    /**
     * Process Print Shipment Label
     * Hooked via wp_ajax_sejoli-print-shipment-label, priority 1
     * @since   1.0.0
     * @return  json
     */
	public function print_shipment_label(){
		
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
			  	require_once( plugin_dir_path( __FILE__ ) . 'partials/sejoli-print-label-template.php' );

			endif;

		endif;

		$options = new Options();
		$options->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($options);
		$dompdf->load_html($html);
		$dompdf->setPaper('P', 'A4', 'portrait');
		$dompdf->render();
		$output = $dompdf->output();
		wp_mkdir_p( SEJOLI_JNE_UPLOAD_DIR );
		$file_name = 'label-pengiriman-'.date("Y-m-d h:i:sa").'.pdf';
		$file_path = SEJOLI_JNE_UPLOAD_DIR . '/'. $file_name;
		file_put_contents( $file_path, $output);
		$invoice_url = SEJOLI_JNE_UPLOAD_URL . '/'. $file_name;
		return wp_send_json($invoice_url);
	
	}

}
