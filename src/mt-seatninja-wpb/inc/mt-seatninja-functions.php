<?php

function mt_snj_get_restaurants() {

    $restaurants = get_option( 'mt-snj-restaurants' );

    if ( ! $restaurants || empty( $restaurants ) ) {

        $response = MT_SeatNinja::getDataFromApi( 'GET',
            MT_SEATNINJA_API_URL . '/restaurants' );

        if ( $response ) {
            $restaurants = $response['data'];
            add_option( 'mt-snj-restaurants', $restaurants );
        }
    }

    return $restaurants;
}

function mt_snj_get_restaurant_details() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $restaurants    = mt_snj_get_restaurants();
    $newRestaurants = array();
    $restaurantId = isset( $_REQUEST['restaurantId'] ) ? $_REQUEST['restaurantId'] : '';

    if ( ! $restaurantId ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    $response = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/restaurant/' . $restaurantId . '/details' );

    if ( $response == null ) {
        wp_send_json( array( 'error' => esc_html__( 'Can\'t find this restaurant', 'mt-snj' ) ) );
    }

    if ( $response['data'] == null ) {
        wp_send_json( array( 'error' => $response['message'] ) );
    }

    $details = $response['data'];

    // Update in database
    foreach ( $restaurants as $restaurant ) {

        if ( $restaurant['id'] == $restaurantId ) {
            $restaurant['website'] = $details['website'];
        }

        $newRestaurants[] = $restaurant;
    }

    add_option( 'mt-snj-restaurants', $newRestaurants );

    wp_send_json( $details );
}

add_action( 'wp_ajax_nopriv_get_restaurant_details', 'mt_snj_get_restaurant_details' );
add_action( 'wp_ajax_get_restaurant_details', 'mt_snj_get_restaurant_details' );

function mt_snj_get_restaurant_info_from_db() {

    $restaurants  = mt_snj_get_restaurants();
    $restaurantId = isset( $_REQUEST['restaurantId'] ) ? $_REQUEST['restaurantId'] : '';

    if ( ! $restaurantId ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    foreach ( $restaurants as $restaurant ) {

        if ( $restaurant['id'] == $restaurantId ) {
            wp_send_json( $restaurant );
            break;
        }
    }
}

add_action( 'wp_ajax_nopriv_get_restaurant_info_from_db', 'mt_snj_get_restaurant_info_from_db' );
add_action( 'wp_ajax_get_restaurant_info_from_db', 'mt_snj_get_restaurant_info_from_db' );

function mt_snj_get_restaurant_profile() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $restaurants    = mt_snj_get_restaurants();
    $newRestaurants = array();
    $restaurantId   = isset( $_REQUEST['restaurantId'] ) ? $_REQUEST['restaurantId'] : '';

    if ( ! $restaurantId ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    $response = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/restaurant/' . $restaurantId . '/profile' );

    if ( $response == null ) {
        wp_send_json( array( 'error' => esc_html__( 'Can\'t find this restaurant', 'mt-snj' ) ) );
    }

    if ( $response['data'] == null ) {
        wp_send_json( array( 'error' => $response['message'] ) );
    }

    $profile = $response['data'];

    // Update in database
    foreach ( $restaurants as $restaurant ) {

        if ( $restaurant['id'] == $restaurantId ) {
            $restaurant['name'] = $profile['name'];
        }

        $newRestaurants[] = $restaurant;
    }

    update_option( 'mt-snj-restaurants', $newRestaurants );

    wp_send_json( $profile );
}

add_action( 'wp_ajax_nopriv_get_restaurant_profile', 'mt_snj_get_restaurant_profile' );
add_action( 'wp_ajax_get_restaurant_profile', 'mt_snj_get_restaurant_profile' );

function mt_snj_get_sections( $restaurant_id ) {
    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $sections = get_option( 'mt-snj-restaurant-sections-' . $restaurant_id );

    if ( ! $sections || empty( $sections ) ) {
        $response = MT_SeatNinja::getDataFromApi( 'GET',
            MT_SEATNINJA_API_URL . '/restaurant/' . $restaurant_id . '/sections' );

        if ( $response == null ) {
            wp_send_json( array( 'error' => esc_html__( 'Can\'t find this restaurant', 'mt-snj' ) ) );
        }

        if ( $response['data'] == null ) {
            wp_send_json( array( 'error' => $response['message'] ) );
        }

        $sections = $response['data'];
        add_option( 'mt-snj-restaurant-sections-' . $restaurant_id, $sections );
    }

    return $sections;
}

function mt_snj_get_reservation_times() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $result       = array();
    $restaurantId = isset( $_REQUEST['restaurantId'] ) ? $_REQUEST['restaurantId'] : '';
    $partySize    = isset( $_REQUEST['partySize'] ) ? $_REQUEST['partySize'] : - 1;
    $date         = isset( $_REQUEST['date'] ) ? $_REQUEST['date'] : '';

    if ( ! $restaurantId ) {
        wp_send_json( array( 'error' => esc_html__( 'Restaurant ID is not set', 'mt-snj' ) ) );
    }

    if ( $partySize < 1 ) {
        wp_send_json( array( 'error' => esc_html__( 'Party size is not set', 'mt-snj' ) ) );
    }

    if ( ! $date ) {
        wp_send_json( array( 'error' => esc_html__( 'Date is not set', 'mt-snj' ) ) );
    }

    // Get restaurant's time zone
    $restaurants = mt_snj_get_restaurants();
    $location    = array(
        'lat' => '',
        'lon' => '',
    );

    foreach ( $restaurants as $restaurant ) {

        if ( $restaurant['id'] == $restaurantId ) {
            $location['lat'] = $restaurant['lat'];
            $location['lon'] = $restaurant['lon'];
            break;
        }
    }

    $usa_timezone = MT_SeatNinja::getUSATimeZone( $location );

    $response = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/reservations/' . $restaurantId . '/availabletimes/' . $date . '/' . $partySize );

    if ( $response == null ) {
        wp_send_json( array( 'error' => esc_html__( 'Can\'t find available times of this restaurant', 'mt-snj' ) ) );
    }

    if ( $response['data'] == null ) {
        wp_send_json( array( 'error' => $response['message'] ) );
    }

    // Get sections
    $sections = mt_snj_get_sections( $restaurantId );

    foreach ( $response['data']['availableTimes'] as $t ) {
        $ts         = array();
        $section_id = $t['diningTableSectionId'];
        $times      = $t['times'];

        foreach ( $sections as $s ) {
            if ( $s['id'] == $section_id ) {
                $ts['section_name'] = $s['name'];
                break;
            }
        }

        $ts['times'] = array();

        foreach ( $times as $time ) {
            $current_time = new DateTime( $time );
            $current_time->setTimezone( $usa_timezone );
            $time_USA      = $current_time->format( 'h:i A' );
            $ts['times'][] = array(
                'text'  => $time_USA,
                'value' => $time,
            );
        }

        $result[] = $ts;
    }

    wp_send_json( $result );
}

add_action( 'wp_ajax_nopriv_get_reservation_times', 'mt_snj_get_reservation_times' );
add_action( 'wp_ajax_get_reservation_times', 'mt_snj_get_reservation_times' );

function mt_snj_booking_reservation() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $restaurantId = isset( $_REQUEST['restaurantId'] ) ? $_REQUEST['restaurantId'] : '';

    $response = MT_SeatNinja::getDataFromApi( 'POST',
        MT_SEATNINJA_API_URL . '/reservations/' . $restaurantId . '/reservation/unauthenticated',
        array(),
        $_REQUEST );

    wp_send_json( $response );
}

add_action( 'wp_ajax_nopriv_booking_reservation', 'mt_snj_booking_reservation' );
add_action( 'wp_ajax_booking_reservation', 'mt_snj_booking_reservation' );

function mt_snj_get_restaurants_for_vc() {

    $restaurants = mt_snj_get_restaurants();
    $restaurants_vc = array();

    foreach ( $restaurants as $restaurant ) {
        $key                    = $restaurant['name'] . ' - ' . $restaurant['id'];
        $restaurants_vc[ $key ] = $restaurant['id'];
    }

    return $restaurants_vc;
}
