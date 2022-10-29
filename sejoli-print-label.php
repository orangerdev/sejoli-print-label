<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sejoli.co.id
 * @since             1.0.0
 * @package           Sejoli_Print_Label
 *
 * @wordpress-plugin
 * Plugin Name:       Sejoli - Print Label
 * Plugin URI:        https://sejoli.co.id
 * Description:       Plugin untuk Sejoli Standalone untuk membuat label pengiriman.
 * Version:           1.1.1
 * Author:            Sejoli
 * Author URI:        https://sejoli.co.id
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sejoli-print-label
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {

	die;

}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SEJOLI_PRINT_LABEL_VERSION', '1.1.1' );
define( 'SEJOLI_PRINT_LABEL_DIR', plugin_dir_path( __FILE__ ) );
define( 'SEJOLI_PRINT_LABEL_URL', plugin_dir_url( __FILE__ ) );

// Set up paths to include DOMPDF
$plugin_path = plugin_dir_path( __FILE__ );
define( 'SEJOLI_JNE_DOMPDF', $plugin_path . 'vendor/dompdf/' );

// Set up directory to save PDF
$upload_dir = wp_upload_dir();

define( 'SEJOLI_JNE_UPLOAD_DIR', $upload_dir['basedir'] . '/label-pengiriman');
define( 'SEJOLI_JNE_UPLOAD_URL', $upload_dir['baseurl'] . '/label-pengiriman');

if(version_compare(PHP_VERSION, '7.2.1') < 0 && !class_exists( 'WP_CLI' )) :

	add_action('admin_notices', 'sejoli_print_label_error_php_message', 1);

	/**
	 * Display error message when PHP version is lower than 7.2.0
	 * Hooked via admin_notices, priority 1
	 * @return 	void
	 */
	function sejoli_print_label_error_php_message() {
		?>
		<div class="notice notice-error">
			<h2>SEJOLI TIDAK BISA DIGUNAKAN DI HOSTING ANDA</h2>
			<p>
				Versi PHP anda tidak didukung oleh SEJOLI dan HARUS diupdate. Update versi PHP anda ke versi yang terbaru. <br >
				Minimal versi PHP adalah 7.2.1 dan versi PHP anda adalah <?php echo PHP_VERSION; ?>
			</p>
			<p>
				Jika anda menggunakan cpanel, anda bisa ikuti langkah ini <a href='https://www.rumahweb.com/journal/memilih-versi-php-melalui-cpanel/' target="_blank" class='button'>Update Versi PHP</a>
			</p>
			<p>
				Jika anda masih kesulitan untuk update versi PHP anda, anda bisa meminta bantuan pada CS hosting anda.
			</p>
		</div>
		<?php
	}

else :

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-sejoli-print-label-activator.php
	 */
	function activate_sejoli_print_label() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-print-label-activator.php';

		Sejoli_Print_Label_Activator::activate();

	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-sejoli-print-label-deactivator.php
	 */
	function deactivate_sejoli_print_label() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-print-label-deactivator.php';

		Sejoli_Print_Label_Deactivator::deactivate();

	}

	register_activation_hook( __FILE__, 'activate_sejoli_print_label' );
	register_deactivation_hook( __FILE__, 'deactivate_sejoli_print_label' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require_once SEJOLI_PRINT_LABEL_DIR . 'vendor/autoload.php';
	require plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-print-label.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_sejoli_print_label() {

		$plugin = new Sejoli_Print_Label();
		$plugin->run();

	}

	run_sejoli_print_label();

endif;
