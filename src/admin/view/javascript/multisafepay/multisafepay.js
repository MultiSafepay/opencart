function togglePaymentOptionFieldStatus(payment_method_panel, is_active) {
    if (is_active) {
        payment_method_panel.find('.panel-body .form-group:first select.form-control').val(0).change();
    } else {
        payment_method_panel.find('.panel-body .form-group:first select.form-control').val(1).change();
    }
}

function togglePaymentOptionIconStatus(payment_method_panel, status) {
    if (status === '1') {
        payment_method_panel.find('.panel-heading .panel-title a span.status').addClass('active');
    } else {
        payment_method_panel.find('.panel-heading .panel-title a span.status').removeClass('active');
    }
}

function removeOldFilesVersion(text_remove_old_files_confirm, token_name, token, text_empty_old_files, oc_version) {
    $('#remove-old-files').on('click', function(e) {
        e.preventDefault();
        if (confirm(text_remove_old_files_confirm)) {
            var key_prefix = '';
            if(oc_version === '3.0' || oc_version === '2.3') {
                key_prefix = 'extension/';
            }
            $.ajax({
                url: 'index.php?route='+ key_prefix +'payment/multisafepay/removeOldFiles&' + token_name + '=' + token,
                dataType: 'json',
                async: false,
                beforeSend: function() {
                    $('#remove-old-files').button('loading');
                },
                complete: function() {
                    $('#remove-old-files').button('reset');
                },
                success: function(json) {
                    if (json['error']) {
                        $('#tab-maintenance').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                    if (json['success']) {
                        $('#maintenance-warning').remove();
                        $('#multisafepay-maintenance').html(text_empty_old_files);
                        $('#tab-maintenance').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
}

function toggleTokenizationFieldStatus(payment_component_gateway) {
    for (var i = 0; i < payment_component_gateway.length; i++) {
        if (payment_component_gateway[i]) {
            var payment_component_id = $('#payment-multisafepay-' + payment_component_gateway[i] + '-payment-component');
            var tokenization_id = $('#payment-multisafepay-' + payment_component_gateway[i] + '-tokenization');
            var tokenization_field = $('#payment-multisafepay-' + payment_component_gateway[i] + '-tokenization-field');

            if (payment_component_id.find('option').filter(':selected').val() === '0') {
                tokenization_id.prop('selectedIndex', 0);
                tokenization_field.slideUp();
            }

            if (payment_component_id.find('option').filter(':selected').val() === '1') {
                tokenization_field.slideDown();
            }
        }
    }
}

$(document).ready(function() {
    $('#payment-multisafepay-generate-payment-links-status').on('change', function() {
        var selected = $(this).val();
        if (selected === '1') {
            $('#initialize-payment-request-form-group').slideDown();
        }
        if (selected === '0') {
            $('#initialize-payment-request-form-group').slideUp();
        }
    });

    $('#input-filter-payment-method').on('change', function() {
        var selected = $(this).val();
        if (selected !== 'gateway' || selected !== 'giftcard' || selected !== 'generic') {
            $('.payment-type-giftcard, .payment-type-gateway, .payment-type-generic, .drag-and-drop-control').show();
        }
        if (selected === 'gateway') {
            $('.payment-type-gateway').show();
            $('.payment-type-giftcard, .payment-type-generic, .drag-and-drop-control').hide();
        }
        if (selected === 'giftcard') {
            $('.payment-type-giftcard').show();
            $('.payment-type-gateway, .payment-type-generic, .drag-and-drop-control').hide();
        }
        if (selected === 'generic') {
            $('.payment-type-generic').show();
            $('.payment-type-giftcard, .payment-type-gateway, .drag-and-drop-control').hide();
        }
    });

    var default_drake = dragula([document.querySelector('#dragula-container #accordion'), document.querySelector('#dragula-container #accordion')], {
        direction: 'vertical',
        moves: function(el, container, handle) {
            return handle.classList.contains('drag-and-drop-control');
        },
    });
    default_drake.on('drag', function(el) {
        $(el).find('.panel-heading').parent('.payment-method-panel').addClass('drag-active gu-transit');
    });
    default_drake.on('drop', function(el) {
        $(el).find('.panel-heading').parent('.payment-method-panel').removeClass('drag-active gu-transit');
    });
    default_drake.on('cancel', function(el) {
        $(el).find('.panel-heading').parent('.payment-method-panel').removeClass('drag-active gu-transit');
    });
    default_drake.on('dragend', function() {
        $('#dragula-container #accordion .payment-method-panel').each(function(i, obj) {
            $(obj).find('.sort-order').attr('value', i + 1);
        });
    });

    $('.multisafepay-admin-page #tab-payment-methods .panel-group .panel').each(function() {
        var payment_method_panel = $(this);
        payment_method_panel.find('.panel-heading .panel-title a span.status').click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            togglePaymentOptionFieldStatus(payment_method_panel, $(this).hasClass('active'));
        });
        payment_method_panel.find('.panel-body .form-group:first select.form-control').change(function() {
            togglePaymentOptionIconStatus(payment_method_panel, $(this).val());
        });
    });

    var tokenizable_gateways = [];
    $('div[id*=-tokenization-field]').each(function() {
        var gateway = $(this).attr('data-gateway');
        if (gateway) {
            tokenizable_gateways.push(gateway);
        }
    });

    $('select[id*=-payment-component]').each(function() {
        $(this).on('change', function() {
            toggleTokenizationFieldStatus(tokenizable_gateways);
        });
    });
    toggleTokenizationFieldStatus(tokenizable_gateways);
});
