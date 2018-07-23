<?php
/**
 * Displays the content of the custom fields editor page.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Defines the list of js variables passed to vuejs for the custom fields editor.
 *
 * @return array
 */
function pno_get_custom_fields_editor_js_vars() {

	$js_vars = [
		'plugin_url'         => PNO_PLUGIN_URL,
		'rest'               => esc_url_raw( rest_url() ),
		'nonce'              => wp_create_nonce( 'wp_rest' ),
		'create_field_nonce' => wp_create_nonce( 'wp_rest' ),
		'delete_field_nonce' => wp_create_nonce( 'wp_rest' ),
		'field_types'        => pno_get_registered_field_types(),
		'labels'             => [
			'documentation'       => esc_html__( 'Documentation' ),
			'addons'              => esc_html__( 'View Addons' ),
			'title'               => esc_html__( 'Posterno custom fields' ),
			'custom_users'        => esc_html__( 'Customize profile fields' ),
			'custom_listings'     => esc_html__( 'Customize listings fields' ),
			'custom_registration' => esc_html__( 'Customize registration form' ),
			'profile'             => [
				'title'            => esc_html__( 'Posterno profile fields editor' ),
				'add_new'          => esc_html__( 'Add new profile field' ),
				'field_admin_only' => esc_html__( 'This field is editable only by an administrator.' ),
				'field_order'      => esc_html__( 'Drag and drop the rows below to change the order of the fields.' ),
			],
			'table'               => [
				'title'     => esc_html__( 'Field title' ),
				'type'      => esc_html__( 'Type' ),
				'required'  => esc_html__( 'Required' ),
				'privacy'   => esc_html__( 'Privacy' ),
				'editable'  => esc_html__( 'Editable' ),
				'actions'   => esc_html__( 'Actions' ),
				'not_found' => esc_html__( 'No fields yet, click the button above to add fields.' ),
				'edit'      => esc_html__( 'Edit field' ),
				'delete'    => esc_html__( 'Delete field' ),
			],
			'modal'               => [
				'field_name'      => esc_html__( 'New field name:' ),
				'field_type'      => esc_html__( 'Select field type:' ),
				'about_to_delete' => esc_html__( 'You are about to delete the field:' ),
				'delete_message'  => esc_html__( 'Are you sure you want to delete this field? This action is irreversible.' ),
			],
			'success'             => esc_html__( 'Changes successfully saved.' ),
		],
	];

	return $js_vars;

}

/**
 * Undocumented function
 *
 * @return void
 */
function pno_get_users_custom_fields_page_vars() {

	global $post;

	$js_vars = [
		'field_id'        => carbon_get_post_meta( $post->ID, 'field_meta_key' ),
		'field_type'      => carbon_get_post_meta( $post->ID, 'field_type' ),
		'is_default'      => (bool) get_post_meta( $post->ID, 'is_default_field', true ),
		'restricted_keys' => pno_get_registered_default_meta_keys(),
		'messages'        => [
			'no_meta_key_changes' => esc_html__( 'You are not allowed to change the reserved meta key for default fields.' ),
			'no_type_changes'     => esc_html__( 'The field type for default fields cannot be changed.' ),
			'reserved_key'        => esc_html__( 'This is a reserved meta key, please select a different key.' ),
		],
	];

	return $js_vars;

}

/**
 * Function responsible of displaying the custom fields page.
 * Actual output handled by vuejs.
 *
 * @since 0.1.0
 * @return void
 */
function pno_custom_fields_page() {
	echo '<div id="posterno-custom-fields-page"></div>';
}
