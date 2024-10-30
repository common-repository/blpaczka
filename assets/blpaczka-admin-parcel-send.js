jQuery(document).ready(function ($) {
    const nonce = blpaczkaData['nonce'];

    $.ajaxSetup({
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', nonce);
        }
    });

    $('.js-download-waybill').on('click', function (e) {
        $.get({
            url: $(this).data('link'),
            xhrFields: {
                responseType: 'blob'
            },
            success: function (data) {
                var blob = new Blob([data], {type: 'application/pdf'});
                var url = window.URL.createObjectURL(blob);
                window.open(url);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Błąd przy pobieraniu pliku PDF:', textStatus, errorThrown);
            }
        });
    });

    $('#create-order').on('click', function (e) {
        e.preventDefault();

        $('.blpaczka-response-container').remove();
        $('#blpaczka-shipment-buttons').find('#loading-section').html('<img class="blpaczka-spinner" src="/wp-admin/images/spinner.gif" alt="spinner"></div>');

        let isValid = true;
        $('#blpaczka-shipment-form').find(':input[required]').each(function () {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('form-invalid');
                if (!$(this).next('.error').length) {
                    $(this).after('<div class="error">To pole jest wymagane</div>');
                }
            } else {
                $(this).removeClass('form-invalid');
                $(this).next('.error').remove();
            }
        });

        if (!isValid) {
            $('#blpaczka-shipment-buttons').find('#loading-section').html('');
            return;
        }

        const formData = new FormData($('#blpaczka-shipment-form')[0]);
        let json = BLPACZKA_prepareData(formData);

        BLPACZKA_orderCreate(json);
    })


    $('form.blpaczka-shipment-form-list').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData($(this)[0]);
        let json = BLPACZKA_prepareData(formData);
        let createOrderButton = $(this).find('.create-order-list-button');
        let thisForm = $(this);
        $.post(
            window.wpApiSettings.root + 'blpaczka/create-order',
            json,
            function (response) {
                let parsedResponse = JSON.parse(response);
                createOrderButton.remove()
                thisForm.after('Zamówiono ✅ <a target="_blank" href="' + parsedResponse['data']['waybill_link'] + '"><div class="button-primary">Etykieta 📄</div></a>');
            }
        )
            .fail((jqXHR) => {
                createOrderButton.replaceWith('<button class="button-primary" style="border-color: red; background-color: #ff6a6a;">Wystąpiły błędy</button><div>Przejdź do edycji zamówienia, aby nadać paczkę</div>');
            });
    })

    $('#get-valuation').on('click', function (e) {
        e.preventDefault();

        $('#blpaczka-shipment-buttons').find('#loading-section').html('<img class="blpaczka-spinner" src="/wp-admin/images/spinner.gif" alt="spinner">');
        const formData = new FormData($('#blpaczka-shipment-form')[0]);
        let json = BLPACZKA_prepareData(formData);
        BLPACZKA_getValuation(json);
    });

    $('#blpaczka-meta-box #blpaczka-shipment-form #no_pickup').change(function () {
        if ($(this).is(':checked')) {
            $('#blpaczka_pickup_data').hide();
        } else {
            $('#blpaczka_pickup_data').show();
        }
    });

    $('#no_pickup').trigger('change');


    function BLPACZKA_orderCreate(json) {
        $.post(
            window.wpApiSettings.root + 'blpaczka/create-order',
            json,
            function (response) {
                $('.blpaczka-response-container').remove();
                let parsedResponse = JSON.parse(response);

                let postId = $('#post_id')[0].value;
                $('#blpaczka-meta-box').html(
                    '<p>Dziękujemy za złożenie zamówienia :)</p>' +
                    '<a class="button-primary" target="_blank" href="' + parsedResponse['data']['waybill_link'] + '">Pobierz etykietę</a>'
                );
                BLPACZKA_hideErrors()
            }
        )
            .done(() => {
                $('.blpaczka-spinner').remove()
                BLPACZKA_hideErrors()
            })
            .fail((jqXHR) => {
                    $('.blpaczka-response-container').remove();
                    let responseMessage = '';
                    try {
                        responseMessage = JSON.parse(jqXHR.responseJSON['message']);
                    } catch (err) {
                        responseMessage = jqXHR.responseJSON['message'];
                    }
                    BLPACZKA_displayErrors(responseMessage);
                    $('.blpaczka-spinner').remove()
                }
            );
    }

    function BLPACZKA_displayErrors(errors) {
        let errorsHtml = '<div class="blpaczka-response-container blpaczka-errors"><div>Błędy walidacji:</div>';
        if (typeof errors === 'string') {
            errorsHtml += `<div class="blpaczka-error">${errors}</div>`;
        } else {
            errors?.Cart?.forEach(cartItem => {
                Object.entries(cartItem.Order).forEach(([errorField, errorMessages]) => {
                    errorsHtml += `<div class="blpaczka-error"><strong>${errorField}:</strong> <ul>`;
                    errorMessages.forEach(errorMessage => {
                        errorsHtml += `<li>${errorMessage}</li>`;
                    });
                    errorsHtml += '</ul></div>';
                });
            });
            errorsHtml += '</div>';
        }

        $('#blpaczka-validation-errors').html(errorsHtml);
        $('#blpaczka-validation-errors').show();
    }


    function BLPACZKA_displayErrorsValuation(errors) {
        let errorsHtml = '<div class="blpaczka-response-container blpaczka-errors"><div>Błędy walidacji:</div>';

        if (typeof errors === 'string') {
            errorsHtml += `<div class="blpaczka-error">${errors}</div>`;
        } else {
            let errorsObj = errors?.validationErrors?.CourierSearch;
            if (errorsObj && typeof errorsObj === 'object') {
                for (var key in errorsObj) {
                    errorsHtml += `<div class="blpaczka-error"><strong>${key}:</strong> <ul>`;
                    errorsHtml += `<li>${errorsObj[key]}</li>`;
                    errorsHtml += '</ul></div>';
                    errorsHtml += '</div>';
                }
            }
        }

        $('#blpaczka-validation-errors').html(errorsHtml);
        $('#blpaczka-validation-errors').show();
    }

    function BLPACZKA_getValuation(json) {
        $.post(
            window.wpApiSettings.root + 'blpaczka/get-valuation',
            json,
            function (response) {
                $('.blpaczka-response-container').remove();
                let parsedResponse = JSON.parse(response);

                parsedResponse['data']['results'].forEach((result) => {
                    let logoLink = result['Courier']['logo'] === '' ? blpaczkaData['pluginDirUrl'] + 'assets/img/logo_blpaczka.svg' : result['Courier']['logo'];

                    $('#blpaczka-get-valuation-results').append(
                        '<button class="blpaczka-response-container blpaczka-courier-valuation p-3 m-2 col">' +
                        '<div class="row">' +
                        '<div class="col col-5 d-flex justify-content-center align-self-center">' +
                        '<img style="object-fit: contain;" alt="courier logo" width="100px" height="100px" ' +
                        'src="' + logoLink + '"/>' +
                        '</div>' +
                        '<div class="col col-7 d-flex justify-content-center align-self-center">' +
                        '<div class="row">' +
                        '<div class="fs-6">' + result['Courier']['name'] + '</div>' +
                        '<div class="d-flex justify-content-center align-self-center fs-5">' + result['Price']['value'] + ' zł' + ' </div>' +
                        '<input class="selected-courier-code" value="' + result['Courier']['courier_code'] + '" type="hidden"/>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</button>')

                    $('.blpaczka-courier-valuation').on('click', function (e) {
                        e.preventDefault();

                        $('.blpaczka-courier-valuation').css({border: '', 'background-color': ''})
                        let courierCode = $(this).find('.selected-courier-code').val();
                        $('#courier_code').val(courierCode).change();
                        // border: '2px solid green',
                        $(this).css({border: '2px solid green', 'background-color': 'rgba(8, 251, 61, 0.06)'});
                    });
                });

                BLPACZKA_hideErrors()
            }
        )
            .done(() => {
                BLPACZKA_hideErrors()
                $('.blpaczka-spinner').remove()
            })
            .fail((jqXHR) => {
                    $('.blpaczka-response-container').remove();
                    let responseMessage = '';
                    try {
                        responseMessage = JSON.parse(jqXHR.responseJSON['message']);
                    } catch (err) {
                        responseMessage = jqXHR.responseJSON['message'];
                    }
                    BLPACZKA_displayErrorsValuation(responseMessage);
                    $('.blpaczka-spinner').remove()
                }
            );
    }


    function BLPACZKA_hideErrors() {
        $('#blpaczka-validation-errors').hide();
    }

    function BLPACZKA_prepareData(formData) {
        let object = {};
        formData.forEach((value, key) => {
            let keys = key.split('.');
            let lastKey = keys.pop();
            let deepRef = keys.reduce((acc, curr) => {
                if (!acc[curr]) {
                    acc[curr] = {};
                }
                return acc[curr];
            }, object);
            if (value === 'on' || value === 'off') {
                deepRef[lastKey] = value === 'on';
            } else {
                deepRef[lastKey] = value;
            }
        });

        return JSON.stringify(object);
    }
});