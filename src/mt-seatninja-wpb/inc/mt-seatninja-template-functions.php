<?php

function mt_seatninja_restaurant_selectbox ($label = true) {

    $html = array();

    $restaurants = mt_snj_get_restaurants();

    if ( $label ) {
        $html[] = '<label for="restaurants-select">' . esc_html__('Select a restaurant', 'mt-snj') . '</label>';
    }

    $html[] = '<select class="restaurant-id">';
    $html[] = '<option id="-1">---</option>';

    if ( ! empty( $restaurants ) ) {

        foreach ( $restaurants as $restaurant ) {
            $html[] = '<option value="' . $restaurant['id'] . '">' .  $restaurant['name'] . '</option>';
        }
    }

    $html[] = '</select>';

    return implode( '', $html );
}

function mt_seatninja_partysize_selectbox ($label = true) {

    $html = array();

    if ( $label ) {
        $html[] = '<label for="party-size">' . esc_html__('Party Size', 'mt-snj') . '</label>';
    }

    $html[] = '<select class="party-size">';
    $html[] = '<option value="-1">---</option>';

    $html[] = '</select>';

    return implode( '', $html );
}

function mt_seatninja_date_picker ($label = true) {

    $html = array();

    if ( $label ) {
        $html[] = '<label for="datetimepicker">' . esc_html__('Date', 'mt-snj') . '</label>';
    }

    $html[] = '<input class="datepicker" type="text" >';

    return implode( '', $html );
}

function mt_seatninja_time_picker ($label = true) {

    $html = array();

    if ( $label ) {
        $html[] = '<label for="timepicker">' . esc_html__('Time', 'mt-snj') . '</label>';
    }

    $html[] = '<input class="timeepicker" type="text" >';

    return implode( '', $html );
}
