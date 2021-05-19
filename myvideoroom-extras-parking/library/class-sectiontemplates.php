<?php

/**
 * Display section templates
 *
 * @package MyVideoRoomExtrasPlugin\Library
 */

namespace MyVideoRoomExtrasParking\Library;

use MyVideoRoomExtrasPlugin\Core\SiteDefaults;
use \MyVideoRoomExtrasPlugin\Library\VideoHelpers;
use MyVideoRoomExtrasParking\Factory;
use MyVideoRoomExtrasParking\Shortcode as Shortcode;


/**
 * Class SectionTemplate
 */
class SectionTemplates extends Shortcode {

	/**
	 * Render MVR Specific Upgrade Template - will become the basis for a UMP Module - so include in plugin v1+
	 *
	 * @return string,null
	 */
	public function mvr_ump_wcfm_upgrade_template() {
		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 29585 );
		} else {
			return null;
		}
	}

	/**
	 * Render Booking Center Admin Page template for Bookings -
	 *
	 * @return string
	 */
	public function mvr_ctr_basic_admin_template(): string {
		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 28653 );
		}
	}

	// ---
	// Find and Search PagesTemplates.

	/**
	 * Render Find Pages Signed In user Template
	 *
	 * @return string
	 */
	public function find_signed_in_template(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 29134 );
		}
	}
	/**
	 * Render Find Pages Signed Out user Template
	 *
	 * @return string
	 */
	public function find_signed_out_template(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 31200 );
		}
	}

	// Account Control Panel Center Templates.

	/**
	 * Render Main Dashboard Template for user's own account control panel
	 *
	 * @return string
	 */
	public function account_control_centre_dashboard(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 27468 );
		}
	}
	/**
	 * Render Main Dashboard Alternate (non Owner-Store-Admin) for user's own account control panel
	 *
	 * @return string
	 */
	public function account_control_centre_alternate_dashboard(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 29160 );
		}
	}

	// ---
	// Staff-Lobby Landing Templates

	/**
	 * Render Staff Upgrade Template for staff members who need to activate a subscription
	 *
	 * @return string
	 */
	public function staff_lobby_get_credentials_template(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 22918 );
		}
	}

	/**
	 * Render Staff Upgrade Template for staff members who need to activate a subscription
	 *
	 * @return string
	 */
	public function staff_lobby_sales_landing_template(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 22921 );
		}
	}

}
