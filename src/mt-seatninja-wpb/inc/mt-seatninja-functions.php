<?php

function mt_snj_get_restaurants() {

    $restaurants = array();
    $keys        = MT_SeatNinja::get_snj_keys();

    $result = MT_SeatNinja::getDataFromApi( MT_SEATNINJA_API_URL . '/restaurants',
        array( 'x-api-key:' . $keys['api-key'] ) );

    if ( $result ) {
        $restaurants = $result['data'];
    }

    return $restaurants;
}