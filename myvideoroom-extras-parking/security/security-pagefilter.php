<?php
/*

For MVR- to get upgrades into buddypress templates.

if ( 'pbrhost' === $room_type ) {

// MVR - get membership levels for filtering subscribers and rejecting non subscribed users who shouldn't get video.
// @TODO FB Will be upgraded to plugin section when support for UMP and WCFM role types is added as plugin explicitly.

// MVR Case - valid to block personal rooms from non premium users, and non storeowners.
if ( Factory::get_instance( SiteDefaults::class )->is_mvr() ) {
	$umpblock  = Factory::get_instance( PageSwitches::class )->ump_membership_upgrade_block();
	$wcfmblock = Factory::get_instance( PageSwitches::class )->wcfm_membership_upgrade_block();

	if ( $umpblock ) {
		return $umpblock;
	}
	if ( $wcfmblock ) {
		return $wcfmblock;
	}
}
}
*/