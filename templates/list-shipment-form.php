<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * @var $order
 * @var $orderSum
 * @var $orderData
 * @var $street
 * @var $houseNumber
 * @var $apartmentNumber
 * @var $shipping_settings
 * @var $orderId
 * @var $selectedPoint
 * @var $shippingInstanceId
 */
$is_cod_payment = $orderData['payment_method'] === 'cod';
$cod_amount = $is_cod_payment ? $orderSum : 0;
?>

<form>
</form>

<input type="hidden" id="instance_id" name="instance_id" value="<?php echo esc_attr($shippingInstanceId); ?>">
<form class="blpaczka-shipment-form-list" method="post">
    <div hidden="hidden">
        <input type="hidden" id="auth_login" name="auth.login"
               value="<?php echo esc_attr(get_option( 'blpaczka_auth_login' )); ?>">
        <input type="hidden" id="auth_api_key" name="auth.api_key"
               value="<?php echo esc_attr(get_option( 'blpaczka_auth_key' )); ?>">
        <input type="hidden" id="post_id" name="post_id" value="<?php echo esc_attr($orderId); ?>">
        <input type="hidden" id="CourierSearch.foreign" name="CourierSearch.foreign"
               value="<?php echo $orderData['shipping']['country'] === 'PL' ? '0' : '1'; ?>">
		<?php if ( ! empty( $shipping_settings['blpaczka_settings_courier'] && $shipping_settings['blpaczka_settings_courier'] !== 'dowolny' ) ): ?>
            <input type="text" class="form-control" id="courier_code"
                   name="CourierSearch.courier_code"
                   value="<?php echo esc_attr($shipping_settings['blpaczka_settings_courier']); ?>" readonly>
		<?php else: ?>
            <select id="courier_code" class="form-control my-2" name="CourierSearch.courier_code">
                <option value="">Dowolny</option>
                <option value="poczta"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'poczta' ) ? 'selected' : ''; ?>
                >Poczta
                </option>
                <option value="dpd"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'dpd' ) ? 'selected' : ''; ?>
                >DPD
                </option>
                <option value="ups"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'ups' ) ? 'selected' : ''; ?>
                >UPS
                </option>
                <option value="dhl"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'dhl' ) ? 'selected' : ''; ?>
                >DHL
                </option>
                <option value="blp_cross_border"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'blp_cross_border' ) ? 'selected' : ''; ?>
                >BLP Cross-Border
                </option>
                <option value="blp_cross_border_eco"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'blp_cross_border_eco' ) ? 'selected' : ''; ?>
                >BLP Cross-Border Eco
                </option>
                <option value="fedex"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'fedex' ) ? 'selected' : ''; ?>
                >FedEx
                </option>
                <option value="gls"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'gls' ) ? 'selected' : ''; ?>
                >GLS
                </option>
                <option value="hellman"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'hellman' ) ? 'selected' : ''; ?>
                >Hellman
                </option>
                <option value="inpost"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'inpost' ) ? 'selected' : ''; ?>
                >InPost
                </option>
                <option value="orlen"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'orlen' ) ? 'selected' : ''; ?>
                >Orlen
                </option>
                <option value="paczkomaty"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'paczkomaty' ) ? 'selected' : ''; ?>
                >InPost Paczkomat
                </option>

                <option value="paczkomaty_eco"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'paczkomaty_eco' ) ? 'selected' : ''; ?>
                >InPost Paczkomat Eco
                </option>
                <option value="paczkomaty_to_door"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'paczkomaty_to_door' ) ? 'selected' : ''; ?>
                >InPost Paczkomat do drzwi
                </option>
                <option value="poczta_ecommerce_envelope"
					<?php echo ( get_option( 'blpaczka_package_courier' ) === 'poczta_ecommerce_envelope' ) ? 'selected' : ''; ?>
                >Poczta eCommerce Koperta
                </option>
            </select>
		<?php endif; ?>

		<?php if ( $orderData['shipping']['country'] !== 'PL' ): ?>
            <input type="text" id="CourierSearch.country_code" name="CourierSearch.country_code"
                   value="<?php echo esc_attr($orderData['shipping']['country']); ?>" readonly>
		<?php else: ?>
            <input type="text" id="CourierSearch.country_code" name="CourierSearch.country_code"
                   value="<?php echo esc_attr($orderData['shipping']['country']); ?>" hidden="hidden">
		<?php endif; ?>

        <select id="type" class="form-control" name="CourierSearch.type">
            <option value="package">Paczka</option>
            <option value="pallet">Paleta</option>
            <option value="envelope">Koperta</option>
        </select>


        <input type="number" class="form-control" id="weight" name="CourierSearch.weight" min="0"
               step="0.01" value="<?php echo esc_attr(get_option( 'blpaczka_package_weight' )); ?>">

        <input type="number" class="form-control" id="side_x" name="CourierSearch.side_x" min="0"
               value="<?php echo esc_attr(get_option( 'blpaczka_package_length' )); ?>">

        <input type="number" class="form-control" id="side_y" name="CourierSearch.side_y" min="0"
               value="<?php echo esc_attr(get_option( 'blpaczka_package_width' )); ?>">

        <input type="number" class="form-control" id="side_z" name="CourierSearch.side_z" min="0"
               value="<?php echo esc_attr(get_option( 'blpaczka_package_height' )); ?>">

        <input type="text" class="form-control" id="blpaczka_package_content"
               name="Cart.0.Order.package_content" min="0"
               value="<?php echo esc_attr(get_option( 'blpaczka_package_content' )); ?>">

        <input type="number" class="form-control" id="orderSum" name="CourierSearch.cover" min="0"
               value="<?php echo esc_attr($orderSum); ?>">

        <input type="number" class="form-control" id="uptake" name="CourierSearch.uptake" min="0"
               min="0"
               value="<?php echo esc_attr($cod_amount); ?>">

        <input class="form-check-input" type="checkbox" id="sortable"
               name="CourierSearch.sortable" <?php echo get_option( 'blpaczka_package_sortable' ) === 'yes' ? 'checked' : ''; ?>
        >

        <input class="form-check-input" type="checkbox" id="no_pickup"
               name="CourierSearch.no_pickup" <?php echo get_option( 'blpaczka_package_no_pickup' ) === 'yes' ? 'checked' : ''; ?>
        >
        <input class="form-date-input" type="date" id="pickup_date"
               name="Cart.0.Order.pickup_date"
               value="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( '+1 day' ) ) ); ?>"
        >
        <input class="form-control" type="number" id="pickup_ready_time"
               name="Cart.0.Order.pickup_ready_time"
               value="<?php echo esc_attr(get_option( 'blpaczka_package_pickup_ready_time' )) ?: ''; ?>"
        >
        <input class="form-control" type="number" id="pickup_ready_time_minute"
               name="Cart.0.Order.pickup_ready_time_minute"
               value="<?php echo esc_attr(get_option( 'blpaczka_package_pickup_ready_time_minute' )) ?: ''; ?>"
        >
        <input class="form-control" type="number" id="pickup_close_time"
               name="Cart.0.Order.pickup_close_time"
               value="<?php echo esc_attr(get_option( 'blpaczka_package_pickup_close_time' )) ?: ''; ?>"
        >

        <input class="form-control" type="number" id="pickup_close_time_minute"
               name="Cart.0.Order.pickup_close_time_minute"
               value="<?php echo esc_attr(get_option( 'blpaczka_package_pickup_close_time_minute' )) ?: ''; ?>"
        >

        <input type="text" class="form-control" id="sender_name" name="Cart.0.Order.name"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_name' )); ?>" data-key="company_name"
        >
        <input type="text" class="form-control" id="sender_company" name="Cart.0.Order.vat_company"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_company' )); ?>">
        <input type="email" class="form-control" id="sender_email" name="Cart.0.Order.email"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_email' )); ?>">
        <input type="text" class="form-control" id="sender_street" name="Cart.0.Order.street"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_street' )); ?>">
        <input type="text" class="form-control" id="sender_house_no" name="Cart.0.Order.house_no"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_house_no' )); ?>">
        <input type="text" class="form-control" id="sender_locum_no" name="Cart.0.Order.locum_no"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_locum_no' )); ?>">
        <input type="text" class="form-control" id="sender_postal" name="Cart.0.Order.postal"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_postal' )); ?>">
        <input type="text" class="form-control" id="sender_city" name="Cart.0.Order.city"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_city' )); ?>"
        >
        <input type="text" class="form-control" id="sender_phone" name="Cart.0.Order.phone"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_phone' )); ?>"
        >
        <input type="text" class="form-control" id="sender_account" name="Cart.0.Order.account"
               value="<?php echo esc_attr(get_option( 'blpaczka_sender_account' )); ?>">
        <select id="payment" class="form-control" name="CartOrder.payment">
            <option value="bank" <?php echo( get_option( 'blpaczka_package_payment' ) === 'bank' ? 'selected' : '' ); ?>
                    selected>Skarbonka
            </option>
            <option value="pay_later" <?php echo( get_option( 'blpaczka_package_payment' ) === 'pay_later' ? 'selected' : '' ); ?>>
                Płatność odroczona
            </option>
        </select>
        <input type="text" class="form-control" id="sender_name" name="Cart.0.Order.taker_name"
               value="<?php echo esc_attr($orderData['shipping']['first_name'] . ' ' . $orderData['shipping']['last_name']); ?>"
        >
        <input type="text" class="form-control" id="sender_name" name="Cart.0.Order.taker_phone"
               value="<?php echo esc_attr($orderData['billing']['phone']); ?>">
        <input type="text" class="form-control" id="sender_name" name="Cart.0.Order.taker_email"
               value="<?php echo esc_attr($orderData['billing']['email']); ?>">
		<?php if ( ! empty( $selectedPoint ) ): ?>
            <input type="text" class="form-control" id="receiver_point"
                   name="Cart.0.Order.taker_point" value="<?php echo esc_attr( $selectedPoint ); ?>" readonly>
		<?php endif; ?>


        <input type="text" class="form-control" id="receiver_street"
               name="Cart.0.Order.taker_street"
               value="<?php echo esc_attr($street); ?>"
        >
        <input type="text" class="form-control" id="receiver_house_no"
               name="Cart.0.Order.taker_house_no"
               value="<?php echo esc_attr($houseNumber); ?>"
        >
        <input type="text" class="form-control" id="receiver_apartment_no"
               name="Cart.0.Order.taker_locum_no"
               value="<?php echo esc_attr($apartmentNumber); ?>">
        <input type="text" class="form-control" id="receiver_postal"
               name="Cart.0.Order.taker_postal"
               value="<?php echo esc_attr($orderData['shipping']['postcode']); ?>">
        <input type="text" class="form-control" id="receiver_city"
               name="Cart.0.Order.taker_city"
               value="<?php echo esc_attr($orderData['shipping']['city']); ?>">
    </div>
    <?php
   if (!empty($shipping_settings['blpaczka_settings_courier'])): ?>
        <button type="submit" class="create-order-list-button button-secondary">Szybkie zamówienie
            - <?php echo esc_html(ucfirst($shipping_settings['blpaczka_settings_courier'])); ?> 🚚
        </button>
    <?php else: ?>
        Zdefiniuj kuriera w ustawieniach
    <?php endif; ?>

</form>

