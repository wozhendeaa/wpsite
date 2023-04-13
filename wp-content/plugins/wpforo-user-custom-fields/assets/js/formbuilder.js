(function ($) {
    window.wpfucfFormBuilder = {
        rebuild: function () {
            var rows = $('.wpfucf-rows-wrapper.wpfucf-sortable:not(.wpfucf-tab-fields) .wpfucf-row');
            for (var i = 0; i < rows.length; i++) {
                var row = $(rows[i]);
                row.attr('id', "wpfucfRow-" + i);
                var cols = $('.wpfucf-row-col', row);
                for (var j = 0; j < cols.length; j++) {
                    var col = $(cols[j]);
                    col.attr('id', "wpfucfRowCol-" + i + "_" + j);
                    var fields = $('.wpfucf-field', col);
                    for (var k = 0; k < fields.length; k++) {
                        var field = $(fields[k]);
                        var hiddens = $('.wpfucf-hide :input', field);
                        var fieldId = field.attr('id');
                        field.attr('id', fieldId.replace(/[\d]+$/g, k));
                        $(hiddens).each(function () {
                            var fieldProp = $(this);
                            var fieldPropName = fieldProp.attr('name');
                            fieldProp.attr('name', fieldPropName.replace(/wpforoucf\[([\d]+)\]\[([\d]+)\](\[[\d]+\])/g, "wpforoucf[" + i + "][" + j + "][" + k + "]"));
                        });
                    }
                }
            }
        },
        sortableRows: function () {
            $('.wpfucf-sortable').sortable({
                items: '.wpfucf-sortable-row-item',
                update: function (event, ui) {
                    window.wpfucfFormBuilder.rebuild();
                }
            });
        },
        sortableCols: function () {
            $('.wpfucf-row-cols-wrapper').sortable({
                items: '.wpfucf-sortable-col-item',
                update: function (event, ui) {
                    window.wpfucfFormBuilder.rebuild();
                }
            });
        },
        sortableFields: function () {
            $('.wpfucf-row-col-children').sortable({
                items: '.wpfucf-sortable-child-item',
                connectWith: '.wpfucf-row-col-children',
                placeholder: "ui-state-highlight",
                start: function (event, ui) {
                    window.dragSource = $(ui.item).parents('.wpfucf-rows-wrapper');
                },
                stop: function (event, ui) {
                    var item = $(ui.item);
                    if (!$('.wpfucf-rows-wrapper.wpfucf-tab-fields').length) {
                        var targetContainer = item.parents('.wpfucf-rows-wrapper');
                        var target = item.parents('.wpfucf-row-col-children');
                        /* If start dragging in inactive fields container stop it! */
                        if (window.dragSource.hasClass('wpfucf-inactive') && targetContainer.hasClass('wpfucf-inactive')) {
                            $(this).sortable("cancel");
                        } else if (target.hasClass('wpfucf-inactive-fields') && $(item).hasClass('wpfucf-cant-be-inactive')) {
                            $(this).sortable("cancel");
                            alert(wpfucfVars.msgCannotBeInactive);
                        }
                    } else {
                        /* Trash fields! */
                        if (item.parents('.wpfucf-trashed').length) {
                            $('.wpfucf-field-action-trash', item).removeClass('wpfucf-hide');
                            $('.wpfucf-field-action-duplicate', item).addClass('wpfucf-hide');
                            $('.wpfucf-field-action-edit', item).addClass('wpfucf-hide');
                            var jsonField = $('.wpfucf-hide .field-json-data', item);
                            var name = jsonField.attr('name');
                            jsonField.attr('name', name.replace('wpforoucf', 'wpforoucf-trashed'));
                            var jsonVal = JSON.parse(jsonField.val());
                            jsonVal['isTrashed'] = 1;
                            jsonField.val(JSON.stringify(jsonVal));
                        } else if (item.parents('.wpfucf-tab-fields').length) {
                            $('.wpfucf-field-action-trash', item).addClass('wpfucf-hide');
                            $('.wpfucf-field-action-duplicate', item).removeClass('wpfucf-hide');
                            $('.wpfucf-field-action-edit', item).removeClass('wpfucf-hide');
                            var jsonField = $('.wpfucf-hide .field-json-data', item);
                            var name = jsonField.attr('name');
                            jsonField.attr('name', name.replace('wpforoucf-trashed', 'wpforoucf'));
                            var jsonVal = JSON.parse(jsonField.val());
                            jsonVal['isTrashed'] = 0;
                            jsonField.val(JSON.stringify(jsonVal));
                        }
                    }
                },
                receive: function (event, ui) {
                    if ($('.wpfucf-rows-wrapper.wpfucf-tab-fields').length) {
                        $(ui.item).appendTo(this);
                    }
                },
                update: function (event, ui) {
                    window.wpfucfFormBuilder.rebuild();
                }
            });
        },
        makeSortable: function () {
            window.wpfucfFormBuilder.sortableRows();
            window.wpfucfFormBuilder.sortableCols();
            window.wpfucfFormBuilder.sortableFields();
        }
    };
}(jQuery));