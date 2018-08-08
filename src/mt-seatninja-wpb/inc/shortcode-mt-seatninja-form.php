<?php

vc_map( array(
    'name'        => esc_html__( 'SeatNinja Form', 'mt-snj' ),
    'base'        => 'mt_seatninja_form',
    'description' => esc_html__( 'Show a reservation form' ),
    'params'      => array(
        array(
            'group'      => esc_html__( 'General', 'mt-snj' ),
            'type'       => 'textarea_html',
            'heading'    => esc_html__( 'Form Text', 'mt-snj' ),
            'param_name' => 'text',
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

add_shortcode( 'mt_seatninja_form', 'mt_seatninja_form' );

function mt_seatninja_form( $atts ) {
    wp_enqueue_style( 'datetimepicker' );
    wp_enqueue_script( 'datetimepicker' );
    wp_enqueue_style( 'magnific-popup' );
    wp_enqueue_script( 'magnific-popup' );
    wp_enqueue_style( 'mt-seatninja-wpb' );
    wp_enqueue_script( 'mt-seatninja-wpb' );

    $atts = shortcode_atts( array(
        'text'     => '',
        'el_class' => '',
        'css'      => '',
    ),
        $atts,
        __FUNCTION__ );

    extract( $atts );

    $css_class = array(
        'mt-seatninja-form',
        $atts['el_class'],
        vc_shortcode_custom_css_class( $atts['css'] ),
    );

    $html = array();
    $keys = MT_SeatNinja::get_snj_keys();

    $html[] = '<form class="mt-snj-reservation-form">';
    $html[] = '<div class="mt-snj__message"></div>';
    $html[] = '<div class="mt-snj-form__error"></div>';
    $html[] = '<div class="container">';

    if ( ! empty( $keys ) ) {
        if ( $atts['text'] ) {
            $html[] = '<div class="row">';
            $html[] = '<div class="col-xs-12">';
            $html[] = '<p class="mt-seatninja-form-text">' . $atts['text'] . '</p>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $html[]  = '<div class="row">';
        $html[]  = '<div class="col-xs-12 col-sm-6 col-md-3">';
        $html[]  = mt_seatninja_restaurant_selectbox(false);
        $html[]  = '</div>';
        $html[]  = '<div class="col-xs-12 col-sm-6 col-md-3">';
        $html[]  = '<div class="mt-snj-form-group">';
        $html [] = '<input type="text" id="party-size" value="" placeholder="' . esc_html( 'Number of people',
                'mt-snj' ) . '">';
        $html [] = '</div>';
        $html[]  = '</div>';
        $html[]  = '<div class="col-xs-12 col-sm-6 col-md-3">';
        $html[]  = '<div class="mt-snj-form-group">' . mt_seatninja_date_picker( false ) . '</div>';
        $html[]  = '</div>';
        $html[]  = '<div class="col-xs-12 col-sm-6 col-md-3">';
        $html[]  = '<div class="mt-snj-form-group">' . mt_seatninja_time_picker( false ) . '</div>';
        $html[] = '<input type="hidden" id="time"/>';
        $html[]  = '</div>';
        $html[]  = '</div>';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-sm-6 col-md-3 mt-snj-form-group">';
        $html[] = '<input type="text" name="first-name" id="first-name" required placeholder="' . esc_html( 'First Name', 'mt-snj' ) . '" />';
        $html[] = '</div>';
        $html[] = '<div class="col-xs-12 col-sm-6 col-md-3 mt-snj-form-group">';
        $html[] = '<input type="text" name="last-name" id="last-name" required placeholder="' . esc_html( 'Last Name', 'mt-snj' ) . '" />';
        $html[] = '</div>';
        $html[] = '<div class="col-xs-12 col-sm-6 col-md-3 mt-snj-form-group">';
        $html[] = '<input type="text" name="phone" id="phone" required placeholder="' . esc_html( 'Phone Number',
                'mt-snj' ) . '" />';
        $html[] = '</div>';
        $html[] = '<div class="col-xs-12 col-sm-6 col-md-3 mt-snj-form-group">';
        $html[] = '<input type="email" name="email" id="email" required placeholder="' . esc_html( 'Email',
                'mt-snj' ) . '" />';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12">';
        $html[] = '<textarea name="notes" id="notes" placeholder="' . esc_html( 'Add special request (Optional)',
                'mt-snj' ) . '"></textarea>';
        $html[] = '</div>';

        $html[] = '<div class="buttons">';
        $html[] = '<input type="submit" value="Book a table" />';
        $html[] = '<input type="button" value="Reset" />';
        $html[] = '</div>';
    } else {
        $html[] = esc_html__( 'Seat Ninja API Key & Customer AuthToken is not set', 'mt-snj' );
    }
    $html[] = '</div>';
    $html[] = '</form>';

    return sprintf( '<div class="%s">%s</div>',
        trim( implode( ' ', $css_class ) ),
        implode( '', $html ) );
}