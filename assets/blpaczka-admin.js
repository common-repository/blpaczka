jQuery(document).ready(function($) {
    function togglePickupPointField() {
        var courierSelect = $('#woocommerce_flat_rate_blpaczka_settings_courier');
        var additionalField = $('#woocommerce_flat_rate_blpaczka_require_pickup_point');

        if (courierSelect.length && additionalField.length) {
            var selectedOption = courierSelect.val();
            var couriersPudo = blpaczkaData.couriersPudo;
            var courierRequirePudo = blpaczkaData.couriersRequirePudo;

            if (couriersPudo.includes(selectedOption)) {
                additionalField.parent().show();
                if (courierRequirePudo.includes(selectedOption)) {
                    additionalField.prop('checked', true)
                } else {
                    additionalField.prop('checked', false)
                }
            } else {
                additionalField.prop('checked', false)
                additionalField.parent().hide();
            }
        }
    }

    $(document).on('wc_backbone_modal_loaded', function() {
        togglePickupPointField();

        $('#woocommerce_flat_rate_blpaczka_settings_courier').on('change', function() {
            togglePickupPointField();
        });
    });
});
