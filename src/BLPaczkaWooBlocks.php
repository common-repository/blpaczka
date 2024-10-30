<?php
/**
 * @package BLPaczka
 * @version 1.0.1
 */
namespace BLPaczka\BLPaczkaWoocommerce;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

class BLPaczkaWooBlocks implements IntegrationInterface {
	public function get_name() {
		return 'checkout-form';
	}

	public function initialize() {
		wp_register_script( 'blpaczka-integration', str_replace( '/src', '', plugin_dir_url( __FILE__ ) ) . 'assets/checkout-form.js', [
			'wp-blocks',
			'wp-components',
			'wp-data',
			'wp-element',
		], '1.312', true );

		wp_localize_script( 'blpaczka-integration', 'blpaczkaData', [ 'nonce' => wp_create_nonce( 'blpaczka_pickup_action' ) ] );
	}

	public function get_script_handles() {
		return [ 'blpaczka-integration' ];
	}

	public function get_editor_script_handles() {
		return [ 'blpaczka-integration' ];
	}

	public function get_script_data() {
		return [
			'blpaczka' => '',
		];
	}
}