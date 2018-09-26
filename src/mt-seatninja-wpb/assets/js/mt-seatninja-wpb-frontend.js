(function ($) {
    mtui_wpb_frontend = (function () {
        return {
            init               : function () {
                let currentDate,
                    timeZone
                this.datePicker()
                this.timePicker()
                this.partySizeEvent()
                this.getRestaurantApi()
                this.bookingReservation()
            },
            formatDate         : function (date) {
                let d     = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day   = '' + d.getDate(),
                    year  = d.getFullYear()

                if (month.length < 2) {
                    month = '0' + month
                }
                if (day.length < 2) {
                    day = '0' + day
                }

                return [month, day, year].join('-')
            },
            datePicker         : function () {
                let self = this

                $('.datepicker').datetimepicker({
                    value           : new Date(),
                    timepicker      : false,
                    scrollInput     : false,
                    format          : 'M d Y',
                    onChangeDateTime: function (e, $input) {

                        if ($input.val() !== self.currentDate) {
                            self.currentDate = $input.val()
                            self.getReservationTimes($input.closest('.mt-snj-reservation-form'))
                        }
                    }
                })
            },
            timePicker: function () {
                $('.timepicker').SumoSelect({
                    placeholder: 'Select time',
                    search     : true,
                    searchText : 'Search time...',
                })
            },
            partySizeEvent     : function () {
                let self = this

                $('.party-size').on('change', function () {
                    self.getReservationTimes($(this).closest('.mt-snj-reservation-form'))
                })
            },
            getReservationTimes: function ($el) {
                let self = this
                let restaurantId = $el.find('.restaurant-id').val()
                let partySize = $el.find('.party-size').val()
                let date = $el.find('.datepicker').val()
                let $timepicker = $el.find('.timepicker')

                if (!restaurantId || partySize < 1 || partySize > 15) {
                    $timepicker.datetimepicker('destroy')
                    return false
                }

                $timepicker.closest('.mt-snj-form-group').addClass('mt-snj-loading')

                if (partySize > 0 && date) {
                    $.ajax({
                        type   : 'GET',
                        url    : mtSeatNinja.ajaxUrl,
                        timeout: 30000,
                        data   : {
                            action      : 'get_reservation_times',
                            restaurantId: restaurantId,
                            partySize   : partySize,
                            date        : self.formatDate(date),
                            nonce       : mtSeatNinja.ajaxNonce
                        },
                        success: (res) => {

                            $timepicker.closest('.mt-snj-form-group').removeClass('mt-snj-loading')

                            for (let i = 0; i < res.length; i++) {
                                let times = res[i].times

                                for (let j = 0; j < times.length; j++) {
                                    $timepicker[0].sumo.add(times[j].value, times[j].text)
                                }
                            }
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })
                }
            },
            getRestaurantApi   : function () {

                let self          = this,
                    $restaurantID = $('.restaurant-id')

                $restaurantID.on('change', function () {
                    self.getReservationTimes($(this).closest('.mt-snj-reservation-form'))
                })
            },
            bookingReservation : function () {

                let $_form = $('.mt-snj-reservation-form'),
                    self = this

                $_form.on('submit', function (e) {

                    e.preventDefault()

                    let $form = $(this);

                    $form.addClass('mt-snj-loading')

                    let restaurantName = $form.find('.restaurant-id option:selected').text()
                    let timeText       = $form.find('.datepicker').val() + ' ' + $form.find('.timepicker option:selected').text()
                    let notes          = $form.find('.notes').val()

                    let data = {
                        action          : 'booking_reservation',
                        restaurantId    : $form.find('.restaurant-id').val(),
                        restaurantName  : restaurantName,
                        time            : $form.find('.timepicker').val(),
                        timeText        : timeText,
                        partySize       : $form.find('.party-size').val(),
                        firstName       : $form.find('.first-name').val(),
                        lastName        : $form.find('.last-name').val(),
                        phoneNumber     : $form.find('.phone').val(),
                        email           : $form.find('.email').val(),
                        notes           : notes,
                        nonce           : mtSeatNinja.ajaxNonce,
                    }

                    if (parseInt(data.partySize) > 14) {
                        $form.find('.mt-snj__message')
                             .html('If you would like to make a reservation for 15 or more, please contact the restaurant directly. Thank you!')
                        return false
                    }

                    if (!data.time) {
                        data.time = $form.find('.timepicker').val()
                    }

                    $.ajax({
                        method : 'POST',
                        url    : mtSeatNinja.ajaxUrl,
                        timeout: 30000,
                        data   : data,
                        success: (res) => {
                            $form.removeClass('mt-snj-loading')
                            $form.find('.mt-snj__message').html('')
                            $form.find('.mt-snj-form__error').html('')

                            if (res.data) {

                                if ($form.closest('#reservation-modal').length) {
                                    $form.addClass('mt-snj-hidden')
                                }

                                let message = '<p>Thank you! We will call back soon for you to confirm</p>'
                                message += '<p>Here is the reservation information:</p>'
                                message += 'Restaurant: <strong>' + restaurantName + '</strong><br/>'
                                message += 'Number of people: <strong>' + data.partySize + '</strong><br/>'
                                message += 'Time: <strong>' + timeText + '</strong><br/>'
                                message += 'Name: <strong>' + data.firstName + ' ' + data.lastName + '</strong><br/>'
                                message += 'Phone Number: <strong>' + data.phoneNumber + '</strong><br/>'
                                message += 'Email: <strong>' + data.email + '</strong><br/>'
                                message += 'Note: <strong>' + notes + '</strong>'

                                $form.find('.mt-snj__message').html(message)

                                $.magnificPopup.open({
                                    items: {
                                        src : '.mt-snj__message',
                                        type: 'inline'
                                    }
                                })
                                $form[0].reset()
                                self.datePicker()
                                $form.find('.timepicker option').remove()
                                $form.find('.timepicker')[0].sumo.unload();
                                self.timePicker()
                            } else if (res.error) {

                                let html = ''
                                html = '<p>' + res.error.message + '</p>'
                                html += '<ul class="mt-snj__errors">'

                                if (res.error.messages !== null) {
                                    res.error.messages.forEach((mes) => {
                                        html += '<li>' + mes + '</li>'
                                    })
                                }

                                html += ''
                                html += '</ul>'

                                $('.mt-snj-form__error').html(html)
                            }
                        },
                        error  : (err) => {
                            console.log(err)
                        }
                    })
                })
            }
        }
    }())
})(jQuery)

jQuery(document).ready(function () {
    mtui_wpb_frontend.init()
})