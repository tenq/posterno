<?php
/**
 * Registers all the filters for the administration.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add new links to the plugin's action links list.
 *
 * @since 1.0.0
 * @return array
 */
function pno_add_settings_link( $links ) {
	$settings_link = '<a href="' . admin_url( 'edit.php?post_type=listings&page=posterno-settings' ) . '">' . esc_html__( 'Settings' ) . '</a>';
	$docs_link     = '<a href="https://docs.posterno.com/" target="_blank">' . esc_html__( 'Documentation' ) . '</a>';
	$addons_link   = '<a href="https://posterno.com/addons" target="_blank">' . esc_html__( 'Addons' ) . '</a>';
	array_unshift( $links, $settings_link );
	array_unshift( $links, $docs_link );
	array_unshift( $links, $addons_link );
	return $links;
}
add_filter( 'plugin_action_links_' . PNO_PLUGIN_BASE, 'pno_add_settings_link' );

/**
 * Highlight all pages used by Posterno into the pages list table.
 *
 * @param array $post_states
 * @param object $post
 * @return void
 */
function pno_highlight_pages( $post_states, $post ) {
	$mark    = 'Posterno';
	$post_id = $post->ID;
	switch ( $post_id ) {
		case pno_get_login_page_id():
		case pno_get_registration_page_id():
		case pno_get_password_recovery_page_id():
			$post_states['pno_page'] = $mark;
			break;
	}
	return $post_states;
}
add_filter( 'display_post_states', 'pno_highlight_pages', 10, 2 );

/**
 * Prevents cancellation of default custom fields.
 *
 * @param array $caps
 * @param string $cap
 * @param string $user_id
 * @param array $args
 * @return array
 */
function pno_prevent_default_fields_cancellation( $caps, $cap, $user_id, $args ) {

	if ( 'delete_post' !== $cap || empty( $args[0] ) ) {
		return $caps;
	}

	// Target the payment and transaction post types.
	if ( in_array( get_post_type( $args[0] ), [ 'pno_users_fields' ], true ) ) {
		$is_default = get_post_meta( $args[0], 'is_default_field', true );
		if ( $is_default ) {
			$caps[] = 'do_not_allow';
		}
	} elseif ( in_array( get_post_type( $args[0] ), [ 'pno_signup_fields' ], true ) ) {
		$is_default = carbon_get_post_meta( $args[0], 'field_is_default' );
		if ( $is_default ) {
			$caps[] = 'do_not_allow';
		}
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'pno_prevent_default_fields_cancellation', 10, 4 );

