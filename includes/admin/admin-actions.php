<?php
/**
 * Registers all the actions for the administration.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Delete cached list of pages when a page is updated or created.
 * This is needed to refresh the list of available pages for the options panel.
 *
 * @param string $post_id
 * @return void
 */
function pno_delete_pages_transient( $post_id ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	delete_transient( 'pno_get_pages' );
}
add_action( 'save_post_page', 'pno_delete_pages_transient' );

/**
 * Determines when the custom shortcodes editor can be loaded.
 *
 * @access public
 * @since  0.1.0
 * @return void
*/
function pno_shortcodes_add_mce_button() {

	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'pno_shortcodes_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'pno_shortcodes_register_mce_button' );
	}
}
add_action( 'admin_head', 'pno_shortcodes_add_mce_button' );

/**
 * Adds js strings to the footer so the shortcodes editor is translatable.
 *
 * @return void
 */
function pno_localize_tinymce_editor() {

	$js_vars = [
		'title' => esc_html__( 'Posterno shortcodes' ),
		'forms' => [
			'title'        => esc_html__( 'Forms' ),
			'login'        => esc_html__( 'Login form' ),
			'registration' => esc_html__( 'Registration form' ),
			'password'     => esc_html__( 'Password recovery form' ),
		],
		'links' => [
			'title'  => esc_html__( 'Links' ),
			'login'  => [
				'title'    => esc_html__( 'Login link' ),
				'redirect' => esc_html__( 'Redirect after login (optional)' ),
				'label'    => esc_html__( 'Link Label' ),
			],
			'logout' => [
				'title'    => esc_html__( 'Logout link' ),
				'redirect' => esc_html__( 'Redirect after logout (optional)' ),
				'label'    => esc_html__( 'Link Label' ),
			],
		],
		'pages' => [
			'title'     => esc_html__( 'Pages' ),
			'dashboard' => esc_html__( 'Dashboard' ),
		],
	];

	?>
	<script type="text/javascript">
		var pnotinymce = <?php echo json_encode( $js_vars ); ?>
	</script>
	<?php

}
add_action( 'admin_footer', 'pno_localize_tinymce_editor' );

/**
 * Hides certain settings of custom fields post type.
 *
 * @return void
 */
function pno_hide_custom_fields_pt_settings() {

	global $post_type;

	$post_types = array(
		'pno_users_fields',
		'pno_signup_fields',
	);

	if ( in_array( $post_type, $post_types ) ) {
		echo '
		<style type="text/css">
			#post-preview, #view-post-btn,
			#misc-publishing-actions #visibility,
			#misc-publishing-actions .misc-pub-curtime,
			.page-title-action {
				display: none;
			}

			.pno-field-is-default-notice {
				background: #e5f5fa;
				padding: 10px !important;
				margin: -10px -12px -16px !important;
			}

		</style>';
	}
}
add_action( 'admin_head-post-new.php', 'pno_hide_custom_fields_pt_settings' );
add_action( 'admin_head-post.php', 'pno_hide_custom_fields_pt_settings' );

/**
 * Add link back to the fields list table within the post type editing screen.
 *
 * @return void
 */
function pno_after_custom_fields_post_title() {

	global $post;

	if ( $post instanceof WP_Post && isset( $post->post_type ) && $post->post_type == 'pno_users_fields' ) {

		$admin_url = admin_url( 'edit.php?post_type=listings&page=posterno-custom-fields#/profile-fields' );
		echo '<br/><span class="dashicons dashicons-editor-table"></span> <a href="' . $admin_url . '">' . esc_html__( 'All profile fields' ) . '</a>';

	}

}
add_action( 'edit_form_before_permalink', 'pno_after_custom_fields_post_title' );

/**
 * Force wipe of custom field when trashed.
 *
 * @param string $post_id
 * @return void
 */
function pno_force_delete_on_custom_fields_trash( $post_id ) {

	if ( get_post_type( $post_id ) == 'pno_users_fields' ) {
		wp_delete_post( $post_id, true );

		wp_safe_redirect( admin_url( 'edit.php?post_type=listings&trashed=true&page=posterno-custom-fields#/profile-fields' ) );
		exit;
	} elseif ( get_post_type( $post_id ) == 'pno_signup_fields' ) {
		wp_delete_post( $post_id, true );

		wp_safe_redirect( admin_url( 'edit.php?post_type=listings&trashed=true&page=posterno-custom-fields#/registration-form' ) );
		exit;
	}

}
add_action( 'wp_trash_post', 'pno_force_delete_on_custom_fields_trash' );
