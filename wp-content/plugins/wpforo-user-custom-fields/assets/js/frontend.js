jQuery(document).ready(function ($) {

    $(document).on('submit', 'form[name=wpfreg], form#wpf-profile-account-form', function (e) {
        if (!validate($(this))) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        }
    });

    /**
     * show entered password
     */
    $(document).on('click', '.wpfucf-show-password', function () {
        var btn = $(this);
        var parent = btn.parents('.wpfucf-field-icon-wrapper');
        var input = $(':input', parent);
        if (input.attr('type') == 'password') {
            input.attr('type', 'text');
            btn.removeClass('fa-eye-slash');
            btn.addClass('fa-eye');
        } else {
            input.attr('type', 'password');
            btn.removeClass('fa-eye');
            btn.addClass('fa-eye-slash');
        }
    });

    function validate(form) {
        var isValid = true;
        $(':input', form).each(function () {
            var input = $(this);
            var parent = input.parents('.wpf-field');
            if (input.attr('type') == 'checkbox' || input.attr('type') == 'radio') {
                if (parent.hasClass('wpf-field-required')) {
                    var inputs = '';
                    var inputClass = '';
                    if (input.hasClass('wpf-input-checkbox')) {
                        inputs = $('.wpf-input-checkbox', parent);
                        inputClass = 'wpf-input-checkbox';
                    } else if (input.hasClass('wpf-input-radio')) {
                        inputs = $('.wpf-input-radio', parent);
                        inputClass = 'wpf-input-radio';
                    }

                    if (inputs.length) {
                        var checked = $('.' + inputClass + ':checked', parent).length;
                        if (!checked) {
                            parent.addClass('wpfucf-invalid');
                            window.wpforo_prev_submit_time = 0;
                            wpforo_load_hide();
                            wpforo_notice_show(wpfucfVars.msgRequiredCheckboxes, 'error');
                            $('html, body').animate({
                                scrollTop: $(parent).offset().top - 52
                            }, 500);
                            isValid = false;
                            return false;
                        } else {
                            parent.removeClass('wpfucf-invalid');
                            isValid = true;
                        }
                    }
                }
            }
        });

        return isValid;
    }
});