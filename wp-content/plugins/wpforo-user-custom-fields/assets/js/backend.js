jQuery(document).ready(function ($) {
    var rowCount = 0;
    var editWrapperID = '';
    var fieldUniqueId = '';
    window.wpfucfFormBuilder.makeSortable();
    window.wpfucfFormBuilder.rebuild();

    var hasActiveTab = false;
    $('.wpfucf-wrapper .nav-tab-wrapper .nav-tab').each(function () {
        if ($(this).hasClass('nav-tab-active')) {
            hasActiveTab = true;
            return;
        }
    });

    if (!hasActiveTab) {
        $('.wpfucf-wrapper .nav-tab-wrapper .nav-tab:first').addClass('nav-tab-active');
    }

    wpfucfIconPicker('.wpfucf-tab-faicon');
    $(document).on('click', '.wpfucf-tab-action-trash.wpfucf-not-clicked', function (e) {
        return confirm(wpfucfVars.msgConfirmTabDelete);
    });

    /**
     * on form submit remove empty columns and rebuild form
     */
    $(document).on('submit', '.wpfucf-form', function (e) {
        var form = $(this);
        if (form.hasClass('wpfucf-form-member-tabs')) {
            $.each($('.wpfucf-rows-wrapper.wpfucf-sortable .wpfucf-row .wpfucf-tab'), function (i) {
                $(this).find('.wpfucf-member-tab-order').attr('name', 'wpforoucf[' + i + ']')
            });
        } else {
            $('.wpfucf-row .wpfucf-row-col').not(':has(.wpfucf-field)').remove();
            $('.wpfucf-row').not(':has(.wpfucf-row-col)').remove();

            $('.data-wpfucf-row-index').val($('.wpfucf-row', form).length);
            window.wpfucfFormBuilder.rebuild();

            if ($(this).hasClass('wpfucf-form-fields')) {
                var fieldNames = $('.wpfucf-field-name', form);
                var isValid = true;
                for (var i = 0; i < fieldNames.length; i++) {
                    var fieldName = $(fieldNames[i]);
                    var nameValue = fieldName.val();
                    for (var j = i + 1; j < fieldNames.length; j++) {
                        var fieldNameToCheck = $(fieldNames[j]);
                        var nameValueToCheck = fieldNameToCheck.val();
                        if (nameValue == nameValueToCheck) {
                            fieldName.parents('.wpfucf-field').css('border', '1px solid #EF4F4F');
                            fieldNameToCheck.parents('.wpfucf-field').css('border', '1px solid #EF4F4F');
                            isValid = false;
                        }
                    }
                }

                if (!isValid) {
                    alert(wpfucfVars.msgInvalidFieldName);
                    e.preventDefault();
                    return false;
                }
            }
        }
    });

    /**
     * on  add/edit form submit check required fields
     */
    $(document).on('submit', '.wpfucf-add-edit-form', function (e) {
        e.preventDefault();
        var form = $(this);
        var action = $('.wpfucf-add-edit-action', form).val();
        if (action === 'add') {
            wpfucfAddField(form);
        } else {

            wpfucfSaveField(form);
        }
    });

    /**
     * confirm options reset
     */
    $(document).on('click', '.wpfucf-reset-options', function (e) {
        if (!confirm(wpfucfVars.msgConfirmResetOptions)) {
            e.preventDefault();
            return false;
        }
    });


    $(document).on('click', '.wpfucf-row-col-actions a, .wpfucf-row-actions a, .wpfucf-field-actions a, .wpfucf-tab-action-cant-view', function (e) {
        e.preventDefault();
        return false;
    });

    /**
     * adds a new row
     */
    $(document).on('click', '.wpfucf-row-action-add', function () {
        rowCount = $('.data-wpfucf-row-index', '.wpfucf-rows-wrapper').val();
        ++rowCount;
        var wpfucfEmptyRow = wpfucfVars.wpfucfEmptyRow.replace(/WPFUCF_ROW_I/g, rowCount);
        $('#wpfucf-rows-anchor').before(wpfucfEmptyRow);
        $('.data-wpfucf-row-index', '.wpfucf-rows-wrapper').val(rowCount);
        window.wpfucfFormBuilder.makeSortable();
    });

    /**
     * delete row
     */
    $(document).on('click', '.wpfucf-row-action-trash', function () {
        var btn = $(this);
        var parent = btn.parents('.wpfucf-row');
        var isRemovable = true;
        if ($('.wpfucf-field.wpfucf-cant-be-inactive', parent).length) {
            alert(wpfucfVars.msgNonRemovableRow);
            isRemovable = false;
            return;
        }
//        $('.wpfucf-field', parent).each(function () {
//            if (!($(this).hasClass('wpfucf-field-removable')) && ($('.wpfucf-rows-wrapper').hasClass('wpfucf-tab-account'))) {
//                isRemovable = false;
//                return;
//            }
//        });

        if (isRemovable && confirm(wpfucfVars.msgConfirmRowDelete)) {
            var rowCountInfo = $('.data-wpfucf-row-index', '.wpfucf-rows-wrapper');
            $.each($('.wpfucf-field', parent), function () {
                $(this).appendTo('.wpfucf-inactive-fields');
            });
            rowCount = rowCountInfo.val();
            rowCountInfo.val(--rowCount);
            btn.parents('.wpfucf-row').remove();
        } else {
            alert(wpfucfVars.msgCannotDeleteDefaultFields);
        }
    });

    /**
     * delete a field
     */
    $(document).on('click', '.wpfucf-field.wpfucf-field-removable .wpfucf-field-action-trash', function () {
        if (confirm(wpfucfVars.msgConfirmFieldDelete)) {
            $(this).parents('.wpfucf-field').remove();
            window.wpfucfFormBuilder.rebuild();
        }
    });

    /**
     * duplicate a field
     */
    $(document).on('click', '.wpfucf-field-action-duplicate.wpfucf-not-clicked', function () {
        var btn = $(this);
        btn.removeClass('wpfucf-not-clicked');
        $('.fas', btn).addClass('fa-pulse fa-spinner');
        var column = btn.parents('.wpfucf-row-col');
        var container = $('.wpfucf-row-col-children', column);
        var parent = btn.parents('.wpfucf-field');
        var data = JSON.parse($('.field-json-data', parent).val());
        var ajax = wpfucfGetAjax(data, 'wpfucfDuplicateField');
        ajax.done(function (response) {
            btn.addClass('wpfucf-not-clicked');
            $('.fas', btn).removeClass('fa-pulse fa-spinner');
            try {
                var obj = JSON.parse(response);
                if (obj.code == 1) {
                    container.append(obj.data);
                    window.wpfucfFormBuilder.rebuild();
                }
            } catch (e) {
                console.log(e);
            }
        });
    });

    /**
     * makes row multicolumn
     */
    $(document).on('click', '.wpfucf-row-col-actions a', function () {
        var actionBtn = $(this);
        var parentRow = actionBtn.parents('.wpfucf-row');
        $('.wpfucf-row-col-actions a', parentRow).removeClass('active');
        actionBtn.addClass('active');

        var parentRowAttrId = parentRow.attr('id');
        var parentRowId = parentRowAttrId.substr(parentRowAttrId.lastIndexOf('-') + 1);
        var colsAnchor = $('.wpfucf-row-cols-wrapper #wpfucf-cols-anchor', parentRow);
        var newColCount = $('.data-wpfucf-col-type', actionBtn).val();

        var currentCols = $('.wpfucf-row-cols-wrapper', parentRow).children('.wpfucf-row-col');
        var currentColCount = currentCols.length;

        if (newColCount > currentColCount) {
            for (var i = 0; i < (newColCount - currentColCount); i++) {
                var wpfucfEmptyCol = wpfucfVars.wpfucfEmptyCol.replace(/WPFUCF_ROW_I/g, parentRowId);
                wpfucfEmptyCol = wpfucfEmptyCol.replace(/WPFUCF_COL_J/g, currentColCount + i);
                colsAnchor.before(wpfucfEmptyCol);
            }
        } else {
            for (var i = currentColCount; i > newColCount; i--) {
                var colFields = $('.wpfucf-row-col-children', currentCols[i - 1]).html();
                currentCols[i - 1].remove();
                $('.wpfucf-row-col-children', currentCols[0]).append(colFields);
            }
        }

        $('#' + parentRowAttrId + ' .wpfucf-row-cols-wrapper .wpfucf-row-col').each(function () {
            var classes = $(this).attr('class');
            classes = classes.replace(/wpfucf\-col\-type\-[\d]+/g, "wpfucf-col-type-" + newColCount);
            $(this).removeClass();
            $(this).addClass(classes);
        });
        window.wpfucfFormBuilder.makeSortable();
        window.wpfucfFormBuilder.rebuild();
    });

    /**
     * opens popup with all available field types
     */
    $(document).on('click', '.wpfucf-add-field-popup-wrapper a', function (e) {
        e.preventDefault();
        var btn = $(this);
        if (!($('#wpfucfPopupFieldTypes').is(':visible'))) {
            $('#wpfucfPopupFieldTypesAnchor').trigger('click');
        }
        return false;
    });

    /**
     * opens a popup with data attributes for adding a field
     */
    $(document).on('click', '.wpfucf-field-type.wpfucf-not-clicked', function () {
        var btn = $(this);
        btn.removeClass('wpfucf-not-clicked');
        $('.wpfucf-loading').addClass('wpfucf-visible');
        var type = btn.attr('id');

//        if (type == 'html') {
        var wWidth = $(window).width();
        var wHeight = $(window).height();
        var popupWidth = wWidth - (wWidth * 30 / 100);
        var popupHeight = wHeight - (wHeight * 25 / 100);
        $('#wpfucfPopupEditAdd').css('width', popupWidth + 'px');
        $('#wpfucfPopupEditAdd').css('min-height', popupHeight + 'px');
//        }

        var data = {type: type};
        var ajax = wpfucfGetAjax(data, 'wpfucfAddPopup');
        ajax.done(function (response) {
            btn.addClass('wpfucf-not-clicked');
            $('.wpfucf-loading').removeClass('wpfucf-visible');
            try {
                var obj = JSON.parse(response);
                if (obj.code == 1) {
                    if (!($('#wpfucfPopupEditAdd').is(':visible'))) {
                        $('#wpfucfPopupEditAdd').html(obj.data);
                        $('#wpfucfPopupEditAddAnchor').trigger('click');
                        wpfucfIconPicker('.wpfucf-field-add-edit-faicon');
                    }
                }
            } catch (e) {
                console.log(e);
            }
        });
    });


    /**
     * open popup for editing field data
     */
    $(document).on('click', '.wpfucf-field-action-edit.wpfucf-not-clicked', function (e) {
        var btn = $(this);
        var editWrapper = btn.parents('.wpfucf-field');
        editWrapperID = editWrapper.attr('id');

        fieldUniqueId = editWrapper.attr('data-field-uniqueid');
        var type = editWrapper.attr('data-field-type');
//        if (type == 'html') {
        var wWidth = $(window).width();
        var wHeight = $(window).height();
        var popupWidth = wWidth - (wWidth * 30 / 100);
        var popupHeight = wHeight - (wHeight * 25 / 100);
        $('#wpfucfPopupEditAdd').css('width', popupWidth + 'px');
        $('#wpfucfPopupEditAdd').css('min-height', popupHeight + 'px');
//        }

        btn.removeClass('wpfucf-not-clicked');
        $('.fas', btn).addClass('fa-pulse fa-spinner');
        var parent = btn.parents('.wpfucf-field');
        var data = JSON.parse($('.field-json-data', parent).val());

        var ajax = wpfucfGetAjax(data, 'wpfucfEditPopup');
        ajax.done(function (response) {
            btn.addClass('wpfucf-not-clicked');
            $('.fas', btn).removeClass('fa-pulse fa-spinner');
            try {
                var obj = JSON.parse(response);
                if (obj.code == 1) {
                    if (!($('#wpfucfPopupEditAdd').is(':visible'))) {
                        $('#wpfucfPopupEditAdd').html(obj.data);
                        $('#wpfucfPopupEditAdd').attr('data-field-uniqueid', fieldUniqueId);
                        $('#wpfucfPopupEditAddAnchor').trigger('click');
                        wpfucfIconPicker('.wpfucf-field-add-edit-faicon');
                    }
                }
            } catch (e) {
                console.log(e);
            }
        });
    });

    /**
     * add a new field
     */
    function wpfucfAddField(form) {
        if (/*validateCanEdit() && validateCanView() && */validateUsergroup()) {
            var data = wpfucfGetValuesAsJSON('.wpfucf-add-edit-form');
            if (wpfucfIsFieldExists('add', data.name, fieldUniqueId)) {
                alert(wpfucfVars.msgFieldAlreadyExists);
                $('#wpfucf-field-add-edit-fieldname').val('');
                return;
            }

            var btn = $('.wpfucf-add-edit-submit', form);
            btn.removeClass('wpfucf-not-clicked');
//            $('.wpfucf-loading').addClass('wpfucf-visible');
            var ajax = wpfucfGetAjax(data, 'wpfucfAddField');
            ajax.done(function (response) {
                btn.addClass('wpfucf-not-clicked');
//                $('.wpfucf-loading').removeClass('wpfucf-visible');
                try {
                    var obj = JSON.parse(response);
                    if (obj.code == 1) {
                        $('#wpfucfRowColFields .wpfucf-row-col-children').append(obj.data);
                        wpfucfClosePopup(btn);
                        $('.wpfucf-add-field-popup-wrapper a .data-wpfucf-nthchild').val(obj.nthChild);
                        $('#wpfucfPopupFieldTypes .wpfucf-action-msg').addClass('wpfucf-action-success');
                        setTimeout(function () {
                            $('#wpfucfPopupFieldTypes .wpfucf-action-msg').removeClass('wpfucf-action-success');
                        }, 2000);
                        window.wpfucfFormBuilder.rebuild();
                    } else {
                        alert(obj.error);
                    }
                } catch (e) {
                    console.log(e);
                }
            });
        }
        return false;
    }


    /**
     * edit the field
     */
    function wpfucfSaveField(form) {
        if (/*validateCanEdit() && validateCanView() && */validateUsergroup()) {
            var btn = $('.wpfucf-add-edit-submit', form);
            var data = wpfucfGetValuesAsJSON('.wpfucf-add-edit-form');
            if (wpfucfIsFieldExists('edit', data.name, fieldUniqueId)) {
                alert(wpfucfVars.msgFieldAlreadyExists);
                $('#wpfucf-field-add-edit-fieldname').val('');
                return;
            }

            btn.removeClass('wpfucf-not-clicked');
//            $('.wpfucf-loading').addClass('wpfucf-visible');
            var ajax = wpfucfGetAjax(data, 'wpfucfSaveField', false);
            ajax.done(function (response) {
                btn.addClass('wpfucf-not-clicked');
//                $('.wpfucf-loading').removeClass('wpfucf-visible');
                try {
                    var obj = JSON.parse(response);
                    if (obj.code == 1) {
                        $('#' + editWrapperID).replaceWith(obj.data);
                        wpfucfClosePopup(btn);
                        window.wpfucfFormBuilder.rebuild();
                    } else {
                        alert(obj.error);
                    }
                } catch (e) {
                    console.log(e);
                }
            });
        }
        return false;
    }

    $(document).on('click', '#wpfucf-reset-timezone-location', function () {
        if (confirm(wpfucfVars.msgResetTimezoneLocation)) {
            var btn = $(this);
            btn.removeClass('wpfucf-not-clicked');
            $('.fas', btn).addClass('fa-pulse fa-spinner');
            var data = wpfucfGetValuesAsJSON('.wpfucf-add-edit-form');
            var ajax = wpfucfGetAjax(data, 'wpfucfResetTimezoneLocation');
            ajax.done(function (response) {
                btn.addClass('wpfucf-not-clicked');
                $('.fas', btn).removeClass('fa-pulse fa-spinner');
                try {
                    var obj = JSON.parse(response);
                    if (obj.code == 1) {
                        $('#wpfucf-field-add-edit-values').val(obj.data);
                        $('.wpfucf-loading').toggleClass('wpfucf-visible');
                    } else {
                        alert(obj.error);
                    }
                } catch (e) {
                    console.log(e);
                }
            });
        }
        return false;
    });

    /**
     * remove invalid class from inputs on focus
     */
    $(document).on('focus', '.wpfucf-field-add-edit-wrapper :input', function () {
        if ($(this).hasClass('wpfucf-invalid')) {
            $(this).removeClass('wpfucf-invalid');
        }
    });

    /**
     * close popup on cancel button click
     */
    $(document).on('click', '.wpfucf-popup-close', function () {
        wpfucfClosePopup($(this));
    });

    /**
     * close popup by selector
     */
    function wpfucfClosePopup(elem) {
        var parentDoc = elem.parents('.lity-wrap');
        $(parentDoc, window.parent.document).trigger('click');
    }

    /**
     * show ajax loading icon
     */
    function wpfucfAjaxHelper(btn) {
        btn.addClass('wpfucf-not-clicked');
        $('.wpfucf-loading').removeClass('wpfucf-visible');
    }

    function wpfucfUcFirst(str) {
        var tmpStr = str.charAt(0).toUpperCase();
        return tmpStr + str.substr(1, str.length - 1);

    }

    /**
     * get add/edit popup field values
     */
    function wpfucfGetInputValues() {
        var data = {};
        var editAddWrapper = $('.wpfucf-field-add-edit-wrapper');
        var inputs = $(":input", editAddWrapper);
        inputs.each(function () {
            if (this.name != '') {
                if (this.type == 'checkbox') {
                    var checkboxes = $('input[name="' + this.name + '"]:checked').map(function () {
                        return this.value;
                    }).get();

                    data[this.name] = checkboxes;
                } else if (this.type == 'radio') {
                    data[this.name] = $('input[name="' + this.name + '"]:checked').val();
                } else {
                    data[this.name] = $(this).val();
                }
            }
        });
        return data;
    }

    /**
     * @param {type} formSelector
     * @returns given form inputs data as JSON object
     */
    function wpfucfGetValuesAsJSON(formSelector) {
        var disabled = $(formSelector).find(':input:disabled').removeAttr('disabled');
        var data = {};
        var namePattern = /([^\[\]\s\t]+)/g;
        $.each($(formSelector).serializeArray(), function () {
            var attrName = '';
            var nested = this.name.match(/\[\]/g);
            if (nested) {
                var matches = this.name.match(namePattern);
                attrName = matches[0];
                if (!(attrName in data)) {
                    data[attrName] = [];
                }
                data[attrName].push(this.value);
            } else {
                attrName = this.name;
                data[attrName] = this.value;
            }
        });
        disabled.attr('disabled', 'disabled');
        return data;
    }

    function wpfucfIconPicker(selector) {
        $(selector).iconpicker({
            placement: 'top',
            selectedCustomClass: 'wpfucf-bg-primary',
            component: '.wpfucf-icon-preview',
            collision: true
        });
    }

    function validateCanEdit() {
        var isValid = true;
        var canEditCheckboxes = $('.wpfucf-can-edit-group').length;
        if (canEditCheckboxes) {
            var checked = $('.wpfucf-can-edit-group:checked').length;
            if (!checked) {
                alert(wpfucfVars.msgCanEditAtLeastOne);
                isValid = false;
            }
        }
        return isValid;
    }

    function validateCanView() {
        var isValid = true;
        var canViewCheckboxes = $('.wpfucf-can-view-group').length;
        if (canViewCheckboxes) {
            var checked = $('.wpfucf-can-view-group:checked').length;
            if (!checked) {
                alert(wpfucfVars.msgCanViewAtLeastOne);
                isValid = false;
            }
        }
        return isValid;
    }

    function wpfucfIsFieldExists(action, name) {
        var isExists = false;
        if (action == 'add') {
            if ($('#wpfucfRowColFields div#wpfucf_' + name).length) {
                isExists = true;
            }
        } else {
            $('#wpfucfRowColFields .wpfucf-field').each(function (i, v) {
                var fCurrId = 'wpfucf_' + name;
                var fId = $(v).attr('id');
                var fUId = $(v).attr('data-field-uniqueid');
                if (fId == fCurrId && fUId != fieldUniqueId) {
                    isExists = true;
                    return false;
                }
            });
        }
        return isExists;
    }

    function validateUsergroup() {
        var isValid = true;
        var usergroupCheckboxes = $('.wpfucf-allowed-group-ids').length;
        if (usergroupCheckboxes) {
            var checked = $('.wpfucf-allowed-group-ids:checked').length;
            if (!checked) {
                alert(wpfucfVars.msgUsergroupAtLeastOne);
                isValid = false;
            }
        }
        return isValid;
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

    /* ACCORDION FUNCTIONS */
    $('.wpfucf-accordion-title').on('click', function () {
        $(this).parent().siblings('.wpfucf-accordion-item').removeClass('wpfucf-accordion-current');
        $(this).parent().siblings('.wpfucf-accordion-item').find('.wpfucf-accordion-content').slideUp();
        $(this).siblings('.wpfucf-accordion-content').slideToggle();
        $(this).parent().toggleClass('wpfucf-accordion-current');
    });

});