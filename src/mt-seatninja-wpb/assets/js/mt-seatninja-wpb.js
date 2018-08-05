(function ($) {
    mtui_wpb = (function () {
        return {
            init      : function () {
                this.saveForm()
                this.clearCache()
            },
            saveForm  : function () {
                var $form        = $('#mt-snj-settings'),
                    $button      = $form.find('#submit-btn'),
                    ajax_loading = false

                $form.on('submit', function (e) {

                    e.preventDefault()

                    if (ajax_loading) {
                        return false
                    }

                    ajax_loading = true
                    $form.addClass('loading')
                    $button.addClass('loading').attr('disabled', true)

                    var keys = []

                    $form.find('input[type="text"]').each(function () {
                        var $this = $(this)
                        keys.push({
                            name : $this.attr('id'),
                            value: $this.val()
                        })
                    })

                    $.ajax({
                        type   : 'POST',
                        url    : mtSeatNinja.ajaxUrl,
                        timeout: 10000,
                        data   : {
                            action: 'mt_snj_save_settings',
                            nonce : mtSeatNinja.ajaxNonce
                        },
                        success: (res) => {

                            ajax_loading = false
                            $form.removeClass('loading')
                            $button.removeClass('loading').attr('disabled', false)

                            if (res.success) {
                                $.growl.notice({
                                    title  : '',
                                    message: res.data
                                })
                            } else {
                                $.growl.error({
                                    title  : '',
                                    message: res.data
                                })
                            }
                        },
                        error  : (err) => {
                            console.log(err)
                            $.growl.error({
                                title  : '',
                                message: err
                            })
                        }
                    })
                })
            },
            clearCache: function () {
                var $form        = $('#mt-snj-settings'),
                    $button      = $('#clear-cache'),
                    ajax_loading = false

                $button.on('click', function (e) {
                    e.preventDefault()

                    if (ajax_loading) {
                        return false
                    }

                    ajax_loading = true
                    $form.addClass('loading')
                    $button.addClass('loading').attr('disabled', true)

                    $.ajax({
                        type   : 'POST',
                        url    : mtSeatNinja.ajaxUrl,
                        timeout: 10000,
                        data   : {
                            action : 'mt_snj_clear_cache',
                            nonce  : mtSeatNinja.ajaxNonce
                        },
                        success: (res) => {
                            ajax_loading = false
                            $form.removeClass('loading')
                            $button.removeClass('loading').attr('disabled', false)

                            if (res.success) {
                                $.growl.notice({
                                    title  : '',
                                    message: res.data
                                })
                            } else {
                                $.growl.error({
                                    title  : '',
                                    message: res.data
                                })
                            }
                        },
                        error  : (err) => {
                            console.log(err)
                            $.growl.error({
                                title  : '',
                                message: err
                            })
                        }
                    })
                })
            }
        }
    }())
})(jQuery)

jQuery(document).ready(function () {
    mtui_wpb.init()
})