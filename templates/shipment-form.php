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

<div id="blpaczka-meta-box">
    <input type="hidden" id="instance_id" name="instance_id" value="<?php echo esc_attr($shippingInstanceId); ?>" required>
    <form id="blpaczka-shipment-form" method="post">
        <div class="container" style="margin-left: 0; margin-right: 0;">
			<?php
			if ( empty( get_option( 'blpaczka_auth_key' ) ) || empty( get_option( 'blpaczka_auth_login' ) ) ) {
				echo '<p style="color: #4285F4;">Możesz ustawić autoryzację w ustawieniach wtyczki BLPaczka</p>';
			}
			?>
            <input type="hidden" id="auth_login" name="auth.login"
                   value="<?php echo esc_attr(get_option( 'blpaczka_auth_login' )); ?>" required>
            <input type="hidden" id="auth_api_key" name="auth.api_key"
                   value="<?php echo esc_attr(get_option( 'blpaczka_auth_key' )); ?>" required>
            <input type="hidden" id="post_id" name="post_id" value="<?php echo esc_attr(esc_html($orderId)); ?>" required>
            <input type="hidden" id="CourierSearch.foreign" name="CourierSearch.foreign"
                   value="<?php echo esc_attr($orderData['shipping']['country'] === 'PL' ? '0' : '1'); ?>" required>
            <div class="blpaczka-column">
                <div class="blpaczka-row">
                    <div class="col col-6">
                        <h1 class="py-1">Dane przesyłki📦:</h1>

                        <div class="form-group row py-1">
                            <label for="courier_code"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Kod
                                kuriera:</label>
                            <div class="col-sm-8 ">
								<?php if ( ! empty( $shipping_settings['blpaczka_settings_courier'] && $shipping_settings['blpaczka_settings_courier'] !== 'dowolny' ) ): ?>
                                    <input type="text" class="form-control" id="courier_code"
                                           name="CourierSearch.courier_code"
                                           value="<?php echo esc_attr($shipping_settings['blpaczka_settings_courier']); ?>"
                                           readonly>
								<?php else: ?>
                                    <select id="courier_code" class="form-control my-2"
                                            name="CourierSearch.courier_code">
                                        <option value="">Dowolny</option>
                                        <option value="poczta"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'poczta' ) ? 'selected' : ''); ?>
                                        >Poczta
                                        </option>
                                        <option value="dpd"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'dpd' ) ? 'selected' : ''); ?>
                                        >DPD
                                        </option>
                                        <option value="ups"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'ups' ) ? 'selected' : ''); ?>
                                        >UPS
                                        </option>
                                        <option value="dhl"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'dhl' ) ? 'selected' : ''); ?>
                                        >DHL
                                        </option>
                                        <option value="blp_cross_border"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'blp_cross_border' ) ? 'selected' : ''); ?>
                                        >BLP Cross-Border
                                        </option>
                                        <option value="blp_cross_border_eco"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'blp_cross_border_eco' ) ? 'selected' : ''); ?>
                                        >BLP Cross-Border Eco
                                        </option>
                                        <option value="fedex"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'fedex' ) ? 'selected' : ''); ?>
                                        >FedEx
                                        </option>
                                        <option value="gls"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'gls' ) ? 'selected' : ''); ?>
                                        >GLS
                                        </option>
                                        <option value="hellman"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'hellman' ) ? 'selected' : ''); ?>
                                        >Hellman
                                        </option>
                                        <option value="inpost"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'inpost' ) ? 'selected' : ''); ?>
                                        >InPost
                                        </option>
                                        <option value="orlen"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'orlen' ) ? 'selected' : ''); ?>
                                        >Orlen
                                        </option>
                                        <option value="paczkomaty"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'paczkomaty' ) ? 'selected' : ''); ?>
                                        >InPost Paczkomat
                                        </option>
                                        <option value="paczkomaty_eco"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'paczkomaty_eco' ) ? 'selected' : ''); ?>
                                        >InPost Paczkomat Eco
                                        </option>
                                        <option value="paczkomaty_to_door"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'paczkomaty_to_door' ) ? 'selected' : ''); ?>
                                        >InPost Paczkomat do drzwi
                                        </option>
                                        <option value="poczta_ecommerce_envelope"
											<?php echo esc_html(( get_option( 'blpaczka_package_courier' ) === 'poczta_ecommerce_envelope' ) ? 'selected' : ''); ?>
                                        >Poczta eCommerce Koperta
                                        </option>
                                    </select>
								<?php endif; ?>
                            </div>
                        </div>

						<?php if ( $orderData['shipping']['country'] !== 'PL' ): ?>
                            <div class="form-group row py-1">
                                <label for="type"
                                       class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Kod
                                    kraju:</label>
                                <div class="col-sm-8">
                                    <input type="text" id="CourierSearch.country_code" name="CourierSearch.country_code"
                                           value="<?php echo esc_attr($orderData['shipping']['country']); ?>" required readonly>
                                </div>
                            </div>
						<?php else: ?>
                            <input type="text" id="CourierSearch.country_code" name="CourierSearch.country_code"
                                   value="<?php echo esc_attr($orderData['shipping']['country']); ?>" required hidden="hidden">
						<?php endif; ?>

                        <div class="form-group row py-1">
                            <label for="type"
                                   class="col-sm-4 col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Typ:</label>
                            <div class="col-sm-8">
                                <select id="type" class="form-control" name="CourierSearch.type" required>
                                    <option value="package">Paczka</option>
                                    <option value="pallet">Paleta</option>
                                    <option value="envelope">Koperta</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="weight"
                                   class="col-sm-4 col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Waga
                                (kg):</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="weight" name="CourierSearch.weight"
                                       min="0"
                                       step="0.01" value="<?php echo esc_attr(get_option( 'blpaczka_package_weight' )); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="side_x"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Długość
                                (cm):</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="side_x" name="CourierSearch.side_x"
                                       min="0"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_package_length' )); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="side_y"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Szerokość
                                (cm):</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="side_y" name="CourierSearch.side_y"
                                       min="0"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_package_width' )); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="side_x"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Wysokość
                                (cm):</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="side_z" name="CourierSearch.side_z"
                                       min="0"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_package_height' )); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="blpaczka_package_content"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Zawartość
                                przesyłki:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="blpaczka_package_content"
                                       name="Cart.0.Order.package_content" min="0"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_package_content' )); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"
                                   for="orderSum">Ubezpieczenie (w zł):</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="orderSum" name="CourierSearch.cover"
                                       min="0"
                                       value="<?php echo esc_attr($orderSum); ?>">
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="uptake"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Pobranie
                                (w zł):</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="uptake" name="CourierSearch.uptake"
                                       min="0"
                                       value="<?php echo esc_attr($cod_amount); ?>">
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sortable"
                                   class="col-sm-4  col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Sortowalne:</label>
                            <div class="col-sm-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sortable"
                                           name="CourierSearch.sortable" <?php echo esc_html(get_option( 'blpaczka_package_sortable' ) === 'yes' ? 'checked' : ''); ?>
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Bez
                                podjazdu kuriera:</label>
                            <div class="col-sm-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="no_pickup"
                                           name="CourierSearch.no_pickup" <?php echo esc_html(get_option( 'blpaczka_package_no_pickup' ) === 'yes' ? 'checked' : ''); ?>
                                    >
                                </div>
                            </div>
                        </div>
                        <div id="blpaczka_pickup_data">
                            <div class="form-group row py-1">
                                <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Dzień
                                    przyjazdu kuriera:</label>
                                <div class="col-sm-8">
                                    <div class="form-check">
                                        <input class="form-date-input" type="date" id="pickup_date"
                                               name="Cart.0.Order.pickup_date"
                                               value="<?php echo esc_attr(gmdate( 'Y-m-d', strtotime( '+ 1 day' ) )); ?>"
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row py-1">
                                <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Godzina
                                    OD której kurier może przyjechać:</label>
                                <div class="col-sm-8">
                                    <div class="form-check">
                                        <input class="form-control" type="time" id="pickup_ready_time"
                                               name="pickup_ready_time"
                                               value="<?php echo esc_attr(get_option( 'blpaczka_package_pickup_ready_time' ) ?: ''); ?>"
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row py-1">
                                <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Godzina
                                    DO której kurier może przyjechać:</label>
                                <div class="col-sm-8">
                                    <div class="form-check">
                                        <input class="form-control" type="time" id="pickup_close_time"
                                               name="pickup_close_time"
                                               value="<?php echo esc_attr(get_option( 'blpaczka_package_pickup_close_time' ) ?: ''); ?>"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="blpaczka-row">
                    <div class="col col-6">
                        <h1 class="py-1">Dane nadawcy:</h1>
                        <div class="form-group row py-1">
                            <label for="sender_name"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Nazwa
                                nadawcy:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_name" name="Cart.0.Order.name"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_name' )); ?>"
                                       data-key="company_name"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_company"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Nazwa
                                firmy:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_company"
                                       name="Cart.0.Order.vat_company"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_company' )); ?>">
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_email"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Adres
                                email:</label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" id="sender_email" name="Cart.0.Order.email"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_email' )); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_street"
                                   class="col-sm-4 col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Ulica:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_street" name="Cart.0.Order.street"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_street' )); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_house_no"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Numer
                                domu:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_house_no"
                                       name="Cart.0.Order.house_no"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_house_no' )); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_locum_no"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Numer
                                mieszkania:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_locum_no"
                                       name="Cart.0.Order.locum_no"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_locum_no' )); ?>">
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_postal"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Kod
                                pocztowy:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_postal" name="Cart.0.Order.postal"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_postal' )); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_city"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Miasto:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_city" name="Cart.0.Order.city"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_city' )); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_phone"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Nr.
                                telefonu:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_phone" name="Cart.0.Order.phone"
                                       value="<?php echo esc_attr(get_option( 'blpaczka_sender_phone' )); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row py-1" <?php echo esc_html($is_cod_payment ? '' : 'style="display:none;"'); ?>>
                            <label for="sender_account" class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">
                                Nr konta:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_account" name="Cart.0.Order.account"
                                       value="<?php echo esc_attr(get_option('blpaczka_sender_account')); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="blpaczka-column">
                <div class="blpaczka-row">
                    <div class="col col-6">
                        <h1 class="py-1">Informacje o odbiorcy:</h1>
                        <div class="form-group row py-1">
                            <label for="sender_name"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Nazwa
                                odbiorcy:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_name" name="Cart.0.Order.taker_name"
                                       value="<?php echo esc_attr($orderData['shipping']['first_name'] . ' ' . $orderData['shipping']['last_name']); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_name"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Nr.
                            telefonu:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_name" name="Cart.0.Order.taker_phone"
                                       value="<?php echo esc_attr($orderData['billing']['phone']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="sender_name"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Adres
                            email:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sender_name" name="Cart.0.Order.taker_email"
                                       value="<?php echo esc_attr($orderData['billing']['email']); ?>">
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label for="receiver_point"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Punkt
                            odbioru:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="receiver_point"
                                       name="Cart.0.Order.taker_point" value="<?php echo esc_attr( $selectedPoint ?: '' ); ?>">
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Sprawdź,
                            czy adres jest poprawnie rozdzielony:</label>
                            <div class="col-sm-8">
                                <p><?php echo esc_html($orderData['shipping']['address_1'] . ' ' . $orderData['shipping']['address_2']); ?>
                                    .</p>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label class="col-sm-4 col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Ulica:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="receiver_street"
                                       name="Cart.0.Order.taker_street"
                                       value="<?php echo esc_attr($street); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Numer
                            domu:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="receiver_house_no"
                                       name="Cart.0.Order.taker_house_no"
                                       value="<?php echo esc_attr($houseNumber); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Numer
                            lokalu:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="receiver_apartment_no"
                                       name="Cart.0.Order.taker_locum_no"
                                       value="<?php echo esc_attr($apartmentNumber); ?>">
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Kod
                            pocztowy:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="receiver_postal"
                                       name="Cart.0.Order.taker_postal"
                                       value="<?php echo esc_attr($orderData['shipping']['postcode']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row py-1">
                            <label class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm"">Miasto:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="receiver_city"
                                       name="Cart.0.Order.taker_city"
                                       value="<?php echo esc_attr($orderData['shipping']['city']); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="blpaczka-row">
                    <div class="col col-6">
                        <h1 class="py-1">Płatność:</h1>
                        <div class="form-group row py-1">
                            <label for="payment"
                                   class="col col-form-label blpaczka-input-label col-form-label blpaczka-input-label-sm">Sposób
                                płatności:</label>
                            <div class="col-sm-8">
                                <select id="payment" class="form-control" name="CartOrder.payment" required>
                                    <option value="bank" <?php echo( get_option( 'blpaczka_package_payment' ) === 'bank' ? 'selected' : '' ); ?>
                                            selected>Skarbonka
                                    </option>
                                    <option value="pay_later" <?php echo( get_option( 'blpaczka_package_payment' ) === 'pay_later' ? 'selected' : '' ); ?>>
                                        Płatność odroczona
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div>

                </div>
            </div>
        </div>
        <div id="blpaczka-get-valuation-results" class="row row-cols-4"></div>
        <div id="blpaczka-validation-errors"></div>
        <div class="card py-1" style="max-width: 100%">
            <div id="blpaczka-shipment-buttons">
                        <button id="create-order" type="submit" class="btn btn-outline-success text-black">Złóż zamówienie 🚚
                        </button>
                        <button id="get-valuation" type="submit" class="btn btn-outline-info text-black">Wyceń 💸
                        </button>
                        <div id="loading-section" class="d-flex flex-row-reverse align-items-center"></div>
            </div>
        </div>
    </form>
</div>