<?php

function mt_snj_get_restaurants() {

    $restaurants = get_option( 'mt-snj-restaurants' );

    if ( ! $restaurants || empty( $restaurants ) ) {

        $response = MT_SeatNinja::getDataFromApi( 'GET',
            MT_SEATNINJA_API_URL . '/restaurants' );

        if ( $response ) {
            $restaurants = $response['data'];
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

    $response = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/restaurant/' . $id . '/details' );

    if ( $response == null ) {
        wp_send_json( array( 'error' => esc_html__( 'Can\'t find this restaurant', 'mt-snj' ) ) );
    }

    if ( $response['data'] == null ) {
        wp_send_json( array( 'error' => $response['message'] ) );
    }

    wp_send_json( $response );
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

        $response = MT_SeatNinja::getDataFromApi( 'GET',
            MT_SEATNINJA_API_URL . '/restaurant/' . $id . '/profile' );

        if ( $response == null ) {
            wp_send_json( array( 'error' => esc_html__( 'Can\'t find this restaurant', 'mt-snj' ) ) );
        }

        if ( $response['data'] == null ) {
            wp_send_json( array( 'error' => $response['message'] ) );
        }

        $profile = $response['data'];
        update_option( 'mt-snj-restaurant-profile-' . $id, $profile );
    }

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
        update_option( 'mt-snj-restaurant-sections-' . $restaurant_id, $sections );
    }

    return $sections;
}

function mt_snj_get_reservation_times() {

    check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

    $result    = array();
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

    // Get restaurant's time zone
    $restaurants = mt_snj_get_restaurants();
    $location    = array(
        'lat' => '',
        'lon' => '',
    );

    foreach ( $restaurants as $restaurant ) {

        if ( $restaurant['id'] == $id ) {
            $location['lat'] = $restaurant['lat'];
            $location['lon'] = $restaurant['lon'];
            break;
        }
    }

    $usa_timezone = MT_SeatNinja::getUSATimeZone( $location );

    $response = MT_SeatNinja::getDataFromApi( 'GET',
        MT_SEATNINJA_API_URL . '/reservations/' . $id . '/availabletimes/' . $date . '/' . $partySize );

    if ( $response == null ) {
        wp_send_json( array( 'error' => esc_html__( 'Can\'t find available times of this restaurant', 'mt-snj' ) ) );
    }

    if ( $response['data'] == null ) {
        wp_send_json( array( 'error' => $response['message'] ) );
    }

    // Get sections
    $sections = mt_snj_get_sections( $id );

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
            $time_USA      = $current_time->format( 'H:i' );
            $date_USA  = $current_time->format( 'Y-m-d' );
            $ts['times'][] = array(
                'text'  => $time_USA,
                'value' => $date_USA . 'T' . $time_USA . ':00.000Z'
            );
        }

        $result[] = $ts;
    }

    wp_send_json( $result );
}

add_action( 'wp_ajax_nopriv_get_reservation_times', 'mt_snj_get_reservation_times' );
add_action( 'wp_ajax_get_reservation_times', 'mt_snj_get_reservation_times' );
