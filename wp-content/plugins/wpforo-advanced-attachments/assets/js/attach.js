jQuery(document).ready(function ($) {
    var wpforo_wrap = $('#wpforo-wrap')
    var dialog = $('#wpfa_dialog_wrap')
    var wpfa_fileupload = $('#wpfa_fileupload')
    var wpfa_forms = $('#wpforo-wrap form[data-textareaid]')

    window.addEventListener('dragover', function (e) {
        e = e || event
        e.preventDefault()
    }, false)

    window.addEventListener('drop', function (e) {
        e = e || event
        e.preventDefault()
        $('.wpforo-dropzone', wpforo_wrap).remove()
    }, false)

    $(document).on('click', '#wpfa_dialog_close', function () {
        if (dialog.is(':visible')) {
            $('.wpfattach-portable-wrap').fadeOut(50, 'linear')
        }
    })

    $(document).on('keydown', dialog, function (e) {
        if (dialog.is(':visible')) {
            if (e.code === 'Escape') {
                $('.wpfattach-portable-wrap').fadeOut(50, 'linear')
            }
        }
    })

    $(document).on('dragenter', 'body', function (e) {
        if (!parseInt(wpfaOptions['is_daily_limit_exceeded'])) {
            if (wpfa_isSourceExternalFile(e.originalEvent.dataTransfer)) {
                if (!$('.wpforo-dropzone').length && (!!parseInt(wpfaOptions['auto_upload']) || dialog.is(':visible'))) {
                    $('<div class="wpforo-dropzone"><span>' + wpfa_phrase('DROP HERE') + '</span></div>').appendTo(wpfa_forms).delay(5000).fadeOut(50, function () {
                        $(this).remove()
                    })
                }
            }
        }
    })

    document.addEventListener('wpforo_tinymce_paste', function (e) {
        if (wpfa_isSourceExternalFile(e.detail.clipboardData)) e.detail.preventDefault()
        $('form[data-textareaid="' + wpforo_editor.get_active_textareaid() + '"]').trigger({
            type: 'paste',
            delegatedEvent: { originalEvent: { clipboardData: e.detail.clipboardData } },
            originalEvent: { clipboardData: e.detail.clipboardData }
        })
    })

    document.addEventListener('wpforo_topic_portable_form', function (e) {
        if (wpfa_fileupload.length) {
            wpfa_forms = $('#wpforo-wrap form[data-textareaid]')
            wpfa_init_fileupload(wpfa_fileupload, wpfa_forms, wpfa_forms)
        }
    })

    var wpfa_insert_content_on_fileuploadstop = true
    wpforo_wrap.on('click', '.wpfa-add-files', function () {
        wpfa_insert_content_on_fileuploadstop = false
    })

    wpforo_wrap.on('click', 'label.wpfa-browse', function () {
        var wrap = $(this).parents('form')
        var portable_wrap = $('.wpfattach-portable-wrap', wrap)
        $('.wpfattach-portable-wrap').not(portable_wrap).hide()
        dialog.appendTo(portable_wrap)
    })

    wpforo_wrap.on('click', '.wpf_attach_button', function (e) {
        e.preventDefault()

        var wrap = $(this).parents('form')
        var portable_wrap = $('.wpfattach-portable-wrap', wrap)
        $('.wpfattach-portable-wrap').not(portable_wrap).hide()
        dialog.appendTo(portable_wrap)
        portable_wrap.toggle()

        if (portable_wrap.is(':visible') && !dialog.is(':visible')) {
            var wpfa_dialog_rows = $('#wpfa_dialog_rows', dialog)
            wpfa_dialog_rows.empty()
            wpfa_dialog_rows.data('nomore', 0)
            $('.wpfa-checkbox-select-all input').prop('checked', false)
            dialog.fadeIn(400, 'linear', function () {
                wpfa_load_files(wpfa_fileupload)
            })
        }
    })

    $('#wpfa_dialog_items', dialog).on('scroll', function () {
        if ((this.scrollHeight - this.scrollTop - this.clientHeight) < 200 && $.active === 0) {
            if (!$('#wpfa_dialog_rows').data('nomore')) {
                wpfa_load_files(wpfa_fileupload)
            }
        }
    })

    /// tools ///
    dialog.on('click', '.wpfa-close-error', function () {
        var error_row = $(this).parents('.wpfa-file-not-allow')
        error_row.fadeOut(200, function () {
            error_row.remove()
        })
    })

    function wpfa_is_attachs_count_exceed_max () {
        var max_attachs_per_post = parseInt(wpfaOptions.max_attachs_per_post)
        if (max_attachs_per_post) {
            var checkeds = $('#wpfa_dialog_items .wpfa-template-download .wpfa-item-col-checkbox input[type="checkbox"][name="delete"]:checked')
            var raw_text = wpforo_editor.get_content('raw')
            var attachs_count = (raw_text.match(/<figure[^<>]*?data-attachids="\d+"[^<>]*?>.+?<\/figure>/gi) || []).length
            if (checkeds.length) {
                return (attachs_count + checkeds.length) > max_attachs_per_post
            } else {
                return attachs_count >= max_attachs_per_post
            }
        }
        return false
    }

    dialog.on('click', '#wpfa_dialog_items .wpfa-template-download .wpfa-item-col-checkbox input[type="checkbox"][name="delete"]', function (e) {
        if (this.checked && wpfa_is_attachs_count_exceed_max()) {
            wpforo_notice_show('You have exceeded the maximum allowed attachments per post', 'neutral', parseInt(wpfaOptions.max_attachs_per_post))
            e.preventDefault()
        }
    })

    dialog.on('click', '#wpf_attach_do', function () {
        if (wpfa_is_attachs_count_exceed_max()) {
            wpforo_notice_show('You have exceeded the maximum allowed attachments per post', 'neutral', parseInt(wpfaOptions.max_attachs_per_post))
            return
        }
        wpforo_load_show()
        var attachids = []
        $.each($('#wpfa_dialog_items .wpfa-template-download .wpfa-item-col-checkbox input[type="checkbox"][name="delete"]:checked'), function (i) {
            attachids[i] = parseInt($(this).parents('#wpfa_dialog_items .wpfa-template-download').data('attachid'))
        })
        attachids = attachids.filter(function (attachid) {
            return !isNaN(attachid)
        })
        attachids = attachids.join(',')
        if (attachids) {
            var html = wpfa_build_front_view(attachids)
            wpforo_editor.insert_content(' ' + html + ' ')
        } else {
            wpforo_notice_show(wpfa_phrase('Please select a file(s) using right checkbox(es) to insert in post'))
        }
        wpforo_load_hide()
    })

    dialog.on('click', '.wpfa-dialog-item-row .wpfa-button.wpfa-button-attach', function () {
        if (wpfa_is_attachs_count_exceed_max()) {
            wpforo_notice_show('You have exceeded the maximum allowed attachments per post', 'neutral', parseInt(wpfaOptions.max_attachs_per_post))
            return
        }
        var row_wrap = $(this).closest('.wpfa-dialog-item-row')
        if (row_wrap.length) {
            var attachid = parseInt(row_wrap.data('attachid'))
            if (attachid) {
                var attachids = [attachid]
                var html = wpfa_build_front_view(attachids)
                wpforo_editor.insert_content(' ' + html + ' ')
            }
        }
    })

    wpfa_fileupload.on('fileuploadsubmit', function (e, data) {
        var file = data.files[0]
        var finfo = wpfa_file_info(file)
        if (!finfo.is_allowed) {
            wpforo_notice_show('<i class="wpfa-hide-for-large far ' + finfo.fa_ico + ' fa-2x"></i> ' + file.name + ' - ' + finfo.notice, 'error')
            return false
        }
        wpfa_append_load_to_current_form($(e.delegatedEvent.delegateTarget))
    })

    var wpfa_fileids = []
    wpfa_fileupload.on('fileuploaddone', function (e, data) {
        var file = data.result.files[0]
        wpfa_fileids[wpfa_fileids.length] = file.id
        var wpf_type = wpforo_get_file_type(file.name)
        var wpf_file_fa_ico = wpf_type !== 'file' ? 'fa-file-' + wpf_type : 'fa-file'
        var wpfa_success_msg = wpfa_phrase('Attached Successfully')
        wpforo_notice_show('<i class="wpfa-hide-for-large far ' + wpf_file_fa_ico + ' fa-2x"></i> ' + file.name + ' - ' + wpfa_success_msg, 'success')
    })

    wpfa_fileupload.on('fileuploadstop', function () {
        if (wpfa_fileids.length && wpfa_insert_content_on_fileuploadstop) {
            if (wpfa_is_attachs_count_exceed_max()) {
                wpforo_notice_show('You have exceeded the maximum allowed attachments per post', 'neutral', parseInt(wpfaOptions.max_attachs_per_post))
            } else {
                wpfa_fileids = wpfa_fileids.filter(function (attachid) {
                    return !isNaN(attachid)
                })
                if (wpfa_fileids.length) {
                    var html = wpfa_build_front_view(wpfa_fileids)
                    wpforo_editor.insert_content(' ' + html + ' ')
                }
            }
        }
        wpfa_insert_content_on_fileuploadstop = true
        wpfa_fileids = []
        wpforo_load_hide()
        $('.wpforo-form-load').remove()
    })

    wpforo_wrap.on('click', '.wpforo-dropzone', function () {
        $(this).remove()
    })

    if (wpfa_fileupload.length) {
        wpfa_init_fileupload(wpfa_fileupload, wpfa_forms, wpfa_forms)
    }

    function wpfa_build_front_view (attachids) {
        if (!Array.isArray(attachids)) attachids = attachids.split(',')
        var html = ''
        if (attachids.length) {
            if (wpforo_editor.is_tinymce()) {
                attachids.forEach(function (attachid) {
                    attachid = parseInt(attachid)
                    var front_view = $('.wpfa-dialog-item-row[data-attachid="' + attachid + '"] .wpfa-front-view', dialog)
                    if (front_view.length) html += front_view.html()
                })
            } else {
                attachids = attachids.join(',')
                if (attachids) {
                    html = '[attach]' + attachids + '[/attach]'
                }
            }
        }
        return html
    }

    function wpfa_init_fileupload (initZone, dropZone, pasteZone) {
        if (!dropZone) dropZone = null
        if (!pasteZone) pasteZone = undefined
        initZone.fileupload({
            url: wpforo.ajax_url,
            dropZone: dropZone,
            pasteZone: pasteZone,
            prependFiles: true,
            autoUpload: !!parseInt(wpfaOptions['auto_upload'])
        })
    }

    var load_files_active = false

    function wpfa_load_files (loadZone) {
        if (!load_files_active) {
            load_files_active = true
            $('#wpfa-loading-spinner').show()
            var wpfa_dialog_rows = $('#wpfa_dialog_rows', loadZone)
            var attachs_per_load = parseInt(wpfaOptions['attachs_per_load'])
            var offset = parseInt(wpfa_dialog_rows.data('offset'))
            if (!offset) offset = 0
            loadZone.addClass('wpfa-fileupload-processing')
            jQuery.ajax({
                url: loadZone.fileupload('option', 'url'),
                data: {
                    offset: offset,
                    action: 'wpforoattach_load_ajax_function'
                },
                dataType: 'json',
                context: loadZone[0]
            }).done(function (result) {
                $(this).fileupload('option', 'done').call(this, jQuery.Event('done'), { result: result })
                $('#wpfa-loading-spinner').hide()
                wpfa_dialog_rows.data('offset', (offset + attachs_per_load))
                if (parseInt(result.items_count) < (offset + attachs_per_load)) {
                    wpfa_dialog_rows.data('nomore', 1)
                }
            }).always(function () {
                load_files_active = false
                $(this).removeClass('wpfa-fileupload-processing')
            })
        }
    }

    function wpfa_isSourceExternalFile (dataTransfer) {
        if (!dataTransfer) return false
        var DragDataType = dataTransfer.types

        // Source detection for Chrome on Windows.
        if (typeof Array !== 'undefined') {
            if (DragDataType && Array.isArray(DragDataType)) {
                return DragDataType.indexOf('Files') !== -1
            }
        }

        // Source detection for Safari v5.1.7 on Windows.
        if (typeof Clipboard !== 'undefined') {
            if (dataTransfer && dataTransfer.constructor === Clipboard) {
                return dataTransfer.files.length > 0
            }
        }

        // Source detection for Firefox on Windows.
        if (typeof DOMStringList !== 'undefined') {
            if (DragDataType && DragDataType.constructor === DOMStringList) {
                return !!DragDataType.contains('Files')
            }
        }

    }

    function wpfa_append_load_to_current_form (appendto) {
        if (!$('.wpforo-form-load', appendto).length) {
            $('<div class="wpforo-form-load"><i class="fas fa-3x fa-spinner fa-spin"></i></div>').appendTo(appendto)
        }
        $('.wpforo-dropzone').remove()
        wpforo_load_show()
    }
})

function basename (path, suffix) {

    var b = path
    var lastChar = b.charAt(b.length - 1)

    if (lastChar === '/' || lastChar === '\\') {
        b = b.slice(0, -1)
    }

    b = b.replace(/^.*[\/\\]/g, '')

    if (typeof suffix === 'string' && b.substr(b.length - suffix.length) === suffix) {
        b = b.substr(0, b.length - suffix.length)
    }

    return b
}

function pathinfo (path, options) {

    var opt     = '',
        optName = '',
        optTemp = 0,
        tmp_arr = {},
        cnt     = 0,
        i       = 0
    var have_basename  = false,
        have_extension = false,
        have_filename  = false

    // Input defaulting & sanitation
    if (!path) {
        return false
    }
    if (!options) {
        options = 'PATHINFO_ALL'
    }

    // Initialize binary arguments. Both the string & integer (constant) input is
    // allowed
    var OPTS = {
        'PATHINFO_DIRNAME': 1,
        'PATHINFO_BASENAME': 2,
        'PATHINFO_EXTENSION': 4,
        'PATHINFO_FILENAME': 8,
        'PATHINFO_ALL': 0
    }
    // PATHINFO_ALL sums up all previously defined PATHINFOs (could just pre-calculate)
    for (optName in OPTS) {
        OPTS.PATHINFO_ALL = OPTS.PATHINFO_ALL | OPTS[optName]
    }
    if (typeof options !== 'number') { // Allow for a single string or an array of string flags
        options = [].concat(options)
        for (i = 0; i < options.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[options[i]]) {
                optTemp = optTemp | OPTS[options[i]]
            }
        }
        options = optTemp
    }

    // Internal Functions
    var __getExt = function (path) {
        var str = path + ''
        var dotP = str.lastIndexOf('.') + 1
        return !dotP ? false : dotP !== str.length ? str.substr(dotP) : ''
    }

    // Gather path infos
    if (options & OPTS.PATHINFO_DIRNAME) {
        var dirName = path.replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '') // dirname
        tmp_arr.dirname = dirName === path ? '.' : dirName
    }

    if (options & OPTS.PATHINFO_BASENAME) {
        if (false === have_basename) {
            have_basename = this.basename(path)
        }
        tmp_arr.basename = have_basename
    }

    if (options & OPTS.PATHINFO_EXTENSION) {
        if (false === have_basename) {
            have_basename = this.basename(path)
        }
        if (false === have_extension) {
            have_extension = __getExt(have_basename)
        }
        if (false !== have_extension) {
            tmp_arr.extension = have_extension
        }
    }

    if (options & OPTS.PATHINFO_FILENAME) {
        if (false === have_basename) {
            have_basename = this.basename(path)
        }
        if (false === have_extension) {
            have_extension = __getExt(have_basename)
        }
        if (false === have_filename) {
            have_filename = have_basename.slice(0, have_basename.length - (have_extension ? have_extension.length + 1 :
                have_extension === false ? 0 : 1))
        }

        tmp_arr.filename = have_filename
    }

    // If array contains only 1 element: return string
    cnt = 0
    for (opt in tmp_arr) {
        cnt++
    }
    if (cnt === 1) {
        return tmp_arr[opt]
    }

    // Return full-blown array
    return tmp_arr
}

/** chack file types and font-awesome ico **/
function wpforo_get_file_type (filename) {
    var ext = pathinfo(filename, 'PATHINFO_EXTENSION').toLowerCase()

    if (ext === 'pdf') return 'pdf'
    if (ext === 'doc' || ext === 'docx' || ext === 'odm' || ext === 'odt' || ext === 'ott') return 'word'
    if (ext === 'zip' || ext === 'gz' || ext === 'bz' || ext === 'bz2' || ext === 'tar' || ext === 'tgz' || ext === '7z' || ext === 'jar' || ext === 'rar' || ext === 'iso') return 'archive'
    if (ext === 'csv' || ext === 'xlsx' || ext === 'xls' || ext === 'xlsb' || ext === 'xlsm' || ext === 'xlt' || ext === 'xltm' || ext === 'ots' || ext === 'ods' || ext === 'stc' || ext === 'sxc') return 'excel'
    if (ext === 'xml' || ext === 'html' || ext === 'js' || ext === 'php' || ext === 'sql' || ext === 'css' || ext === 'sh' || ext === 'csh' || ext === 'json' || ext === 'tcl' || ext === 'bat' || ext === 'as' || ext === 'cmd') return 'code'
    if (ext === 'ppt' || ext === 'pptx' || ext === 'otp' || ext === 'odp' || ext === 'pot' || ext === 'pps' || ext === 'shw' || ext === 'sti' || ext === 'sxi' || ext === 'thmx') return 'powerpoint'

    if (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif' || ext === 'bmp' || ext === 'tiff' || ext === 'tif' || ext === 'ico') return 'picture'
    if (ext === 'mp4' || ext === 'flv' || ext === 'mov' || ext === 'mkv' || ext === 'vob' || ext === 'mpg' || ext === 'mpeg' || ext === 'mpe' || ext === '3gp' || ext === 'avi' || ext === 'wmv') return 'video'
    if (ext === 'mp3' || ext === 'wma' || ext === 'wav' || ext === 'amr' || ext === 'mp2' || ext === 'aac') return 'audio'
    if (ext === 'txt' || ext === 'asc') return 'alt'

    return 'file'
}

function wpforo_get_file_fa_ico (filename) {
    var filetype = wpforo_get_file_type(filename)
    filetype = filetype !== 'file' ? 'fa-file-' + filetype : 'fa-file'
    return '<i class="far ' + filetype + ' fa-5x wpfa-file-icon"></i>'
}

function wpfa_phrase (phrase_key) {
    phrase_key = phrase_key.toLowerCase()
    if (wpfaPhrases[phrase_key] !== undefined) phrase_key = wpfaPhrases[phrase_key]
    return phrase_key
}

function wpfa_file_info (file) {
    var is_allowed = true
    var notice = ''

    if (file.size > parseInt(wpfaOptions['maximum_file_size'])) {
        is_allowed = false
        notice = wpfa_phrase('Error: File is too big. Allowed size is: %s').replace('%s', wpfaOptions['maximum_file_size_human'])
    } else if (file.size > parseInt(wpfaOptions['server_upload_max_filesize'])) {
        is_allowed = false
        notice = wpfa_phrase('Error: File is too big. server_upload_max_filesize is: %s').replace('%s', wpfaOptions['server_upload_max_filesize_human'])
    } else if (file.size > parseInt(wpfaOptions['server_post_max_size'])) {
        is_allowed = false
        notice = wpfa_phrase('Error: File is too big. server_post_max_size is: %s').replace('%s', wpfaOptions['server_post_max_size_human'])
    }

    var FileTypesRegex = new RegExp('\.(' + wpfaOptions['accepted_file_types'] + ')$', 'i')
    if (!FileTypesRegex.test(file.name)) {
        is_allowed = false
        notice = wpfa_phrase('Error: Filetype not allowed')
    }

    var type = wpforo_get_file_type(file.name)
    var fa_ico = type !== 'file' ? 'fa-file-' + type : 'fa-file'

    return {
        is_allowed: is_allowed,
        notice: notice,
        type: type,
        fa_ico: fa_ico
    }
}
