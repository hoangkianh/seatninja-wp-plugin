(function ($) {
    mtui_wpb_frontend = (function () {
        return {
            init                : function () {
                var currentDate,
                    map,
                    marker
                this.dateTimePicker()
                this.partySizeSelectBox()
                this.getRestaurantProfile()
                this.bookingReservation()

                if (mtSeatNinja.gmapsApiKey) {
                    this.gmap()
                }
            },
            formatDate          : function (date) {
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
            dateTimePicker      : function () {
                var self = this

                $('#datetimepicker').datetimepicker({
                    value           : new Date(),
                    timepicker      : false,
                    scrollInput     : false,
                    format          : 'M d Y',
                    onChangeDateTime: function (e, $input) {

                        if ($input.val() !== self.currentDate) {
                            self.currentDate = $input.val()
                            self.getReservationTimes()
                        }
                    }
                })
            },
            partySizeSelectBox  : function () {
                var self = this

                $('#party-size').on('change', function () {
                    self.getReservationTimes()
                })
            },
            getReservationTimes : function () {
                let self = this
                let restaurantId = $('#restaurants-select').val()
                let partySize = $('#party-size').val()
                let date = $('#datetimepicker').val()

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

                            let html = ''

                            for (let i = 0; i < res.length; i++) {
                                html += '<p class="mt-snj-section-name">' + res[i].section_name + '</p>'

                                let times = res[i].times

                                html += '<ul class="row mt-snj-times-list">'

                                for (let j = 0; j < times.length; j++) {
                                    html += '<li class="col-md-3 col-xs-6 mt-snj-times-list__item">'
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
            getRestaurantProfile: function () {

                var self = this

                $('#restaurants-select').on('change', function () {
                    let restaurantId = $(this).val()

                    $.ajax({
                        type   : 'GET',
                        url    : mtSeatNinja.ajaxUrl,
                        timeout: 10000,
                        data   : {
                            action      : 'get_restaurant_details_from_db',
                            restaurantId: restaurantId,
                            nonce       : mtSeatNinja.ajaxNonce
                        },
                        success: (res) => {

                            if (res && typeof self.map !== 'undefined' && typeof self.marker !== 'undefined') {

                                let location = {
                                    lat: res.lat,
                                    lng: res.lon
                                }

                                self.marker.setPosition(location)
                                self.map.panTo(self.marker.getPosition())
                            }
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })

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
                                maxPartySize = 10,
                                $options     = '<option value="-1">---</option>'

                            if (typeof res.minPartySizeForReservation !== 'undefined') {
                                minPartySize = res.minPartySizeForReservation
                            }

                            if (typeof res.maxPartySizeForReservation !== 'undefined') {
                                maxPartySize = res.maxPartySizeForReservation
                            }

                            for (let i = minPartySize; i <= maxPartySize; i++) {
                                $options +=
                                    '<option value="' + i + '">' + mtSeatNinja.partyOfText + ' ' + i + '</option>'
                            }

                            $('#party-size').html($options)
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })
                })
            },
            gmap                : function () {

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
            reservationModal    : function () {

                $('.mt-snj-times-list__link').on('click', function () {
                    $('#mt-snj-reservation-form #time').val($(this).attr('data-value'))
                })

                $('.mt-snj-times-list__link').magnificPopup({
                    type: 'inline'
                })
            },
            bookingReservation  : function () {

                let $form = $('#mt-snj-reservation-form')

                $form.on('submit', function (e) {

                    e.preventDefault()

                    $form.addClass('mt-snj_loading')

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
                            $form.removeClass('mt-snj_loading')
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