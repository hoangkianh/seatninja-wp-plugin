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

define( 'MT_SEATNINJA_PATH', plugin_dir_url( __FILE__ ) );
define( 'MT_SEATNINJA_DIR', dirname( __FILE__ ) );


if ( ! class_exists( 'MT_SeatNinja' ) ) {

	class MT_SeatNinja {

		public function __construct() {
			add_action( 'init', array( $this, 'load_textdomain' ), 10 );
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
				array( $this, 'mt_seat_ninja_general_page' ),
				'dashicons-carrot' );

			add_submenu_page( 'mt-snj-general',
				esc_html__( 'About', 'mt-snj' ),
				esc_html__( 'About', 'mt-snj' ),
				$capability,
				'mt-snj-about',
				array( $this, 'mt_snj_about_page' ) );

			$submenu['mt-snj-general'][0][0] = esc_html__( 'General Settings', 'mt-snj' );
		}

		public function mt_seat_ninja_general_page() {
			include_once( MT_SEATNINJA_DIR . '/inc/pages/settings.php' );
		}

		public function enqueue_scripts() {

		}
	}

	new MT_SeatNinja();
}