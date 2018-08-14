<?php

vc_map( array(
    'name'        => esc_html__( 'SeatNinja', 'mt-snj' ),
    'base'        => 'mt_seatninja',
    'description' => esc_html__( 'Show a reservation form' ),
    'params'      => array(
        array(
            'group'      => esc_html__( 'General', 'mt-snj' ),
            'type'       => 'checkbox',
            'param_name' => 'show_all',
            'value'      => array(
                esc_html__( 'Show all restaurants', 'mt-snj' ) => 'yes',
            ),
        ),
        array(
            'group'      => esc_html__( 'General', 'mt-snj' ),
            'type'       => 'dropdown',
            'heading'    => esc_html__( 'Restaurant', 'mt-snj' ),
            'param_name' => 'restaurant_id',
            'value'      => mt_snj_get_restaurants_for_vc(),
            'dependency' => array(
                'element'            => 'show_all',
                'value_not_equal_to' => 'yes',
            ),
            'admin_label' => true
        ),
        array(
            'group'      => esc_html__( 'General', 'mt-snj' ),
            'type'       => 'checkbox',
            'param_name' => 'show_info',
            'value'      => array(
                esc_html__( 'Show restaurant informations', 'mt-snj' ) => 'yes',
            ),
            'dependency' => array(
                'element'            => 'show_all',
                'value_not_equal_to' => 'yes',
            ),
        ),
        array(
            'group'       => esc_html__( 'General', 'mt-snj' ),
            'type'        => 'textfield',
            'heading'     => esc_html__( 'Extra class name', 'mt-snj' ),
            'param_name'  => 'el_class',
            'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.',
                'mt-snj' ),
        ),
        array(
            'group'      => esc_html__( 'Design Options', 'mt-snj' ),
            'type'       => 'css_editor',
            'heading'    => esc_html__( 'CSS box', 'mt-snj' ),
            'param_name' => 'css',
        ),
    ),
) );

add_shortcode( 'mt_seatninja', 'mt_seatninja' );
function mt_seatninja( $atts ) {
    wp_enqueue_style( 'datetimepicker' );
    wp_enqueue_script( 'datetimepicker' );
    wp_enqueue_script( 'google-map' );
    wp_enqueue_style( 'magnific-popup' );
    wp_enqueue_script( 'magnific-popup' );
    wp_enqueue_style( 'mt-seatninja-wpb' );
    wp_enqueue_script( 'mt-seatninja-wpb' );

    $atts = shortcode_atts( array(
        'show_all'      => '',
        'restaurant_id' => '',
        'show_info'     => '',
        'el_class'      => '',
        'css'           => '',
    ),
        $atts,
        __FUNCTION__ );

    extract( $atts );

    $show_all  = isset( $atts['show_all'] ) && $atts['show_all'] == 'yes';
    $show_info = isset( $atts['show_info'] ) && $atts['show_info'] == 'yes';

    $css_class = array(
        'mt-seatninja',
        $show_all ? '' : 'mt-seatninja--single',
        $atts['el_class'],
        vc_shortcode_custom_css_class( $atts['css'] ),
    );

    $html = array();
    $keys = MT_SeatNinja::get_snj_keys();

    $html[] = '<div class="container">';
    $html[] = '<div class="row">';

    if ( ! empty( $keys ) ) {
        $html[] = '<div class="col-xs-12' . ($show_info ? ' col-md-7 col-lg-8' : '') . ' mt-snj-main">';
        $html[] = '<div class="row">';

        $html[] = '<div class="col-xs-12 col-sm-6 col-md-3">';
        if ( $show_all ) {
            $html[] = mt_seatninja_restaurant_selectbox();
        } elseif ( isset( $atts['restaurant_id'] ) ) {
            $html[] = mt_seatninja_restaurant_selectbox(true, $atts['restaurant_id']);
        }
        $html[] = '</div>';

        $html[]  = '<div class="col-xs-12 col-sm-6 col-md-3">';
        $html[]  = '<label for="party-size">' . esc_html__( 'Number of people', 'mt-snj' ) . '</label>';
        $html [] = '<input type="number" min="1" class="party-size" value="">';
        $html[]  = '</div>';

        $html[] = '<div class="col-xs-12 col-sm-6 col-md-3">';
        $html[] = mt_seatninja_date_picker();
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 mt-snj-times"></div>';

        $html[] = '</div></div>';

        if ( $show_info ) {

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
        }


        $html[] = '<div id="reservation-modal" class="mfp-hide">';
        $html[] = '<div class="mt-snj__message"></div>';
        $html[] = '<form class="mt-snj-reservation-form">';
        $html[] = '<div class="mt-snj-form__error"></div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="first-name">First Name <mark>*</mark></label>';
        $html[] = '<input type="text" name="first-name" class="first-name" required />';
        $html[] = '</div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="last-name">Last Name</label>';
        $html[] = '<input type="text" name="last-name" class="last-name" />';
        $html[] = '</div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="phone">Phone Number<mark>*</mark></label>';
        $html[] = '<input type="text" name="phone" class="phone" required />';
        $html[] = '</div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="email">Email<mark>*</mark></label>';
        $html[] = '<input type="email" name="email" class="email" required />';
        $html[] = '</div>';
        $html[] = '<div class="mt-snj-input-group">';
        $html[] = '<label for="notes">Notes</label>';
        $html[] = '<textarea name="notes" class="notes"></textarea>';
        $html[] = '</div>';
        $html[] = '<input type="hidden" id="time"/>';
        
        if (!$show_all) {
            $html[] = '<input type="hidden" id="time-text"/>';
        }
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

    $html[] = '</div></div>';

    return sprintf( '<div class="%s">%s</div>',
        trim( implode( ' ', $css_class ) ),
        implode( '', $html ) );
}
