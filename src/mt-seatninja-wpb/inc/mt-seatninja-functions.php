<?php

function mt_snj_get_restaurants() {

    $restaurants = get_option( 'mt-snj-restaurants' );

    if ( ! $restaurants || empty( $restaurants ) ) {

        $result = MT_SeatNinja::getDataFromApi( 'GET',
            MT_SEATNINJA_API_URL . '/restaurants' );

        if ( $result ) {
            $restaurants = $result['data'];
            update_option( 'mt-snj-restaurants', $restaurants );
        }
    }

    return $restaurants;
}

function mt_snj_get_restaurant_details() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $id = isset( $_REQUEST['restaurant_id'] ) ? $_REQUEST['restaurant_id'] : '';

    if ( ! $id ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    $result = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/restaurant/' . $id . '/details' );

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

function mt_snj_get_restaurant_profile_from_db() {

    $restaurants = mt_snj_get_restaurants();
    $id          = isset( $_REQUEST['restaurant_id'] ) ? $_REQUEST['restaurant_id'] : '';

    if ( ! $id ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    foreach ( $restaurants as $restaurant ) {

        if ( $restaurant['id'] == $id ) {
            wp_send_json( $restaurant );
            break;
        }
    }
}

add_action( 'wp_ajax_nopriv_get_restaurant_profile_from_db', 'mt_snj_get_restaurant_profile_from_db' );
add_action( 'wp_ajax_get_restaurant_profile_from_db', 'mt_snj_get_restaurant_profile_from_db' );

function mt_snj_get_restaurant_profile() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $id = isset( $_REQUEST['restaurant_id'] ) ? $_REQUEST['restaurant_id'] : '';

    if ( ! $id ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    $profile = get_option( 'mt-snj-restaurant-profile-' . $id );

    if ( ! $profile || empty( $profile ) ) {

        $result = MT_SeatNinja::getDataFromApi( 'GET',
            MT_SEATNINJA_API_URL . '/restaurant/' . $id . '/profile' );

        if ( $result == null ) {
            wp_send_json( array( 'error' => esc_html__( 'Can\'t find this restaurant', 'mt-snj' ) ) );
        }

        if ( $result['data'] == null ) {
            wp_send_json( array( 'error' => $result['message'] ) );
        }

        $profile = $result['data'];
        update_option( 'mt-snj-restaurant-profile-' . $id, $profile );
    }

    wp_send_json( $profile );
}

add_action( 'wp_ajax_nopriv_get_restaurant_profile', 'mt_snj_get_restaurant_profile' );
add_action( 'wp_ajax_get_restaurant_profile', 'mt_snj_get_restaurant_profile' );

function mt_snj_get_reservation_times() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $id        = isset( $_REQUEST['restaurant_id'] ) ? $_REQUEST['restaurant_id'] : '';
    $partySize = isset( $_REQUEST['party_size'] ) ? $_REQUEST['party_size'] : - 1;
    $date      = isset( $_REQUEST['date'] ) ? $_REQUEST['date'] : '';

    if ( ! $id ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    if ( $partySize < 1 ) {
        wp_send_json( array( 'error' => esc_html__( 'Party size is not set', 'mt-snj' ) ) );
    }

    if ( ! $date ) {
        wp_send_json( array( 'error' => esc_html__( 'Date is not set', 'mt-snj' ) ) );
    }

    $restaurants = mt_snj_get_restaurants();
    $location    = array(
        'lat' => '',
        'lon' => '',
    );
    $timeZone = '';

    foreach ( $restaurants as $restaurant ) {

        if ( $restaurant['id'] == $id ) {
            $location['lat'] = $restaurant['lat'];
            $location['lon'] = $restaurant['lon'];
            break;
        }
    }

    MT_SeatNinja::getTimeInUSA( $location );

    $result = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/reservations/' . $id . '/availabletimes/' . $date . '/' . $partySize );

    if ( $result == null ) {
        wp_send_json( array( 'error' => esc_html__( 'Can\'t find available times of this restaurant', 'mt-snj' ) ) );
    }

    if ( $result['data'] == null ) {
        wp_send_json( array( 'error' => $result['message'] ) );
    }

    wp_send_json( $result['data'] );
}

add_action( 'wp_ajax_nopriv_get_reservation_times', 'mt_snj_get_reservation_times' );
add_action( 'wp_ajax_get_reservation_times', 'mt_snj_get_reservation_times' );
