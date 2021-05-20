<?php
/**
 * Addon functionality for Site Video Room
 *
 * @package MyVideoRoomExtrasPlugin\Modules\SiteVideo
 */

namespace MyVideoRoomExtrasPlugin\Modules\WooCommerceBookings;

use MyVideoRoomExtrasPlugin\Shortcode as Shortcode;
use \MyVideoRoomPlugin\SiteDefaults;
use MyVideoRoomPlugin\Library\VideoHelpers;
use MyVideoRoomPlugin\Entity\UserVideoPreference;
use MyVideoRoomExtrasPlugin\Factory;
use MyVideoRoomExtrasPlugin\Modules\MVRPersonalMeeting\MVRPersonalMeeting;
use MyVideoRoomExtrasPlugin\Modules\MVRPersonalMeeting\Library\MVRPersonalMeetingControllers;

/**
 * Class WooCommerceBookingsViews - Renders the Video Views and Templates for WooCommerce Bookings.
 */
class WooCommerceBookingsViews extends Shortcode {

	/**
	 * Render form when no booking is found
	 *
	 * @return string
	 */
	public function no_bookings_found_form(): string {
		return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 24508 );
	}

	// ---
	// Booking Center Template Section.
	// Used for Action Centre Template Selection.

	/**
	 * Render Booking Center Admin Page template for bookings -
	 *
	 * @return string
	 */
	public function booking_ctr_site_admin_template(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 28653 );
		}

		// Check for WCFM - If exists WCFM Template - Else Normal User Signed In Template.
		if ( Factory::get_instance( SiteDefaults::class )->is_wcfm_active() ) {
			// @TODO make template for Store Owners as Well
			echo 'StoreOwnerTemplate is on the TODO List';
		} else {
			return $this->booking_center_signedin_template();
		}
	}

	/**
	 * Render Booking and Meet Center Normal Signed In Template.
	 *
	 * @return string
	 */
	public function booking_center_signedin_template(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 30955 );
		}

		wp_enqueue_style( 'cc-menutab', plugins_url( '/stylesheets/menu-tab.css', __FILE__ ) );
		$user_id = get_current_user_id();
		?>
		<script type="text/javascript">
			function activateTab(pageId) {
				var tabCtrl = document.getElementById('tabCtrl');
				var pageToActivate = document.getElementById(pageId);
				for (var i = 0; i < tabCtrl.childNodes.length; i++) {
					var node2 = tabCtrl.childNodes[i];
					if (node2.nodeType == 1) {
						/* Element */
						node2.style.display = (node2 == pageToActivate) ? 'block' : 'none';
					}
				}
			}
		</script>
		<ul class="menu">
			<a class="cc-menu-header" href="javascript:activateTab( 'page1' )"><?php esc_html_e( 'Video Booking', 'my-video-room' ); ?></a>
			<a class="cc-menu-header" href="javascript:activateTab( 'page2' )"><?php esc_html_e( 'Join Meeting', 'my-video-room' ); ?></a>
			<a class="cc-menu-header" href="javascript:activateTab( 'page3' )"><?php esc_html_e( 'Host a Meeting', 'my-video-room' ); ?></a>
			<a class="cc-menu-header" href="javascript:activateTab( 'page4' )"><?php esc_html_e( 'Host Settings', 'my-video-room' ); ?></a>
		</ul>
		<div id="tabCtrl">
			<div id="page1" style="display: block;">
			<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already sanitised output.
			echo Factory::get_instance( Connect::class )->connect();
			?>
			</div>
			<div id="page2" style="display: none;">
			<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already sanitised output.
			echo Factory::get_instance( MVRPersonalMeetingControllers::class )->personal_meeting_guest_shortcode();
			?>
			</div>
			<div id="page3" style="display: none;">
			<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already sanitised output.
			echo Factory::get_instance( MVRPersonalMeetingControllers::class )->personal_meeting_host_shortcode();
			?>
			</div>
			<div id="page4" style="display: none;">
				<?php
				$layout_setting = Factory::get_instance( UserVideoPreference::class )->choose_settings( $user_id, MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING, array( 'basic', 'premium' ) );
				//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already sanitised output.
				echo $layout_setting;
				?>
			</div>
		</div>

		<?php
		return '';
	}

	/**
	 * Render Booking Center Store Owner Page template for bookings.
	 *
	 * @return string
	 */
	public function booking_ctr_store_owner_template(): string {

		// Filter MVR - suitable for Elementor Template Change.
		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 28637 );
		}

		// Check for WCFM - If exists WCFM Template - Else Normal User Signed In Template.
		if ( Factory::get_instance( SiteDefaults::class )->is_wcfm_active() ) {
			// @TODO make template for Store Owners as Well
			echo 'StoreOwnerTemplate is on the TODO List';
		} else {
			return $this->booking_center_signedin_template();
		}
	}

	/**
	 * Render Booking Center Request Booking Number Form -
	 *
	 * @return string
	 */
	public function booking_ctr_request_booking_number_form(): string {
		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 30831 );
		}
		wp_enqueue_style( 'cc-booking-ctr', plugins_url( '/stylesheets/booking-ctr.css', __FILE__ ) );
		?>
		<div class="mvr-row">
			<h2 class="mvr-reception-header"><?php esc_html_e( 'Please Enter Your Booking Number ', 'my-video-room' ); ?></h2>

			<table style="width:100%">
				<tr>
					<th style="width:50%"><img src="
										<?php
										// Get ClubCloud Logo from Plugin folder for Form, or use Site Logo if loaded in theme.
										$custom_logo_id = get_theme_mod( 'custom_logo' );
										$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
										if ( ! $image ) {
											$image = plugins_url( '/images/logoCC-clear.png', __FILE__ );
											echo $image;
										} else {
											echo $image[0];
										}
										?>
										" alt="Site Logo"></th>
					<th>
						<form action="">
							<label for="host"><?php esc_html_e( 'My Booking Number:', 'my-video-room' ); ?> </label>
							<input type="text" id="booking" name="booking">
							<p class="cc-title-label"><?php esc_html_e( 'This is the number you received when making your booking, check email and purchase confirmation details (and Junk Mail folder) ', 'my-video-room' ); ?></p>
							<input type="submit" value="Submit">
						</form>

					</th>

				</tr>

			</table>
		</div>
		<?php
		return '';
	}
	/**
	 * Render Booking Center Signed Out Page template for bookings -
	 *
	 * @return string
	 */
	public function booking_ctr_signed_out_template(): string {

		if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
			return Factory::get_instance( VideoHelpers::class )->call_elementor_template( 28648 );
		}

		wp_enqueue_style( 'cc-menutab', plugins_url( '/stylesheets/menu-tab.css', __FILE__ ) );
		?>
		<script type="text/javascript">
			function activateTab(pageId) {
				var tabCtrl = document.getElementById('tabCtrl');
				var pageToActivate = document.getElementById(pageId);
				for (var i = 0; i < tabCtrl.childNodes.length; i++) {
					var node2 = tabCtrl.childNodes[i];
					if (node2.nodeType == 1) {
						/* Element */
						node2.style.display = (node2 == pageToActivate) ? 'block' : 'none';
					}
				}
			}
		</script>
		<ul class="menu">
			<a class="cc-menu-header" href="javascript:activateTab( 'page1' )"><?php esc_html_e( 'Video Booking', 'my-video-room' ); ?></a>
			<a class="cc-menu-header" href="javascript:activateTab( 'page2' )"<?php esc_html_e( 'Meet', 'my-video-room' ); ?>></a>
			<a class="cc-menu-header" href="javascript:activateTab( 'page3' )"><?php esc_html_e( 'Sign In', 'my-video-room' ); ?></a>
		</ul>
		<div id="tabCtrl">
			<div id="page1" style="display: block;">
			<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already sanitised output.
			echo Factory::get_instance( Connect::class )->connect();
			?>
			</div>
			<div id="page2" style="display: none;">
			<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already sanitised output.
			echo Factory::get_instance( VideoControllers::class )->personal_meeting_guest_shortcode();
			?>
			</div>
			<div id="page3" style="display: none;">
			<?php
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped -- Function already sanitised output.
			echo 'PlaceHolder for Login Form';
			?>
			</div>
		</div>
		</div>

		<?php
		return '';
	}

}
