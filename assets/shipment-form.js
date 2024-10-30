const $ = jQuery

$(document).ready(function () {
    const blpaczkaApiUrl = blpaczkaData['apiUrl'];
    const nonce = blpaczkaData['nonce'];

    $.ajaxSetup({
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', nonce);
        }
    });

    function isBlockCheckout() {
        return !!document.querySelector('.wc-block-checkout');
    }

    function BLPACZKA_waitForElement(selector) {
        return new Promise((resolve) => {
            function BLPACZKA_checkForElement() {
                const element = document.querySelector(selector);
                if (element) {
                    resolve(element);
                    return true;
                }
                return false;
            }

            if (BLPACZKA_checkForElement()) return;
            const observer = new MutationObserver(() => {
                if (BLPACZKA_checkForElement()) {
                    observer.disconnect();
                }
            });
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    }

    function BLPACZKA_setPudoPoint(val, container) {
        if (typeof container !== undefined && container !== '' && container !== null) {
            let setInputValue = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, "value").set;
            let event = new Event("input", {bubbles: true});
            setInputValue.call(container, val);
            container.dispatchEvent(event);
        }
    }

    function BLPACZKA_addPudoMapToChecked(element) {
        $('.custom_shipping_option_input_container')?.remove();
        var checkedOption = $(element).find('.wc-block-components-radio-control__option-checked');

        var checkedOptionId = $(checkedOption).find('input').attr('id');
        var instanceId = checkedOptionId.substring(checkedOptionId.lastIndexOf(':') + 1);
        var postalCode = $('#shipping-postcode')?.val()?.replace('-', '');
        if (postalCode === undefined || postalCode === '' || postalCode.length < 5) {
            var BLPACZKA_newInputContainer = $('<div>', {
                class: 'custom_shipping_option_input_container'
            });
            var message = $('<div>', {
                class: 'custom_shipping_option_message',
                text: 'Żeby wyświetlić opcje nadania paczki, podaj dane dostawy'
            });
            $(BLPACZKA_newInputContainer).append(message);
            $(checkedOption).append(BLPACZKA_newInputContainer);
        } else {
            $.get(
                window.wpApiSettings.root + 'blpaczka/check-instance-pudo-map',
                {instanceId: instanceId},
                function (response) {
                    BLPACZKA_setPudoPoint('', document.getElementById('blpaczka-point'))
                    if (response !== false) {
                        var BLPACZKA_newInputContainer = $('<div>', {
                            class: 'custom_shipping_option_input_container'
                        });
                        var BLPACZKA_modalBtn = $('<button>', {
                            text: 'Wybierz punkt',
                            class: 'blpaczka-point-modal-btn',
                            type: 'button'
                        });
                        var BLPACZKA_map_address = blpaczkaApiUrl + '/pudo-map?api_type=' + response['courier'] + '&postalCode=' + postalCode;

                        window.addEventListener('message', function (event) {
                            if (getDomain(event.origin) !== getDomain(blpaczkaApiUrl)) return;

                            if (event.data.type === 'SELECT_CHANGE') {
                                let point = event.data.value;
                                if (point) {
                                    if ($('#custom_shipping_option_input').length) {
                                        $('#custom_shipping_option_input').val(point.name);
                                    } else {
                                        var newInput = $('<input>', {
                                            type: 'text',
                                            id: 'custom_shipping_option_input',
                                            name: 'custom_shipping_option_input',
                                            hidden: 'hidden',
                                            value: point.name
                                        });
                                    }

                                    if ($('.blpaczka-chosen-point-info').length) {
                                        $('.blpaczka-chosen-point-info').remove();
                                    }

                                    var BLPACZKA_chosenPointInfo = $('<span>', {
                                        html: event.data.value.pointData,
                                        class: 'blpaczka-chosen-point-info'
                                    });

                                    BLPACZKA_setPudoPoint(point.name, document.getElementById('blpaczka-point'))
                                    $('#blpaczka-point-checkout-modal').hide();
                                    BLPACZKA_modalBtn.text('Zmień punkt');
                                    $(BLPACZKA_newInputContainer).append(BLPACZKA_chosenPointInfo);
                                    $(BLPACZKA_newInputContainer).append(newInput);
                                }
                            }
                        });

                        $(BLPACZKA_newInputContainer).append(BLPACZKA_modalBtn);
                        $(checkedOption).append(BLPACZKA_newInputContainer);

                        $('.blpaczka-point-modal-btn').on('click', function (e) {
                            e.preventDefault();
                            if ($('#blpaczka-point-checkout-modal').length === 0) {
                                var BLPACZKA_modal = ` 
                                    <div id="blpaczka-point-checkout-modal">
                                        <div class="blpaczka-point-modal-content">
                                            <span class="blpaczka-point-modal-close">&times;</span>
                                            <iframe src="${BLPACZKA_map_address}" width="100%" height="100%" style="border: none; overflow: hidden;"></iframe> 
                                        </div>
                                    </div>
                                `;
                                $(BLPACZKA_newInputContainer).append(BLPACZKA_modal);
                                $('.blpaczka-point-modal-close').on('click', function () {
                                    $('#blpaczka-point-checkout-modal').hide();
                                });
                            }
                            $('#blpaczka-point-checkout-modal').css('display', 'flex');
                        });
                    }
                }
            )
        }
    }

    BLPACZKA_waitForElement('.wc-block-checkout__shipping-fields').then((element) => {
        BLPACZKA_waitForElement('.wc-block-components-shipping-rates-control').then((element) => {
            let filteredElements = $(element).filter(function () {
                return $(this).parents('.wp-block-woocommerce-cart-order-summary-shipping-block').length === 0;
            });
            if (filteredElements.length === 1) {
                BLPACZKA_addPudoMapToChecked(element);
                $(element).on('change', '.wc-block-components-radio-control__input', function () {
                    BLPACZKA_addPudoMapToChecked(element);
                });
                $('#shipping-postcode').on('input', function () {
                    BLPACZKA_addPudoMapToChecked(element);
                });
            }
        });
    });

    if (!isBlockCheckout()) {
        $(document).on('click', '.blpaczka-point-modal-btn', function (e) {
            e.preventDefault();
            if ($('#blpaczka-point-checkout-modal').length === 0) {
                document.getElementById('blpaczka-point').value = '';
                var postalCodeSelector = $('#ship-to-different-address-checkbox').is(':checked') ? $('#shipping_postcode') : $('#billing_postcode');
                var postalCode = postalCodeSelector.val().replace('-', '');
                var BLPACZKA_map_address = blpaczkaApiUrl + '/pudo-map?api_type=' + $('.blpaczka-selected-courier-code').val() + '&postalCode=' + postalCode;
                var BLPACZKA_modal = ` 
                                    <div id="blpaczka-point-checkout-modal">
                                        <div class="blpaczka-point-modal-content">
                                            <span class="blpaczka-point-modal-close">&times;</span>
                                            <iframe class="blpaczka-point-iframe" src="${BLPACZKA_map_address}" width="100%" height="100%""></iframe> 
                                        </div>
                                    </div>
                                `;
                $('.blpaczka-point-modal-btn').after(BLPACZKA_modal);
                window.addEventListener("message", function (event) {
                    if (getDomain(event.origin) === getDomain(blpaczkaApiUrl) && event.data.type === "SELECT_CHANGE" && event.data.value.name.length > 0) {
                        $("#blpaczka-point").val(event.data.value.name);
                        if ($('.blpaczka-chosen-point-info').length) {
                            $('.blpaczka-chosen-point-info').remove();
                        }

                        var BLPACZKA_chosenPointInfo = $('<span>', {
                            html: event.data.value.pointData,
                            class: 'blpaczka-chosen-point-info'
                        });

                        $('#blpaczka-point-checkout-modal').hide();
                        $('.blpaczka-point-modal-btn').text('Zmień punkt');
                        $('.blpaczka-point-modal-btn').after(BLPACZKA_chosenPointInfo);
                    }
                });

            }
            $('#blpaczka-point-checkout-modal').css('display', 'flex');
            $('.blpaczka-point-modal-close').on('click', function () {
                $('#blpaczka-point-checkout-modal').hide();
            });
        });
    }

    function getDomain(url) {
        const hostname = new URL(url).hostname;
        const parts = hostname.split('.');
        return parts.slice(-2).join('.');
    }
});

