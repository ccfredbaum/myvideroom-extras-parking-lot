<?php
/**
 * Helper functions for WordPress User Roles
 *
 * @package MyVideoRoomExtrasPlugin\Library
 */

namespace MyVideoRoomExtrasParking\Library;

/**
 * Class UserRoles
 */
class UserRoles {

	/**
	 * The target user
	 *
	 * @var \WP_User|null
	 */
	private ?\WP_User $user;

	// ---

	/**
	 * UserRoles constructor.
	 *
	 * @param string $user - WP User Object.
	 */
	public function __construct( \WP_User $user = null ) {
		if ( $user ) {
			$this->user = $user;
		} else {
			$this->user = \wp_get_current_user();
		}
	}

	// ---

	/**
	 * Is the current WordPress user an administrator?
	 *
	 * @return bool
	 */
	public function is_wordpress_administrator(): bool {
		return $this->user_has_role( 'administrator' );
	}

	/**
	 * Is the current WordPress user a WCFM Vendor?
	 *
	 * @return bool
	 */
	public function is_wcfm_vendor(): bool {
		return $this->user_has_role( 'wcfm_vendor' );
	}

	/**
	 * Is the current WordPress user a WCFM Shop Staff Member?
	 *
	 * @return bool
	 */
	public function is_wcfm_shop_staff(): bool {
		return $this->user_has_role( 'shop_staff' );
	}

	/**
	 * Is the current WordPress user a WCFM Store Manager?
	 *
	 * @return bool
	 */
	public function is_wcfm_store_manager(): bool {
		return $this->user_has_role( 'store_manager' );
	}

	/**
	 * Does the user have a certain role
	 *
	 * @param string $role The role to check.
	 *
	 * @return bool
	 */
	private function user_has_role( string $role ): bool {
		return ( $this->user && in_array( $role, $this->user->roles, true ) );
	}

	/**
	 * Get current user WordPress Roles
	 *
	 * @return array of roles
	 * @param int $user_id - the user id.
	 */
	public function get_user_roles( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$user_meta  = get_userdata( $user_id );
		$user_roles = $user_meta->roles;

		return $user_roles;

	}





}
