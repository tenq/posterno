<?php
/**
 * Handles manipulation and registration of admin pages.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register admin pages for the plugin.
 *
 * @return void
 */
function pno_add_admin_pages() {

	global $pno_settings_page, $pno_custom_fields_page, $pno_registration_fields_page, $pno_profile_fields_page, $pno_listings_fields_page, $pno_emails_editor;

	$pno_custom_fields_page       = add_submenu_page( 'edit.php?post_type=listings', __( 'Custom fields' ), __( 'Custom fields' ), 'manage_options', 'posterno-custom-fields', 'pno_custom_fields_page' );
	$pno_profile_fields_page      = add_submenu_page( 'edit.php?post_type=listings', __( 'Custom profile fields' ), __( 'Custom profile fields' ), 'manage_options', 'posterno-custom-profile-fields', 'pno_custom_profile_fields_page' );
	$pno_registration_fields_page = add_submenu_page( 'edit.php?post_type=listings', __( 'Customize registration form' ), __( 'Customize registration form' ), 'manage_options', 'posterno-custom-registration-form', 'pno_custom_registration_fields_page' );
	$pno_settings_page            = add_submenu_page( 'edit.php?post_type=listings', __( 'Posterno Settings' ), __( 'Settings' ), 'manage_options', 'posterno-settings', 'pno_options_page' );

	$pno_emails_editor = add_menu_page( esc_html__( 'Posterno emails editor' ), esc_html__( 'Emails' ), 'manage_options', 'posterno-emails', 'my_menu_output', 'dashicons-email-alt', 70 );

}
add_action( 'admin_menu', 'pno_add_admin_pages', 10 );

/**
 * Remove taxonomies and other menu items from the listings menu in the admin panel.
 *
 * @return void
 */
function pno_admin_remove_submenus() {

	$taxonomies = get_object_taxonomies( 'listings' );

	if ( empty( $taxonomies ) ) {
		return;
	}

	foreach ( $taxonomies as $taxonomy ) {
		remove_submenu_page( 'edit.php?post_type=listings', 'edit-tags.php?taxonomy=' . $taxonomy . '&amp;post_type=listings' );
	}

	remove_submenu_page( 'edit.php?post_type=listings', 'posterno-custom-registration-form' );
	remove_submenu_page( 'edit.php?post_type=listings', 'posterno-custom-profile-fields' );

}
add_action( 'admin_menu', 'pno_admin_remove_submenus', 999 );
