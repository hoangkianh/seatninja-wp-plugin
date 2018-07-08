<?php
/**
 * Plugin Name: SeatNinja for WPBakery PageBuilder
 * Plugin URI: https://wpmortar.com/mt-seatninja-wpb
 * Description: This plugin makes it easy to create reservation pages for your restaurant. All data is taken from seatninja.com. You can use its elements for WPBakery Page Builder, also included 3 themes, or you can customize very easily. You can increase the number of customers of your restaurant with our plugin!
 * Version: 1.0
 * Author: WPMortar
 * Author URI: https://wpmortar.com/
 * License: GPL-3.0
 * Domain: mt-snj
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MT_SEATNINJA_API_URL', 'https://sandbox.seatninja.com' );
define( 'MT_SEATNINJA_PATH', plugin_dir_url( __FILE__ ) );
define( 'MT_SEATNINJA_DIR', dirname( __FILE__ ) );


if ( ! class_exists( 'MT_SeatNinja' ) ) {

    class MT_SeatNinja {

        public function __construct() {
            add_action( 'init', array( $this, 'load_textdomain' ), 10 );

            if ( is_admin() ) {
                add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
                add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
                add_action( 'wp_ajax_nopriv_mt_snj_save_settings',
                    array( $this, 'mt_snj_save_settings' ) );
                add_action( 'wp_ajax_mt_snj_save_settings',
                    array( $this, 'mt_snj_save_settings' ) );
            } else {

            }

            include_once( MT_SEATNINJA_DIR . '/inc/shortcode-mt-seatninja.php' );
            include_once( MT_SEATNINJA_DIR . '/inc/mt-seatninja-functions.php' );
        }

        /**
         * Load text domain
         */
        public function load_textdomain() {
            $dir = trailingslashit( WP_LANG_DIR );
            load_plugin_textdomain( 'mt-snj', false, $dir . 'plugins' );
        }

        /**
         * Add Page
         */
        public function register_admin_menu() {

            global $submenu;

            if ( is_multisite() && ! current_user_can( 'manage_network_options' ) ) {
                return;
            } else {
                $capability = 'manage_network_options';
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            } else {
                $capability = 'manage_options';
            }

            add_menu_page( esc_html__( 'Seat Ninja', 'mt-snj' ),
                esc_html__( 'Seat Ninja', 'mt-snj' ),
                $capability,
                'mt-snj-general',
                array( $this, 'mt_snj_general_page' ),
                'dashicons-carrot' );

            add_submenu_page( 'mt-snj-general',
                esc_html__( 'About', 'mt-snj' ),
                esc_html__( 'About', 'mt-snj' ),
                $capability,
                'mt-snj-about',
                array( $this, 'mt_snj_about_page' ) );

            $submenu['mt-snj-general'][0][0] = esc_html__( 'General Settings', 'mt-snj' );
        }

        public function mt_snj_general_page() {
            include_once( MT_SEATNINJA_DIR . '/inc/page-settings.php' );
        }

        public function admin_enqueue_scripts() {
            wp_enqueue_style( 'growl-js', MT_SEATNINJA_PATH . 'assets/libs/growl/jquery.growl.css' );
            wp_enqueue_script( 'growl-js',
                MT_SEATNINJA_PATH . 'assets/libs/growl/jquery.growl.min.js',
                null,
                null,
                true );

            wp_enqueue_style( 'mt-seatninja-wpb', MT_SEATNINJA_PATH . 'assets/css/mt-seatninja-wpb.css' );

            wp_enqueue_script( 'mt-seatninja-wpb',
                MT_SEATNINJA_PATH . 'assets/js/mt-seatninja-wpb.js',
                null,
                null,
                true );

            $keys = self::get_snj_keys();

            wp_localize_script( 'mt-seatninja-wpb',
                'mtSeatNinja',
                array(
                    'ajax_url'       => esc_url( admin_url( 'admin-ajax.php' ) ),
                    'ajax_nonce'     => wp_create_nonce( 'mt-seatninja-wpb' ),
                    'api_key'        => $keys['api-key'],
                    'customer_token' => $keys['customer-token'],
                ) );
        }

        public function mt_snj_save_settings() {

            check_ajax_referer( 'mt-seatninja-wpb', 'nonce' );

            if ( ! isset( $_POST['snj_keys'] ) ) {
                wp_send_json_error( 'Data is not sent' );
            }

            update_option( 'mt-snj-keys', $_POST['snj_keys'] );
            wp_send_json_success( 'Saved successfully' );
        }

        public static function get_snj_keys() {
            $snj_keys = get_option( 'mt-snj-keys' );
            $keys     = array();

            foreach ( $snj_keys as $key ) {
                $keys[ $key['name'] ] = $key['value'];
            }

            return $keys;
        }

        public static function getDataFromApi( $url, $args ) {

            $curl = curl_init();

            curl_setopt_array( $curl,
                array(
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 30,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'GET',
                    CURLOPT_HTTPHEADER     => $args
                ) );

            $response = curl_exec( $curl );
            $err      = curl_error( $curl );

            curl_close( $curl );

            if ( $err ) {
                $result = json_decode( $err, true );
            } else {
                $result = json_decode( $response, true );
            }

            return $result;
        }
    }

    new MT_SeatNinja();
}