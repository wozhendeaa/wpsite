import $ from 'jquery';
import Notiflix from "notiflix";

(function ($) {
    'use strict';

    $(document).ready(function() {
        if (ajax_tik_tok_feed_admin_object.has_api_auth_secret === 'false') {
            alertConnection(ajax_tik_tok_feed_admin_object.strings.auth_alerts.notice);
        } else {
            getClientIdAndExecuteAjaxCall();
        }

        let buttonDiv = $('<div/>');

        let button = $('<button/>', {
            text: ajax_tik_tok_feed_admin_object.strings.auth_button,
            id: 'tik-tok-api-auth',
            class: 'button button-secondary button-large',
            type: 'button',
            style: 'margin-top: 10px;',
        });

        buttonDiv.append(button);

        $('input[name^="carbon_fields_compact_input"]').each(function() {
            if ($(this).attr('name') === 'carbon_fields_compact_input[_ttf_customer_id]') {
                $(this).parent('.cf-field__body').parent('.cf-field').append(buttonDiv);

                let buttonElement = $('#tik-tok-api-auth');

                buttonBehavior($(this).val(), buttonElement);

                $(this).keyup(function() {
                    buttonBehavior($(this).val(), buttonElement);
                });
            }
        });

        $('#tik-tok-api-auth').on('click', function(e) {
            e.preventDefault();

            getClientIdAndExecuteAjaxCall();
        });
    });

    function getClientIdAndExecuteAjaxCall() {
        let clientId = '';

        $('input[name^="carbon_fields_compact_input"]').each(function() {
            if ($(this).attr('name') === 'carbon_fields_compact_input[_ttf_customer_id]') {
                clientId = this.value;
            }
        });

        authenticatorAjaxCall(clientId);
    }

    function authenticatorAjaxCall(clientId) {
        $.ajax({
            type: 'POST',
            url: ajax_tik_tok_feed_admin_object.ajax_url,
            data: {
                action: 'api_authenticator',
                security: ajax_tik_tok_feed_admin_object.ajax_nonce,
                client_id: clientId,
            },
            dataType: 'json',
            beforeSend: function () {
                Notiflix.Loading.Standard(ajax_tik_tok_feed_admin_object.strings.please_wait);
            },
            success: function (data) {
                console.log(data);
                $('.notice').remove();

                if (data.isSuccess === true) {
                    alertConnection(data.message, 'success');
                } else {
                    alertConnection(data.message, 'error');
                }
            },
            complete: function (data) {
                Notiflix.Loading.Remove();
            },
        });
    }

    function alertConnection(message, type = 'info') {
        let alert = $('<div/>', {
            class: 'notice notice-' + type + ' is-dismissible'
        });

        alert.html('<p>' + message + '</p>');

        $('.carbon-theme-options').find('h2').append(alert);
    }

    function buttonBehavior(inputVal, button) {
        if (inputVal !== '') {
            button.attr('disabled', false);
        } else {
            button.attr('disabled', true);
        }
    }

})($);