(function( $ ) {
    mtui_wpb_frontend = (function() {
        return {
            init           : function() {
                this.getRestaurantProfile();
            },
            getRestaurantProfile: function () {

                $('#restaurants-select').on('change', function () {
                    let id = $(this).val();

                    $.ajax({
                        type: 'GET',
                        url: mtSeatNinja.ajax_url,
                        timeout: 10000,
                        data: {
                            action: 'get_restaurant_details_from_db',
                            restaurant_id : id,
                            nonce: mtSeatNinja.ajax_nonce
                        },
                        success: (res) => {

                        },
                        error: (error) => {
                            console.log(error);
                        }
                    })
                });
            }
        }
    }());
})( jQuery );

jQuery( document ).ready( function() {
    mtui_wpb_frontend.init();
} );