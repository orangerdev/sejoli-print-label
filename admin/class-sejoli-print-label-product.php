<?php
namespace Sejoli_Print_Label\Admin;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Product {

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
	 * Setup shipping label in product setting
	 * Hooked via filter sejoli/product/fields, priority 42
	 * @param  array  $fields
	 * @return array
	 */
	public function setup_print_label_setting_fields(array $fields) {

		$fields['shipping-label'] = [
			'title'	=> __('Label Pengiriman', 'sejoli-print-label'),
            'fields' =>  [
				Field::make( 'separator', 	'sep_print_address', __('Pengaturan Alamat Pengiriman', 'sejoli-print-label'))
					->set_classes('sejoli-with-help'),

				Field::make('text', 'print_label_store_name', __('Nama Pengirim', 'sejoli-print-label'))
	                ->set_required(true)
					->set_default_value(get_bloginfo('name')),

	            Field::make('textarea', 'print_label_store_address', __('Alamat Pengirim', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('text', 'print_label_store_phone', __('Nomor Telepon', 'sejoli-print-label'))
	                ->set_attribute('type', 'number')
	                ->set_required(true),

				Field::make( 'separator', 'sep_print_label', __('Pengaturan Label Pengiriman', 'sejoli-print-label'))
					->set_classes('sejoli-with-help'),

				Field::make('text', 'print_label_invoice_text', __('No. Invoice (Text Label)', 'sejoli-print-label'))
	                ->set_default_value(__('INV', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('text', 'print_label_shipper_text', __('Penerima (Text Label)', 'sejoli-print-label'))
	                ->set_default_value(__('PENERIMA', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('text', 'print_label_receiver_text', __('Pengirim (Text Label)', 'sejoli-print-label'))
	                ->set_default_value(__('PENGIRIM', 'sejoli-print-label'))
	                ->set_required(true),

	            Field::make('checkbox', 'print_label_phone_visible', __('Tampilkan No. Telepon Pengirim', 'sejoli-print-label'))
	                ->set_option_value('yes')
	                ->set_default_value(true),

	            Field::make('checkbox', 'print_label_total_price', __('Tampilkan Total Biaya', 'sejoli-print-label'))
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
