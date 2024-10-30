<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * @var $blpaczkaOrderId
 * @var $blpaczkaWaybillLink
 * @var $trackingData
 */
?>

<div class="container">
    <div style="border: 1px solid grey; padding: 10px">
        <h4>Dziękujemy za złożenie zamówienia :)</h4>
        <p>Nr zamówienia w BLPaczka: <?php echo esc_html($blpaczkaOrderId); ?></p>

        <a class="button-primary" target="_blank" href="<?php echo esc_url($blpaczkaWaybillLink); ?>">Pobierz etykietę A4</a>
        <?php if (get_option( 'permalink_structure') === '') {
            $link = get_rest_url( null, 'blpaczka/download-waybill/' . $blpaczkaOrderId . '&LBL=1' );
        } else {
            $link = get_rest_url( null, 'blpaczka/download-waybill/' . $blpaczkaOrderId . '?LBL=1' );
        };?>
        <button class="button-primary js-download-waybill" type="button" data-link="<?php echo esc_url($link); ?>">Pobierz etykietę A6 (LBL)</button>
        <h4>Lista statusów:</h4>
        <ul>
            <?php foreach ($trackingData as $tracking): ?>
                <li><b><?php echo esc_html($tracking['TrackingStatus']['status'] . ':</b> ' . $tracking['TrackingStatus']['status_desc']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
