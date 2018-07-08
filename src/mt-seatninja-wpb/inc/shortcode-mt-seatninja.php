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

    $restaurants = mt_snj_get_restaurants();
    $html[] = '<select id="restaurants">';

    if ( ! empty( $restaurants ) ) {

        foreach ( $restaurants as $restaurant ) {
            $html[] = '<option value="' . $restaurant['id'] . '">' .  $restaurant['name'] . ' - ' . $restaurant['id'] . '</option>';
        }
    }

    $html[] = '</select>';

    return sprintf( '<div class="%s">%s</div>',
        trim( implode( ' ', $css_class ) ),
        implode( '\n', $html ) );
}
