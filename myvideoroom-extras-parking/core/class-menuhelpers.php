<?php
/**
 * Shortcodes for menus
 *
 * @package MyVideoRoomExtrasParking\Core
 */

namespace MyVideoRoomExtrasParking\Core;

use MyVideoRoomExtrasPlugin\Modules\UltimateMembershipPro\MembershipLevel;
use MyVideoRoomExtrasParking\Library\UserRoles;
use \MyVideoRoomExtrasPlugin\Library\WordPressUser;
use MyVideoRoomExtrasParking\Shortcode as Shortcode;
use \MyVideoRoomExtrasPlugin\Modules\WCFM\WCFMHelpers;

/**
 * Class MenuHelpers
 */
class MenuHelpers extends Shortcode {


	/**
	 * Provide Runtime
	 */
	public function runtime() {
		$this->add_shortcode( 'bpdisplayname', array( $this, 'get_bp_displayname' ) );
		$this->add_shortcode( 'storebutton', array( $this, 'store_button_shortcode' ) );
		$this->add_shortcode( 'vsslug', array( $this, 'vs_slug_shortcode' ) );
		$this->add_shortcode( 'marketplaceurl', array( $this, 'marketplace_url_shortcode' ) );
		$this->add_shortcode( 'profileurl', array( $this, 'profile_url_shortcode' ) );
		$this->add_shortcode( 'videostorefrontbutton', array( $this, 'video_storefront_button_shortcode' ) );
		$this->add_shortcode( 'nicename', array( $this, 'nice_name_shortcode' ) );
		$this->add_shortcode( 'invitedisplay', array( $this, 'invite_display_shortcode' ) );
		$this->add_shortcode( 'menuname', array( $this, 'menu_name' ) );
		$this->add_shortcode( 'name', array( $this, 'get_name_shortcode' ) );
		$this->add_shortcode( 'menulink', array( $this, 'menu_link_shortcode' ) );
		$this->add_shortcode( 'storevisitor', array( $this, 'store_visitor_shortcode' ) );
		$this->add_shortcode( 'namedump', array( $this, 'name_dump_shortcode' ) );
		$this->add_shortcode( 'refresh', array( $this, 'refresh' ) );
		$this->add_shortcode( 'nameformat', array( $this, 'name_format_sc' ) );
		$this->add_shortcode( 'searchyouzergroups', array( $this, 'search_youzer_groups_shortcode' ) );
	}

	/**
	 * A Function to refresh a page back to where it was sent
	 * This function takes all site parameters and assembles correctly a Store URL taking merchants and staff into consideration and name of Marketplace parameter
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	public function refresh( $params = array() ): void {
		$referer = $params['referer'] ?? htmlspecialchars( $_SERVER['HTTP_REFERER'] );
		$host    = $params['host'] ?? htmlspecialchars( $_GET['r'] );

		if ( $host ) {
			$user             = wp_get_current_user();
			$membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
			$memlev           = explode( ',', $membership_level[0] );
			$array_count      = count( $memlev );

			for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
				switch ( $memlev[ $x ] ) {
					case '1': // Basic User Refresh.
						$url = $referer . '/' . $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' );

				}
			}
		}
		wp_redirect( $url );
		exit();
	}
	/**
	 * A Correctly Render Pictures for Store Owners, Staff, and Users in social who are owners.
	 *
	 * @param $inbound_image
	 *
	 * @return string
	 */
	public function picture_link( string $inbound_image ): string {

		// Set up basics.
		$user           = \wp_get_current_user();
		$user_roles     = $this->get_instance( UserRoles::class );
		$parent_id      = $user->_wcfm_vendor;
		$store_user     = \wcfmmp_get_store( $parent_id );
		$store_gravatar = $store_user->get_avatar();

		// Filter Out Other People Stores and exit function.
		$checksum_id = $user->ID;
		$display_id  = \bp_displayed_user_id();

		if ( $checksum_id !== $display_id ) {
			return $inbound_image;
		}

		switch ( true ) {
			case $user_roles->is_wcfm_vendor():
				return '<img loading="lazy" src="' . $store_gravatar . '" alt="Profile Photo" width="150" height="150">';

			case $user_roles->is_wcfm_shop_staff():
			default:
				return $inbound_image;
		}
	}

	/**
	 * A Function to Return the User Nicename for menus
	 *
	 * @param integer $user_id
	 *
	 * @return string
	 */
	public function nice_name( int $user_id = null ): string {

		if ( $user_id ) {
			$user = $this->get_instance( WordPressUser::class )->get_wordpress_user_by_id( $user_id );
		} else {
			$user = \wp_get_current_user();
		}

		return $user->user_nicename;
	}

	/**
	 * Get the nice name for a user from a shortcode
	 *
	 * @return string
	 */
	public function nice_name_shortcode(): string {
		return $this->nice_name();
	}


	/**
	 * A Function to Correctly Render Pictures for Store Owners, Staff, and Users Being Viewed by Visitors
	 *
	 * @param string $inbound_image
	 *
	 * @return string
	 */
	public function picture_link_view( string $inbound_image ): string {

		// Set up basics.
		$user_id    = \bp_displayed_user_id();
		$user_roles = $this->get_instance( UserRoles::class );

		switch ( true ) {
			case $user_roles->is_wcfm_vendor():
				$store_user     = \wcfmmp_get_store( $user_id );
				$store_gravatar = $store_user->get_avatar();

				return '<img loading="lazy" src="' . $store_gravatar . '" alt="Profile Photo" class="yz-profile-img" width="150" height="150">';

			case $user_roles->is_wcfm_shop_staff():
			default:
				return $inbound_image;
		}
	}

	/**
	 * Correctly Render Names for apostrophes to avoid s's
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function name_format_sc( $params = array() ): string {
		$type = $params['type'] ?? '';

		return $this->name_format( $type );
	}

	public function name_format( string $name ): string {
		if ( ! $name ) {
			$name = $this->nice_name();
		}

		$pieces    = explode( ' ', $name );
		$last_word = array_pop( $pieces );

		$last = substr( $last_word, - 1 );
		if ( 's' === $last || 'S' === $last ) {
			return "' ";
		} else {
			return "'s ";
		}

	}

	/**
	 * A Shortcode to Return the Correctly Formatted Username for Social Override in Youzer
	 */
	public function menu_name( $username = '' ): string {
		$user       = \wp_get_current_user();
		$user_roles = $this->get_instance( UserRoles::class );

		// Filter out not being in your own page.
		$checksum_id = $user->ID;
		$display_id  = \bp_displayed_user_id();

		if ( $checksum_id !== $display_id ) {
			return $username;
		}

		switch ( true ) {
			case $user_roles->is_wcfm_vendor():
				$store_user = \wcfmmp_get_store( $user->ID );
				$store_info = $store_user->get_shop_info();
				return $store_info['store_name'];

			case $user_roles->is_wcfm_shop_staff():
				$parent_id  = $user->_wcfm_vendor;
				$store_user = \wcfmmp_get_store( $parent_id );
				$store_info = $store_user->get_shop_info();

				return $store_info['store_name'] . $this->name_format( $store_info['store_name'] ) . \bp_get_displayed_user_fullname();

			default:
				// If they aren't a vendor then we simply return User Login.
				return \bp_get_displayed_user_fullname();
		}

	}
	/**
	 * A Shortcode to Return the Correctly Formatted Username for Social Override in Viewing Merchants Correctly and Bypassing Social
	 */
	public function menu_name_reversed(): string {
		$user = $this->get_instance( WordPressUser::class )->get_wordpress_user_by_id( \bp_displayed_user_id() );

		$user_roles = $this->get_instance( UserRoles::class );

		switch ( true ) {
			case $user_roles->is_wcfm_vendor():
				$store_user = \wcfmmp_get_store( $user->ID );
				$store_info = $store_user->get_shop_info();

				return $store_info['store_name'];

			case $user_roles->is_wcfm_shop_staff():
				$parent_id  = $user->_wcfm_vendor;
				$store_user = \wcfmmp_get_store( $parent_id );
				$store_info = $store_user->get_shop_info();

				return $store_info['store_name'] . $this->name_format( $store_info['store_name'] ) . \bp_get_displayed_user_fullname();

			default:
				// If they aren't a vendor then we simply return User Login.
				return \bp_get_displayed_user_fullname();
		}
	}

	/**
	 * Correctly Render Pictures for Store Owners, Staff, and Users in social being viewed by visitors
	 *
	 * @param string $inbound_image
	 *
	 * @return string
	 */
	public function header_link_view( string $inbound_image ): string {
		$user_roles = $this->get_instance( UserRoles::class );

		switch ( true ) {
			case $user_roles->is_wcfm_vendor():
				$user_id      = \bp_displayed_user_id();
				$store_user   = \wcfmmp_get_store( $user_id );
				$store_banner = $store_user->get_banner();

				return '<div class="yz-header-cover" style="background-image:url( ' . $store_banner . ' ); background-size: cover;">';

			case $user_roles->is_wcfm_shop_staff():
			default:
				return $inbound_image;
		}

	}

	/**
	 * Correctly Render Pictures for Store Owners, Staff, and Users in social
	 *
	 * @param string $inbound_image
	 *
	 * @return string
	 */
	public function header_link( string $inbound_image ): string {
		// To ensure we escape function in case browsing someone else's profile.
		$user        = \wp_get_current_user();
		$checksum_id = $user->ID;
		$display_id  = \bp_displayed_user_id();

		if ( $checksum_id != $display_id ) {
			return $inbound_image;
		}

		$user_roles = $this->get_instance( UserRoles::class );

		switch ( true ) {
			case $user_roles->is_wcfm_vendor():
				$parent_id    = $user->_wcfm_vendor;
				$store_user   = \wcfmmp_get_store( $parent_id );
				$store_banner = $store_user->get_banner();

				return '<div class="yz-header-cover" style="background-image:url( ' . $store_banner . ' ); background-size: cover;">';

			case $user_roles->is_wcfm_shop_staff():
			default:
				return $inbound_image;
		}

	}

	/**
	 * Get the full displayname from BuddyPress
	 *
	 * @return string
	 */
	public function get_bp_displayname(): string {
		return \bp_get_displayed_user_fullname();
	}


	/**
	 * Generate a merchant URL from $ID
	 * This function takes all site parameters and assembles correctly a Store URL taking merchants and staff into consideration and name of Marketplace parameter
	 *
	 * @param int|null $user_id
	 *
	 * @return string
	 */
	public function get_store_url( int $user_id = null ): string {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$slug = $this->get_name( $user_id );

		return get_site_url() . '/' . get_option( 'wcfm_store_url' ) . '/' . $slug;
	}

	/**
	 * A Shortcode to Format Store Theme Control Buttons for Merchants and Staff separately
	 * NB !!!!!!!!! This shortcode also does security to move a basic subscriber out of the premium management area
	 * Vendor store theme settings are the only one applied, staff theme settings have no effect
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function store_button_shortcode( $params = array() ): string {
		$type = $params['type'] ?? null;
		return $this->store_button( $type );
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	public function store_button( string $type = null ): string {
		$user       = wp_get_current_user();
		$user_roles = $this->get_instance( UserRoles::class );

		// Case of Store Admin -> Inside /Myarea admin page.
		if ( 'store_admin' === $type ) {

			// Bounce non-store owners out.
			if (
					! $user_roles->is_wcfm_shop_staff() &&
					! $user_roles->is_wcfm_vendor() &&
					! $user_roles->is_wordpress_administrator()
			) {
				$url = $this->get_instance( SiteDefaults::class )->defaults( 'profile_url' );
				wp_redirect( $url );
				exit();
			}

			if ( $user_roles->is_wcfm_vendor() ) {
				return do_shortcode( '[elementor-template id="32586"]' );
			} elseif ( $user_roles->is_wcfm_shop_staff() ) {
				return do_shortcode( '[elementor-template id="32591"]' );
			}
		}

		// Reject Non-Store Owners.
		$owner_id  = $this->get_instance( SiteDefaults::class )->page_owner();
		$parent_id = $this->get_instance( WCFMHelpers::class )->staff_to_parent( $user->ID );
		if ( $owner_id !== $parent_id ) {
			return '';
		}

		switch ( $type ) {
			case 'widget':
				if ( $user_roles->is_wcfm_vendor() || $user_roles->is_wcfm_shop_staff() ) {
					return do_shortcode( '[elementor-template id="35448"]' );
				}
				break;

			case 'store':
				if ( $user_roles->is_wcfm_vendor() || $user_roles->is_wcfm_shop_staff() ) {
					return do_shortcode( '[elementor-template id="35461"]' );
				}
				break;

			case 'staff_members':
				if ( $user_roles->is_wcfm_vendor() || $user_roles->is_wcfm_shop_staff() ) {
					return do_shortcode( '[elementor-template id="35471"]' );
				}
				break;

			case 'store_theme':
				if ( $user_roles->is_wcfm_vendor() ) {
					return do_shortcode( '[elementor-template id="35491"]' );
				}
				break;

			case '':
			default:
				$membership_level = get_user_meta( $user->id, 'ihc_user_levels' );
				$memlev           = explode( ',', $membership_level[0] );
				$array_count      = count( $memlev );
				// Role Selection Switch- There are Array of subscription options, so we run this once for each major position in Array.
				for ( $x = 0; $x <= $array_count - 1; $x ++ ) {
					switch ( $memlev[ $x ] ) {
						case MembershipLevel::BASIC:
							$url = $this->get_instance( SiteDefaults::class )->defaults( 'profile_url' );
							wp_redirect( $url );
							exit();

					}
				}

				if ( $user_roles->is_wcfm_vendor() ) {
					return do_shortcode( '[elementor-template id="32586"]' );
				} elseif ( $user_roles->is_wcfm_shop_staff() ) {
					return do_shortcode( '[elementor-template id="32591"]' );
				}
		}

		return '';

	}

	/**
	 * Shortcode to create Store Details for Displaying in Visitor Templates
	 * It is different from Storelink - as it renders from the perspective of the target store and not the logged in user
	 *
	 * @param string|null $type
	 *
	 * @return string
	 */
	public function store_visitor( string $type = null ): string {

		$user_roles = $this->get_instance( UserRoles::class );

		$po = bp_displayed_user_id();
		if ( 'ownerstorelogo' === $type ) {

			$user_id = $this->get_instance( WCFMHelpers::class )->staff_to_parent( $this->get_instance( SiteDefaults::class )->page_owner() );

		}

		if ( 'adminpage_owner' === $type ) {

			$user_id = $this->get_instance( WCFMHelpers::class )->staff_to_parent( get_current_user_id() );
		}

		$parent_id = $user_id;

		if ( 'stafflogo' === $type ) {
			$store_user = \wcfmmp_get_store( $parent_id );
		}

		// parent is for returning Parent Accounts, Childaccount is for returning childaccount.

		if (
			'childaccount' === $type ||
			'breakout' === $type
		) {
			$user_id = $po;

			if ( $user_roles->is_wcfm_vendor() ) {
				$store_user     = \wcfmmp_get_store( $user_id );
				$store_info     = $store_user->get_shop_info();
				$store_gravatar = $store_user->get_avatar();
				$url            = $store_gravatar;
				$output         = $store_info['store_name'];
			} elseif (
					$user_roles->is_wcfm_shop_staff() &&
					'breakout' !== $type
			) {
				$url    = bp_core_fetch_avatar(
					array(
						'item_id' => $po,
						'type'    => 'full',
						'html'    => false,
					)
				);
				$output = $this->get_instance( SiteDefaults::class )->displayname();
			} else {

				$url    = bp_core_fetch_avatar(
					array(
						'item_id' => $this->get_instance( SiteDefaults::class )->page_owner(),
						'type'    => 'full',
						'html'    => false,
					)
				);
				$output = $this->get_instance( SiteDefaults::class )->displayname();
			}
		} elseif ( $user_roles->is_wcfm_shop_staff() || $user_roles->is_wcfm_vendor() ) {

			$store_user = \wcfmmp_get_store( $parent_id );

			$store_info     = $store_user->get_shop_info();
			$store_gravatar = $store_user->get_avatar();
			$url            = $store_gravatar;
			$output         = $store_info['store_name'];
		} else {
			$url    = \bp_core_fetch_avatar(
				array(
					'item_id' => $this->get_instance( SiteDefaults::class )->page_owner(),
					'type'    => 'full',
					'html'    => false,
				)
			);
			$output = $this->get_instance( SiteDefaults::class )->displayname();
		}

		ob_start();

		?>

		<div class="yz-primary-nav-area">

			<div class="yz-primary-nav-settings">
				<div class="yz-primary-nav-img" style="background-image: url(<?php echo $url; ?>)"></div>
				<span>
				<?php
				echo $output
				?>
				</span>

				<?php if ( 'on' === yz_option( 'yz_disable_wp_menu_avatar_icon', 'on' ) ) : ?>
					<i class="fas fa-angle-down yz-settings-icon"></i><?php endif; ?>
			</div>

		</div>

		<script type="text/javascript">

			// Show/Hide Primary Nav Message
			jQuery( '.yz-primary-nav-settings' ).click(function (e) {
				// e.preventDefault();
				// Get Parent Box.
				var settings_box = jQuery(this).closest( '.yz-primary-nav-area' );
				// Toggle Menu.
				settings_box.toggleClass( 'open-settings-menu' );
				// Display or Hide Box.
				settings_box.find( '.yz-settings-menu' ).fadeToggle(400);
			});

		</script>

		<?php

		return ob_get_clean();

	}

	public function store_visitor_shortcode( $params = array() ): string {
		$type = $params['type'] ?? null;
		return $this->store_visitor( $type );
	}


	/**
	 * A Shortcode to Return the Correctly Formatted Username in Menus dealing with Merchants
	 * For Merchants it returns store name or their nice name - for staff it returns parent store name
	 *
	 * @param int|null $id
	 *
	 * @return string
	 */
	public function get_name( int $id = null ): string {
		if ( $id ) {
			$user = $this->get_instance( WordPressUser::class )->get_wordpress_user_by_id( (int) $id );
		} else {
			$user = wp_get_current_user();
		}

		$user_roles = $this->get_instance( UserRoles::class, array( $user ) );

		switch ( true ) {
			case $user_roles->is_wcfm_vendor():
				return $user->user_nicename;

			case $user_roles->is_wcfm_shop_staff():
				$parent_id  = $user->_wcfm_vendor;
				$store_user = \wcfmmp_get_store( $parent_id );
				$store_info = $store_user->get_shop_info();

				return $store_info['store_slug'];

			default:
				// If they aren't a vendor then we simply return User Login.
				return $user->user_nicename;
		}
	}

	/**
	 * A Shortcode to Return the Correctly Formatted Username in Menus dealing with Merchants
	 *
	 * @return mixed|string
	 */
	public function get_name_shortcode(): string {
		return $this->get_name();
	}

	/**
	 * Shortcode to createImage of Anything as Menu Item
	 * Create Menu Shortcode.
	 */
	public function menu_link_shortcode() {

		// Get Logged-IN User ID.

		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
		$url            = $image[0];

		ob_start();

		?>

		<div class="yz-primary-nav-area">

			<div class="yz-primary-nav-settings">
				<div class="yz-primary-nav-img" style="background-image: url(<?php echo $url; ?>)"></div>
				<span>
			<?php
			echo get_bloginfo( 'name' );
			?>
			</span>
			</div>

		</div>

		<script type="text/javascript">

			// Show/Hide Primary Nav Message
			jQuery( '.yz-primary-nav-settings' ).click(function (e) {
				// e.preventDefault();
				// Get Parent Box.
				var settings_box = jQuery(this).closest( '.yz-primary-nav-area' );
				// Toggle Menu.
				settings_box.toggleClass( 'open-settings-menu' );
				// Display or Hide Box.
				settings_box.find( '.yz-settings-menu' ).fadeToggle(400);
			});

		</script>

		<?php

		return ob_get_clean();

	}

	/**
	 * A Shortcode to Generate the Name of the Video Storefront Slug.
	 */
	public function vs_slug_shortcode() {
		return $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' );
	}

	/**
	 * A Shortcode to Generate the Name of the Marketplace Automatically.
	 */
	public function marketplace_url_shortcode() {
		return $this->get_instance( SiteDefaults::class )->defaults( 'marketplace_url' );
	}

	/**
	 * A Shortcode to Generate the User Base in BuddyPress of the logged in user account.
	 */
	public function profile_url_shortcode() {
		return $this->get_instance( SiteDefaults::class )->defaults( 'profile_url' );
	}


	/**
	 * A Function to Return Formatted Header Invite Names for Meeting Rooms.
	 */
	public function invite_display_shortcode(): string {
		$user_roles = $this->get_instance( UserRoles::class );

		switch ( true ) {
			case $user_roles->is_wcfm_vendor():
				$user       = \wp_get_current_user();
				$store_user = \wcfmmp_get_store( $user->ID );
				$store_info = $store_user->get_shop_info();

				return $store_info['store_name'];

			case $user_roles->is_wcfm_shop_staff():
				return \bp_get_displayed_user_fullname();
		}

		return '';
	}

	public function name_dump_shortcode(): string {
		$user = wp_get_current_user();
		return print_r( $user );
	}

	/**
	 * A Shortcode to Return Video Storefront selection only for Merchants and not staff.
	 * Vendor store theme settings are the only one applied, staff theme settings have no effect.
	 *
	 * @TODO @FRED Something is wrong with `$type` on the function definition - this should be an array
	 */
	public function video_storefront_button_shortcode( $type = '' ): ?string {
		$user_roles = $this->get_instance( UserRoles::class );

		if ( ! $type ) {
			if ( $user_roles->is_wcfm_vendor() ) {
				return do_shortcode( '[elementor-template id="32754"]' );
			} else {
				return null;
			}
		} elseif ( 'staffpersonal' === $type ) {
			$user = wp_get_current_user();
			if ( $this->get_instance( SiteDefaults::class )->is_premium_check( $user->ID ) ) {
				return do_shortcode( '[elementor-template id="34501"]' );

				// return premium group selection button.
			} else {
				// return basic group selection button.
			}
		}
	}

	/**
	 * A Shortcode to Return Youzer Search
	 * This is meant to be the new universal formatting invite list
	 *
	 * @param  string $params = host.
	 * @return string - a shortcode search.
	 */
	public function search_youzer_groups_shortcode( $params = array() ) {
		$raw_host = $params['host'] ?? htmlspecialchars( $_GET['s'] ?? '' );
		$host     = preg_replace( '/[^A-Za-z0-9\-]/', ' ', $raw_host );

		return do_shortcode( '[youzer_members per_page="12" meta_key="_wcfm_vendor"  search_terms ="' . $host . '"]' );

	}
}

