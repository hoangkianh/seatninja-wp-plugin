<?php

function mt_snj_get_restaurants() {

    $restaurants = array();
    $keys        = MT_SeatNinja::get_snj_keys();

    $result = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/restaurants',
        array( 'x-api-key:' . $keys['api-key'] ) );

    if ( $result ) {
        $restaurants = $result['data'];
        update_option( 'mt-snj-restaurants', $restaurants );
    }

    return $restaurants;
}

function mt_snj_get_restaurant_details() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $keys = MT_SeatNinja::get_snj_keys();
    $id   = isset( $_REQUEST['restaurant_id'] ) ? $_REQUEST['restaurant_id'] : '';

    if ( ! $id ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    $result = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/restaurant/' . $id . '/details',
        array( 'x-api-key:' . $keys['api-key'] ) );

    if ( $result == null ) {
        wp_send_json( array( 'error' => esc_html__( 'Can\'t find this restaurant', 'mt-snj' ) ) );
    }

    if ( $result['data'] == null ) {
        wp_send_json( array( 'error' => $result['message'] ) );
    }

    wp_send_json( $result );
}

add_action( 'wp_ajax_nopriv_get_restaurant_details', 'mt_snj_get_restaurant_details' );
add_action( 'wp_ajax_get_restaurant_details', 'mt_snj_get_restaurant_details' );

function mt_snj_get_restaurant_details_from_db() {

    $restaurants = get_option( 'mt-snj-restaurants' );
    $id          = isset( $_REQUEST['restaurant_id'] ) ? $_REQUEST['restaurant_id'] : '';

    if ( ! $id ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    if ( empty( $restaurants ) ) {
        $restaurants = mt_snj_get_restaurants();
    }

    foreach ( $restaurants as $restaurant ) {

        if ( $restaurant['id'] == $id ) {
            wp_send_json( $restaurant );
            break;
        }
    }
}

add_action( 'wp_ajax_nopriv_get_restaurant_details_from_db', 'mt_snj_get_restaurant_details_from_db' );
add_action( 'wp_ajax_get_restaurant_details_from_db', 'mt_snj_get_restaurant_details_from_db' );

function mt_snj_get_restaurant_profile() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $keys = MT_SeatNinja::get_snj_keys();
    $id   = isset( $_REQUEST['restaurant_id'] ) ? $_REQUEST['restaurant_id'] : '';

    if ( ! $id ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    $result = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/restaurant/' . $id . '/profile',
        array( 'x-api-key:' . $keys['api-key'] ) );

    if ( $result == null ) {
        wp_send_json( array( 'error' => esc_html__( 'Can\'t find this restaurant', 'mt-snj' ) ) );
    }

    if ( $result['data'] == null ) {
        wp_send_json( array( 'error' => $result['message'] ) );
    }

    wp_send_json( $result );
}

add_action( 'wp_ajax_nopriv_get_restaurant_profile', 'get_restaurant_profile' );
add_action( 'wp_ajax_get_restaurant_profile', 'get_restaurant_profile' );