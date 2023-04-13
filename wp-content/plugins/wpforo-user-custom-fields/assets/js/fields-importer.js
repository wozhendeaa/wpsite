jQuery(document).ready(function ($) {

    var itemsPerRequest;

    /* BUDDYPRESS IMPORTER FUNCTIONS */

    $(document).on('click', '.wpfucf-fields-importer', function () {
        $('.wpfucf-fields-importer').removeClass('wpfucf-last-clicked');
        $(this).addClass('wpfucf-last-clicked');
    });


    $(document).on('click', '.wpfucf-bp-fields-importer.wpfucf-not-clicked', function () {
        var btn = $(this);
        var parent = btn.parents('.wpfucf-import-item');
        btn.removeClass('wpfucf-not-clicked');
        $('.wpfucf-loading', parent).toggleClass('wpfucf-visible');
        var data = {plugin: wpfucfVars.importSupportPlugins[0]};
        var ajax = wpfucfGetAjax(data, 'wpfucfFieldsToImportPopup');
        ajax.done(function (response) {
            btn.addClass('wpfucf-not-clicked');
            $('.wpfucf-loading', parent).toggleClass('wpfucf-visible');
            try {
                var obj = JSON.parse(response);
                if (obj.code == 1) {
                    if (!($('#wpfucfPopupFieldsToImport').is(':visible'))) {
                        $('#wpfucfPopupFieldsToImportContent').html(obj.data);
                        $('#wpfucfPopupFieldsToImportAnchor').trigger('click');
                    }
                } else {
                    alert(obj.data);
                }
            } catch (e) {
                console.log(e);
            }
        });
    });

    $(document).on('click', '.wpfucf-import-fields.wpfucf-not-clicked', function (e) {
        e.preventDefault();
        var btn = $(this);
        var parent = btn.parents('.wpfucf-import-fields-actions');
        btn.removeClass('wpfucf-not-clicked');
        $('.wpfucf-loading', parent).toggleClass('wpfucf-visible');
        var data = {plugin: '', action: '', fieldIds: []};
        var nestedPattern = /(fieldIds)\[([\d]+)\]/g;
        $.each($('.wpfucf-available-fields-form').serializeArray(), function () {
            var attrName = this.name;
            if (attrName.match(nestedPattern)) {
                if (this.value > 0) {
                    data.fieldIds.push(this.value);
                }
            } else {
                data[attrName] = this.value;
            }
        });
        var ajax = wpfucfGetAjax(data, data.action);
        ajax.done(function (response) {
            btn.addClass('wpfucf-not-clicked');
            $('.wpfucf-loading', parent).toggleClass('wpfucf-visible');
            try {
                var obj = JSON.parse(response);
                if (obj.code == 1) {
                    wpfucfClosePopup(btn);
                    itemsPerRequest = parseInt(prompt(wpfucfVars.msgImportItemsPerRequest, 250));
                    while (isNaN(itemsPerRequest) || itemsPerRequest <= 0 || itemsPerRequest < 10 || itemsPerRequest > 500) {
                        itemsPerRequest = parseInt(prompt(wpfucfVars.msgImportItemsPerRequest));
                    }
                    var pluginBtnParent = $('.wpfucf-last-clicked').parents('.wpfucf-import-item');
                    $('.wpfucf-fields-importer').attr('disabled', 'disabled');
                    $('.wpfucf-loading', pluginBtnParent).toggleClass('wpfucf-visible');
                    $('.wpfucf-process', pluginBtnParent).html('1%');
                    window.onbeforeunload = confirmExit;
                    function confirmExit() {
                        return "";
                    }
                    data.step = 0;
                    data.progress = 1;
                    data.itemsPerRequest = itemsPerRequest;
                    wpfucfBPUpdateUsersData(data, obj.updateAction);
                } else {
                    alert(obj.data);
                }
            } catch (e) {
                console.log(e);
            }
        });
    });

    function wpfucfBPUpdateUsersData(data, action) {
        var ajax = wpfucfGetAjax(data, action);
        ajax.done(function (response) {
            try {
                var obj = JSON.parse(response);
                var plugin = obj.plugin;
                var step = obj.step;
                var progress = parseInt(obj.progress);
                var pluginBtn = $('.wpfucf-last-clicked');
                var pluginBtnParent = pluginBtn.parents('.wpfucf-import-item');

                if (isNaN(progress)) {
                    progress = 100;
                }
                $('.wpfucf-process', pluginBtnParent).html(progress + '%');
                var data = {plugin: plugin, step: step, itemsPerRequest: itemsPerRequest};
                if (progress < 100) {
                    wpfucfBPUpdateUsersData(data, action);
                } else {
                    window.onbeforeunload = null;
                    pluginBtn.removeAttr('disabled');
                    $('.wpfucf-loading', pluginBtnParent).toggleClass('wpfucf-visible');
                    $('.wpfucf-process', pluginBtnParent).html('');
                }
            } catch (e) {
                console.log(e);
            }
        });
    }

    /**
     * close popup by selector
     */
    function wpfucfClosePopup(elem) {
        var parentDoc = elem.parents('.lity-wrap');
        $(parentDoc, window.parent.document).trigger('click');
    }

    /**
     * create and return ajax object for given action
     */
    function wpfucfGetAjax(data, action) {
        return $.ajax({
            type: 'POST',
            url: wpfucfVars.wpfucfAjaxUrl,
            data: {
                wpfucfAjaxData: JSON.stringify(data),
                action: action
            }
        });
    }
});