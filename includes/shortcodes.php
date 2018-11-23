<?php
/**
 * Shortcodes definition
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Display a login link.
 *
 * @param array  $atts attributes list of the shortcode.
 * @param string $content content added within the shortcode.
 * @return string
 */
function pno_login_link( $atts, $content = null ) {
	// phpcs:ignore
	extract(
		shortcode_atts(
			array(
				'redirect' => '',
				'label'    => esc_html__( 'Login' ),
			),
			$atts
		)
	);
	if ( is_user_logged_in() ) {
		$output = '';
	} else {
		$url    = wp_login_url( $redirect );
		$output = '<a href="' . esc_url( $url ) . '" class="pno-login-link">' . esc_html( $label ) . '</a>';
	}
	return $output;
}
add_shortcode( 'pno_login_link', 'pno_login_link' );

/**
 * Display a logout link.
 *
 * @param array  $atts attributes list of the shortcode.
 * @param string $content content added within the shortcode.
 * @return string
 */
function pno_logout_link( $atts, $content = null ) {
	// phpcs:ignore
	extract(
		shortcode_atts(
			array(
				'redirect' => '',
				'label'    => esc_html__( 'Logout' ),
			),
			$atts
		)
	);
	$output = '';
	if ( is_user_logged_in() ) {
		$output = '<a href="' . esc_url( wp_logout_url( $redirect ) ) . '">' . esc_html( $label ) . '</a>';
	}
	return $output;
}
add_shortcode( 'pno_logout_link', 'pno_logout_link' );

/**
 * Displays the dashboard for the listings.
 *
 * @return string
 */
function pno_dashboard() {

	ob_start();

	posterno()->templates->get_template_part( 'dashboard' );

	return ob_get_clean();

}
add_shortcode( 'pno_dashboard', 'pno_dashboard' );

/**
 * Displays the login form to visitors and display a notice to logged in users.
 *
 * @return string
 */
function pno_login_form() {

	ob_start();

	if ( is_user_logged_in() ) {

		$data = [
			'user' => wp_get_current_user(),
		];

		posterno()->templates
			->set_template_data( $data )
			->get_template_part( 'logged-user' );

	} else {

		//phpcs:ignore
		echo posterno()->forms->get_form( 'login' );

	}

	return ob_get_clean();
}
add_shortcode( 'pno_login_form', 'pno_login_form' );

/**
 * Display the registration form.
 *
 * @return string
 */
function pno_registration_form() {

	ob_start();

	if ( is_user_logged_in() ) {

		$data = [
			'user' => wp_get_current_user(),
		];

		posterno()->templates
			->set_template_data( $data )
			->get_template_part( 'logged-user' );

	} else {

		//phpcs:ignore
		echo posterno()->forms->get_form( 'registration' );

	}

	return ob_get_clean();

}
add_shortcode( 'pno_registration_form', 'pno_registration_form' );

/**
 * Displays the password recovery form to visitors and a notice to logged in users.
 *
 * @return string
 */
function pno_password_recovery_form() {

	ob_start();

	if ( is_user_logged_in() ) {

		$data = [
			'user' => wp_get_current_user(),
		];

		posterno()->templates
			->set_template_data( $data )
			->get_template_part( 'logged-user' );

	} else {

		//phpcs:ignore
		echo posterno()->forms->get_form( 'password-recovery' );

	}

	return ob_get_clean();

}
add_shortcode( 'pno_password_recovery_form', 'pno_password_recovery_form' );

/**
 * Displays the account customization form.
 *
 * @return string
 */
function pno_account_form() {

	ob_start();

	if ( is_user_logged_in() ) {
		//phpcs:ignore
		echo posterno()->forms->get_form( 'account' );
	}

	return ob_get_clean();

}
add_shortcode( 'pno_account_customization_form', 'pno_account_form' );

/**
 * Displays the change password form.
 *
 * @return string
 */
function pno_change_password_form() {

	ob_start();

	if ( is_user_logged_in() ) {
		//phpcs:ignore
		echo posterno()->forms->get_form( 'password-change' );

	}

	return ob_get_clean();

}
add_shortcode( 'pno_change_password_form', 'pno_change_password_form' );

/**
 * Displays the user's data request form.
 *
 * @return string
 */
function pno_request_data_form() {

	ob_start();

	if ( is_user_logged_in() ) {
		//phpcs:ignore
		echo posterno()->forms->get_form( 'data-request' );

	}

	return ob_get_clean();

}
add_shortcode( 'pno_request_data_form', 'pno_request_data_form' );

/**
 * Displays the user's data erasure form.
 *
 * @return string
 */
function pno_request_data_erasure_form() {

	ob_start();

	if ( is_user_logged_in() ) {
		//phpcs:ignore
		echo posterno()->forms->get_form( 'data-erasure' );
	}

	return ob_get_clean();

}
add_shortcode( 'pno_request_data_erasure_form', 'pno_request_data_erasure_form' );

/**
 * Displays the account cancellation form.
 *
 * @return string
 */
function pno_delete_account_form() {

	ob_start();

	if ( is_user_logged_in() ) {
		//phpcs:ignore
		echo posterno()->forms->get_form( 'account-delete' );
	}

	return ob_get_clean();

}
add_shortcode( 'pno_delete_account_form', 'pno_delete_account_form' );
