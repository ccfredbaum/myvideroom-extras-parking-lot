<?php

/**
 * Addon functionality for BuddyPress - that is MVR Specific
 *
 * @package MyVideoRoomExtrasParking\Modules\BuddyPress
 */

namespace MyVideoRoomExtrasParking\MVR;

use MyVideoRoomExtrasParking\Library\UserRoles;
use MyVideoRoomExtrasParking\Library\WordPressUser;
use MyVideoRoomExtrasParking\Shortcode as Shortcode;
use MyVideoRoomExtrasParking\Core\SiteDefaults;
use MyVideoRoomExtrasParking\Shortcode\MyVideoRoomApp;
use MyVideoRoomExtrasParking\Modules\WCFM\WCFMHelpers;
use MyVideoRoomExtrasParking\Modules\BuddyPress\BuddyPressVideo;

/**
 * Class BuddyPress
 */
class MVRBuddyPress extends Shortcode {



	/**
	 * Provide Runtime
	 */
	public function init() {

		// add_action( 'bp_init', array( $this, 'setup_root_nav_action' ), 1000 );
		// add_action( 'bp_init', array( $this, 'setup_group_nav_action' ) );

		/**
		 * Naming Screen Functions Section - This section hosts the page construction templates for each named clickable function.
		 * Insert each function that the constructor above instantiates inside each separate template function
		 * Example - if the tab above has cc_group_video_meeting_content as the screen function - the rendering function cc_group_video_meeting_content must be built below for the tab to render content
		 */

		$this->add_shortcode( 'bpgroupname', array( $this, 'bp_groupname_shortcode' ) );
		$this->add_shortcode( 'displayportfolio', array( $this, 'display_portfolio_shortcode' ) );
	}


	/**
	 * Renders the Video Meeting tab Content that is a child of groups
	 *
	 * @param array $params
	 *
	 * @return bool|string|true|null
	 */
	public function bp_groupname_shortcode( $params = array() ) {
		global $bp;

		$type = $params['type'] ?? 'name';

		$group_link = $bp->root_domain . '/' . \bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';

		switch ( $type ) {
			case 'name':
				return $bp->groups->current_group->name;
			case 'url':
				return $group_link;
			case 'test':
				return print_r( $bp->groups->current_group );
			case 'ownerid':
				return $bp->groups->current_group->creator_id;
			case 'groupid':
				return $bp->groups->current_group->id;
			case 'status':
				return $bp->groups->current_group->status;
			case 'description':
				return $bp->groups->current_group->description;
			case 'banner':
				if ( \bp_has_groups( $args ) ) {

					while ( \bp_groups() ) {
						\bp_the_group();

						// Get the Cover Image.
						$group_cover_image_url = \bp_attachments_get_attachment(
							'url',
							array(
								'object_dir' => 'groups',
								'item_id'    => \bp_get_group_id(),
							)
						);

						echo '<img src="' . $group_cover_image_url . ' ">';
					}
				}

				break;

			case 'permissions':
				if (
					\groups_is_user_admin( $bp->loggedin_user->id, $bp->groups->current_group->id )
					|| \groups_is_user_mod( $bp->loggedin_user->id, $bp->groups->current_group->id )
					|| \is_super_admin()
					|| \is_network_admin()
				) {
					return true;
				}

				break;

			case 'guest':
				$xprofile_field   = 2483;
				$xprofile_setting = \xprofile_get_field_data( $xprofile_field, $bp->groups->current_group->creator_id );

				if ( ! $xprofile_setting ) {
					// going to site level backup field.
					$xprofile_setting = \xprofile_get_field_data( 2502, 1 );
				}

				// this setting comes from field 2555 in buddypress from the creator.
				$reception = \xprofile_get_field_data( 2555, $bp->groups->current_group->creator_id );

				$myvideoroom_app = MyVideoRoomApp::create_instance(
					$this->get_instance( SiteDefaults::class )->room_map( 'group', $bp->groups->current_group->id ),
					$xprofile_setting,
				);

				if ( $reception ) {
					$myvideoroom_app->enable_reception();
				}

				return $myvideoroom_app->output_shortcode();

			case 'host':
				$xprofile_field   = 2483;
				$xprofile_setting = \xprofile_get_field_data( $xprofile_field, $bp->groups->current_group->creator_id );

				if ( ! $xprofile_setting ) {
					// going to site level backup field.
					$xprofile_setting = \xprofile_get_field_data( 2502, 1 );
				}

				return MyVideoRoomApp::create_instance(
					$this->get_instance( SiteDefaults::class )->room_map( 'group', $bp->groups->current_group->id ),
					$xprofile_setting,
				)->enable_admin()->output_shortcode();

			case 'ownerbutton':
				if ( ! \is_user_logged_in() ) {
					// dont process signed out users.
					return null;
				}

				// to check if user is group owner.
				$user_id    = $bp->loggedin_user->id;
				$creator_id = $bp->groups->current_group->creator_id;

				if ( $creator_id === $user_id ) {
					return \do_shortcode( '[elementor-template id="32982" ]' );
				} else {
					return \do_shortcode( '[elementor-template id="33018" ]' );
				}

			case 'ownername':
				$owner_id = $bp->groups->current_group->creator_id;

				$owner_object = $this->get_instance( WordPressUser::class )->get_wordpress_user_by_id( $owner_id );
				$display_name = $owner_object->display_name;

				return $display_name;
		}
	}

	/**
	 * Main Constructor
	 * - This function loads all tabs and subtabs in one action
	 * - each tab calls a 'screen function' which must be in the screen function section
	 * You can add tabs, and sub tabs here - The parent slug defines if it is a sub navigation item, or a navigation item
	 */
	public function setup_root_nav_action() {
		$user_roles = $this->get_instance( UserRoles::class );

		if (
			$user_roles->is_wcfm_vendor()
			|| $user_roles->is_wcfm_shop_staff()
			|| $user_roles->is_wordpress_administrator()
		) {
			// Setup Family Settings Tab.
			\bp_core_new_nav_item(
				array(
					'name'                    => $this->get_instance( SiteDefaults::class )->defaults( 'account_type' ) . ' Settings',
					'slug'                    => 'family-settings',
					'show_for_displayed_user' => false,
					'screen_function'         => array( $this, 'settings_render_main_screen_function' ),
					'item_css_id'             => 'far fa-address-card',
					'user_has_access'         => \bp_is_my_profile(),
					'position'                => 70,
				)
			);

			\bp_core_new_subnav_item(
				array(
					'name'            => 'Shop Settings',
					'slug'            => 'shop-settings',
					'parent_url'      => trailingslashit( \bp_displayed_user_domain() . 'family-settings' ),
					'parent_slug'     => 'family-settings',
					'screen_function' => array( $this, 'settingssubtab_render_main_screen_function' ),
					'position'        => 100,
					'user_has_access' => \bp_is_my_profile(),
				)
			);

			\bp_core_new_subnav_item(
				array(
					'name'            => 'Products',
					'slug'            => 'products',
					'parent_url'      => trailingslashit( \bp_displayed_user_domain() . 'family-settings' ),
					'parent_slug'     => 'family-settings',
					'screen_function' => array( $this, 'productsubtab_render_main_screen_function' ),
					'position'        => 100,
					'user_has_access' => \bp_is_my_profile(),
				)
			);

			\bp_core_new_subnav_item(
				array(
					'name'            => 'Video',
					'slug'            => 'video-settings',
					'parent_url'      => trailingslashit( \bp_displayed_user_domain() . 'family-settings' ),
					'parent_slug'     => 'family-settings',
					'screen_function' => array( $this, 'videosubtab_render_main_screen_function' ),
					'position'        => 20,
					'user_has_access' => \bp_is_my_profile(),
				)
			);

			\bp_core_new_subnav_item(
				array(
					'name'            => 'Customers',
					'slug'            => 'customer-settings',
					'parent_url'      => trailingslashit( \bp_displayed_user_domain() . 'family-settings' ),
					'parent_slug'     => 'family-settings',
					'screen_function' => array( $this, 'customersubtab_render_main_screen_function' ),
					'position'        => 10,
					'user_has_access' => \bp_is_my_profile(),
				)
			);

			\bp_core_new_subnav_item(
				array(
					'name'            => 'Members and Payments',
					'slug'            => 'advanced-settings',
					'parent_url'      => trailingslashit( \bp_displayed_user_domain() . 'family-settings' ),
					'parent_slug'     => 'family-settings',
					'screen_function' => array( $this, 'advancedsubtab_render_main_screen_function' ),
					'position'        => 200,
					'user_has_access' => \bp_is_my_profile(),
				)
			);
		}

		// Setup My Video Tab.
		\bp_core_new_nav_item(
			array(
				'name'                    => 'Video Space',
				'slug'                    => 'video-space',
				'show_for_displayed_user' => true,
				'screen_function'         => array( $this, 'myvideo_render_main_screen_function' ),
				'item_css_id'             => 'far fa-address-card',
				'position'                => 1,
			)
		);

		// Setup my accounts tab.
		\bp_core_new_nav_item(
			array(
				'name'                    => 'My Account',
				'slug'                    => 'my-account',
				'show_for_displayed_user' => false,
				'screen_function'         => array( $this, 'account_render_main_screen_function' ),
				'item_css_id'             => 'far fa-address-card',
				'position'                => 80,
			)
		);
	}

	public function settings_render_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template
		\add_action( 'bp_template_content', array( $this, 'bp_settings_tab_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * This function renders the Settings Page Tab function
	 */
	public function bp_settings_tab_action() {
		echo $this->get_instance( \MyVideoRoomExtrasParking\Library\SectionTemplates::class )->account_centre_landing();
	}

	public function settingssubtab_render_main_screen_function() {

		// add title and content here - last is to call the members plugin.php template
		\add_action( 'bp_template_content', array( $this, 'bp_settingssubnav_tab_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		// echo do_shortcode ( '[youzer_groups per_page="10"]' );
	}

	/**
	 * This function renders the Settings Subnav Tab function
	 */
	public function bp_settingssubnav_tab_action() {
		echo \do_shortcode( '[wcfm endpoint="wcfm-settings"]' );
	}

	public function productsubtab_render_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template.
		\add_action( 'bp_template_content', array( $this, 'bp_productssubnav_tab_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * This function renders the Product Settings Subnav Tab function
	 */
	public function bp_productssubnav_tab_action() {
		echo $this->get_instance( \MyVideoRoomExtrasParking\Library\SectionTemplates::class )->control_panel_store_products();
	}

	public function videosubtab_render_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template
		\add_action( 'bp_template_content', array( $this, 'bp_videossubnav_tab_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * This function renders the Product Settings Subnav Tab function
	 */
	function bp_videossubnav_tab_action() {
		echo $this->get_instance( \MyVideoRoomExtrasParking\Library\SectionTemplates::class )->control_panel_store_video();
	}

	public function customersubtab_render_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template
		\add_action( 'bp_template_content', array( $this, 'bp_customersubnav_tab_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * This function renders the Product Settings Subnav Tab function
	 */
	public function bp_customersubnav_tab_action() {
		echo \do_shortcode( '[elementor-template id="36015"]' );
	}

	public function advancedsubtab_render_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template.
		\add_action( 'bp_template_content', array( $this, 'bp_advancedsubnav_tab_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * This function renders the Product Settings Subnav Tab function
	 */
	public function bp_advancedsubnav_tab_action() {
		echo $this->get_instance( \MyVideoRoomExtrasParking\Library\SectionTemplates::class )->control_panel_store_advanced();
	}

	public function account_render_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template
		\add_action( 'bp_template_content', array( $this, 'bp_account_tab_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * This function renders the Account Tab function
	 */
	public function bp_account_tab_action() {
		echo $this->get_instance( \MyVideoRoomExtrasParking\Library\SectionTemplates::class )->account_control_centre_alternate_dashboard();
	}

	public function myvideo_render_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template.
		\add_action( 'bp_template_content', array( $this, 'bp_myvideo_tab_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	function bp_myvideo_tab_action() {
		echo \do_shortcode( '[ccmvrswitch]' );
	}

	public function get_list( $input = '' ) {
		if ( is_numeric( $input ) ) {
			$field_id = $input;
		} else {
			$field_id = $this->get_instance( SiteDefaults::class )->defaults( $input );
		}

		if ( ! is_numeric( $field_id ) ) {
			return null;
		} else {

			$values = \xprofile_get_field( $field_id )->get_children();
		}

		?>
		<html lang="en">

		<head>
			<meta charset="utf-8">
			<title>Dynamically Generate Select Dropdowns</title>
		</head>

		<body>
			<form>
				<select>
					<option selected="selected">Choose one</option>
					<?php
					// A sample product array
					// $products = array("Mobile", "Laptop", "Tablet", "Camera");

					// Iterating through the product array
					foreach ( $values as $item ) {
						echo "<option value='strtolower( $item)'>$item</option>";
					}
					?>
				</select>
				<input type="submit" value="Submit">
			</form>
		</body>

		</html>



		<?php
	}



	/**
	 * Function to display the Portfolio area outside of the Buddypress normal nav menu
	 * This function has an issue with certain Elementor pages that call it - which means it can be disabled to edit the pages.
	 *
	 * @param array $params
	 *
	 * @return false|string
	 */
	public function display_portfolio_shortcode( $params = array() ) {
		$id = $params['id'] ?? null;
		return $this->display_portfolio( $id );
	}

	public function display_portfolio( $id ) {
		if ( $id ) {
			$user_id = $id;
		} else {
			$user_id = \bp_displayed_user_id();
		}
		$parent_id = $this->get_instance( WCFMHelpers::class )->staff_to_parent( $user_id );
		$bp        = \buddypress();

		// backup the child Id.
		$child_id = $bp->displayed_user->id;

		// set to the parent Id.
		$bp->displayed_user->id = $parent_id;

		// render whatever you want
		// Get Overview Widgets
		$profile_widgets = apply_filters(
			'yz_profile_main_widgets',
			\yz_option(
				'yz_profile_main_widgets',
				array(
					'slideshow' => 'visible',
					'project'   => 'visible',
					'skills'    => 'visible',
					'portfolio' => 'visible',
					'quote'     => 'visible',
					'instagram' => 'visible',
					'services'  => 'visible',
					'post'      => 'visible',
					'link'      => 'visible',
					'video'     => 'visible',
					'reviews'   => 'visible',
				)
			)
		);
		ob_start();

		include_once YZ_PUBLIC_CORE . 'functions/yz-general-functions.php';
		include_once YZ_PUBLIC_CORE . 'functions/yz-profile-functions.php';
		include_once YZ_PUBLIC_CORE . 'functions/yz-user-functions.php';
		include_once YZ_PUBLIC_CORE . 'class-yz-widgets.php';
		\yz_widgets()->get_widget_content( $profile_widgets );

		// reset.
		$bp->displayed_user->id = $child_id;

		return ob_get_clean();
	}

	public function setup_group_nav_action() {
		global $bp;
		if ( \bp_is_active( 'groups' ) && $bp->groups && $bp->groups->current_group ) {

			\bp_core_new_subnav_item(
				array(
					'name'            => 'Find Groups',
					'slug'            => 'find-groups',
					'parent_url'      => $bp->loggedin_user->domain . $bp->groups->slug . '/',
					'parent_slug'     => $bp->groups->slug,
					'screen_function' => array( $this, 'group_find_main_screen_function' ),
					'position'        => 40,
				)
			);

			$group_link = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';
			\bp_core_new_subnav_item(
				array(
					'name'            => __( 'Video Meeting', 'bp-invite-anyone' ),
					'slug'            => 'video-meeting',
					'parent_url'      => $group_link,
					'parent_slug'     => $bp->groups->current_group->slug,
					'screen_function' => array( $this, 'group_video_main_screen_function' ),
					'position'        => 300,
					'item_css_id'     => 'send-invites-by-email',
				)
			);
		}
	}

	/**
	 * This function renders the group Find tab function
	 */
	public function group_find_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template.
		\add_action( 'bp_template_content', array( $this, 'group_find_main_content_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		// echo do_shortcode ( '[youzer_groups per_page="10"]' );
	}

	/**
	 * This function renders the group Find tab Content
	 */
	public function group_find_main_content_action() {
		echo \do_shortcode( '[elementor-template id="32840"]' );
		// echo "hello";
	}

	/**
	 * This function renders the group Video Meet tab function
	 */
	public function group_video_main_screen_function() {
		// add title and content here - last is to call the members plugin.php template.
		\add_action( 'bp_template_content', array( $this, 'group_video_meeting_content_action' ) );
		\bp_core_load_template( \apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		// echo do_shortcode ( '[youzer_groups per_page="10"]' );
	}

	/**
	 * This function renders the Video Meeting tab Content that is a child of Video meet
	 */
	public function group_video_meeting_content_action() {
		echo $this->get_instance( BuddyPressVideo::class )->groupmeet_switch();
	}
}
