/*
 * jQuery File Upload User Interface Plugin
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* jshint nomen:false */
/* global define, require, window */

;(function (factory) {
    'use strict'
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            'blueimp-tmpl',
            '../../wpf-third-party/file-uploader/js/jquery.fileupload-image',
            '../../wpf-third-party/file-uploader/js/jquery.fileupload-audio',
            '../../wpf-third-party/file-uploader/js/jquery.fileupload-video',
            '../../wpf-third-party/file-uploader/js/jquery.fileupload-validate'
        ], factory)
    } else if (typeof exports === 'object') {
        // Node/CommonJS:
        factory(
            require('jquery'),
            require('blueimp-tmpl'),
            require('../../wpf-third-party/file-uploader/js/jquery.fileupload-image'),
            require('../../wpf-third-party/file-uploader/js/jquery.fileupload-audio'),
            require('../../wpf-third-party/file-uploader/js/jquery.fileupload-video'),
            require('../../wpf-third-party/file-uploader/js/jquery.fileupload-validate')
        )
    } else {
        // Browser globals:
        factory(window.jQuery)
    }
}(function ($) {
    'use strict'

    $.blueimp.fileupload.prototype._specialOptions.push(
        'filesContainer',
        'uploadTemplateId',
        'downloadTemplateId'
    )

    // The UI version extends the file upload widget
    // and adds complete user interface interaction:
    $.widget('blueimp.fileupload', $.blueimp.fileupload, {

        options: {
            // By default, files added to the widget are uploaded as soon
            // as the user clicks on the start buttons. To enable automatic
            // uploads, set the following option to true:
            autoUpload: false,
            // The ID of the upload template:
            uploadTemplateId: 'wpfa-template-upload',
            // The ID of the download template:
            downloadTemplateId: 'wpfa-template-download',
            // The container for the list of files. If undefined, it is set to
            // an element with class "files" inside of the widget element:
            filesContainer: undefined,
            // By default, files are appended to the files container.
            // Set the following option to true, to prepend files instead:
            prependFiles: true,
            // The expected data type of the upload response, sets the dataType
            // option of the $.ajax upload requests:
            dataType: 'json',

            // Error and info messages:
            messages: {
                unknownError: 'Unknown error'
            },

            processdone: function (e, data) {
                if (!$('#wpfa_dialog_wrap').is(':visible')) return false
            },

            // Function returning the current number of files,
            // used by the maxNumberOfFiles validation:
            getNumberOfFiles: function () {
                return this.filesContainer.children().not('.processing').length
            },

            // Callback to retrieve the list of files from the server response:
            getFilesFromResponse: function (data) {
                if (data.result && Array.isArray(data.result.files)) {
                    return data.result.files
                }
                return []
            },

            // The add callback is invoked as soon as files are added to the fileupload
            // widget (via file input selection, drag & drop or add API call).
            // See the basic file upload widget for more information:
            add: function (e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var $this   = $(this),
                    that    = $this.data('blueimp-fileupload') ||
                              $this.data('wpfa_fileupload'),
                    options = that.options
                data.context = that._renderUpload(data.files).data('data', data).addClass('processing')
                options.filesContainer[
                    options.prependFiles ? 'prepend' : 'append'
                    ](data.context)
                that._forceReflow(data.context)
                that._transition(data.context)
                data.process(function () {
                    return $this.fileupload('process', data)
                }).always(function () {
                    data.context.each(function (index) {
                        $(this).find('.wpfa-size').text(
                            that._formatFileSize(data.files[index].size)
                        )
                    }).removeClass('processing')
                    that._renderPreviews(data)
                }).done(function () {
                    data.context.find('.wpfa-start').prop('disabled', false)
                    if ((that._trigger('added', e, data) !== false) &&
                        (options.autoUpload || data.autoUpload) &&
                        data.autoUpload !== false) {
                        data.submit()
                    }
                }).fail(function () {
                    if (data.files.error) {
                        data.context.each(function (index) {
                            var error = data.files[index].error
                            if (error) {
                                $(this).find('.wpfa-error').text(error)
                            }
                        })
                    }
                })
            },
            // Callback for the start of each file upload request:
            send: function (e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data('blueimp-fileupload') ||
                           $(this).data('wpfa_fileupload')
                if (data.context && data.dataType &&
                    data.dataType.substr(0, 6) === 'iframe') {
                    // Iframe Transport does not support progress events.
                    // In lack of an indeterminate progress bar, we set
                    // the progress to 100%, showing the full animated bar:
                    data.context.find('.wpfa-progress').addClass(
                        !$.support.transition && 'progress-animated'
                    ).attr('aria-valuenow', 100).children().first().css(
                        'width',
                        '100%'
                    )
                }
                return that._trigger('sent', e, data)
            },
            // Callback for successful uploads:
            done: function (e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that                 = $(this).data('blueimp-fileupload') ||
                                           $(this).data('wpfa_fileupload'),
                    getFilesFromResponse = data.getFilesFromResponse ||
                                           that.options.getFilesFromResponse,
                    files                = getFilesFromResponse(data),
                    template,
                    deferred
                if (data.context) {
                    data.context.each(function (index) {
                        var file = files[index] ||
                            { error: 'Empty file upload result' }
                        deferred = that._addFinishedDeferreds()
                        that._transition($(this)).done(
                            function () {
                                var node = $(this)
                                template = that._renderDownload([file]).replaceAll(node)
                                that._forceReflow(template)
                                that._transition(template).done(
                                    function () {
                                        data.context = $(this)
                                        that._trigger('completed', e, data)
                                        that._trigger('finished', e, data)
                                        deferred.resolve()
                                    }
                                )
                            }
                        )
                    })
                } else {
                    template = that._renderDownload(files)[
                        'appendTo'
                        ](that.options.filesContainer)
                    that._forceReflow(template)
                    deferred = that._addFinishedDeferreds()
                    that._transition(template).done(
                        function () {
                            data.context = $(this)
                            that._trigger('completed', e, data)
                            that._trigger('finished', e, data)
                            deferred.resolve()
                        }
                    )
                }
            },
            // Callback for failed (abort or error) uploads:
            fail: function (e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data('blueimp-fileupload') ||
                           $(this).data('wpfa_fileupload'),
                    template,
                    deferred
                if (data.context) {
                    data.context.each(function (index) {
                        if (data.errorThrown !== 'abort') {
                            var file = data.files[index]
                            file.error = file.error || data.errorThrown ||
                                         data.i18n('unknownError')
                            deferred = that._addFinishedDeferreds()
                            that._transition($(this)).done(
                                function () {
                                    var node = $(this)
                                    template = that._renderDownload([file]).replaceAll(node)
                                    that._forceReflow(template)
                                    that._transition(template).done(
                                        function () {
                                            data.context = $(this)
                                            that._trigger('failed', e, data)
                                            that._trigger('finished', e, data)
                                            deferred.resolve()
                                        }
                                    )
                                }
                            )
                        } else {
                            deferred = that._addFinishedDeferreds()
                            that._transition($(this)).done(
                                function () {
                                    $(this).remove()
                                    that._trigger('failed', e, data)
                                    that._trigger('finished', e, data)
                                    deferred.resolve()
                                }
                            )
                        }
                    })
                } else if (data.errorThrown !== 'abort') {
                    data.context = that._renderUpload(data.files)[
                        that.options.prependFiles ? 'prependTo' : 'appendTo'
                        ](that.options.filesContainer).data('data', data)
                    that._forceReflow(data.context)
                    deferred = that._addFinishedDeferreds()
                    that._transition(data.context).done(
                        function () {
                            data.context = $(this)
                            that._trigger('failed', e, data)
                            that._trigger('finished', e, data)
                            deferred.resolve()
                        }
                    )
                } else {
                    that._trigger('failed', e, data)
                    that._trigger('finished', e, data)
                    that._addFinishedDeferreds().resolve()
                }
            },
            // Callback for upload progress events:
            progress: function (e, data) {
                if (!$('#wpfa_dialog_wrap').is(':visible')) return false
                if (data.context) {
                    data.context.find('.wpfa-progress').progressbar(
                        'option',
                        'value',
                        parseInt(data.loaded / data.total * 100, 10)
                    )
                }
            },
            // Callback for global upload progress events:
            progressall: function (e, data) {
                if (!$('#wpfa_dialog_wrap').is(':visible')) return false
                var $this = $(this)
                $this.find('.wpfa-fileupload-progress').find('.wpfa-progress').progressbar(
                    'option',
                    'value',
                    parseInt(data.loaded / data.total * 100, 10)
                ).end().find('.wpfa-progress-extended').each(function () {
                    $(this).html(
                        ($this.data('blueimp-fileupload') ||
                         $this.data('wpfa_fileupload'))._renderExtendedProgress(data)
                    )
                })
            },
            // Callback for uploads start, equivalent to the global ajaxStart event:
            start: function (e) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data('blueimp-fileupload') ||
                           $(this).data('wpfa_fileupload')
                that._resetFinishedDeferreds()
                that._transition($(this).find('.wpfa-fileupload-progress')).done(
                    function () {
                        that._trigger('started', e)
                    }
                )
            },
            // Callback for uploads stop, equivalent to the global ajaxStop event:
            stop: function (e) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that     = $(this).data('blueimp-fileupload') ||
                               $(this).data('wpfa_fileupload'),
                    deferred = that._addFinishedDeferreds()
                $.when.apply($, that._getFinishedDeferreds()).done(function () {
                    that._trigger('stopped', e)
                })
                that._transition($(this).find('.wpfa-fileupload-progress')).done(
                    function () {
                        $(this).find('.wpfa-progress').attr('aria-valuenow', '0').children().first().css('width', '0%')
                        $(this).find('.wpfa-progress-extended').html('&nbsp;')
                        deferred.resolve()
                    }
                )
            },
            processstart: function (e) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                $(this).addClass('wpfa-fileupload-processing')
            },
            processstop: function (e) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                $(this).removeClass('wpfa-fileupload-processing')
            },
            // Callback for file deletion:
            destroy: function (e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that       = $(this).data('blueimp-fileupload') ||
                                 $(this).data('wpfa_fileupload'),
                    removeNode = function () {
                        that._transition(data.context).done(
                            function () {
                                $(this).remove()
                                that._trigger('destroyed', e, data)
                            }
                        )
                    }
                if (data.url) {
                    data.dataType = data.dataType || that.options.dataType
                    $.ajax(data).done(removeNode).fail(function () {
                        that._trigger('destroyfailed', e, data)
                    })
                } else {
                    removeNode()
                }
            }
        },

        _resetFinishedDeferreds: function () {
            this._finishedUploads = []
        },

        _addFinishedDeferreds: function (deferred) {
            if (!deferred) {
                deferred = $.Deferred()
            }
            this._finishedUploads.push(deferred)
            return deferred
        },

        _getFinishedDeferreds: function () {
            return this._finishedUploads
        },

        // Link handler, that allows to download files
        // by drag & drop of the links to the desktop:
        _enableDragToDesktop: function () {
            var link = $(this),
                url  = link.prop('href'),
                name = link.prop('download'),
                type = 'application/octet-stream'
            link.on('dragstart', function (e) {
                try {
                    e.originalEvent.dataTransfer.setData(
                        'DownloadURL',
                        [type, name, url].join(':')
                    )
                } catch (ignore) {}
            })
        },

        _formatFileSize: function (bytes) {
            if (typeof bytes !== 'number') {
                return ''
            }
            if (bytes >= 1000000000) {
                return (bytes / 1000000000).toFixed(2) + ' GB'
            }
            if (bytes >= 1000000) {
                return (bytes / 1000000).toFixed(2) + ' MB'
            }
            return (bytes / 1000).toFixed(2) + ' KB'
        },

        _formatBitrate: function (bits) {
            if (typeof bits !== 'number') {
                return ''
            }
            if (bits >= 1000000000) {
                return (bits / 1000000000).toFixed(2) + ' Gbit/s'
            }
            if (bits >= 1000000) {
                return (bits / 1000000).toFixed(2) + ' Mbit/s'
            }
            if (bits >= 1000) {
                return (bits / 1000).toFixed(2) + ' kbit/s'
            }
            return bits.toFixed(2) + ' bit/s'
        },

        _formatTime: function (seconds) {
            var date = new Date(seconds * 1000),
                days = Math.floor(seconds / 86400)
            days = days ? days + 'd ' : ''
            return days +
                   ('0' + date.getUTCHours()).slice(-2) + ':' +
                   ('0' + date.getUTCMinutes()).slice(-2) + ':' +
                   ('0' + date.getUTCSeconds()).slice(-2)
        },

        _formatPercentage: function (floatValue) {
            return (floatValue * 100).toFixed(2) + ' %'
        },

        _renderExtendedProgress: function (data) {
            return this._formatBitrate(data.bitrate) + ' | ' +
                   this._formatTime(
                       (data.total - data.loaded) * 8 / data.bitrate
                   ) + ' | ' +
                   this._formatPercentage(
                       data.loaded / data.total
                   ) + ' | ' +
                   this._formatFileSize(data.loaded) + ' / ' +
                   this._formatFileSize(data.total)
        },

        _renderTemplate: function (func, files) {
            if (!func) {
                return $()
            }
            var result = func({
                files: files,
                formatFileSize: this._formatFileSize,
                options: this.options
            })
            if (result instanceof $) {
                return result
            }
            return $(this.options.templatesContainer).html(result).children()
        },

        _renderPreviews: function (data) {
            data.context.find('.wpfa-preview').each(function (index, elm) {
                $(elm).append(data.files[index].preview)
            })
        },

        _renderUpload: function (files) {
            var node = this._renderTemplate(this.options.uploadTemplate, files)
            node.find('.wpfa-progress').empty().progressbar()
            if (node.hasClass('wpforo-fade')) {
                node.hide()
            }
            return node
        },

        _renderDownload: function (files) {
            var node = this._renderTemplate(this.options.downloadTemplate, files).find('a[download]').each(this._enableDragToDesktop).end()
            if (node.hasClass('wpforo-fade')) {
                node.hide()
            }
            return node
        },

        _startHandler: function (e) {
            e.preventDefault()
            var button   = $(e.currentTarget),
                template = button.closest('.wpfa-template-upload'),
                data     = template.data('data')
            button.prop('disabled', true)
            if (data && data.submit) {
                data.submit()
            }
        },

        _cancelHandler: function (e) {
            e.preventDefault()
            var template = $(e.currentTarget).closest('.wpfa-template-upload,.wpfa-template-download'),
                data     = template.data('data') || {}
            data.context = data.context || template
            if (data.abort) {
                data.abort()
            } else {
                data.errorThrown = 'abort'
                this._trigger('fail', e, data)
            }
        },

        _deleteHandler: function (e) {
            e.preventDefault()
            if (!confirm(wpforo_phrase('Please note! This file will also be removed from all posts where it has been attached. This action cannot be undo. Are you sure you want to delete this file?'))) return
            var button = $(e.currentTarget)
            this._trigger('destroy', e, $.extend({
                context: button.closest('.wpfa-template-download'),
                type: 'DELETE'
            }, button.data()))
        },

        _forceReflow: function (node) {
            return $.support.transition && node.length &&
                   node[0].offsetWidth
        },

        _transition: function (node) {
            var deferred = $.Deferred()
            if (node.hasClass('wpforo-fade')) {
                node.fadeToggle(
                    this.options.transitionDuration,
                    this.options.transitionEasing,
                    function () {
                        deferred.resolveWith(node)
                    }
                )
            } else {
                deferred.resolveWith(node)
            }
            return deferred
        },

        _initButtonBarEventHandlers: function () {
            var fileUploadButtonBar = this.element.find('.wpfa-fileupload-buttonbar'),
                filesList           = this.options.filesContainer
            this._on(fileUploadButtonBar.find('.wpfa-start'), {
                click: function (e) {
                    e.preventDefault()
                    filesList.find('.wpfa-start').trigger('click')
                }
            })
            this._on(fileUploadButtonBar.find('.wpfa-cancel'), {
                click: function (e) {
                    e.preventDefault()
                    filesList.find('.wpfa-cancel').trigger('click')
                }
            })
            this._on(fileUploadButtonBar.find('.wpfa-delete'), {
                click: function (e) {
                    e.preventDefault()
                    filesList.find('.wpfa-toggle:checked').closest('.wpfa-template-download').find('.wpfa-delete').trigger('click')
                    fileUploadButtonBar.find('.wpfa-toggle').prop('checked', false)
                }
            })
            this._on(fileUploadButtonBar.find('.wpfa-toggle'), {
                change: function (e) {
                    filesList.find('.wpfa-toggle').prop(
                        'checked',
                        $(e.currentTarget).is(':checked')
                    )
                }
            })
        },

        _destroyButtonBarEventHandlers: function () {
            this._off(
                this.element.find('.wpfa-fileupload-buttonbar').find('.wpfa-start, .wpfa-cancel, .wpfa-delete'),
                'click'
            )
            this._off(
                this.element.find('.wpfa-fileupload-buttonbar .wpfa-toggle'),
                'change.'
            )
        },

        _initEventHandlers: function () {
            this._super()
            this._on(this.options.filesContainer, {
                'click .wpfa-start': this._startHandler,
                'click .wpfa-cancel': this._cancelHandler,
                'click .wpfa-delete': this._deleteHandler
            })
            this._initButtonBarEventHandlers()
        },

        _destroyEventHandlers: function () {
            this._destroyButtonBarEventHandlers()
            this._off(this.options.filesContainer, 'click')
            this._super()
        },

        _enableFileInputButton: function () {
            this.element.find('.fileinput-button input').prop('disabled', false).parent().removeClass('disabled')
        },

        _disableFileInputButton: function () {
            this.element.find('.fileinput-button input').prop('disabled', true).parent().addClass('disabled')
        },

        _initTemplates: function () {
            var options = this.options
            options.templatesContainer = this.document[0].createElement(
                options.filesContainer.prop('nodeName')
            )
            options.uploadTemplate = this.wpfa_template_upload
            options.downloadTemplate = this.wpfa_template_download
        },

        _initFilesContainer: function () {
            var options = this.options
            if (options.filesContainer === undefined) {
                options.filesContainer = this.element.find('#wpfa_dialog_rows')
            } else if (!(options.filesContainer instanceof $)) {
                options.filesContainer = $(options.filesContainer)
            }
        },

        _initSpecialOptions: function () {
            this._super()
            this._initFilesContainer()
            this._initTemplates()
        },

        _create: function () {
            this._super()
            this._resetFinishedDeferreds()
            if (!$.support.fileInput) {
                this._disableFileInputButton()
            }
            this.element.find('.wpfa-progress').progressbar()
        },

        _destroy: function () {
            this.element.find('.wpfa-progress').progressbar('destroy')
            this._super()
        },

        enable: function () {
            var wasDisabled = false
            if (this.options.disabled) {
                wasDisabled = true
            }
            this._super()
            if (wasDisabled) {
                this.element.find('input, button').prop('disabled', false)
                this._enableFileInputButton()
            }
        },

        disable: function () {
            if (!this.options.disabled) {
                this.element.find('input, button').prop('disabled', true)
                this._disableFileInputButton()
            }
            this._super()
        },

        wpfa_template_upload: function (o) {
            var r = ''
            o.files.forEach(function (file, i) {
                var finfo = wpfa_file_info(file)
                if (finfo.is_allowed) {
                    r += '<div class="wpfa-dialog-item-row wpfa-template-upload">'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-id wpfa-show-for-large">'
                    r += '<i class="id"><strong>__</strong></i>'
                    r += '</div>'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-preview">'
                    if (finfo.type === 'picture' || finfo.type === 'video' || finfo.type === 'audio') {
                        r += '<span class="wpfa-preview wpfa-show-for-large"></span>'
                        r += '<i class="wpfa-hide-for-large far ' + finfo.fa_ico + ' fa-3x"></i>'
                    } else {
                        r += '<i class="far ' + finfo.fa_ico + ' fa-3x"></i>'
                    }
                    r += '</div>'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-filename wpfa-hide-for-small">'
                    r += '<span class="wpfa-name" style="overflow-x:hidden;">' + file.name + '</span>'
                    r += '<strong class="wpfa-error"></strong>'
                    r += '</div>'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-filesize">'
                    r += '<span class="wpfa-size">( ' + o.formatFileSize(file.size) + ' )</span>'
                    r += '<div class="wpfa-progress"></div>'
                    r += '</div>'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-action-buttons">'
                    if (!i && !o.options.autoUpload) {
                        r += '<button type="button" class="wpfa-start-upload wpfa-start wpfa-button wpfa-button-start" disabled>'
                        r += '<span class="wpfa-button-icon"><i class="fas fa-upload"></i></span>'
                        r += '<span class="wpfa-button-text wpfa-show-for-large">' + wpfa_phrase('Start') + '</span>'
                        r += '</button>'
                    }
                    if (!i) {
                        r += '<button type="button" class="wpfa-cancel wpfa-button wpfa-button-cancel">'
                        r += '<span class="wpfa-button-icon"><i class="fas fa-ban"></i></span>'
                        r += '<span class="wpfa-button-text wpfa-show-for-large">' + wpfa_phrase('Cancel') + '</span>'
                        r += '</button>'
                    }
                    r += '</div>'
                    r += '</div>'
                } else {
                    r += '<div class="wpfa-dialog-item-row wpfa-file-not-allow" style="background-color: bisque;">'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-id wpfa-show-for-large">'
                    r += '<i class="id"><strong>__</strong></i>'
                    r += '</div>'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-preview">'
                    r += '<i class="far ' + finfo.fa_ico + ' fa-3x"></i>'
                    r += '</div>'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-filename wpfa-hide-for-small wpfa-show-for-large">'
                    r += '<span class="name" style="overflow-x:hidden;" title="' + file.name + ' ( ' + o.formatFileSize(file.size) + ' )">' + file.name + '</span>'
                    r += '( <span class="size">' + o.formatFileSize(file.size) + '</span> )'
                    r += '</div>'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-error">'
                    r += '<span class="wpfa-error"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i></span>'
                    r += '<strong class="wpfa-error">' + finfo.notice + '</strong>'
                    r += '</div>'
                    r += '<div class="wpfa-dialog-item-col wpfa-item-col-action-buttons">'
                    r += '<strong class="wpfa-error wpfa-close-error" style="cursor:pointer;">'
                    r += '<i class="fas fa-times fa-2x" aria-hidden="true" title="' + wpfa_phrase('close') + '"></i>'
                    r += '</strong>'
                    r += '</div>'
                    r += '</div>'
                }
            })

            return r
        },

        wpfa_template_download: function (o) {
            var r = ''
            o.files.forEach(function (file, i) {
                if (!file.id) file.id = ''
                r += '<div class="wpfa-dialog-item-row wpfa-template-download"' + (file.error ? ' style="background-color: bisque;" ' : '') + ' data-attachid="' + file.id + '">'
                r += '<label for="' + file.id + '"></label>'
                r += '<div class="wpfa-dialog-item-col wpfa-item-col-id wpfa-show-for-large">'
                r += '<i id="attach' + file.id + '" class="id">'
                r += '<strong><label for="' + file.id + '">' + file.id + '</label></strong>'
                r += '</i>'
                r += '</div>'
                r += '<div class="wpfa-dialog-item-col wpfa-item-col-preview">'
                r += '<span class="wpfa-preview">'
                if (!file.error) {
                    if (file.thumbnailUrl) {
                        r += '<a href="' + file.url + '" title="' + file.filename + '" download="' + file.filename + '" data-gallery>'
                        r += '<img src="' + file.thumbnailUrl + '" alt="' + file.filename + '">'
                        r += '</a>'
                    } else if (file.wpf_type === 'video') {
                        r += '<video class="wpfa-show-for-large" src="' + file.url + '" controls>'
                        r += '<i class="far ' + file.wpf_file_fa_ico + ' fa-3x"></i>'
                        r += '</video>'
                        r += '<a class="wpfa-hide-for-large" href="' + file.url + '" title="' + file.filename + '" download="' + file.filename + '"' + (file.thumbnailUrl ? ' data-gallery' : '') + '>'
                        r += '<i class="far ' + file.wpf_file_fa_ico + ' fa-3x"></i>'
                        r += '</a>'
                    } else if (file.wpf_type === 'audio') {
                        r += '<audio class="wpfa-show-for-large" src="' + file.url + '" controls>'
                        r += '<i class="far ' + file.wpf_file_fa_ico + ' fa-3x"></i>'
                        r += '</audio>'
                        r += '<a class="wpfa-hide-for-large" href="' + file.url + '" title="' + file.filename + '" download="' + file.filename + '"' + (file.thumbnailUrl ? ' data-gallery' : '') + '>'
                        r += '<i class="far ' + file.wpf_file_fa_ico + ' fa-3x"></i>'
                        r += '</a>'
                    } else {
                        r += '<a href="' + file.url + '" title="' + file.filename + '" download="' + file.filename + '"' + (file.thumbnailUrl ? ' data-gallery' : '') + '>'
                        r += '<i class="far ' + file.wpf_file_fa_ico + ' fa-3x"></i>'
                        r += '</a>'
                    }
                } else {
                    r += '<span class="wpfa-error"><i class="fas fa-exclamation-triangle fa-3x" aria-hidden="true"></i></span>'
                }
                r += '</span>'
                r += '</div>'
                r += '<div class="wpfa-dialog-item-col wpfa-item-col-attach">' +
                     '<button type="button" class="wpfa-attach wpfa-button wpfa-button-attach">' +
                     '<span class="wpfa-button-icon"><i class="fas fa-arrow-circle-right"></i></span>' +
                     '<span class="wpfa-button-text wpfa-show-for-large">' + wpfa_phrase('Add To Post') + '</span>' +
                     '</button>' +
                     '</div>'
                r += '<div class="wpfa-dialog-item-col wpfa-item-col-filename wpfa-hide-for-small">'
                if (!file.error) {
                    r += '<a href="' + file.url + '" title="' + file.filename + '" download="' + file.filename + '"' + (file.thumbnailUrl ? ' data-gallery' : '') + '>'
                    r += file.filename
                    r += '</a>'
                } else {
                    r += '<span class="wpfa-error">Error</span> ' + file.error
                }
                r += '</div>'
                r += '<div class="wpfa-dialog-item-col wpfa-item-col-filesize wpfa-show-for-large">'
                if (!file.error) r += '<span class="size"><label for="' + file.id + '">' + o.formatFileSize(file.size) + '</label></span>'
                r += '</div>'
                r += '<div class="wpfa-dialog-item-col wpfa-item-col-action-buttons">'
                if (!file.error && parseInt(wpfaOptions['disable_delete']) !== 1) {
                    r += '<button type="button" class="wpfa-delete wpfa-button wpfa-button-delete" ' +
                         'data-type="' + file.deleteType + '" data-url="' + file.deleteUrl + '&action=wpforoattach_load_ajax_function"' +
                         (file.deleteWithCredentials ? ' data-xhr-fields=\'{"withCredentials":true}\'' : '') + '>'
                    r += '<span class="wpfa-button-icon"><i class="fas fa-trash-alt"></i></span>'
                    r += '<span class="wpfa-button-text wpfa-show-for-large">' + wpfa_phrase('Delete') + '</span>'
                    r += '</button>'
                }
                r += '</div>'
                r += '<div class="wpfa-dialog-item-col wpfa-item-col-checkbox">'
                if (!file.error) {
                    r += '<input id="' + file.id + '" type="checkbox" name="delete" value="1" class="wpfa-toggle"' + (file.wpf_is_new ? ' checked' : '') + '>'
                }
                r += '</div>'
                r += '<div class="wpfa-front-view">' + file.front_view + '</div>'
                r += '</div>'
            })

            return r
        }

    })

}))
