<?php
/**
 * Abstract class for all shortcodes
 *
 * @package MyVideoRoomExtrasParking
 */

declare(strict_types=1);

namespace MyVideoRoomExtrasParking;

/**
 * Abstract Shortcode
 */
abstract class Shortcode {


	/**
	 * Registers a shortcode in WordPress
	 *
	 * @param string   $tag The suffix of the tag.
	 * @param callable $callback The callback to render the shortcode.
	 */
	protected function add_shortcode( string $tag, callable $callback ) {
		foreach ( Plugin::SHORTCODE_PREFIXS as $prefix ) {
			add_shortcode(
				$prefix . $tag,
				$callback
			);
		}
	}

	/**
	 * Get the current version of the installed plugin
	 * Used for cache-busting.
	 *
	 * @return string
	 */
	protected function get_plugin_version(): string {
		$plugin_data = get_plugin_data( __DIR__ . '/../index.php' );

		return $plugin_data['Version'] . '-' . time(); // @TODO - Remove this cachebuster
	}

	/**
	 * Get an instance of the required object
	 *
	 * @param string $class_name The class you want to get.
	 * @param array  $params     Optional parameters to pass to the constructor.
	 *
	 * @return object
	 */
	protected function get_instance( string $class_name, array $params = array() ): object {
		return Factory::get_instance( $class_name, $params );
	}
}
