<?php
/**
 * View Connections
 *
 * @package MyVideoRoomExtrasParking\Modules\WCFM
 */

namespace MyVideoRoomExtrasParking\MVR;

use MyVideoRoomExtrasParking\Core\SiteDefaults;
use MyVideoRoomExtrasParking\Shortcode as Shortcode;

/**
 * Class ViewConnections
 */
class ViewConnections extends Shortcode {

	/**
	 * Install the shortcode
	 */
	public function runtime() {
		\add_filter( 'wcmp_vendor_store_header_hide_store_email', array( $this, 'wcmp_vendor_store_header_custom_hide_email' ) );

		$this->add_shortcode( 'vstaffview', array( $this, 'view_staff' ) );
	}

	public function wcmp_vendor_store_header_custom_hide_email() {
		return 'Enable';
	}

	public function view_staff() {

		$vendor_id = $this->get_instance( SiteDefaults::class )->page_owner();

		$shop_staff_html = '';

		$staff_user_role = \apply_filters( 'wcfm_staff_user_role', 'shop_staff' );
		$args            = array(
			'role__in'    => array( $staff_user_role ),
			'orderby'     => 'ID',
			'order'       => 'ASC',
			'offset'      => 0,
			'number'      => -1,
			'meta_key'    => '_wcfm_vendor',
			'meta_value'  => $vendor_id,
			'count_total' => false,
		);

		$wcfm_shop_staffs_array = get_users( $args );

		$result_count = count( $wcfm_shop_staffs_array );
		if ( $result_count >= 1 ) {
			$is_first = true;
			foreach ( $wcfm_shop_staffs_array as $wcfm_shop_staffs_single ) {
				if ( ! $is_first ) {
					$shop_staff_html .= ', ';
				}
				$shop_staff_html .= $wcfm_shop_staffs_single->ID;
				$is_first         = false;
			}
		}

		return \do_shortcode( '[youzer_members include="' . $shop_staff_html . '" ]' );
	}



}
