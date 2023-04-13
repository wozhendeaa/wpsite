(function ($) {
    /* global tinymce */
    /* global wpfucfObject */
    tinymce.PluginManager.add('wpfucf', function (ed, url) {
        if (ed.id === "wpfucf_html_tab") {
            ed.addButton('wpfucf', {
                image: wpfucfObject.image,
                tooltip: wpfucfObject.tooltip,
                onclick: function () {
                    $('#wpfucf_shortcodes').trigger('click');
                }
            });
        }
    });

    $(document).on('click', '.wpfucf-field-shortcode', function () {
        tinymce.activeEditor.execCommand('mceInsertContent', 0, '[wpfucf field="' + $(this).data('key') + '"]');
        $('[data-wpfucf-close]', window.parent.document).trigger('click');
    });
})(jQuery);