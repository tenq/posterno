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
		case pno_get_listing_submission_page_id():
			$post_states['pno_page'] = $mark;
			break;
	}
	return $post_states;
}
add_filter( 'display_post_states', 'pno_highlight_pages', 10, 2 );

/**
 * Prevents cancellation of default custom fields.
 *
 * @param array  $caps capabilities list.
 * @param string $cap current capability being checked.
 * @param string $user_id the user id being checked.
 * @param array  $args args list of the post being checked.
 * @return array
 */
function pno_prevent_default_fields_cancellation( $caps, $cap, $user_id, $args ) {

	if ( 'delete_post' !== $cap || empty( $args[0] ) ) {
		return $caps;
	}

	// Target the payment and transaction post types.
	if ( in_array( get_post_type( $args[0] ), [ 'pno_users_fields' ], true ) ) {
		$is_default = carbon_get_post_meta( $args[0], 'profile_is_default_field', true );
		if ( $is_default ) {
			$caps[] = 'do_not_allow';
		}
	} elseif ( in_array( get_post_type( $args[0] ), [ 'pno_signup_fields' ], true ) ) {
		$is_default = carbon_get_post_meta( $args[0], 'registration_field_is_default' );
		if ( $is_default ) {
			$caps[] = 'do_not_allow';
		}
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'pno_prevent_default_fields_cancellation', 10, 4 );

/**
 * Setup the buttons for the tinymce editor for the emails editor post type.
 *
 * @param array $settings the settings defined for the editor.
 * @return array
 */
function pno_setup_tinymce_buttons_for_emails_editor( $settings ) {

	$screen = get_current_screen();

	if ( isset( $screen->id ) && $screen->id === 'pno_emails' ) {
		$settings['toolbar1'] = 'formatselect,bold,italic,underline,bullist,numlist,blockquote,hr,link,unlink,strikethrough,spellchecker,undo,redo,dfw,wp_help';
		$settings['toolbar2'] = '';
	}

	return $settings;

}
add_filter( 'tiny_mce_before_init', 'pno_setup_tinymce_buttons_for_emails_editor', 10 );

/**
 * Remove the add media button for the tinymce editor of the emails editor.
 *
 * @param array $settings the settings defined for the editor.
 * @return array
 */
function pno_remove_add_media_button_for_emails_editor( $settings ) {

	$current_screen = get_current_screen();

	// Post types for which the media buttons should be removed.
	$post_types = array( 'pno_emails' );

	// Bail out if media buttons should not be removed for the current post type.
	if ( ! $current_screen || ! in_array( $current_screen->post_type, $post_types, true ) ) {
		return $settings;
	}

	$settings['media_buttons'] = false;

	return $settings;

}
add_filter( 'wp_editor_settings', 'pno_remove_add_media_button_for_emails_editor', 10 );

/**
 * Add a new column to the post type admin list table.
 *
 * @param array $columns already registered columns.
 * @return array
 */
function pno_emails_post_type_columns( $columns ) {

	unset( $columns['date'] );

	$columns['situations'] = esc_html__( 'Situations' );

	return $columns;
}
add_filter( 'manage_pno_emails_posts_columns', 'pno_emails_post_type_columns' );
