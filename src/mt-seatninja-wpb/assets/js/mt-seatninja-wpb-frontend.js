(function ($) {
    mtui_wpb_frontend = (function () {
        return {
            init                       : function () {
                let currentDate,
                    map,
                    marker,
                    timeZone
                this.datePicker()
                this.partySizeEvent()
                this.getRestaurantApi()
                this.bookingReservation()

                if (mtSeatNinja.gmapsApiKey && $('#mt-snj-map').length) {
                    this.gmap()
                }
            },
            formatDate                 : function (date) {
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
            datePicker                 : function () {
                let self = this

                $('.datepicker').datetimepicker({
                    value           : new Date(),
                    timepicker      : false,
                    scrollInput     : false,
                    format          : 'M d Y',
                    onChangeDateTime: function (e, $input) {

                        let $form = $(this).closest('.mt-seatninja')

                        if ($input.val() !== self.currentDate && $input.closest('.mt-seatninja').length) {
                            self.currentDate = $input.val()
                            self.getReservationTimes()
                        }
                    }
                })
            },
            partySizeEvent             : function () {
                let self = this

                $('.party-size').on('change', function () {
                    self.getReservationTimes()
                })
            },

            getReservationTimes        : function () {
                let self = this
                let restaurantId = $('.restaurant-id').val()
                let partySize = $('.party-size').val()
                let date = $('.datepicker').val()
                let $timepicker = $('.timepicker')

                if (isNaN(restaurantId) || partySize < 1 || partySize > 15) {
                    $timepicker.datetimepicker('destroy')
                    return false
                }

                // if (partySize > 14) {
                    // $form.find('.mt-snj__message').html('If you would like to make a reservation for 15 or more, please contact the restaurant directly. Thank you!')
                    // $form.find('.mt-snj-times').html('')
                    // $.magnificPopup.open({
                    //     items: {
                    //         src : '.mt-snj__message',
                    //         type: 'inline'
                    //     }
                    // })
                //     return false
                // }

                $timepicker.addClass('mt-snj-loading')

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

                            for (let i = 0; i < res.length; i++) {
                                let times = res[i].times
                                self.timeZone = res[i].timezone

                                for (let j = 0; j < times.length; j++) {
                                    $('.timepicker').append($('<option>', {
                                        value: times[j].value,
                                        text: times[j].text
                                    }));
                                }
                            }
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })
                }
            },
            getRestaurantApi           : function () {

                let self          = this,
                    $restaurantID = $('.restaurant-id')

                $restaurantID.on('change', function () {

                    if ($(this).closest('.mt-seatninja-form').length) {
                        return
                    }

                    let restaurantId = $(this).val()

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
                            }
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })
                })

                if ($restaurantID.closest('.mt-seatninja--single').length) {
                    $restaurantID.trigger('change')
                }
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

                        let name = ''

                        if (typeof res.name !== 'undefined') {
                            name = res.name
                        }
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
            gmap                       : function () {

                let location = {
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
            bookingReservation         : function () {

                let $form = $('.mt-snj-reservation-form')

                $form.on('submit', function (e) {

                    e.preventDefault()

                    $form.addClass('mt-snj-loading')

                    let data = {
                        action      : 'booking_reservation',
                        restaurantId: $form.find('.restaurant-id').val(),
                        time        : $form.find('.timepicker').val(),
                        partySize   : $form.find('.party-size').val(),
                        firstName   : $form.find('.first-name').val(),
                        lastName    : $form.find('.last-name').val(),
                        phoneNumber : $form.find('.phone').val(),
                        email       : $form.find('.email').val(),
                        notes       : $form.find('.notes').val(),
                        nonce       : mtSeatNinja.ajaxNonce,
                    }

                    if (parseInt(data.partySize) > 14) {
                        $form.find('.mt-snj__message').html('If you would like to make a reservation for 15 or more, please contact the restaurant directly. Thank you!');
                        return false;
                    }

                    if (!data.time) {
                        data.time = $('.timepicker').val()
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

                                let restaurantName =$form.find('.restaurant-id option:selected').text()

                                let message = '<p>Thank you! We will call back soon for you to confirm</p>'
                                message += '<p>Here is the reservation information:</p>'
                                message += 'Restaurant: <strong>' + restaurantName + '</strong><br/>'
                                message += 'Number of people: <strong>' + data.partySize + '</strong><br/>'
                                message += 'Time: <strong>' + $form.find('.datepicker').val() + ' ' + $form.find('.timepicker option:selected').text() + '</strong><br/>'
                                message += 'Name: <strong>' + data.firstName + ' ' + data.lastName + '</strong><br/>'
                                message += 'Phone Number: <strong>' + data.phoneNumber + '</strong><br/>'
                                message += 'Email: <strong>' + data.email + '</strong>'

                                $form.find('.mt-snj__message').html(message)

                                $.magnificPopup.open({
                                    items: {
                                        src : '.mt-snj__message',
                                        type: 'inline'
                                    }
                                })
                                $form[0].reset()
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