<?php
/**
 * Shortcodes for URLS
 *
 * @package MyVideoRoomExtrasPlugin\Core
 */

namespace MyVideoRoomExtrasParking\Core;

use MyVideoRoomExtrasPlugin\Modules\UltimateMembershipPro\MembershipLevel;
use MyVideoRoomExtrasPlugin\Library\UserRoles;
use MyVideoRoomExtrasPlugin\Modules\MVRPersonalMeeting\MVRPersonalMeeting;
use MyVideoRoomExtrasParking\Shortcode as Shortcode;
use MyVideoRoomExtrasPlugin\Shortcode\UserVideoPreference;
use MyVideoRoomExtrasPlugin\Modules\WooCommerceBookings\WCHelpers;
use MyVideoRoomExtrasPlugin\Modules\WCFM\WCFMHelpers;

/**
 * Class URLSwitch
 */
class URLSwitch extends Shortcode {


	/**
	 * Install the shortcode
	 */
	public function install() {
		// A Shortcode to Return URL Sequences for Menus
		// This is meant to be the new universal formatting invite list

		// Shortcode Wrapper Function

		// Main Function

		$this->add_shortcode( 'nameurl', array( $this, 'get_name_url_shortcode' ) );
	}

	public function get_name_url( $pre, $type, $post ) {

		$user       = wp_get_current_user();
		$user_roles = $this->get_instance( UserRoles::class );

		?>

		<script type="text/javascript">
			var removeHeader = function() {
				$iframe = jQuery("#iframe1");

				$iframe.css( 'visibility', 'visible' );

				var $head = $iframe.contents().find("head");
				var css = '<style type="text/css">' +
					'.yz-account-header{margin-bottom: 35px; background-color: #fff; display: none!important;}' +
					'</style>';
				jQuery( $head).append(css);
			};
		</script>

		<?php /*For Widgets  */ ?>

		<script type="text/javascript">
			var removeHeader2 = function() {
				$iframe = jQuery("#iframe2");

				$iframe.css( 'visibility', 'visible' );

				var $head = $iframe.contents().find("head");
				var css = '<style type="text/css">' +
					'.yz-account-settings-menu .yz-account-menu { display: none!important;} ' +
					'.yz-head-buttons {display: none!important;} '+
					'.elementor-33677:not(.elementor-motion-effects-element-type-background), .elementor-33677 > .elementor-motion-effects-container > .elementor-motion-effects-layer {background-color: #FFFFFF; display: none;}'+
					'</style>';
				jQuery( $head).append(css);
			};
		</script>

		<?php /*For Widgets  */ ?>

		<script type="text/javascript">
			var removeHeader3 = function() {
				$iframe = jQuery("#iframe3");

				$iframe.css( 'visibility', 'visible' );

				var $head = $iframe.contents().find("head");
				var css = '<style type="text/css">' +
					'#site-header {  display: none;}' +
					'.yz-head-buttons {display: none!important;} '+
					'.elementor-33677:not(.elementor-motion-effects-element-type-background), .elementor-33677 > .elementor-motion-effects-container > .elementor-motion-effects-layer {background-color: #FFFFFF; display: none;}'+
					'</style>';
				jQuery( $head).append(css);
			};
		</script>


		<?php

		switch ( $type ) {
			case '1':
				switch ( true ) {
					case $user_roles->is_wcfm_vendor():
						$store_user = \wcfmmp_get_store( $user->ID );
						$store_info = $store_user->get_shop_info();

						return $pre . $store_info['store_slug'] . $post;

					case $user_roles->is_wcfm_shop_staff():
						$parent_id  = $user->_wcfm_vendor;
						$store_user = \wcfmmp_get_store( $parent_id );
						$store_info = $store_user->get_shop_info();

						return $pre . $store_info['store_slug'] . $post;
					default:
						// If they aren't a vendor then we simply return User Login.
						return $pre . $user->user_login . $post;
				}

			case '2':
				// Case 2 is about Setting Breakout Room Settings for Premium Users As its called by premium Store area.

				switch ( true ) {
					case $user_roles->is_wcfm_vendor():
						$store_user = \wcfmmp_get_store( $user->ID );
						$store_info = $store_user->get_shop_info();

						return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1 " src="/users/' . $store_info['store_slug'] . '/profile/edit/group/4/" width="1600px" height="900px" frameborder="0" scrolling="no" align="left"> </iframe>';

					case $user_roles->is_wcfm_shop_staff():
						$parent_id  = $user->_wcfm_vendor;
						$store_user = \wcfmmp_get_store( $parent_id );
						$store_info = $store_user->get_shop_info();

						return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/4/" width="1600px" height="900px" frameborder="0" scrolling="no" align="left"> </iframe>';

					default:
						// If they aren't a vendor then we simply return User Login
						return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $user->user_login . '/profile/edit/group/4/" width="1600px" height="900px" frameborder="0" scrolling="no" align="left"> </iframe>';

				}

			case '3':
				// Case for separating merchant owners and users editing by simple role. Case 4 is more advanced.

				switch ( true ) {

					case $user_roles->is_wcfm_vendor():
						$store_user = \wcfmmp_get_store( $user->ID );
						$store_info = $store_user->get_shop_info();
						return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/2/" width="1600px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';

					case $user_roles->is_wcfm_shop_staff():
						return '<div style="font-size:1.5em;color:black">Staff Members cannot Access Store Video Appearance <br></div>';

					default:
						// If they aren't a vendor then we simply return User Login
						return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $user->user_login . '/profile/edit/group/2/" width="1600px" height="900px" frameborder="0" scrolling="yes" align="left"></iframe>';

				}

			case '4':
				// Returning Basic or Premium Room Type Configuration Windows from Calls from Buttons.
				// Case 4 is for Storefront Video/Room Main Room
				$membership_levels = \get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
							$store_user = \wcfmmp_get_store( $user->ID );
							$store_info = $store_user->get_shop_info();
							return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/2/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
						case MembershipLevel::PREMIUM:
							$store_user = \wcfmmp_get_store( $user->ID );
							$store_info = $store_user->get_shop_info();
							return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/2/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
						case MembershipLevel::BASIC:
							$store_user = \wcfmmp_get_store( $user->ID );
							$store_info = $store_user->get_shop_info();
							return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/6/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
						case MembershipLevel::PLATINUM:
						case MembershipLevel::DIAMOND:
						case MembershipLevel::SITE_ADMIN:
						case MembershipLevel::AMBASSADOR:
							break;
						case MembershipLevel::VENDOR_STAFF:
							return '<div style="font-size:1.5em;color:black">Staff/Child Account Members cannot Access ' . $this->get_instance( SiteDefaults::class )->display_defaults( 'video_storefront' ) . ' Appearance <br></div>';
					}
				}

				switch ( true ) {
					case $user_roles->is_wcfm_vendor():
						$store_user = wcfmmp_get_store( $user->ID );
						$store_info = $store_user->get_shop_info();
						return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/2/" width="1600px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';

					case $user_roles->is_wcfm_shop_staff():
						return '<div style="font-size:1.5em;color:black">Staff Members cannot Access Store Video Appearance <br></div>';

					default:
						// If they aren't a vendor then we simply return User Login.
						return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $user->user_login . '/profile/edit/group/2/" width="1600px" height="900px" frameborder="0" scrolling="yes" align="left"></iframe>';
				}
				// Returning Basic or Premium Personal Boardroom Type Configuration Windows from Calls from Buttons.
				// Case PBR is for Storefront Video/Room Main Room
			case 'pbr':
				$membership_levels = get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
							$layout_setting = $this->get_instance( UserVideoPreference::class )->choose_settings(
								$user->ID,
								MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING,
								array( 'basic', 'premium' )
							);

							$store_user = \wcfmmp_get_store( $user->ID );
							$store_info = $store_user->get_shop_info();

							return $layout_setting;
							// MVR_ change backup return $layout_setting . '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/4/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
							break;
						case MembershipLevel::PREMIUM:
							$layout_setting = $this->get_instance( UserVideoPreference::class )->choose_settings(
								$user->ID,
								MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING,
								array( 'basic', 'premium' )
							);

							$store_user = \wcfmmp_get_store( $user->ID );
							$store_info = $store_user->get_shop_info();

							return $layout_setting;
							// MVR_ change backup return $layout_setting . '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/4/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
							break;
						case MembershipLevel::BASIC:
							$layout_setting = $this->get_instance( UserVideoPreference::class )->choose_settings(
								$user->ID,
								MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING,
								array( 'basic', 'premium' )
							);

							$store_user = \wcfmmp_get_store( $user->ID );
							$store_info = $store_user->get_shop_info();
							return $layout_setting;
							// return $layout_setting . '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/9/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
							break;

						// @TODO Fred- fix this membership logic
						case MembershipLevel::VENDOR_STAFF:
							$parent_id = $user->_wcfm_vendor;
							if ( $this->get_instance( SiteDefaults::class )->is_premium_check( $parent_id ) == true ) {
								return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $user->user_nicename . '/profile/edit/group/4/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
							} else {
								return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $user->user_nicename . '/profile/edit/group/9/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>'; }
					}
				}

				// Handling Admin Users.
				if ( $user_roles->is_wordpress_administrator() ) {
					$layout_setting = $this->get_instance( UserVideoPreference::class )->choose_settings(
						$user->ID,
						MVRPersonalMeeting::ROOM_NAME_PERSONAL_MEETING,
						array( 'basic', 'premium' )
					);

					return $layout_setting;
					// . '<iframe style="visibility: hidden; background-color:white;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $user->user_nicename . '/profile/edit/group/4/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
				}
				break;

			case 'store_switch':
				$membership_levels = get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
							$store_user = \wcfmmp_get_store( $user->ID );
							$store_info = $store_user->get_shop_info();
							return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/5/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
							break;
						case MembershipLevel::PREMIUM:
							$store_user = \wcfmmp_get_store( $user->ID );
							$store_info = $store_user->get_shop_info();
							return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/5/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';

						case MembershipLevel::BASIC:
							// Not needed at present as they get BP scenario.
							break;
						case MembershipLevel::PLATINUM:
						case MembershipLevel::DIAMOND:
						case MembershipLevel::SITE_ADMIN:
						case MembershipLevel::AMBASSADOR:
							break;
						case MembershipLevel::VENDOR_STAFF:
							$store_id   = $this->get_instance( WCFMHelpers::class )->staff_to_parent( get_current_user_id() );
							$store_user = \wcfmmp_get_store( $store_id );
							$store_info = $store_user->get_shop_info();
							return '<iframe style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $store_info['store_slug'] . '/profile/edit/group/5/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
							break;
					}
				}

				// Handling Admin Users.
				if ( $user_roles->is_wordpress_administrator() ) {
					return '<iframe style="visibility: hidden; background-color:white;" onload="removeHeader()" name="iframe1" id="iframe1" src="/users/' . $user->user_nicename . '/profile/edit/group/5/" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';
				}
				break;

			case 'owner_picture':
				$membership_levels = get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
						case MembershipLevel::PREMIUM:
						case MembershipLevel::BASIC:
							return \do_shortcode( '[wcfm endpoint="wcfm-settings"]' );
						case MembershipLevel::PLATINUM:
						case MembershipLevel::DIAMOND:
						case MembershipLevel::SITE_ADMIN:
						case MembershipLevel::AMBASSADOR:
							break;
						case MembershipLevel::VENDOR_STAFF:
							return '<h1>' . $this->get_instance( SiteDefaults::class )->defaults( 'staff_name' ) . ' cannot change' . $this->get_instance( SiteDefaults::class )->defaults( 'account_type' ) . ' picture settings </h1>';
					}
				}

				// Handling Admin Users.
				if ( $user_roles->is_wordpress_administrator() ) {
					return do_shortcode( '[wcfm endpoint="wcfm-settings"]' );
				}
				break;

			case 'owner_picture_url':
				$membership_levels = \get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				$owner_id          = $this->get_instance( SiteDefaults::class )->page_owner();
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
							if ( $owner_id === $user->id ) {
								return \do_shortcode( '[elementor-template id="36300"]' );}

							break;              case MembershipLevel::PREMIUM:
							if ( $owner_id === $user->id ) {
								return \do_shortcode( '[elementor-template id="36300"]' );}

							break;          case MembershipLevel::BASIC:
								if ( $owner_id === $user->id ) {
									return \do_shortcode( '[elementor-template id="36300"]' );}

							break;
							case MembershipLevel::PLATINUM:
							case MembershipLevel::DIAMOND:
							case MembershipLevel::SITE_ADMIN:
							case MembershipLevel::AMBASSADOR:
							break;
							case MembershipLevel::VENDOR_STAFF:
							break;
					}
				}

				// Handling Admin Users.
				if (
						$user_roles->is_wordpress_administrator() &&
						$user->id === $owner_id
				) {
						return do_shortcode( '[elementor-template id="36300"]' );
				}
				return do_shortcode( '[elementor-template id="36297"]' );

			case 'portfolio':
				$store_id = $this->get_instance( WCHelpers::class )->get_store( 'id' );
				$url      = $this->get_instance( SiteDefaults::class )->defaults( 'portfolio', $store_id );
				return '<iframe class="embeddediframe" style="visibility: hidden;" onload="removeHeader()" name="iframe1" id="iframe1" src="' . $url . '" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';

			case 'manage_widgets':
				$store_id = $this->get_instance( WCHelpers::class )->get_store( 'id' );
				$url      = $this->get_instance( SiteDefaults::class )->defaults( 'widget_setting', $store_id );
				return '<iframe class="embeddediframe" style="visibility: hidden;" onload="removeHeader2()" name="iframe2" id="iframe2" src="' . $url . '" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';

			case 'renderportfolio':
				$store_id = $this->get_instance( WCHelpers::class )->get_store( 'id' );
				$url      = $this->get_instance( SiteDefaults::class )->defaults( 'portfolio_render', $store_id );
				return '<iframe class="embeddediframe" name="iframerp" id="iframerp" src="' . $url . '" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';

			case 'products':
				$store_id = $this->get_instance( SiteDefaults::class )->page_owner();
				$url      = $this->get_instance( SiteDefaults::class )->defaults( 'product_iframe', $store_id );
				return '<iframe class="embeddediframe" onload="removeHeader3()" name="iframe3" id="iframe3" src="' . $url . '" width="1200px" height="900px" frameborder="0" scrolling="yes" align="left"> </iframe>';

			case '5':
				return do_shortcode( '[elementor-template id="24937"]' );

			case '6':
				// Case 6 is about changing the Avatar Picture separately for premium users and Normal Users.
				$membership_levels = \get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
							return "<a href='" . \get_site_url() . "/tf21' target='_blank' class='yz-profile-img'>" . $pre . '</a>';

						case MembershipLevel::PREMIUM:
							return "<a href='" . \get_site_url() . "/tf21' target='_blank' class='yz-profile-img'>" . $pre . '</a>';

						case MembershipLevel::BASIC:
							return "<a href='" . \get_site_url() . "/tf21' target='_blank' class='yz-profile-img'>" . $pre . '</a>';

						case MembershipLevel::PLATINUM:
						case MembershipLevel::DIAMOND:
						case MembershipLevel::SITE_ADMIN:
						case MembershipLevel::AMBASSADOR:
							break;
						case MembershipLevel::VENDOR_STAFF:
							// Vendor staff have normal accounts so must use BP
							return "<a href='/users/" . $this->get_instance( MenuHelpers::class )->nice_name() . "/profile/change-avatar/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
							break;
					}
				}
				// If not subscriber must be normal user so launch Normal Profile Change using BP
				return "<a href='/users/" . $this->get_instance( MenuHelpers::class )->nice_name() . "/profile/change-avatar/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';

				break;

			case '7':
				// Case 7 is about changing Profile Cover picture from Buttons
				$membership_levels = get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
							return "<a href='" . get_site_url() . "/tf21' target='_blank' class='yz-profile-link'>___________________________________Change Cover Picture____________________</a>";
						case MembershipLevel::PREMIUM:
							return "<a href='" . get_site_url() . "/tf21' target='_blank' class='yz-profile-link'>___________________________________Change Cover Picture____________________</a>";
						case MembershipLevel::BASIC:
							return "<a href='" . get_site_url() . "/tf21' target='_blank' class='yz-profile-link'>___________________________________Change Cover Picture____________________</a>";
						case MembershipLevel::PLATINUM:
						case MembershipLevel::DIAMOND:
						case MembershipLevel::SITE_ADMIN:
						case MembershipLevel::AMBASSADOR:
							break;
						case MembershipLevel::VENDOR_STAFF:
							// Vendor staff have normal accounts so must use BP
							return "<a href='/users/" . $this->get_instance( MenuHelpers::class )->nice_name() . "/profile/change-cover-image/' target='_blank' class='yz-profile-link'>___________________________________Change Cover Picture____________________</a>";
							break;
					}
				}
				// If not subscriber must be normal user so launch Normal Profile Change using BP
				return "<a href='/users/" . $this->get_instance( MenuHelpers::class )->nice_name() . "/profile/change-cover-image/' target='_blank' class='yz-profile-link'>___________________________________Change Cover Picture____________________</a>";

				break;

			case '8':
				$membership_levels = get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
							return "<a href='" . get_site_url() . "/merchant-centre/store-manager/settings/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
						case MembershipLevel::PREMIUM:
							return "<a href='" . get_site_url() . "/merchant-centre/store-manager/settings/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
						case MembershipLevel::BASIC:
							return "<a href='" . get_site_url() . "/merchant-centre/store-manager/settings/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
						case MembershipLevel::PLATINUM:
						case MembershipLevel::DIAMOND:
						case MembershipLevel::SITE_ADMIN:
						case MembershipLevel::AMBASSADOR:
							break;
						case MembershipLevel::VENDOR_STAFF:
							// Vendor staff have normal accounts so must use BP
							return "<a href='/users/" . $this->get_instance( MenuHelpers::class )->nice_name() . "/profile/change-avatar/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
							break;
					}
				}
				// If not subscriber must be normal user so launch Normal Profile Change using BP
				return "<a href='/users/" . $this->get_instance( MenuHelpers::class )->nice_name() . "/profile/change-avatar/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';

				break;

			case '9':
				// Case 9 is wip
				$membership_levels = get_user_meta( $user->id, 'ihc_user_levels' );
				$membership_level  = explode( ',', $membership_levels[0] );
				$array_count       = count( $membership_level );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $membership_level[ $x ] ) {
						case MembershipLevel::BUSINESS:
							return "<a href='" . get_site_url() . "/merchant-centre/store-manager/settings/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
						case MembershipLevel::PREMIUM:
							return "<a href='" . get_site_url() . "/merchant-centre/store-manager/settings/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
						case MembershipLevel::BASIC:
							return "<a href='" . get_site_url() . "/merchant-centre/store-manager/settings/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
						case MembershipLevel::PLATINUM:
						case MembershipLevel::DIAMOND:
						case MembershipLevel::SITE_ADMIN:
						case MembershipLevel::AMBASSADOR:
							break;
						case MembershipLevel::VENDOR_STAFF:
							// Vendor staff have normal accounts so must use BP.
							return "<a href='/users/" . $this->get_instance( MenuHelpers::class )->nice_name() . "/profile/change-avatar/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';
							break;
					}
				}
				// If not subscriber must be normal user so launch Normal Profile Change using BP
				return "<a href='/users/" . $this->get_instance( MenuHelpers::class )->nice_name() . "/profile/change-avatar/' target='_blank' class='yz-profile-img'>" . $pre . '</a>';

				break;

		}

	}

	public function get_name_url_shortcode( $params = array() ) {
		$pre  = $params['pre'] ?? '';
		$type = $params['type'] ?? 1;
		$post = $params['post'] ?? '';

		return $this->get_name_url( $pre, $type, $post );
	}
}
