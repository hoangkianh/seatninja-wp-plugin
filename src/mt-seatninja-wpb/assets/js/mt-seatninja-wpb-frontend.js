(function ($) {
    mtui_wpb_frontend = (function () {
        return {
            init                       : function () {
                var currentDate,
                    map,
                    marker
                this.datePicker()
                this.timePicker()
                this.partySizeSelectBox()
                this.getRestaurantApi()
                this.bookingReservation()

                if (mtSeatNinja.gmapsApiKey && $('#mt-snj-map').length) {
                    this.gmap()
                }
            },
            formatDate                 : function (date) {
                var d     = new Date(date),
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
            datePicker                 : function () {
                var self = this

                $('#datepicker').datetimepicker({
                    value           : new Date(),
                    timepicker      : false,
                    scrollInput     : false,
                    format          : 'M d Y',
                    onChangeDateTime: function (e, $input) {

                        if ($input.val() !== self.currentDate && $input.closest('.mt-seatninja').length) {
                            self.currentDate = $input.val()
                            self.getReservationTimes()
                        }

                        if ($input.closest('.mt-seatninja-form').length) {
                            let date = $input.val()
                            let time = $('#timepicker').val()
                            let newDate = new Date(date + ' ' + time)
                            $('.mt-seatninja-form').find('#time').val(newDate.toISOString())
                        }
                    }
                })
            },
            timePicker                 : function () {
                var self = this

                $('#timepicker').datetimepicker({
                    datepicker: false,
                    step      : 15,
                    format    : 'h:i A',
                    onChangeDateTime: function (e, $input) {

                        if ($input.closest('.mt-seatninja-form').length) {
                            let time = $input.val()
                            let date = $('#datepicker').val()
                            let newDate = new Date(date + ' ' + time)
                            $('.mt-seatninja-form').find('#time').val(newDate.toISOString())
                        }
                    }
                })
            },
            partySizeSelectBox         : function () {
                var self = this

                $('#party-size').on('change', function () {
                    if ($(this).closest('.mt-seatninja').length) {
                        self.getReservationTimes()
                    }
                })
            },
            getReservationTimes        : function () {
                let self = this
                let restaurantId = $('#restaurants-select').val()
                let partySize = $('#party-size').val()
                let date = $('#datepicker').val()

                $('.mt-snj-times').addClass('mt-snj-loading')

                if (isNaN(restaurantId)) {
                    return false
                }

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

                            $('.mt-snj-times').removeClass('mt-snj-loading')

                            let html = ''

                            for (let i = 0; i < res.length; i++) {
                                html += '<p class="mt-snj-section-name">' + res[i].section_name + '</p>'

                                let times = res[i].times

                                html += '<ul class="row mt-snj-times-list">'

                                for (let j = 0; j < times.length; j++) {
                                    html += '<li class="col-xs-6 col-sm-3 mt-snj-times-list__item">'
                                    html +=
                                        '<a href="#reservation-modal" class="mt-snj-times-list__link" data-value="' + times[j].value + '">' + times[j].text + '</a>'
                                    html += '</li>'
                                }

                                html += '</div>'
                            }

                            $('.mt-snj-times').html(html)
                            self.reservationModal()
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })
                }
            },
            getRestaurantApi           : function () {

                var self = this

                $('#restaurants-select').on('change', function () {

                    if ($(this).closest('.mt-seatninja-form').length) {
                        return
                    }

                    let restaurantId = $(this).val(),
                        $inputGroup  = $('select#party-size').closest('.mt-snj-input-group')

                    $inputGroup.addClass('mt-snj-loading')
                    $('.mt-snj-times').html('')

                    $.ajax({
                        type   : 'GET',
                        url    : mtSeatNinja.ajaxUrl,
                        timeout: 10000,
                        data   : {
                            action      : 'get_restaurant_info_from_db',
                            restaurantId: restaurantId,
                            nonce       : mtSeatNinja.ajaxNonce
                        },
                        success: (res) => {

                            if (res) {

                                $('.mt-snj-details ').removeClass('mt-snj-hidden')

                                $inputGroup.removeClass('mt-snj-loading')

                                if (typeof self.map !== 'undefined' && typeof self.marker !== 'undefined') {

                                    let location = {
                                        lat: res.lat,
                                        lng: res.lon
                                    }

                                    self.marker.setPosition(location)
                                    self.map.panTo(self.marker.getPosition())
                                }

                                let address = res.address + ', ' + res.city + ', ' + res.state + ' ' + res.zip

                                $('.mt-snj-info__address .mt-snj-info__text').html(address)
                                $('.mt-snj-info__phone .mt-snj-info__text')
                                    .html('<a href="tel:' + res.phoneNumber + '">' + res.phoneNumber + '</a>')
                                $('.mt-snj-info__logo').attr('src', res.logoUrl)

                                if (typeof res.website === 'undefined') {
                                    self.getRestaurantDetailsromApi(restaurantId)
                                } else {
                                    $('.mt-snj-info__url .mt-snj-info__text')
                                        .html('<a href="' + res.website + '">' + res.website + '</a>')
                                }

                                if (typeof res.minPartySizeForReservation === 'undefined' && typeof res.maxPartySizeForReservation === 'undefined') {
                                    self.getRestaurantProfileFromApi(restaurantId)
                                } else {
                                    self.addPartySizeData(res.minPartySizeForReservation, res.maxPartySizeForReservation)
                                }
                            }
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })
                })
            },
            getRestaurantProfileFromApi: function (restaurantId) {

                let self = this

                $.ajax({
                    type   : 'GET',
                    url    : mtSeatNinja.ajaxUrl,
                    timeout: 30000,
                    data   : {
                        action      : 'get_restaurant_profile',
                        restaurantId: restaurantId,
                        nonce       : mtSeatNinja.ajaxNonce
                    },
                    success: (res) => {

                        let minPartySize = 1,
                            maxPartySize = 10

                        if (typeof res.minPartySizeForReservation !== 'undefined') {
                            minPartySize = res.minPartySizeForReservation
                        }

                        if (typeof res.maxPartySizeForReservation !== 'undefined') {
                            maxPartySize = res.maxPartySizeForReservation
                        }

                        self.addPartySizeData(minPartySize, maxPartySize)
                    },
                    error  : (error) => {
                        console.log(error)
                    }
                })
            },
            getRestaurantDetailsromApi : function (restaurantId) {

                let self = this

                $.ajax({
                    type   : 'GET',
                    url    : mtSeatNinja.ajaxUrl,
                    timeout: 30000,
                    data   : {
                        action      : 'get_restaurant_details',
                        restaurantId: restaurantId,
                        nonce       : mtSeatNinja.ajaxNonce
                    },
                    success: (res) => {

                        let website = res.website

                        $('.mt-snj-info__url .mt-snj-info__text').html('<a href="' + website + '">' + website + '</a>')
                    },
                    error  : (error) => {
                        console.log(error)
                    }
                })
            },
            addPartySizeData           : function (minPartySize, maxPartySize) {

                let $options = '<option value="-1">---</option>'

                for (let i = minPartySize; i <= maxPartySize; i++) {
                    $options += '<option value="' + i + '">' + mtSeatNinja.partyOfText + ' ' + i + '</option>'
                }

                $('#party-size').html($options)
            },
            gmap                       : function () {

                var location = {
                        lat: 37.772323,
                        lng: -122.214897
                    },
                    self     = this

                self.map = new google.maps.Map($('#mt-snj-map')[0], {
                    zoom  : 13,
                    center: location
                })

                self.marker = new google.maps.Marker({
                    position: location,
                    map     : self.map
                })

            },
            reservationModal           : function () {

                $('.mt-snj-times-list__link').on('click', function () {
                    $('#mt-snj-reservation-form #time').val($(this).attr('data-value'))
                })

                $('#mt-snj-reservation-form input[type="button"]').on('click', function () {
                    $.magnificPopup.close()
                })

                $('.mt-snj-times-list__link').magnificPopup({
                    type: 'inline'
                })
            },
            bookingReservation         : function () {

                let $form = $('#mt-snj-reservation-form')

                $form.on('submit', function (e) {

                    e.preventDefault()

                    $form.addClass('mt-snj-loading')

                    let data = {
                        action      : 'booking_reservation',
                        restaurantId: $('#restaurants-select').val(),
                        time        : $form.find('#time').val(),
                        partySize   : $('#party-size').val(),
                        firstName   : $form.find('#first-name').val(),
                        lastName    : $form.find('#last-name').val(),
                        phoneNumber : $form.find('#phone').val(),
                        email       : $form.find('#email').val(),
                        notes       : $form.find('#notes').val(),
                        nonce       : mtSeatNinja.ajaxNonce,
                    }

                    $.ajax({
                        method : 'POST',
                        url    : mtSeatNinja.ajaxUrl,
                        timeout: 30000,
                        data   : data,
                        success: (res) => {
                            $form.removeClass('mt-snj-loading')

                            if (res.data) {
                                $form[0].reset()

                                $('#mt-snj-reservation-form').addClass('mt-snj-hidden')
                                $('.mt-snj__message').addClass('success')
                                $('.mt-snj__message')
                                    .html('<p>Thank you! We will call back soon for you to confirm</p>')
                            } else if (res.error) {

                                let html = ''
                                html = '<p>' + res.error.message + '</p>'
                                html += '<ul class="mt-snj__errors">'

                                res.error.messages.forEach((mes) => {
                                    html += '<li>' + mes + '</li>'
                                })
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