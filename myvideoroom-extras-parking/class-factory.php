<?php
/**
 * A factory method to create new objects
 * Allows for easy mocking in tests f
 *
 * @package MyVideoRoomExtrasParking */

declare(strict_types=1);

namespace MyVideoRoomExtrasParking;

/**
 * Class Factory
 */
class Factory {


	/**
	 * The list of cached objects
	 *
	 * @var array
	 */
	protected static $objects = array();

	/**
	 * Get an instance of the required object
	 *
	 * @param string $class_name The class you want to get.
	 * @param array  $params     Optional parameters to pass to the constructor.
	 *
	 * @return object
	 */
	public static function get_instance( string $class_name, array $params = array() ): object {
		if ( isset( self::$objects[ $class_name ] ) ) {
			return self::$objects[ $class_name ];
		}

		return new $class_name( ...$params );
	}

	/**
	 * Inject a instance
	 *
	 * @param string $class_name      The name of the class to inject.
	 * @param object $injected_object The object to inject.
	 */
	public static function inject_object( string $class_name, object $injected_object ) {
		self::$objects [ $class_name ] = $injected_object;
	}

	/**
	 * Reset all injected objects
	 */
	public static function reset() {
		self::$objects = array();
	}
}
