(function ($) {
    mtui_wpb_frontend = (function () {
        return {
            init                : function () {
                this.dateTimePicker()
                this.partySizeSelectBox()
                this.getRestaurantProfile()
            },
            dateTimePicker: function () {
                var self = this

                $('#datetimepicker').datetimepicker({
                    timepicker: false,
                    format: 'M d',
                    onChangeDateTime:function(e, $input){
                        self.getReservationTimes()
                    }
                })
            },
            partySizeSelectBox: function () {
                var self = this

                $('#party-size').on('click', function () {
                    self.getReservationTimes()
                })
            },
            getReservationTimes: function () {
                let partySize = $('#party-size').val()
                let date      = $('#datetimepicker').datetimepicker('getValue')

                console.log(partySize)
                console.log(date)
            },
            getRestaurantProfile: function () {

                $('#restaurants-select').on('change', function () {
                    let id = $(this).val()

                    $.ajax({
                        type   : 'GET',
                        url    : mtSeatNinja.ajax_url,
                        timeout: 10000,
                        data   : {
                            action       : 'get_restaurant_details_from_db',
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
                                $options += '<option value="' + i + '">' + mtSeatNinja.party_of_text + ' ' + i + '</option>'
                            }

                            $('#party-size').html($options)
                        },
                        error  : (error) => {
                            console.log(error)
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