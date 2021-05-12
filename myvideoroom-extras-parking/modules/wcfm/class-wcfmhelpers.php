<?php
/**
 * Helpers for WCFM
 *
 * @package MyVideoRoomExtrasPlugin\Modules\WCFM
 */

namespace MyVideoRoomExtrasPlugin\Modules\WCFM;

use MyVideoRoomExtrasPlugin\Library\UserRoles;
use MyVideoRoomExtrasPlugin\Library\WordPressUser;
use MyVideoRoomExtrasPlugin\Shortcode as Shortcode;
use \MyVideoRoomExtrasPlugin\Core\SiteDefaults;
use MyVideoRoomExtrasPlugin\Factory;
use MyVideoRoomExtrasPlugin\Modules\BuddyPress\BuddyPress;

/**
 * Class WCFMHelpers
 */
class WCFMHelpers extends Shortcode {


	/**
	 * Install the shortcodes
	 */
	public function runtime() {
		$this->add_shortcode( 'dp', array( $this, 'display_products_shortcode' ) );
		$this->add_shortcode( 'storelink', array( $this, 'store_link_shortcode' ) );
		$this->add_shortcode( 'searchwcfm', array( $this, 'search_wcfm_shortcode' ) );
	}


	/**
	 * Display_products_shortcode.
	 * This function displays parent product owners outside of WCFM - goes with Worker function below.
	 *
	 * @param  string $params - id. The Product ID.
	 * @return int - Function Call to Worker. With ID.
	 */
	public function display_products_shortcode( $params = array() ) {
		$id = $params['id'] ?? null;

		return $this->display_products( $id );
	}

	/**
	 * This function displays parent product owners outside of WCFM
	 *
	 * @param string $id -the product ID to show.
	 *
	 * @return string - a shortcode.
	 */
	public function display_products( $id = null ) {
		if ( $id ) {
			$store_id = $id;
		} else {
			$store_id = $this->get_instance( SiteDefaults::class )->page_owner();
		}

		return \do_shortcode( '[products store="' . $store_id . '" paginate="true"]' );
	}

	/**
	 * Is merchant check.
	 * This function checks if a user is a Merchant
	 *
	 * @param  int $user_id - the ID of the User.
	 * @return bool - is the User a merchant yes or no.
	 */
	public function ismerchant_check( int $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = \get_current_user_id();
		}

		$user       = $this->get_instance( WordPressUser::class )->get_wordpress_user_by_id( (int) $user_id );
		$user_roles = $this->get_instance( UserRoles::class, array( $user ) );

		return ( $user_roles->is_wcfm_shop_staff() || $user_roles->is_wcfm_vendor() );
	}

	/**
	 * Staff To Parent.
	 * This function Returns the Parent ID of the Merchant of a store.
	 * Used to always return the store parent ID, and filter out Staff/Child Accounts.
	 *
	 * @param  int $id -the user ID of the staff account to return the parent of.
	 * @return int|null - depending on parent.
	 */
	public function staff_to_parent( int $id ) {
		if ( ! $id ) {
			return null;
		}

		$staff      = $this->get_instance( WordPressUser::class )->get_wordpress_user_by_id( (int) $id );
		$user_roles = $this->get_instance( UserRoles::class, array( $staff ) );

		if ( $user_roles->is_wcfm_vendor() ) {
			return $id;
		}

		$parent_id = $staff->_wcfm_vendor;

		$parent     = $this->get_instance( WordPressUser::class )->get_wordpress_user_by_id( (int) $parent_id );
		$user_roles = $this->get_instance( UserRoles::class, array( $parent ) );

		if ( $parent && $user_roles->is_wcfm_vendor() ) {
			return $parent_id;
		}

		return null;
	}

	/**
	 * Format Store Display Name or Slug
	 *
	 * @param int    $store_id - the Store ID (UserID of Store Owner).
	 * @param string $input_type - is the type of Display.
	 * @return string - the Store name.
	 */
	public function store_displayname( int $store_id, string $input_type = null ) {

		$store_user = \wcfmmp_get_store( $store_id );
		$store_info = $store_user->get_shop_info();

		if ( 'slug' === $input_type ) {
			return $store_info['store_slug'];
		} else {
			return $store_info['store_name'];
		}

	}


	/**
	 * Store_link_shortcode - returns formatted title information for templates
	 *
	 * @param  mixed $params Type== shortcode type to render.
	 * @return string
	 */
	public function store_link_shortcode( $params = array() ): string {
		$type = $params['type'] ?? null;
		return $this->store_link( $type );
	}

	/**
	 * Shortcode to create Store Details for Menu Item
	 * Create Store Shortcode.
	 *
	 * @param string|null $type
	 *
	 * @return string
	 */
	public function store_link( string $type = null ): string {
		// Get Logged-IN User ID.
		$user = wp_get_current_user();

		$user_roles = $this->get_instance( UserRoles::class );

		if ( 'ownerstorelogo' === $type ) {
			$user_id = $this->get_instance( self::class )->staff_to_parent( get_current_user_id() );
			$user    = $this->get_instance( WordPressUser::class )->get_wordpress_user_by_id( $user_id );
		}

		$parent_id = $user->ID;

		if ( 'stafflogo' === $type ) {
			if ( $user_roles->is_wcfm_vendor() ) {
				$store_user     = \wcfmmp_get_store( $user_id );
				$store_gravatar = $store_user->get_avatar();
				$url            = $store_gravatar;
			} else {
				if ( Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
					$url = \bp_core_fetch_avatar(
						array(
							'item_id' => get_current_user_id(),
							'type'    => 'full',
							'html'    => false,
						)
					);
				}
			}
			$store_user = \wcfmmp_get_store( $parent_id );
			$store_info = $store_user->get_shop_info();
			$output     = $store_info['store_name'];

		} elseif ( 'childaccount' === $type || 'breakout' === $type ) {
			// parent is for returning Parent Accounts, Childaccount is for returning childaccount .
			$user_id = \get_current_user_id();

			if ( $user_roles->is_wcfm_vendor() ) {
				$store_user     = wcfmmp_get_store( $user_id );
				$store_info     = $store_user->get_shop_info();
				$store_gravatar = $store_user->get_avatar();
				$url            = $store_gravatar;
				$output         = $store_info['store_name'];
			} elseif (
					$user_roles->is_wcfm_shop_staff() &&
					'breakout' !== $type
			) {
				if ( Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
					$url = \bp_core_fetch_avatar(
						array(
							'item_id' => get_current_user_id(),
							'type'    => 'full',
							'html'    => false,
						)
					);
				}
				$output = $this->get_instance( SiteDefaults::class )->displayname();

			} else {

				if ( Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
					$url = \bp_core_fetch_avatar(
						array(
							'item_id' => get_current_user_id(),
							'type'    => 'full',
							'html'    => false,
						)
					);
				}
				$output = $this->get_instance( SiteDefaults::class )->displayname();
			}
		} elseif ( $user_roles->is_wcfm_shop_staff() || $user_roles->is_wcfm_vendor() ) {

			$store_user = \wcfmmp_get_store( $parent_id );

			$store_info     = $store_user->get_shop_info();
			$store_gravatar = $store_user->get_avatar();
			$url            = $store_gravatar;
			$output         = $store_info['store_name'];
		} else {
			if ( Factory::get_instance( BuddyPress::class )->is_buddypress_active() ) {
				$url = \bp_core_fetch_avatar(
					array(
						'item_id' => get_current_user_id(),
						'type'    => 'full',
						'html'    => false,
					)
				);
			}
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
	 * A Shortcode to Return WCFM Search
	 * This is meant to be the new universal formatting invite list
	 *
	 * @param  string $params - Search. The Search String.
	 * @return string - a Shortcode.
	 */
	public function search_wcfm_shortcode( $params = array() ) {
		$raw_host = $params['host'] ?? htmlspecialchars( $_GET['s'] ?? '' );
		$host     = preg_replace( '/[^A-Za-z0-9\-]/', ' ', $raw_host );

		return do_shortcode( '[wcfm_stores theme="compact" has_search="no" search_term ="' . $host . '" has_map = "no"]' );

	}

}

