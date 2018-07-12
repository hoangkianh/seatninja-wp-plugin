<?php

function mt_seatninja_restaurant_selectbox () {

    $html = array();

    $restaurants = mt_snj_get_restaurants();
    $html[] = '<label for="restaurants-select">' . esc_html__('Select a restaurant', 'mt-snj') . '</label>';
    $html[] = '<select id="restaurants-select">';
    $html[] = '<option id="-1">---</option>';

    if ( ! empty( $restaurants ) ) {

        foreach ( $restaurants as $restaurant ) {
            $html[] = '<option value="' . $restaurant['id'] . '">' .  $restaurant['name'] . ' - ' . $restaurant['id'] . '</option>';
        }
    }

    $html[] = '</select>';

    return implode( '', $html );
}

function mt_seatninja_partysize_selectbox () {

    $html = array();

    $html[] = '<label for="party-size">' . esc_html__('Party Size', 'mt-snj') . '</label>';
    $html[] = '<select id="party-size">';
    $html[] = '<option value="-1">---</option>';
    for ($i = 4; $i <= 12; $i++) {
        $html[] = '<option value="' . $i . '">' . esc_html__( 'Party of ', 'mt-snj' ) . $i . '</option>';
    }
    $html[] = '</select>';

    return implode( '', $html );
}

function mt_seatninja_date_picker () {

    $html = array();

    $html[] = '<label for="party-size">' . esc_html__('Date', 'mt-snj') . '</label>';
    $html[] = '<input id="datetimepicker" type="text" >';

    return implode( '', $html );
}
