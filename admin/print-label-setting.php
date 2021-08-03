<?php
namespace Sejoli_Print_Label\Admin;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class PrintLabelSetting {

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
		$this->version 	   = $version;

	}

    /**
	 * Setup license fields for product
	 * Hooked via filter sejoli/product/fields, priority 50
	 * @param  array  $fields
	 * @return array
	 */
	public function setup_print_label_setting_fields(array $fields) {

		$fields[] = [
			'title'	=> __('Label Pengiriman', 'sejoli-print-label'),
            'fields' =>  [
				Field::make( 'separator', 'sep_print_address', __('Pengaturan Alamat Pengiriman', 'sejoli-print-label'))
					->set_classes('sejoli-with-help')
					->set_help_text('<a href="' . sejolisa_get_admin_help('license') . '" class="thickbox sejoli-help">Tutorial <span class="dashicons dashicons-video-alt2"></span></a>'),

				Field::make('text', 'print_label_store_name', __('Nama Pengirim / Nama Toko', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('textarea', 'print_label_store_address', __('Alamat Pengirim / Alamat Toko', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('text', 'print_label_store_phone', __('No. Telepon', 'sejoli-print-label'))
	                ->set_attribute('type', 'number')
	                ->set_required(true),

				Field::make( 'separator', 'sep_print_label', __('Pengaturan Label Pengiriman', 'sejoli-print-label'))
					->set_classes('sejoli-with-help')
					->set_help_text('<a href="' . sejolisa_get_admin_help('print-label') . '" class="thickbox sejoli-help">Tutorial <span class="dashicons dashicons-video-alt2"></span></a>'),

				Field::make('text', 'print_label_invoice_text', __('No. Invoice (Text Label)', 'sejoli-print-label'))
	                ->set_default_value(__('Nomor Invoice', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('text', 'print_label_shipper_text', __('Penerima (Text Label)', 'sejoli-print-label'))
	                ->set_default_value(__('Penerima', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('text', 'print_label_receiver_text', __('Pengirim (Text Label)', 'sejoli-print-label'))
	                ->set_default_value(__('Pengirim', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('checkbox', 'print_label_phone_visible', __('Tampilkan No. Telepon Pengirim', 'sejoli-print-label'))
	                ->set_option_value('yes')
	                ->set_default_value(true),

	            Field::make('text', 'print_label_item_text', __('Item (Text Label)', 'sejoli-print-label'))
	                ->set_default_value(__('Item', 'sejoli-print-label'))
	                ->set_required(true),
            ]
        ];

        return $fields;

    }

}
