jQuery(document).ready(function ($) {
    let boardid = $('#wpf-cross-board-id').data('boardid');
    
    $('.wpf-crossposting .wpf-crossposting-tab .wpf-crossposting-tablinks').on('click', function () {
        var content = $(this).data('content');
        $('.wpf-crossposting-tabcontent').hide();
        $('.wpf-crossposting-tablinks').removeClass('active');
        $(this).addClass('active');
        $('#' + content).show();
    });

    $('.wpf-autocross-panel .wpf-autocross-add').on('click', function () {
        var currentContainer = $(this).parent('.wpf-autopost-add-block');
        wpfAutoCrossSpinner(currentContainer);
        var postType = $(this).data('posttype');
        var termID = 0;
        if (currentContainer.find('select.blog-terms option:selected').length) {
            termID = currentContainer.find('select.blog-terms option:selected').val();
        }
        var forumID = currentContainer.find('select.wpforo-furums option:selected').val();
        $.ajax({
            type: 'POST',
            url: wpfCrossAdmin.ajaxURL,
            data: {
                action: 'wpf_add_autocross',
                post_type: postType,
                term_id: termID,
                board_id: boardid,
                forum_id: forumID
            }
        }).done(function (respose) {
            currentContainer.before(respose);
            wpfAutoCrossSpinner(currentContainer, 0);
        });

    });

    $(document).on('click', '.wpf-autocross-action-buttons .wpf-autocross-action', function () {
        var currentContainer = $(this).parent('.wpf-autocross-action-buttons');
        if ($(this).hasClass("wpf-autocross-on")) {
            wpfAutoCrossOnOff(currentContainer);
        } else if ($(this).hasClass("wpf-autocross-off")) {
            wpfAutoCrossOnOff(currentContainer, 0);
        } else if ($(this).hasClass("wpf-autocross-sync")) {
            wpfAutoCrossSync(currentContainer);
        } else if ($(this).hasClass("wpf-autocross-delete")) {
            wpfAutoCrossDelete(currentContainer);
        }
    });

    function wpfGetAutoCrossActionData(container) {
        var info = container.find('.wpf-autocross-info');
        return {
            post_type: info.data('posttype'),
            term_id: info.data('blogterm'),
            taxanomy: info.data('blogtaxanomy'),
            forum_id: info.data('forum'),
            enabled: info.data('autocrossenabled'),
        }
    }

    function wpfAutoCrossOnOff(container, enable = 1) {
        wpfAutoCrossSpinner(container);
        var info = wpfGetAutoCrossActionData(container);
        $.ajax({
            type: 'POST',
            url: wpfCrossAdmin.ajaxURL,
            data: {
                action: 'wpf_autocross_on_off',
                post_type: info.post_type,
                term_id: info.term_id,
                board_id: boardid,
                forum_id: info.forum_id,
                enable: enable
            }
        }).done(function (respose) {
            if (enable == 1) {
                container.find('.wpf-autocross-on').hide();
                container.find('.wpf-autocross-off').show();
                container.find('.wpf-autocross-sync').attr('disabled', false);
            } else {
                container.find('.wpf-autocross-on').show();
                container.find('.wpf-autocross-off').hide();
                container.find('.wpf-autocross-sync').attr('disabled', true);
            }
            wpfAutoCrossSpinner(container, 0);
        });
    }

    function wpfAutoCrossDelete(container) {
        wpfAutoCrossSpinner(container);
        var info = wpfGetAutoCrossActionData(container);
        $.ajax({
            type: 'POST',
            url: wpfCrossAdmin.ajaxURL,
            data: {
                action: 'wpf_autocross_delete',
                post_type: info.post_type,
                term_id: info.term_id,
                board_id: boardid,
                forum_id: info.forum_id
            }
        }).done(function (respose) {
            container.parent('.wpf-autocross-relation').remove();
        });
    }

    function wpfAutoCrossSync(container) {
        wpfAutoCrossSpinner(container);
        var info = wpfGetAutoCrossActionData(container);
        $.ajax({
            type: 'POST',
            url: wpfCrossAdmin.ajaxURL,
            data: {
                action: 'wpf_autocross_sync',
                post_type: info.post_type,
                term_id: info.term_id,
                board_id: boardid,
                taxanomy: info.taxanomy,
                forum_id: info.forum_id
            }
        }).done(function (respose) {
            if (typeof respose === 'object' && respose.success === true) {
                if (respose.data.complete == 0 && (respose.data.posts.length !== 0 || respose.data.comments.length !== 0)) {
                    wpfAutoCrossDisplayStats(container, respose.data);
                    wpfAutoCrossSync(container);
                } else {
                    wpfAutoCrossDisplayStats(container, respose.data);
                }
            } else {
                console.log(respose);
            }
            wpfAutoCrossSpinner(container, 0);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
            wpfAutoCrossSpinner(container, 0);
        });
        
    }

    function wpfAutoCrossDisplayStats(container, data) {
        var statContainer = container.next('.wpf-autocross-statistic-log');
        if (data.complete != 1) {
            data.posts.forEach(function (text) {
                statContainer.append('<i>' + text + '</i><br>');
            });
            data.comments.forEach(function (text) {
                statContainer.append('<i>' + text + '</i><br>');
            });
        } else {
            statContainer.html(data.message);
        }
    }

    function wpfAutoCrossSpinner(container, on = 1) {
        if (on == 1 && !container.find('.wpf-autocross-spiner').length) {
            container.append('<span class="wpf-autocross-spiner"></span>');
        } else {
            container.find('.wpf-autocross-spiner').remove();
        }
    }

});