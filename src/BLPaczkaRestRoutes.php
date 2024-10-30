<?php
/**
 * @package BLPaczka
 * @version 1.0.1
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action('rest_api_init', function () {
    register_rest_route('blpaczka', '/create-order', [
        'methods' => 'POST',
        'callback' => 'BLPACZKA_create_order',
        'permission_callback' => function () {
            return current_user_can('manage_woocommerce');
        },
    ]);
    register_rest_route('blpaczka', '/get-valuation', [
        'methods' => 'POST',
        'callback' => 'BLPACZKA_get_valuation',
        'permission_callback' => function () {
            return current_user_can('manage_woocommerce');
        },
    ]);
    register_rest_route('blpaczka', '/check-instance-pudo-map', [
        'methods' => 'GET',
        'callback' => 'BLPACZKA_check_instance_pudo_map_rest_route',
        'permission_callback' => function () {
            return true;
        },
    ]);
    register_rest_route('blpaczka', '/download-waybill/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'BLPACZKA_api_download_waybill',
        'permission_callback' => function () {
	        return current_user_can('manage_woocommerce');
        },
    ]);
});


function BLPACZKA_create_order(WP_REST_Request $request)
{
    $url = BLPACZKA_API_URL . '/api/createOrderV2.json';
    $requestBody = json_decode($request->get_body(), true);

    if (!empty($requestBody['pickup_ready_time'])) {
        $readyTime = explode(':', $requestBody['pickup_ready_time']);
        if (!empty($readyTime)) {
            $requestBody['Cart'][0]['Order']['pickup_ready_time'] = $readyTime[0];
            $requestBody['Cart'][0]['Order']['pickup_ready_time_minute'] = $readyTime[1];
        }
        unset($requestBody['pickup_ready_time']);
    }
    if (!empty($requestBody['pickup_close_time'])) {
        $closeTime = explode(':', $requestBody['pickup_close_time']);
        if (!empty($closeTime)) {
            $requestBody['Cart'][0]['Order']['pickup_close_time'] = $closeTime[0];
            $requestBody['Cart'][0]['Order']['pickup_close_time_minute'] = $closeTime[1];
        }
        unset($requestBody['pickup_close_time']);
    }

    $postId = $requestBody['post_id'];
    unset($requestBody['post_id']);
    $requestBody['CourierSearch']['origin'] = 'woocommerce';

    $json = wp_json_encode($requestBody);

    $response = wp_remote_post($url, [
        'body' => $json,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]);

    $responseBody = json_decode($response['body'], true);

    if ($responseBody['success'] != true) {
        $message = !empty($responseBody['message']) ? $responseBody['message'] : wp_json_encode($responseBody['data']['validationErrors']);

        return new WP_Error('request_failed', $message, ['status' => 400]);
    } else {
	    $order = wc_get_order($postId);
		if ($order) {
			if ( BLPACZKA_is_HPOS() ) {
				$order->add_meta_data( 'BLPACZKA_blpaczka_order_id', sanitize_text_field( $responseBody['data']['Order'][0]['id'] ) );
				$order->add_meta_data( 'BLPACZKA_blpaczka_waybill_link', sanitize_text_field( $responseBody['data']['waybill_link'] ) );
				$order->update_status( 'completed' );
				$order->save();
			} else {
				update_post_meta( $postId, 'BLPACZKA_blpaczka_order_id', sanitize_text_field( $responseBody['data']['Order'][0]['id'] ) );
				update_post_meta( $postId, 'BLPACZKA_blpaczka_waybill_link', sanitize_text_field( $responseBody['data']['waybill_link'] ) );
				$order->update_status( 'completed' );
				$order->save();
			}
		}
    }

    if (is_wp_error($response)) {
        return new WP_Error('request_failed', wp_json_encode($response, JSON_PRETTY_PRINT), ['status' => 500]);
    }

    return new WP_REST_Response(wp_remote_retrieve_body($response), wp_remote_retrieve_response_code($response));
}

function BLPACZKA_get_valuation(WP_REST_Request $request)
{
    $url = BLPACZKA_API_URL . '/api/getValuation.json';
    $requestBody = json_decode($request->get_body(), true);

    $requestBody['CourierSearch']['postal_delivery'] = $requestBody['Cart'][0]['Order']['taker_postal'];
    $requestBody['CourierSearch']['postal_sender'] = $requestBody['Cart'][0]['Order']['postal'];
    $requestBody['CourierSearch']['origin'] = 'woocommerce';

    unset($requestBody['post_id']);
    unset($requestBody['Cart']);
    unset($requestBody['CartOrder']);

    $json = wp_json_encode($requestBody);

    $response = wp_remote_post(
        $url,
        [
            'body' => $json,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]
    );

    $responseBody = json_decode($response['body'], true);

    if ($responseBody['success'] != true) {
        return new WP_Error('request_failed', empty($responseBody['message']) ? wp_json_encode($responseBody['data']) : $responseBody['message'], ['status' => 400]);
    }

    if (is_wp_error($response)) {
        return new WP_Error('request_failed', wp_json_encode($response, JSON_PRETTY_PRINT), ['status' => 500]);
    }

    return new WP_REST_Response(wp_remote_retrieve_body($response), wp_remote_retrieve_response_code($response));
}

function BLPACZKA_check_instance_pudo_map_rest_route(WP_REST_Request $request)
{
    $instanceId = $request->get_param('instanceId');

    return new WP_REST_Response(BLPACZKA_check_instance_pudo_map($instanceId), '200');
}


function BLPACZKA_check_instance_pudo_map($instanceId)
{
    $option_key = "woocommerce_flat_rate_{$instanceId}_settings";
    $shipping_settings = get_option($option_key);
    $courierName = $shipping_settings['blpaczka_settings_courier'] ?? '';
    $isPudo = in_array($courierName, BLPACZKA_PUDO_COURIERS);

    if ($isPudo) {
        return [
            'courier' => str_replace(
                ['paczkomaty', 'paczkomaty_eco'],
                ['inpost', 'inpost'],
                $courierName),
        ];
    } else {
        return false;
    }
}
