<?php

namespace MyVideoRoomExtrasParking\Modules\Security;

use MyVideoRoomExtrasPlugin\Library\UserRoles;
use MyVideoRoomExtrasPlugin\Shortcode as Shortcode;
use MyVideoRoomExtrasPlugin\Core\SiteDefaults;

/**
 * Class FiltersUtilities
 */
class FiltersUtilities extends Shortcode {



	/**
	 * Provide Runtime
	 */
	public function runtime() {

		/*
		Customise My Account Page.
		This function modifies the default MyAccount page.
		*/
		\add_filter( 'woocommerce_account_menu_items', array( $this, 'my_account_change_filter' ), 40 );

		\add_action( 'init', array( $this, 'add_menu_endpoint_action' ) );    /* Register Permalink Endpoint  */

		/* Content for the new page in My Account endpoint */
		\add_action( 'woocommerce_account_cc_mysubs_endpoint', array( $this, 'subscriptions_endpoint_action' ) );

		/**
		 * Add to Cart Redirect Function.
		 * In order to use product straight to check out use ?add-to-cart=%%ProductPOSTID%% eg ?add-to-cart=34553
		 * Used to be able to send Subscriptions Straight to Checkout so one Click Buy Works
		 */
		\add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'straight_to_checkout_filter' ) );

		\add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'add_my_account_order_actions_filter' ), 10, 2 );

		$this->add_shortcode( 'menu', array( $this, 'menu' ) );

		// Action Filters to Implement a Video Hub system in WCFM Elementor to deploy a video consult room
		// WCFM Template to Implement Video Hub Tabbed menu
		\add_action(
			'wcfmmp_rewrite_rules_loaded',
			function ( $wcfm_store_url ) {
				add_rewrite_rule( $wcfm_store_url . '/([^/]+)/' . $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' ) . '?$', 'index.php?' . $wcfm_store_url . '=$matches[1]&' . $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' ) . '=true', 'top' );
				add_rewrite_rule( $wcfm_store_url . '/([^/]+)/' . $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' ) . '/page/?([0-9]{1,})/?$', 'index.php?' . $wcfm_store_url . '=$matches[1]&paged=$matches[2]&' . $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' ) . '=true', 'top' );
			},
			50
		);

		\add_filter(
			'query_vars',
			function ( $vars ) {
				$vars[] = $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' );

				return $vars;
			},
			50
		);

		\add_filter(
			'wcfmmp_store_tabs',
			function ( $store_tabs ) {
				$store_tabs[ $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' ) ] = $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront' );

				return $store_tabs;
			},
			50,
			2
		);

		\add_filter(
			'wcfmp_store_tabs_url',
			function ( $store_tab_url, $tab ) {
				if ( $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' ) === $tab ) {
					$store_tab_url .= $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' );
				}

				return $store_tab_url;
			},
			50,
			2
		);

		\add_filter(
			'wcfmp_store_default_query_vars',
			function ( $query_var ) {
				if ( get_query_var( $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' ) ) ) {
					$query_var = $this->get_instance( SiteDefaults::class )->defaults( 'video_storefront_slug' );
				}

				return $query_var;
			},
			50
		);

		// Action Filters to Implement a Child Account connection system in WCFM Elementor
		// WCFM Template to Implement New Connections Hub Tabbed menu
		\add_action(
			'wcfmmp_rewrite_rules_loaded',
			function ( $wcfm_store_url ) {
				add_rewrite_rule( $wcfm_store_url . '/([^/]+)/' . $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' ) . '?$', 'index.php?' . $wcfm_store_url . '=$matches[1]&' . $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' ) . '=true', 'top' );
				add_rewrite_rule( $wcfm_store_url . '/([^/]+)/' . $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' ) . '/page/?([0-9]{1,})/?$', 'index.php?' . $wcfm_store_url . '=$matches[1]&paged=$matches[2]&' . $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' ) . '=true', 'top' );
			},
			50
		);

		\add_filter(
			'query_vars',
			function ( $vars ) {
				$vars[] = $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' );

				return $vars;
			},
			50
		);

		\add_filter(
			'wcfmmp_store_tabs',
			function ( $store_tabs ) {
				$store_tabs[ $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' ) ] = $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront' );

				return $store_tabs;
			},
			50,
			2
		);

		\add_filter(
			'wcfmp_store_tabs_url',
			function ( $store_tab_url, $tab ) {
				if ( $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' ) === $tab ) {
					$store_tab_url .= $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' );
				}

				return $store_tab_url;
			},
			50,
			2
		);

		\add_filter(
			'wcfmp_store_default_query_vars',
			function ( $query_var ) {
				if ( get_query_var( $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' ) ) ) {
					$query_var = $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' );
				}

				return $query_var;
			},
			50
		);

		\add_filter(
			'wcfmmp_store_default_template',
			function ( $template, $tab ) {
				if ( $this->get_instance( SiteDefaults::class )->defaults( 'staff_storefront_slug' ) === $tab ) {
					$template = 'store/connections.php';
				}

				return $template;
			},
			50,
			2
		);

		// add_action( 'init', array( $this, 'allow_vendors_media_uploads' ) );
		
		\add_filter( 'big_image_size_threshold', '__return_false' );

		//$this->add_shortcode( 'debughook', array( $this, 'debug_hook' ) );
		$this->add_shortcode( 'logout', array( $this, 'logout' ) );
	}

	/**
	 * A Function to do logout
	 *
	 * @return string
	 */
	public function logout(): string {
		return \wp_logout_url( \home_url() );
	}

	public function debug_hook() {
		\add_action(
			'all',
			function ( $tags ) {
				global $debug;
				if ( in_array( $tags, $debug ) ) {
					return;
				}
				echo $tags . '<br>';
				$debug[] = $tags;
			}
		);
	}

	/**
	 * Club Cloud Menu Name - may be deprecated in favour of ccname - which handles the edge case of store owners
	 *
	 * @return mixed|string
	 */
	public function menu() {
		$user       = \wp_get_current_user();
		$user_roles = $this->get_instance( UserRoles::class );

		if ( $user_roles->is_wcfm_vendor() ) {
			$store_user = \wcfmmp_get_store( $user->ID );
			$store_info = $store_user->get_shop_info();
			$store_data = $store_info['store_slug'];

			return $store_data;
		}

		// If they aren't a vendor then we simply return User Login- if you need to handle Staff use ccgetname.
		return $user->user_login;
	}


	public function allow_vendors_media_uploads() {
		$vendor_role = \get_role( 'seller' );

		// Ensure Vendors Media Upload Capability.
		$vendor_role->add_cap( 'edit_posts' );
		$vendor_role->add_cap( 'edit_post' );
		$vendor_role->add_cap( 'edit_others_posts' );
		$vendor_role->add_cap( 'edit_others_pages' );
		$vendor_role->add_cap( 'edit_published_posts' );
		$vendor_role->add_cap( 'edit_published_pages' );
		$vendor_role->add_cap( 'upload_files' );
	}


	public function add_menu_endpoint_action() {
		\add_rewrite_endpoint( 'cc_mysubs', EP_PAGES );
	}

	public function straight_to_checkout_filter() {
		$cart = \WC()->cart;

		if ( $cart ) {
			return $cart->get_checkout_url();
		}

		return null;
	}

	public function my_account_change_filter( $menu_links ) {
		$menu_links = array_slice( $menu_links, 0, 1, true )
					+ array( 'cc_mysubs' => 'My Subscriptions' )
					+ array_slice( $menu_links, 1, null, true );

		return $menu_links;
	}

	public function subscriptions_endpoint_action() {
		return \do_shortcode( '[elementor-template id="24402"]' );
	}

	/**
	 * Add Order Action to My Order Page
	 *
	 * @param $actions
	 * @param $order
	 *
	 * @return mixed
	 */
	public function add_my_account_order_actions_filter( $actions, $order ) {
		$actions['video'] = array(
			'url'  => '/go/?&order=' . $order->get_order_number(),
			'name' => __( 'Video Room', 'my-textdomain' ),
		);

		return $actions;
	}



}