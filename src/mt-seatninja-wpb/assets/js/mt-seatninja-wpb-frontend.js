(function ($) {
    mtui_wpb_frontend = (function () {
        return {
            init                : function () {
                var currentDate
                this.dateTimePicker()
                this.partySizeSelectBox()
                this.getRestaurantProfile()

                if (mtSeatNinja.gmaps_api_key) {
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
                let restaurant_id = $('#restaurants-select').val()
                let partySize = $('#party-size').val()
                let date = $('#datetimepicker').val()

                if (partySize > 0 && date) {
                    $.ajax({
                        type   : 'GET',
                        url    : mtSeatNinja.ajax_url,
                        timeout: 10000,
                        data   : {
                            action       : 'get_reservation_times',
                            restaurant_id: restaurant_id,
                            party_size   : partySize,
                            date         : self.formatDate(date),
                            nonce        : mtSeatNinja.ajax_nonce
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
                                        '<a href="#" class="mt-snj-times-list__link" data-value="' + times[j].value + '">' + times[j].text + '</a>'
                                    html += '</li>'
                                }

                                html += '</div>'
                            }

                            $('.mt-snj-times').html(html)
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })
                }
            },
            getRestaurantProfile: function () {

                $('#restaurants-select').on('change', function () {
                    let id = $(this).val()

                    $.ajax({
                        type   : 'GET',
                        url    : mtSeatNinja.ajax_url,
                        timeout: 10000,
                        data   : {
                            action       : 'get_restaurant_profile_from_db',
                            restaurant_id: id,
                            nonce        : mtSeatNinja.ajax_nonce
                        },
                        success: (res) => {
                        },
                        error  : (error) => {
                            console.log(error)
                        }
                    })

                    $.ajax({
                        type   : 'GET',
                        url    : mtSeatNinja.ajax_url,
                        timeout: 10000,
                        data   : {
                            action       : 'get_restaurant_profile',
                            restaurant_id: id,
                            nonce        : mtSeatNinja.ajax_nonce
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
                                    '<option value="' + i + '">' + mtSeatNinja.party_of_text + ' ' + i + '</option>'
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
                var center = [37.772323, -122.214897]
                $('#mt-snj-map').gmap3({
                    center   : center,
                    zoom     : 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }).marker({
                    position: center
                })
            }
        }
    }())
})(jQuery)

jQuery(document).ready(function () {
    mtui_wpb_frontend.init()
})