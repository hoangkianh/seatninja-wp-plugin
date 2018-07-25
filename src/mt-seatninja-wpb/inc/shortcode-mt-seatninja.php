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

    wp_enqueue_style('datetimepicker');
    wp_enqueue_script('datetimepicker');
    wp_enqueue_script('google-map');
    wp_enqueue_style('magnific-popup');
    wp_enqueue_script('magnific-popup');
    wp_enqueue_style('mt-seatninja-wpb');
    wp_enqueue_script('mt-seatninja-wpb');

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
        $html[] = '<div class="col-xs-12 col-md-7 col-lg-8 mt-snj-main">';

        $html[] = '<div class="mt-snj-input-group">';
        $html[] = mt_seatninja_restaurant_selectbox();
        $html[] = '</div>';

        $html[] = '<div class="mt-snj-input-group">';
        $html[] = mt_seatninja_partysize_selectbox();
        $html[] = '</div>';

        $html[] = '<div class="mt-snj-input-group">';
        $html[] = mt_seatninja_date_picker();
        $html[] = '</div>';

        $html[] = '<div class="mt-snj-times"></div>';

        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-md-5 col-lg-4 mt-snj-details mt-snj-hidden">';
        $html[] = '<div id="mt-snj-map"></div>';
        $html[] = '<div class="mt-snj-info">';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-8">';
        $html[] = '<p class="mt-snj-info__address"><i class="fa fa-map-marker"></i><span class="mt-snj-info__text"></span></p>';
        $html[] = '<p class="mt-snj-info__phone"><i class="fa fa-phone"></i><span class="mt-snj-info__text"></span></p>';
        $html[] = '<p class="mt-snj-info__url"><i class="fa fa-link"></i><span class="mt-snj-info__text"></span></p>';
        $html[] = '</div>';
        $html[] = '<div class="col-sm-4">';
        $html[] = '<img class="mt-snj-info__logo" />';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div id="reservation-modal" class="mfp-hide">';
        $html[] = '<div class="mt-snj__message"></div>';
        $html[] = '<form id="mt-snj-reservation-form">';
        $html[] = '<div class="mt-snj-form__error"></div>';
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
        $html[] = '<input type="submit" value="Submit" />';
        $html[] = '<input type="button" value="Cancel" />';
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
