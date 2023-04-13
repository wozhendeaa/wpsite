jQuery(document).ready(function ($) {

    $(document).on('click', '.attachs .wpfattach-filename-wrap[data-attachid]', function () {
        var that = $(this)
        var attachid = that.data('attachid')
        var inner_text = $('.wpfattach-filename', that).text().trim()
        var form = $('<div class="wpfattach-filename-edit-wrap"><input type="text" value="' + inner_text + '" data-attachid="' + attachid + '"/><span class="dashicons dashicons-yes-alt wpfattach-filename-edit-submit"></span></div>')

        that.replaceWith(form)
        $('input[data-attachid]', form).trigger('focus')
        $('input[data-attachid]', form).on('keypress', function (event) {
            var keycode = (event.wpf_keyCode ? event.wpf_keyCode : event.code)
            if (keycode === 'Enter' || keycode === 'NumpadEnter') {
                event.preventDefault()
                if ($.active === 0) {
                    $(this).siblings('.wpfattach-filename-edit-submit').removeClass('dashicons-yes-alt').addClass('dashicons-update').addClass('spin')

                    var aid = $(this).data('attachid')
                    var new_filename = $(this).val()

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            attachid: aid,
                            filename: new_filename,
                            action: 'wpforoattach_edit_filename_ajax'
                        }
                    }).done(function (response) {
                        if (response) {
                            $('.wpfattach-filename', that).text(response)
                            form.replaceWith(that)
                        }
                    })
                }
            }
        })
        $('.wpfattach-filename-edit-submit', form).on('click', function () {
            var e = jQuery.Event('keypress')
            e.wpf_keyCode = 'Enter'
            $('input[data-attachid]', form).trigger(e)
        })
    })

    $('.wpfa-go-wrap ul.wpfa-go-tabs > li').on('click', function () {
        if (!$(this).hasClass('active')) {
            $(this).siblings('li').removeClass('active')
            $(this).addClass('active')
            var groupid = $(this).data('groupid')

            $('.wpfa-go-wrap [id^="wpfa-go-group-"]').fadeOut(200)
            setTimeout(function () {
                $('.wpfa-go-wrap #wpfa-go-group-' + groupid).show()
            }, 200)
        }
    })
})
