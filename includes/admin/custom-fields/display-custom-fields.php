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
		'plugin_url' => PNO_PLUGIN_URL,
		'rest'       => esc_url_raw( rest_url() ),
		'nonce'      => wp_create_nonce( 'wp_rest' ),
		'labels'     => [
			'documentation'       => esc_html__( 'Documentation' ),
			'addons'              => esc_html__( 'View Addons' ),
			'title'               => esc_html__( 'Posterno custom fields' ),
			'custom_users'        => esc_html__( 'Customize profile fields' ),
			'custom_listings'     => esc_html__( 'Customize listings fields' ),
			'custom_registration' => esc_html__( 'Customize registration form' ),
			'profile'             => [
				'title'   => esc_html__( 'Posterno profile fields editor' ),
				'add_new' => esc_html__( 'Add new profile field' ),
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
			'success'             => esc_html__( 'Changes successfully saved.' ),
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
