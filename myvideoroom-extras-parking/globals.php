<?php
/**
 * My Video Room Extras Globals.
 * These are legacy global functions that were exported by the plugin
 *
 * @package MyVideoRoomExtrasParking
 */

declare(strict_types=1);

use MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress;
use MyVideoRoomExtrasPlugin\Core\FiltersUtilities;
use MyVideoRoomExtrasParking\Core\MenuHelpers;
use MyVideoRoomExtrasParking\Core\SiteDefaults;
use MyVideoRoomExtrasPlugin\Core\URLSwitch;
use MyVideoRoomExtrasPlugin\Modules\WCFM\WCFMHelpers;
use MyVideoRoomExtrasPlugin\Admin\Setup\RoomAdmin;


if ( ! function_exists( 'cc_staff_to_parent' ) ) {
	/**
	 * Wrapper for legacy global cc_staff_to_parent function
	 *
	 * @return int|mixed|null
	 */
	function cc_defaults() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_defaults called', E_USER_DEPRECATED );
		}

		$site_defaults = new SiteDefaults();
		return $site_defaults->defaults( ...func_get_args() );
	}
}


if ( ! function_exists( 'cc_setup_group_nav' ) ) {
	/**
	 * Wrapper for legacy global cc_setup_group_nav function
	 */
	function cc_setup_group_nav() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_setup_group_nav called', E_USER_DEPRECATED );
		}

		$buddy_press = new BuddyPress();
		return $buddy_press->setup_group_nav_action( ...func_get_args() );
	}
}


if ( ! function_exists( 'cc_store_visitor' ) ) {
	/**
	 * Wrapper for legacy global cc_setup_group_nav function
	 */
	function cc_storevisitor() {

		$menu_helpers = new MenuHelpers();
		return $menu_helpers->store_visitor( ...func_get_args() );
	}
}



if ( ! function_exists( 'cc_orphaned_page_notice' ) ) {
	/**
	 * Wrapper for legacy global cc_orphaned_page_notice function
	 */
	function cc_orphaned_page_notice() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_setup_group_nav called', E_USER_DEPRECATED );
		}

		$room_admin = new RoomAdmin();
		return $room_admin->setup_group_nav_action( ...func_get_args() );
	}
}



if ( ! function_exists( 'cc_add_menu_endpoint' ) ) {
	/**
	 * Wrapper for legacy global cc_add_menu_endpoint function
	 */
	function cc_add_menu_endpoint() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_add_menu_endpoint called', E_USER_DEPRECATED );
		}

		$filters_utilities = new FiltersUtilities();
		return $filters_utilities->add_menu_endpoint_action( ...func_get_args() );
	}
}

if ( ! function_exists( 'cc_ismerchant_check' ) ) {
	/**
	 * Wrapper for legacy global cc_ismerchant_check function
	 */
	function cc_ismerchant_check() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_ismerchant_check called', E_USER_DEPRECATED );
		}

		$wcfm_helpers = new WCFMHelpers();
		return $wcfm_helpers->ismerchant_check( ...func_get_args() );
	}
}

if ( ! function_exists( 'cc_headerlink' ) ) {
	/**
	 * Wrapper for legacy global cc_headerlink function
	 */
	function cc_headerlink() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_headerlink called', E_USER_DEPRECATED );
		}

		$menu_helpers = new MenuHelpers();
		return $menu_helpers->header_link( ...func_get_args() );
	}
}

if ( ! function_exists( 'cc_headerlink_view' ) ) {
	/**
	 * Wrapper for legacy global cc_headerlink_view function
	 */
	function cc_headerlink_view() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_headerlink_view called', E_USER_DEPRECATED );
		}

		$menu_helpers = new MenuHelpers();
		return $menu_helpers->header_link_view( ...func_get_args() );
	}
}


if ( ! function_exists( 'cc_picturelink' ) ) {
	/**
	 * Wrapper for legacy global cc_picturelink function
	 */
	function cc_picturelink() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_picturelink called', E_USER_DEPRECATED );
		}

		$menu_helpers = new MenuHelpers();
		return $menu_helpers->picture_link( ...func_get_args() );
	}
}


if ( ! function_exists( 'cc_getnameurl_worker' ) ) {
	/**
	 * Wrapper for legacy global cc_getnameurl_worker function
	 */
	function cc_getnameurl_worker() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_getnameurl_worker called', E_USER_DEPRECATED );
		}

		$url_switch = new URLSwitch();
		return $url_switch->get_name_url( ...func_get_args() );
	}
}

if ( ! function_exists( 'cc_menuname' ) ) {
	/**
	 * Wrapper for legacy global cc_menuname function
	 */
	function cc_menuname() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_menuname called', E_USER_DEPRECATED );
		}

		$menu_helpers = new MenuHelpers();
		return $menu_helpers->menu_name( ...func_get_args() );
	}
}

if ( ! function_exists( 'cc_menuname_reverse' ) ) {
	/**
	 * Wrapper for legacy global cc_menuname function
	 */
	function cc_menuname_reverse() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_menuname_reverse called', E_USER_DEPRECATED );
		}

		$menu_helpers = new MenuHelpers();
		return $menu_helpers->menu_name_reversed( ...func_get_args() );
	}
}

if ( ! function_exists( 'cc_picturelink_view' ) ) {
	/**
	 * Wrapper for legacy global cc_picturelink_view function
	 */
	function cc_picturelink_view() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Legacy cc_picturelink_view called', E_USER_DEPRECATED );
		}

		$menu_helpers = new MenuHelpers();
		return $menu_helpers->picture_link_view( ...func_get_args() );
	}
}



