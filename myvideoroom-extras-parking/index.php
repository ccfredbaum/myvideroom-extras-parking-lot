<?php
/**
 * My Video Room Parking Lot Plugin Initializer. Will be auto-called by WordPress
 *
 * @package MyVideoRoomExtrasParking
 */

declare(strict_types=1);

/**
 * Plugin Name:         My Video Room ParkingLot
 * Plugin URI:          https://clubcloud.tech
 * Description:         Parking Lot for MVR features and unreleased Software
 * Version:             0.6.7
 * Requires PHP:        7.4
 * Requires at least:   5.6
 * Author:              Fred Baumhardt, Alex Dale, Alec Sammon
 * Author URI:          https://clubcloud.tech/
 * License:             GPLv2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace MyVideoRoomExtrasParking;

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'get_plugin_data' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! class_exists( Plugin::class ) ) {
	/**
	 * Autoloader for classes in the My Video Room Extras Plugin
	 *
	 * @param string $class_name The name of the class to autoload.
	 *
	 * @throws \Exception When file is not found.
	 *
	 * @return boolean
	 */
	function autoloader( string $class_name ): bool {
		if ( strpos( $class_name, 'MyVideoRoomExtrasParking' ) === 0 ) {
			$src_location = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;

			$file_name = str_replace( 'MyVideoRoomExtrasParking\\', '', $class_name );
			$file_name = strtolower( $file_name );

			$file_name = str_replace( '\\', DIRECTORY_SEPARATOR, $file_name ) . '.php';

			$path     = ( pathinfo( $file_name ) );
			$location = realpath( $src_location . $path['dirname'] ) . '/class-' . $path['basename'];

			if ( ! file_exists( $location ) ) {

				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
					trigger_error(
						esc_html( 'Failed to include "' . $src_location . $path['dirname'] . '/class-' . $path['basename'] . '"' ),
						E_USER_ERROR
					);
				}

				return false;
			}

			return (bool) include_once $location;
		}

		return false;
	}

	spl_autoload_register( 'MyVideoRoomExtrasParking\autoloader' );

	add_action( 'plugins_loaded', array( Plugin::class, 'init' ) );
	register_activation_hook( __FILE__, array( Activation::class, 'activate' ) );
	register_uninstall_hook( __FILE__, array( Activation::class, 'uninstall' ) );

	require_once __DIR__ . '/globals.php';
}

