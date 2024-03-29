<?php
/**
 * Functions to work with the settings panel.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Sematico LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Retrieve the list of settings tabs for the options panel.
 *
 * @return array
 */
function pno_get_registered_settings_tabs() {

	$tabs = [
		'general'  => esc_html__( 'General', 'posterno' ),
		'accounts' => esc_html__( 'Accounts', 'posterno' ),
		'profiles' => esc_html__( 'Profiles', 'posterno' ),
		'emails'   => esc_html__( 'Emails', 'posterno' ),
		'listings' => esc_html__( 'Listings', 'posterno' ),
	];

	/**
	 * Allows developers to register or deregister tabs for the
	 * settings panel.
	 *
	 * @since 0.1.0
	 * @param array $tabs
	 */
	return apply_filters( 'pno_registered_settings_tabs', $tabs );

}

/**
 * Retrieve the list of settings subsections for all tabs.
 *
 * @return array
 */
function pno_get_registered_settings_tabs_sections() {

	$sections = [
		'general'  => [
			'pages' => esc_html__( 'Pages setup', 'posterno' ),
			'theme' => esc_html__( 'Theme', 'posterno' ),
			'misc'  => esc_html__( 'Miscellaneous', 'posterno' ),
		],
		'accounts' => [
			'login'                  => esc_html__( 'Login', 'posterno' ),
			'registration'           => esc_html__( 'Registration', 'posterno' ),
			'password_recovery_form' => esc_html__( 'Password recovery', 'posterno' ),
			'redirects'              => esc_html__( 'Redirects', 'posterno' ),
			'privacy'                => esc_html__( 'Privacy', 'posterno' ),
			'profiles_settings'      => esc_html__( 'Profile', 'posterno' ),
		],
		'emails'   => [
			'emails_settings' => esc_html__( 'Configuration', 'posterno' ),
			'emails_listings' => esc_html__( 'Listings', 'posterno' ),
			'emails_test'     => esc_html__( 'Send test email', 'posterno' ),
		],
		'listings' => [
			'listings_settings'   => esc_html__( 'Configuration', 'posterno' ),
			'listings_submission' => esc_html__( 'Submission', 'posterno' ),
			'listings_management' => esc_html__( 'Management', 'posterno' ),
			'listings_hours'      => esc_html__( 'Business hours', 'posterno' ),
			'listings_maps'       => esc_html__( 'Maps', 'posterno' ),
		],
		'profiles' => [
			'profiles_setup' => esc_html__( 'Configuration', 'posterno' ),
		],
	];

	/**
	 * Allows developers to register or deregister subsections for tabs in the
	 * settings panel.
	 *
	 * @since 0.1.0
	 * @param array $sections
	 */
	return apply_filters( 'pno_registered_settings_tabs_sections', $sections );

}

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not.
 *
 * @since 0.1.0
 * @param string $key the key to retrieve.
 * @param mixed  $default default value to use in case option is not available.
 * @global $pno_options Array of all the Posterno's options.
 * @return mixed
 */
function pno_get_option( $key = '', $default = false ) {
	global $pno_options;
	$value = ! empty( $pno_options[ $key ] )
		? $pno_options[ $key ]
		: $default;

	/**
	 * Filters the retrieval of an option.
	 *
	 * @since 0.1.0
	 * @param mixed $value the original value.
	 * @param string $key the key of the option being retrieved.
	 * @param mixed $default default value if nothing is found.
	 */
	$value = apply_filters( 'pno_get_option', $value, $key, $default );
	return apply_filters( 'pno_get_option_' . $key, $value, $key, $default );
}

/**
 * Update an option
 *
 * Updates an pno setting value in both the db and the global variable.
 * Warning: Passing in an empty, false or null string value will remove
 *          the key from the pno_options array.
 *
 * @since 0.1.0
 *
 * @param string          $key         The Key to update.
 * @param string|bool|int $value       The value to set the key to.
 * @global                $pno_options Array of all the Posterno Options.
 * @return boolean True if updated, false if not.
 */
function pno_update_option( $key = '', $value = false ) {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $pno_options;

	// If no key, exit.
	if ( empty( $key ) ) {
		return false;
	}

	if ( empty( $value ) ) {
		$remove_option = pno_delete_option( $key );
		return $remove_option;
	}

	// First let's grab the current settings.
	$options = get_option( 'pno_settings' );

	/**
	 * Filter the final value of an option before being saved into the database.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value the value about to be saved.
	 * @param string $key the key of the option that is being saved.
	 */
	$value = apply_filters( 'pno_update_option', $value, $key );

	// Next let's try to update the value.
	$options[ $key ] = $value;
	$did_update      = update_option( 'pno_settings', $options );

	// If it updated, let's update the global variable.
	if ( $did_update ) {
		$pno_options[ $key ] = $value;
	}
	return $did_update;
}

/**
 * Remove an option
 * Removes a setting value in both the db and the global variable.
 *
 * @since 0.1.0
 *
 * @param string $key         The Key to delete.
 * @global       $pno_options Array of all the Posterno Options.
 * @return boolean True if removed, false if not.
 */
function pno_delete_option( $key = '' ) {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $pno_options;

	// If no key, exit.
	if ( empty( $key ) ) {
		return false;
	}

	// First let's grab the current settings.
	$options = get_option( 'pno_settings' );

	// Next let's try to update the value.
	if ( isset( $options[ $key ] ) ) {
		unset( $options[ $key ] );
	}

	// Remove this option from the global Posterno settings to the array_merge in pno_settings_sanitize() doesn't re-add it.
	if ( isset( $pno_options[ $key ] ) ) {
		unset( $pno_options[ $key ] );
	}

	$did_update = update_option( 'pno_settings', $options );

	// If it updated, let's update the global variable.
	if ( $did_update ) {
		$pno_options = $options;
	}
	return $did_update;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array Posterno settings
 */
function pno_get_settings() {

	// Get the option key.
	$settings = get_option( 'pno_settings' );

	/**
	 * Filter retrieval of all options.
	 *
	 * @since 0.1.0
	 * @param mixed $settings the list of options stored into the database.
	 */
	return apply_filters( 'pno_get_settings', $settings );

}
