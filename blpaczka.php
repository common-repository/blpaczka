<?php
/*
 * Plugin Name:       BLPaczka
 * Description:       Tanie przesyłki prosto z Twojego sklepu
 * Version:           1.0.1
 * Author:            BLPaczka
 * Author URI:        https://blpaczka.com
 * Text Domain:       blpaczka
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( defined( 'BLPACZKA_SANDBOX' ) && BLPACZKA_SANDBOX ) {
	define( 'BLPACZKA_API_URL', 'https://sandbox.blpaczka.com' );
	add_action( 'admin_notices', 'BLPACZKA_sandbox_notice' );
} else {
	define( 'BLPACZKA_API_URL', 'https://send.blpaczka.com' );
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/settings.php';
require_once __DIR__ . '/src/BLPaczkaRestRoutes.php';

use BLPaczka\BLPaczkaWoocommerce\BLPaczkaWooBlocks;

const BLPACZKA_COURIERS      = [
	'poczta'                    => 'Poczta',
	'poczta_ecommerce_envelope' => 'Poczta eCommerce Koperta',
	'dpd'                       => 'DPD',
	'ups'                       => 'UPS',
	'dhl'                       => 'DHL',
	'blp_cross_border'          => 'BLP Cross-Border',
	'blp_cross_border_eco'      => 'BLP Cross-Border Eco',
	'fedex'                     => 'FedEx',
	'gls'                       => 'GLS',
	'hellman'                   => 'Hellman',
	'inpost'                    => 'InPost',
	'orlen'                     => 'Orlen',
	'paczkomaty'                => 'InPost Paczkomat',
	'paczkomaty_eco'            => 'InPost Paczkomat Eco',
	'paczkomaty_to_door'        => 'InPost Paczkomat do drzwi',
//	'paczkomaty_allegro_smart'  => 'InPost Paczkomat Allegro Smart',
//	'allegro_smart_dpd'         => 'DPD Allegro Smart',
//	'allegro_smart_ecommerce'   => 'Allegro Smart eCommerce',
//	'allegro_smart_poczta'      => 'Allegro Smart Poczta',
];

const BLPACZKA_PUDO_COURIERS = [
	'paczkomaty',
	'paczkomaty_eco',
	'paczkomaty_allegro_smart',
	'dhl',
	'dpd',
	'poczta',
	'poczta_ecommerce_envelope',
	'orlen',
];

const BLPACZKA_PUDO_REQUIRED = [
	'paczkomaty',
	'paczkomaty_eco',
	'paczkomaty_allegro_smart',
	'orlen',
];

function BLPACZKA_sandbox_notice() {
	?>
    <div class="notice notice-warning">
        <p><?php esc_html_e( 'BLPaczka: Tryb sandbox jest włączony. Wszystkie operacje są wykonywane w środowisku testowym.', 'blpaczka' ); ?></p>
    </div>
	<?php
}

function BLPACZKA_is_HPOS() {
	return class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) &&
	       method_exists( 'Automattic\WooCommerce\Utilities\OrderUtil', 'custom_orders_table_usage_is_enabled' ) &&
	       Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
}

function BLPACZKA_is_checkout_block() {
	return class_exists( 'WC_Blocks_Utils' ) && WC_Blocks_Utils::has_block_in_page( wc_get_page_id( 'checkout' ), 'woocommerce/checkout' );
}

function BLPACZKA_get_order_id( $post ) {
	$postId = null;
	if ( BLPACZKA_is_HPOS() && ! empty( $post ) && is_a( $post, 'WC_Order' ) ) {
		$postId = $post->get_id();
	} elseif ( ! empty( $post ) && is_object( $post ) ) {
		$postId = $post->ID;
	} elseif ( ! empty( $post ) && is_numeric( $post ) ) {
		$postId = $post;
	}

	return $postId;
}

function BLPACZKA_get_shipping_instance_id( $order ) {
	$shippingInstanceId = null;
	if ( ! empty( $order ) ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order );
		}
		$shippingMethods    = $order->get_shipping_methods();
		$lastShippingMethod = reset( $shippingMethods );
		$shippingInstanceId = $lastShippingMethod !== false ? $lastShippingMethod->get_instance_id() : null;
	}

	return $shippingInstanceId;
}

function BLPACZKA_get_order_data( $order ) {
	$orderData = null;
	if ( ! empty( $order ) ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order );
		}
		$orderData = $order->get_data();
	}

	return $orderData;
}

function BLPACZKA_blpaczka_check_current_screen() {
	add_action( 'add_meta_boxes', 'BLPACZKA_checkout' );
}

function BLPACZKA_checkout() {
	$screen = BLPACZKA_is_HPOS() ? 'woocommerce_page_wc-orders' : 'shop_order';
	add_meta_box(
		'blpaczka-shipment',
		'BLPaczka',
		'BLPACZKA_blpaczka_shipment_meta_box',
		$screen,
		'normal',
		'default'
	);
}

add_action( 'woocommerce_after_order_notes', 'BLPACZKA_custom_checkout_field' );
function BLPACZKA_custom_checkout_field( $checkout ) {
	if ( ! BLPACZKA_is_checkout_block() ) {

		woocommerce_form_field( 'blpaczka-point', [
			'type'              => 'text',
			'required'          => 'true',
			'id'                => 'blpaczka-point',
			'custom_attributes' => [ 'hidden' => 'hidden' ],
		],
			$checkout->get_value( 'blpaczka-point' ) );
		wp_nonce_field( 'blpaczka_pickup_action', 'blpaczka_pickup_nonce' );
	}
}

function BLPACZKA_validate_pickup_point( $posted ) {
	$chosen_methods    = WC()->session->get( 'chosen_shipping_methods' );
	$chosen_shipping   = $chosen_methods[0];
	$instance_id       = str_replace( 'flat_rate:', '', $chosen_shipping );
	$shipping_settings = get_option( 'woocommerce_flat_rate_' . $instance_id . '_settings' );

	if ( ! empty( $shipping_settings['blpaczka_require_pickup_point'] ) && $shipping_settings['blpaczka_require_pickup_point'] === 'yes' ) {
		if ( ! isset( $_POST['blpaczka_pickup_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['blpaczka_pickup_nonce'] ), 'blpaczka_pickup_action' ) ) {
			wc_add_notice( __( 'Error', 'blpaczka' ), 'error' );

			return;
		}

		if ( empty( $_POST['blpaczka-point'] ) ) {
			wc_add_notice( __( 'Proszę wybrać punkt odbioru.', 'blpaczka' ), 'error' );
		}
	}
}

add_action( 'woocommerce_checkout_process', 'BLPACZKA_validate_pickup_point' );


add_action( 'woocommerce_checkout_update_order_meta', 'BLPACZKA_save_blpaczka_point_to_order_meta' );
function BLPACZKA_save_blpaczka_point_to_order_meta( $order_id ) {
	if ( ! isset( $_POST['blpaczka_pickup_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['blpaczka_pickup_nonce'] ), 'blpaczka_pickup_action' ) ) {
		wp_die( 'Security check failed', 'blpaczka-security', [ 'response' => 403 ] );
	}

	if ( ! empty( $_POST['blpaczka-point'] ) ) {
		if ( BLPACZKA_is_HPOS() ) {
			$order = wc_get_order( $order_id );
			$order->add_meta_data( 'blpaczka_selected_point', sanitize_text_field( wp_unslash( $_POST['blpaczka-point'] ) ) );
			$order->save();
		} else {
			update_post_meta( $order_id, 'blpaczka_selected_point', sanitize_text_field( wp_unslash( $_POST['blpaczka-point'] ) ) );
		}
	}
}

function BLPACZKA_enqueue_assets() {
	if ( is_checkout() ) {
		wp_register_script( 'shipment-form', plugin_dir_url( __FILE__ ) . 'assets/shipment-form.js', [
			'jquery',
			'wp-api',
		], '1.0', true );
		wp_enqueue_script( 'shipment-form' );
		wp_localize_script( 'shipment-form', 'blpaczkaData', [
			'apiUrl'         => BLPACZKA_API_URL,
			'pluginDirUrl'   => plugin_dir_url( __FILE__ ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'flatRateValues' => BLPACZKA_get_all_flat_rate_instance_values(),
		] );
	}

	wp_register_style( 'blpaczka', plugin_dir_url( __FILE__ ) . 'assets/blpaczka.css', [], wp_rand( 1, 300 ) );
	wp_enqueue_style( 'blpaczka' );
}


function BLPACZKA_admin_enqueue_assets( $hook ) {
	$screen = get_current_screen();
	if ( ( $hook === 'edit.php' && $screen->post_type === 'shop_order' ) || ( $hook === 'post.php' && $screen->post_type === 'shop_order' ) || ( $hook === 'woocommerce_page_wc-orders' ) ) {
		wp_register_script( 'blpaczka-admin-parcel-send', plugin_dir_url( __FILE__ ) . 'assets/blpaczka-admin-parcel-send.js', [ 'jquery' ], '1.0', true );
		wp_enqueue_script( 'blpaczka-admin-parcel-send' );
		wp_localize_script( 'blpaczka-admin-parcel-send', 'blpaczkaData', [
			'apiUrl'       => BLPACZKA_API_URL,
			'pluginDirUrl' => plugin_dir_url( __FILE__ ),
			'nonce'        => wp_create_nonce( 'wp_rest' ),
		] );

		wp_register_style( 'blpaczka', plugin_dir_url( __FILE__ ) . 'assets/blpaczka.css', [], wp_rand( 1, 300 ) );
		wp_enqueue_style( 'blpaczka' );
	}
}


function BLPACZKA_bal_http_request_args( $r ) {
	$r['timeout'] = 15;

	return $r;
}

add_action( 'woocommerce_after_shipping_rate', 'BLPACZKA_add_pudo_map_to_shipping_option', 10, 2 );

function BLPACZKA_add_pudo_map_to_shipping_option( $shipping_method, $recurring_cart_package_key ) {
	$newShippingMethod = WC()->session->get( 'chosen_shipping_methods' )[0];
	if ( is_checkout() && $shipping_method->id === $newShippingMethod ) {
		$instanceId  = explode( ':', $shipping_method->id )[1];
		$postalCode  = sanitize_text_field( str_replace( '-', '', WC()->customer->get_shipping_postcode() ) );
		$pudoCourier = BLPACZKA_check_instance_pudo_map( $instanceId );

		if ( $pudoCourier !== false ) {
			if ( ! $postalCode || strlen( $postalCode ) > 4 ) {
				echo '<div id="blpaczka-map">
                        <button type="button" class="blpaczka-point-modal-btn">Wybierz punkt</button>
					</div>
					<input class="blpaczka-selected-courier-code" value="' . esc_html( $pudoCourier['courier'] ) . '" type="hidden"/>
					';
			} else {
				echo '<div>Żeby wybrać punkt na mapie uzupełnij kod pocztowy</div>';
			}
		}
	}
}

function BLPACZKA_api_download_waybill( WP_REST_Request $request ) {
	BLPACZKA_downloadMergedWaybills( [ $request->get_param( 'id' ) ], $request->get_param( 'LBL' ) == 1 ? 'LBL' : 'A4' );
}

function BLPACZKA_blpaczka_shipment_meta_box( $post ) {
	$order = wc_get_order( BLPACZKA_get_order_id( $post ) );
	if ( ! $order ) {
		echo 'Brak zamówienia o podanym ID.';

		return;
	}

	$orderData     = BLPACZKA_get_order_data( $order );
	$orderTotal    = floatval( $orderData['total'] ?? 0.0 );
	$shippingTotal = floatval( $orderData['shipping_total'] ?? 0.0 );

	$orderSum = $orderTotal - $shippingTotal;

	$patternToExplodeAddress = '/^(.*?)\s+(\d+(?:\s*m)?)(?:\s*(?:m|\/)\.?(\w+))?(?:,\s*(\w+))?\s*$/';
	$street                  = '';
	$houseNumber             = '';
	$apartmentNumber         = '';

	$address = $orderData['shipping']['address_1'] . ' ' . $orderData['shipping']['address_2'];

	if ( preg_match( $patternToExplodeAddress, trim( $address ), $matches ) ) {
		$street          = $matches[1] ?? '';
		$houseNumber     = $matches[2] ?? '';
		$apartmentNumber = $matches[3] ?? '';
	}

	if ( BLPACZKA_is_HPOS() ) {
		$blpaczkaOrderId = $order->get_meta( 'BLPACZKA_blpaczka_order_id' );
	} else {
		$blpaczkaOrderId = get_post_meta( $post->ID, 'BLPACZKA_blpaczka_order_id', true );
	}
	if ( ! empty( $blpaczkaOrderId ) ) {
		if ( BLPACZKA_is_HPOS() ) {
			$blpaczkaWaybillLink = $order->get_meta( 'BLPACZKA_blpaczka_waybill_link' );
		} else {
			$blpaczkaWaybillLink = get_post_meta( $post->ID, 'BLPACZKA_blpaczka_waybill_link', true );
		}
		$trackingResponse = wp_remote_post( esc_url( BLPACZKA_API_URL . '/api/getWaybillTracking.json' ), [
            'body' => wp_json_encode( [
                'auth' => [
                    'login' => sanitize_text_field( get_option( 'blpaczka_auth_login' ) ),
                    'api_key' => sanitize_text_field( get_option( 'blpaczka_auth_key' ) ),
                ],
                'Order' => [ 'id' => (int) $blpaczkaOrderId ]
            ] ),
            'headers' => ['Content-Type' => 'application/json']
        ] );

        $trackingBody = json_decode( $trackingResponse['body'], true );
		$trackingData = [];
		if ( ! empty( $trackingBody['data']['Tracking'] ) ) {
			$trackingData = $trackingBody['data']['Tracking'];
		}

		include_once( 'templates/shipment-details.php' );
	} else {
		$orderId = BLPACZKA_get_order_id( $post );

		$shippingMethods = $order->get_shipping_methods();

		if ( ! empty( $shippingMethods ) ) {
			$shippingInstanceId = reset( $shippingMethods )->get_instance_id();
			$option_key         = "woocommerce_flat_rate_{$shippingInstanceId}_settings";

			$shipping_settings = get_option( $option_key );
			if ( BLPACZKA_is_HPOS() ) {
				$selectedPoint = $order->get_meta( 'blpaczka_selected_point' ) ?? null;
			} else {
				$selectedPoint = get_post_meta( $orderId, 'blpaczka_selected_point', true ) ?? null;
			}
			include_once( 'templates/shipment-form.php' );
		} else {
			echo 'Nie wybrano metody dostawy';
		}
	}
}

function BLPACZKA_save_selected_point( \WC_Order $order, \WP_REST_Request $request ) {
	$orderId               = BLPACZKA_get_order_id( $order );
	$shipping_methods      = $order->get_shipping_methods();
	$requires_pickup_point = false;

	foreach ( $shipping_methods as $shipping_method ) {
		$instance_id = $shipping_method->get_instance_id();
		$method_id   = $shipping_method->get_method_id();

		$settings = get_option( 'woocommerce_' . $method_id . '_' . $instance_id . '_settings' );

		if ( ! empty( $settings['blpaczka_require_pickup_point'] ) && $settings['blpaczka_require_pickup_point'] === 'yes' ) {
			$requires_pickup_point = true;
			break;
		}
	}

	$checkoutData = json_decode( $request->get_body(), true )['extensions'];
	if ( ! empty( $checkoutData['blpaczka']['blpaczka-point'] ) ) {
		$selectedPoint = sanitize_text_field( $checkoutData['blpaczka']['blpaczka-point'] );

		if ( BLPACZKA_is_HPOS() ) {
			$order->add_meta_data( 'blpaczka_selected_point', $selectedPoint );
			$order->save();
		} else {
			update_post_meta( $orderId, 'blpaczka_selected_point', $selectedPoint );
		}
	} elseif ( $requires_pickup_point ) {
		throw new \WC_REST_Exception( 'woocommerce_rest_invalid_request', esc_html( 'Proszę wybrać punkt odbioru.' ), 400 );
	}
}

add_action( 'current_screen', 'BLPACZKA_blpaczka_check_current_screen' );
add_action( 'wp_enqueue_scripts', 'BLPACZKA_enqueue_assets' );
add_action( 'admin_enqueue_scripts', 'BLPACZKA_admin_enqueue_assets' );
add_action( 'woocommerce_store_api_checkout_update_order_from_request', 'BLPACZKA_save_selected_point', 11, 2 );
add_filter( 'http_request_args', 'BLPACZKA_bal_http_request_args', 100, 1 );
add_action(
	'woocommerce_blocks_checkout_block_registration',
	function ( $integration_registry ) {
		if ( ! $integration_registry->is_registered( 'blpaczka-checkout-form' ) ) {
			$integration_registry->register( new BLPaczkaWooBlocks() );
		}
	}
);

add_filter( 'manage_woocommerce_page_wc-orders_columns', 'BLPACZKA_add_wc_order_list_custom_column' );
add_filter( 'manage_edit-shop_order_columns', 'BLPACZKA_add_wc_order_list_custom_column' );
function BLPACZKA_add_wc_order_list_custom_column( $columns ) {
	$reordered_columns = [];

	foreach ( $columns as $key => $column ) {
		$reordered_columns[ $key ] = $column;

		if ( $key === 'order_status' ) {
			$reordered_columns['blpaczka-column'] = 'BLPaczka 📦↗️';
		}
	}

	return $reordered_columns;
}

add_action( 'manage_woocommerce_page_wc-orders_custom_column', 'BLPACZKA_display_wc_order_list_custom_column_content', 10, 2 );
add_action( 'manage_shop_order_posts_custom_column', 'BLPACZKA_display_wc_order_list_custom_column_content', 10, 2 );
function BLPACZKA_display_wc_order_list_custom_column_content( $column, $order ) {
	$orderId = BLPACZKA_get_order_id( $order );
	switch ( $column ) {
		case 'blpaczka-column' :
			if ( BLPACZKA_is_HPOS() ) {
				$order               = is_a( $order, WC_Order::class ) ? $order : wc_get_order( $orderId );
				$blpaczkaOrderId     = $order->get_meta( 'BLPACZKA_blpaczka_order_id' );
				$blpaczkaWaybillLink = $order->get_meta( 'BLPACZKA_blpaczka_waybill_link' );
			} else {
				$blpaczkaOrderId     = get_post_meta( $orderId, 'BLPACZKA_blpaczka_order_id', true );
				$blpaczkaWaybillLink = get_post_meta( $orderId, 'BLPACZKA_blpaczka_waybill_link', true );
			}
			$wooWaybillLink = get_rest_url( null, 'blpaczka/download-waybill/' . $blpaczkaOrderId );
			if ( ! empty( $blpaczkaWaybillLink ) ) {
				echo '<button type="button" class="button-primary btn-blpaczka-column js-download-waybill" data-link="' . esc_html( $wooWaybillLink ) . '">Pobierz etykietę 📄 (A4)</button>';
			} else {
				$shippingInstanceId = BLPACZKA_get_shipping_instance_id( $order );

				if ( ! $shippingInstanceId ) {
					echo 'Brak metody dostawy';
					break;
				}

				$option_key        = "woocommerce_flat_rate_{$shippingInstanceId}_settings";
				$shipping_settings = get_option( $option_key );

				$patternToExplodeAddress = '/^(.*?)\s+(\d+(?:\s*m)?)(?:\s*(?:m|\/)\.?(\w+))?(?:,\s*(\w+))?\s*$/';
				$street                  = '';
				$houseNumber             = '';
				$apartmentNumber         = '';

				$orderData = BLPACZKA_get_order_data( $order );

				$orderTotal    = floatval( $orderData['total'] ?? 0.0 );
				$shippingTotal = floatval( $orderData['shipping_total'] ?? 0.0 );

				$orderSum = $orderTotal - $shippingTotal;

				$address = $orderData['shipping']['address_1'] . ' ' . $orderData['shipping']['address_2'];

				if ( preg_match( $patternToExplodeAddress, trim( $address ), $matches ) ) {
					$street          = $matches[1] ?? '';
					$houseNumber     = $matches[2] ?? '';
					$apartmentNumber = $matches[3] ?? '';
				}

				if ( BLPACZKA_is_HPOS() ) {
					$order         = is_a( $order, WC_Order::class ) ? $order : wc_get_order( $order );
					$selectedPoint = $order->get_meta( 'blpaczka_selected_point' ) ?? null;
				} else {
					$selectedPoint = get_post_meta( $orderId, 'blpaczka_selected_point', true ) ?? null;
				}

				include 'templates/list-shipment-form.php';
			}
			break;
	}
}

add_filter( 'bulk_actions-woocommerce_page_wc-orders', 'BLPACZKA_download_blpaczka_bulk_actions', 20, 1 );
add_filter( 'bulk_actions-edit-shop_order', 'BLPACZKA_download_blpaczka_bulk_actions', 20, 1 );
function BLPACZKA_download_blpaczka_bulk_actions( $actions ) {
	$actions['BLPACZKA_blpaczka_bulk_order_A6'] = __( 'Pobierz etykiety BLPaczka📦 - A6(LBL)', 'blpaczka' );
	$actions['BLPACZKA_blpaczka_bulk_order_A4'] = __( 'Pobierz etykiety BLPaczka📦 - A4', 'blpaczka' );

	return $actions;
}

add_action( 'handle_bulk_actions-woocommerce_page_wc-orders', 'BLPACZKA_handle_bulk_action_blpaczka', 10, 3 );
add_action( 'handle_bulk_actions-edit-shop_order', 'BLPACZKA_handle_bulk_action_blpaczka', 10, 3 );
function BLPACZKA_handle_bulk_action_blpaczka( $redirect_to, $action, $post_ids ) {
	if ( ! in_array( $action, [ 'BLPACZKA_blpaczka_bulk_order_A6', 'BLPACZKA_blpaczka_bulk_order_A4' ] ) ) {
		return $redirect_to;
	}

	$orderIds = [];

	foreach ( $post_ids as $post_id ) {
		if ( BLPACZKA_is_HPOS() ) {
			$order               = wc_get_order( $post_id );
			$blpaczkaWaybillLink = $order->get_meta( 'BLPACZKA_blpaczka_waybill_link' );
		} else {
			$blpaczkaWaybillLink = get_post_meta( $post_id, 'BLPACZKA_blpaczka_waybill_link', true );
		}
		if ( $blpaczkaWaybillLink === '' ) {
			return $redirect_to = add_query_arg( [
				'blpaczka_label_does_not_exist' => '1',
			], $redirect_to );
		}
		if ( BLPACZKA_is_HPOS() ) {
			$order           = wc_get_order( $post_id );
			$blpaczkaOrderId = $order->get_meta( 'BLPACZKA_blpaczka_order_id' );
		} else {
			$blpaczkaOrderId = get_post_meta( $post_id, 'BLPACZKA_blpaczka_order_id', true );
		}
		$orderIds[] = $blpaczkaOrderId;
	}

	if ( $orderIds === [] ) {
		return $redirect_to = add_query_arg( [
			'blpaczka_select_order_error' => '1',
		], $redirect_to );
	}

	$format = $action === 'BLPACZKA_blpaczka_bulk_order_A6' ? 'LBL' : 'A4';

	BLPACZKA_downloadMergedWaybills( $orderIds, $format );

	return $redirect_to = add_query_arg( [], $redirect_to );
}
function BLPACZKA_validate_pdf_content($content) {
    // Check if content starts with %PDF-
    if (substr($content, 0, 5) !== '%PDF-') {
        return false;
    }
    return true;
}

function BLPACZKA_downloadMergedWaybills( array $orderIds, string $format ) {
	$url = BLPACZKA_API_URL . '/api/downloadWaybillsMerged.json';

	$json = wp_json_encode( [
		'auth'      => [
			'login'   => get_option( 'blpaczka_auth_login' ),
			'api_key' => get_option( 'blpaczka_auth_key' ),
		],
		'order_ids' => $orderIds,
		'format'    => $format,
	] );

	$response = wp_remote_post( $url, [
		'body'    => $json,
		'headers' => [
			'Content-Type' => 'application/json',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'request_failed', $response->get_error_message(), [ 'status' => $response->get_error_code() ] );
	}
	$pdf_content = wp_remote_retrieve_body( $response );

	if ( empty( $pdf_content ) ) {
		return new WP_Error( 'empty_pdf', 'Error: The PDF content is empty.' );
	}

	header( 'Content-Type: application/pdf' );
	header( 'Content-Disposition: attachment; filename="blpaczka-waybill-' . implode( '-', $orderIds ) . '.pdf"' );
	header( 'Content-Length: ' . strlen( $pdf_content ) );


    if(BLPACZKA_validate_pdf_content($pdf_content) === false) {
        return new WP_Error( 'invalid_pdf', 'Error: The PDF content is invalid.' );
    } else {
        echo $pdf_content;
    }
	exit;
}

function BLPACZKA_admin_notice_blpaczka_label_does_not_exist() {
	if ( ! empty( $_REQUEST['blpaczka_label_does_not_exist'] ) ) {
		echo '<div class="notice notice-error is-dismissible">
		<p>Zaznacz tylko paczki, które mają etykietę</p></div>';
	} elseif ( ! empty( $_REQUEST['blpaczka_select_order_error'] ) ) {
		echo '<div class="notice notice-error is-dismissible">
		<p>Nie zaznaczyłeś żadnej paczki BLPaczka</p></div>';
	}

	return;
}

add_action( 'admin_notices', 'BLPACZKA_admin_notice_blpaczka_label_does_not_exist' );


function BLPACZKA_get_all_flat_rate_instance_values() {
	$flat_rate_instances = [];
	if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
		return [];
	}
	$shipping_zones = WC_Shipping_Zones::get_zones();

	foreach ( $shipping_zones as $zone ) {
		$zone_shipping_methods = $zone['shipping_methods'];
		foreach ( $zone_shipping_methods as $method ) {
			if ( $method->id === 'flat_rate' ) {
				$instance_id = $method->instance_id;
				$settings    = get_option( 'woocommerce_flat_rate_' . $instance_id . '_settings' );

				if ( ! empty( $settings['blpaczka_require_pickup_point'] ) && $settings['blpaczka_require_pickup_point'] === 'yes' ) {
					$flat_rate_instances[ 'woocommerce_flat_rate_' . $instance_id . '_settings' ] = 'yes';
				}
			}
		}
	}

	return $flat_rate_instances;
}
