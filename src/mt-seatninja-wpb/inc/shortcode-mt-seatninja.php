<?php

vc_map( array(
    'name'        => esc_html__( 'SeatNinja', 'mt-snj' ),
    'base'        => 'mt_seatninja',
    'description' => esc_html__( 'Show a reservation form' ),
    'params'      => array(//array()
    ),
) );

add_shortcode( 'mt_seatninja', 'mt_seatninja' );
function mt_seatninja( $atts ) {

    $atts = shortcode_atts( array(), $atts, __FUNCTION__ );

    extract( $atts );

    $css_class = array(
        'mt-seatninja',
        $atts['el_class'],
        vc_shortcode_custom_css_class( $atts['css'] ),
    );

    $html = array();
    $keys = MT_SeatNinja::get_snj_keys();

    $html[] = '<div class="container">';
    $html[] = '<div class="row">';

    if ( ! empty( $keys ) ) {
        $html[] = '<div class="col-xs-12 col-md-8 mt-snj-main">';

        $html[] = mt_seatninja_restaurant_selectbox();

        $html[] = mt_seatninja_partysize_selectbox();

        $html[] = mt_seatninja_date_picker();

        $html[] = '<div class="mt-snj-times"></div>';

        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-md-4 mt-snj-details">';
        $html[] = '<div id="mt-snj-map"></div>';
        $html[] = '<div class="mt-snj-info">';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-8">';
        $html[] = '<p class="mt-snj-info__address"><span class="glyphicon icon-location"></span></p>';
        $html[] = '<p class="mt-snj-info__phone"><span class="glyphicon icon-phone-outline"></span></p>';
        $html[] = '<p class="mt-snj-info__url"><span class="glyphicon icon-link"></span></p>';
        $html[] = '</div>';
        $html[] = '<div class="col-md-4">';
        $html[] = '<img class="mt-snj-info__logo" />';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div id="reservation-modal" class="mfp-hide">';
        $html[] = '<form id="mt-snj-reservation-form">';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="first-name">First Name <mark>*</mark></label>';
        $html[] = '<input type="text" name="first-name" id="first-name" required />';
        $html[] = '</div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="last-name">Last Name</label>';
        $html[] = '<input type="text" name="last-name" id="last-name" />';
        $html[] = '</div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="phone">Phone Number<mark>*</mark></label>';
        $html[] = '<input type="text" name="phone" id="phone" required />';
        $html[] = '</div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="email">Email<mark>*</mark></label>';
        $html[] = '<input type="email" name="email" id="email" required />';
        $html[] = '</div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="notes">Notes</label>';
        $html[] = '<textarea name="notes" id="notes"></textarea>';
        $html[] = '</div>';
        $html[] = '<input type="hidden" id="time"/>';
        $html[] = '<div class="buttons">';
        $html[] = '<input type="button" value="Cancel" />';
        $html[] = '<input type="submit" value="Submit" />';
        $html[] = '</div>';
        $html[] = '</form>';
        $html[] = '</div>';
    } else {
        $html[] = '<div class="col-xs-12 mt-snj-main">' . esc_html__( 'Seat Ninja API Key & Customer AuthToken is not set',
                'mt-snj' ) . '</div>';
    }

    return sprintf( '<div class="%s">%s</div>',
        trim( implode( ' ', $css_class ) ),
        implode( '', $html ) );
}
