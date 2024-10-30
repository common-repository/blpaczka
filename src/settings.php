<?php
/**
 * @package BLPaczka
 * @version 1.0.1
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function BLPACZKA_filter_woocommerce_settings_tabs_array( $settings_tabs ) {
	$settings_tabs['blpaczka_settings'] = 'BLPaczka';

	return $settings_tabs;
}

function BLPACZKA_action_woocommerce_sections_blpaczka_settings() {
	global $current_section;

	$tab_id = 'blpaczka_settings';

	// Must contain more than one section to display the links
	// Make first element's key empty ('')
	$sections = [
		''     => 'Ustawienia wysyłki',
		'auth' => 'Autoryzacja',
	];

	echo '<ul class="nav nav-tabs">';

	$array_keys = array_keys( $sections );

	foreach ( $sections as $id => $label ) {
		echo '<li class="nav-item"><a class="nav-link' . esc_html( $current_section == $id ? ' active text-primary border-2' : ' text-black' ) . '" href="' . esc_html( admin_url( 'admin.php?page=wc-settings&tab=' . esc_html( $tab_id ) . '&section=' . esc_html( $id ) ) ) . '">' . esc_html( $label ) . '</a></li>';
	}

	echo '</ul>';
}

function BLPACZKA_get_custom_settings() {
	global $current_section;

	$settings = [];
	if ( $current_section == 'auth' ) {

		$settings = [
			[
				'title' => 'Autoryzacja',
				'type'  => 'title',
				'id'    => 'custom_settings_1',
			],
			[
				'title' => 'Email',
				'type'  => 'text',
                'desc' => sprintf( 'Wpisz tutaj swój login, na który założyłeś konto w <a target="_blank" href="%s">BLPaczka</a>', esc_url( BLPACZKA_API_URL ) ),
                'id'    => 'blpaczka_auth_login',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Klucz API',
				'type'  => 'text',
				'desc'  => 'Wpisz klucz API z zakładki edycja konta',
				'id'    => 'blpaczka_auth_key',
				'css'   => 'min-width:300px;',
			],
			// Section end
			[
				'type' => 'sectionend',
				'id'   => 'custom_settings_1',
			],
		];

	} else {
		$settings = [
			//kurier
			//typ
			//
			[
				'title' => 'Domyślny nadawca',
				'type'  => 'title',
				'id'    => 'custom_settings_overview',
			],
			[
				'title' => 'Imię i nazwisko',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_name',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Nazwa firmy',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_company',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Adres email',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_email',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Ulica',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_street',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Nr domu',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_house_no',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Nr lokalu',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_locum_no',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Kod pocztowy',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_postal',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Miasto',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_city',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Nr telefonu',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_phone',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Nr konta do zwrotów pobrań',
				'type'  => 'text',
				'id'    => 'blpaczka_sender_account',
				'css'   => 'min-width:300px;',
			],
			[
				'type' => 'sectionend',
				'id'   => 'custom_settings_overview',
			],
			[
				'title' => 'Domyślna paczka',
				'type'  => 'title',
				'id'    => 'custom_settings_overview',
			],
			[
				'title' => 'Waga paczki',
				'type'  => 'text',
				'id'    => 'blpaczka_package_weight',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Długość paczki',
				'type'  => 'text',
				'id'    => 'blpaczka_package_length',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Szerokość paczki',
				'type'  => 'text',
				'id'    => 'blpaczka_package_width',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Wysokość paczki',
				'type'  => 'text',
				'id'    => 'blpaczka_package_height',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Zawartość',
				'type'  => 'text',
				'id'    => 'blpaczka_package_content',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Paczka sortowalna',
				'type'  => 'checkbox',
				'id'    => 'blpaczka_package_sortable',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Nie zamawiaj podjazdu',
				'type'  => 'checkbox',
				'id'    => 'blpaczka_package_no_pickup',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Godzina od której kurier może przyjechać',
				'type'  => 'time',
				'id'    => 'blpaczka_package_pickup_ready_time',
				'css'   => 'min-width:300px;',
			],
			[
				'title' => 'Godzina do której kurier może przyjechać',
				'type'  => 'time',
				'id'    => 'blpaczka_package_pickup_close_time',
				'css'   => 'min-width:300px;',
			],
			[
				'title'   => 'Typ paczki',
				'id'      => 'blpaczka_package_type',
				'class'   => 'wc-enhanced-select',
				'css'     => 'min-width:300px;',
				'default' => 'package',
				'type'    => 'select',
				'options' => [
					'package'  => 'Paczka',
					'pallet'   => 'Paleta',
					'envelope' => 'Koperta',
				],
			],
			[
				'title'   => 'Domyślny przewoźnik',
				'id'      => 'blpaczka_package_courier',
				'class'   => 'wc-enhanced-select',
				'css'     => 'min-width:300px;',
				'default' => 'poczta',
				'type'    => 'select',
				'options' => BLPACZKA_COURIERS,
			],
			[
				'title'   => 'Forma płatności',
				'id'      => 'blpaczka_package_payment',
				'class'   => 'wc-enhanced-select',
				'css'     => 'min-width:300px;',
				'default' => 'bank',
				'type'    => 'select',
				'options' => [
					'bank'      => 'Skarbonka',
					'pay_later' => 'Płatność odroczona',
				],
			],
			[
				'title'   => 'Format etykiety',
				'id'      => 'blpaczka_print_format',
				'class'   => 'wc-enhanced-select',
				'css'     => 'min-width:300px;',
				'default' => 'A4',
				'type'    => 'select',
				'options' => [
					'LBL' => 'A6(LBL)',
					'A4'  => 'A4',
				],
			],
			[
				'type' => 'sectionend',
				'id'   => 'custom_settings_overview',
			],
		];
	}

	return $settings;
}

function BLPACZKA_action_woocommerce_settings_blpaczka_settings() {
	$settings = BLPACZKA_get_custom_settings();

	WC_Admin_Settings::output_fields( $settings );
}

function BLPACZKA_action_woocommerce_settings_save_blpaczka_settings() {
	global $current_section;

	$tab_id = 'blpaczka_settings';

	// Call settings function
	$settings = BLPACZKA_get_custom_settings();

	WC_Admin_Settings::save_fields( $settings );

	if ( $current_section ) {
		do_action( 'woocommerce_update_options_' . $tab_id . '_' . $current_section );
	}
}

function BLPACZKA_your_custom_html_output() {
	$blpaczkaApiURLEscaped = esc_url( BLPACZKA_API_URL );
	echo '<div style="padding: 12px;background-color: #f7f7f7;border: 1px solid #ccc;margin-top: 12px;">
            <p><strong>Jak uzyskać Login i Klucz API? 🔒🔑</strong></p>
            <p>→ <a target="_blank" href="' . esc_html( $blpaczkaApiURLEscaped ) . '/rejestracja">Załóż konto</a> albo <a href="' . esc_html( $blpaczkaApiURLEscaped ) . '/login">Zaloguj się</a> na stronie BLPaczki</p>
            <p>→ <a target="_blank" href="' . esc_html( $blpaczkaApiURLEscaped ) . '/edytuj-dane">Wygeneruj</a> i skopiuj klucz API</p>
            <p>→ Przejdź do zakładki <a href="' . esc_html( get_admin_url( null, 'admin.php?page=wc-settings&amp;tab=blpaczka_settings&amp;section=auth' ) ) . '">Autoryzacja</a> na górze strony i wpisz swoje dane</p>
          </div>';
}

function BLPACZKA_add_custom_shipping_option( $fields ) {
	$fields['blpaczka_settings_courier'] = [
		'title'   => 'Wybierz przewoźnika dla dostaw z BLPaczka',
		'class'   => '',
		'css'     => 'min-width:300px;',
		'default' => 'dowolny',
		'type'    => 'select',
		'options' => [
			'dowolny'                   => 'Dowolny',
			'poczta'                    => 'Poczta',
			'dpd'                       => 'DPD',
//            'ups' => 'UPS',
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
			'poczta_ecommerce_envelope' => 'Poczta eCommerce Koperta',
//            'allegro_smart_dpd' => 'DPD Allegro Smart',
//            'paczkomaty_allegro_smart' => 'InPost Paczkomat Allegro Smart',
//            'allegro_smart_ecommerce' => 'Allegro Smart eCommerce',
//            'allegro_smart_poczta' => 'Allegro Smart Poczta',
		],
	];

	$fields['blpaczka_require_pickup_point'] = [
		'label'   => 'Wymagaj punktu odbiorczego',
		'type'    => 'checkbox',
		'default' => 'no',

	];

	return $fields;
}

function BLPACZKA_enqueue_admin_scripts( $hook_suffix ) {
	if ( $hook_suffix === 'woocommerce_page_wc-settings' ) {
		wp_enqueue_script( 'blpaczka-admin-script', plugin_dir_url( __FILE__ ) . '../assets/blpaczka-admin.js', [ 'jquery' ], '1.0', true );
		wp_localize_script( 'blpaczka-admin-script', 'blpaczkaData', [
			'couriersPudo'        => BLPACZKA_PUDO_COURIERS,
			'couriersRequirePudo' => BLPACZKA_PUDO_REQUIRED,
			'nonce'               => wp_create_nonce( 'wp_rest' ),
		] );
	}
}

add_filter( 'woocommerce_shipping_instance_form_fields_flat_rate', 'BLPACZKA_add_custom_shipping_option' );
add_action( 'woocommerce_sections_blpaczka_settings', 'BLPACZKA_action_woocommerce_sections_blpaczka_settings', 10 );
add_action( 'woocommerce_settings_blpaczka_settings', 'BLPACZKA_action_woocommerce_settings_blpaczka_settings', 10 );
add_action( 'woocommerce_settings_save_blpaczka_settings', 'BLPACZKA_action_woocommerce_settings_save_blpaczka_settings', 10 );
add_action( 'woocommerce_settings_tabs_blpaczka_settings', 'BLPACZKA_your_custom_html_output' );
add_filter( 'woocommerce_settings_tabs_array', 'BLPACZKA_filter_woocommerce_settings_tabs_array', 99 );
add_action( 'admin_enqueue_scripts', 'blpaczka_enqueue_admin_scripts' );











