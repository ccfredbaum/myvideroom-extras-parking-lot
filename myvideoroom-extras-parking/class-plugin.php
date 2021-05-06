<?php
/**
 * The entry point for the plugin
 *
 * @package MyVideoRoomExtrasParking
 */

declare( strict_types=1 );

namespace MyVideoRoomExtrasParking;

use MyVideoRoomExtrasParking\Core\MenuHelpers;
use MyVideoRoomExtrasParking\MVR\PageSwitches;
use MyVideoRoomExtrasParking\Core\URLSwitch;

/**
 * Class Plugin
 */
class Plugin {

	const SHORTCODE_PREFIXS = array( 'mvr_', 'cc' );

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		echo 'in constructor of plugin class';
		Factory::get_instance( PageSwitches::class )->install();
		Factory::get_instance( MenuHelpers::class )->install();
		Factory::get_instance( URLSwitch::class )->install();

	}

	/**
	 * Initializer function, returns a instance of the plugin
	 *
	 * @return object
	 */
	public static function init() {
		return Factory::get_instance( self::class );
	}
}
