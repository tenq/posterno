<?php
/**
 * List of functions used within template files.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Parses a form action string to create an ID for the form tag of a form.
 *
 * @param string $form form name.
 * @return void
 */
function pno_get_form_id( $form ) {
	$id = 'pno-form-' . $form;
	return esc_attr( $id );
}

/**
 * Retrieve the correct label for the login form.
 *
 * @return string
 */
function pno_get_login_label() {

	$label = esc_html__( 'Username' );

	$login_method = pno_get_option( 'login_method' );

	if ( $login_method == 'email' ) {
		$label = esc_html__( 'Email' );
	} elseif ( $login_method == 'username_email' ) {
		$label = esc_html__( 'Username or email' );
	}

	return $label;

}

/**
 * Retrieve the classes for a given form field as an array.
 *
 * @param array $field
 * @param string $class
 * @return array
 */
function pno_get_form_field_class( $field_key, $field, $class = '' ) {

	$classes = [ 'pno-field' ];

	if ( $field_key ) {
		$classes[] = 'pno-field-' . $field_key;
	}

	$classes[] = 'pno-field-' . $field['type'];

	if ( $field['type'] == 'checkbox' ) {
		$classes[] = 'form-check';
	} else {
		$classes[] = 'form-group';
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS classes for the current form field.
	 *
	 * @param array $classes
	 * @param array $field
	 * @param string $class
	 */
	$classes = apply_filters( 'pno_form_field_classes', $classes, $field_key, $field, $class );

	return array_unique( $classes );

}

/**
 * Display the classes for a given form field.
 *
 * @param string $field_key
 * @param array $field
 * @param string $class
 * @return void
 */
function pno_form_field_class( $field_key, $field, $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', pno_get_form_field_class( $field_key, $field, $class ) ) . '"';
}
