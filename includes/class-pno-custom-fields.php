<?php
/**
 * Handles registration and management of the custom fields settings.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The class that handles the custom fields settings.
 */
class PNO_Custom_Fields {

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'carbon_fields_register_fields', [ $this, 'register_profile_fields_settings' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'register_registration_fields_settings' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'register_profile_fields' ] );
	}

	/**
	 * Register global settings for all fields.
	 *
	 * @return void
	 */
	public function register_profile_fields_settings() {
		Container::make( 'post_meta', esc_html__( 'Main field settings' ) )
		->where( 'post_type', '=', 'pno_users_fields' )

		->add_tab(
			esc_html__( 'General' ), array(

				Field::make( 'select', 'field_type', esc_html__( 'Field type' ) )
					->set_required()
					->add_options( pno_get_registered_field_types() )
					->set_help_text( esc_html__( 'The selected field type determines how the field will look onto the account and registration forms.' ) ),

				Field::make( 'complex', 'field_selectable_options', esc_html__( 'Field selectable options' ) )
					->set_conditional_logic(
						array(
							'relation' => 'AND', // Optional, defaults to "AND"
							array(
								'field'   => 'field_type',
								'value'   => pno_get_multi_options_field_types(), // Optional, defaults to "". Should be an array if "IN" or "NOT IN" operators are used.
								'compare' => 'IN', // Optional, defaults to "=". Available operators: =, <, >, <=, >=, IN, NOT IN
							),
						)
					)
					->set_layout( 'tabbed-vertical' )
					->set_help_text( esc_html__( 'Add options for this field type.' ) )
					->add_fields(
						array(
							Field::make( 'text', 'option_title', esc_html__( 'Option title' ) )->set_help_text( esc_html__( 'Enter the title of this option.' ) ),
						)
					),

				Field::make( 'text', 'field_label', esc_html__( 'Custom form label' ) )
					->set_help_text( esc_html__( 'This text will be used as label within the registration and account settings forms. Leave blank to use the field title.' ) ),

				Field::make( 'text', 'field_placeholder', esc_html__( 'Placeholder' ) )
					->set_help_text( esc_html__( 'This text will appear within the field when empty. Leave blank if not needed.' ) ),

				Field::make( 'textarea', 'field_description', esc_html__( 'Field description' ) )
					->set_help_text( esc_html__( 'This is the text that appears as a description within the forms. Leave blank if not needed.' ) ),

				Field::make( 'text', 'field_file_max_size', esc_html__( 'Upload max size:' ) )
					->set_conditional_logic(
						array(
							'relation' => 'AND', // Optional, defaults to "AND"
							array(
								'field'   => 'field_type',
								'value'   => 'file', // Optional, defaults to "". Should be an array if "IN" or "NOT IN" operators are used.
								'compare' => '=', // Optional, defaults to "=". Available operators: =, <, >, <=, >=, IN, NOT IN
							),
						)
					)
					->set_help_text( esc_html__( 'Enter the maximum file size (in bytes) allowed for uploads through this field. Leave blank to use server settings.' ) ),

			)
		)
		->add_tab(
			esc_html__( 'Validation' ), array(

				Field::make( 'checkbox', 'field_is_required', esc_html__( 'Set as required' ) )
					->set_help_text( esc_html__( 'Enable this option so the field must be filled before the form can be processed.' ) ),

			)
		)
		->add_tab(
			esc_html__( 'Permissions' ), array(

				Field::make( 'checkbox', 'field_is_hidden', esc_html__( 'Admin only?' ) )
					->set_help_text( esc_html__( 'Enable this option to allow only administrators to customize the field. Hidden fields will not be customizable from the account settings page.' ) ),

				Field::make( 'checkbox', 'field_is_read_only', esc_html__( 'Set as read only' ) )
					->set_help_text( esc_html__( 'Enable to prevent users from editing this field but still make it visible within the account settings page.' ) ),

			)
		);

		Container::make( 'post_meta', esc_html__( 'Advanced' ) )
		->where( 'post_type', '=', 'pno_users_fields' )
		->set_context( 'side' )
		->set_priority( 'default' )
			->add_fields(
				array(
					Field::make( 'text', 'field_meta_key', esc_html__( 'Unique meta key' ) )
						->set_required( true )
						->set_help_text( esc_html__( 'The key must be unique for each field and written in lowercase with an underscore ( _ ) separating words e.g country_list or job_title. This will be used to store information about your users into the database of your website.' ) ),
					Field::make( 'text', 'field_custom_classes', esc_html__( 'Custom css classes' ) )
						->set_help_text( esc_html__( 'Enter custom css classes to customize the style of the field. Leave blank if not needed.' ) ),
				)
			);

	}

	/**
	 * Register profile fields in the admin panel.
	 *
	 * @return void
	 */
	public function register_profile_fields() {

		$fields_query = [
			'post_type'              => 'pno_users_fields',
			'posts_per_page'         => 100,
			'nopaging'               => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'post_status'            => 'publish',
			'meta_query'             => array(
				array(
					'key'     => 'is_default_field',
					'compare' => 'NOT EXISTS',
				),
			),
		];

		$fields       = new WP_Query( $fields_query );
		$admin_fields = [];

		if ( $fields->have_posts() ) {

			while ( $fields->have_posts() ) {

				$fields->the_post();

				$custom_field = new PNO_Profile_Field( get_the_id() );

				if ( $custom_field instanceof PNO_Profile_Field && ! empty( $custom_field->get_meta() ) ) {

					$type = $custom_field->get_type();

					switch ( $type ) {
						case 'url':
						case 'email':
						case 'number':
						case 'password':
							$type = 'text';
							break;
						case 'multicheckbox':
							$type = 'set';
							break;
					}

					if ( $type == 'select' || $type == 'set' || $type == 'multiselect' || $type == 'radio' ) {
						$admin_fields[] = Field::make( $type, $custom_field->get_meta(), $custom_field->get_name() )->add_options( $custom_field->get_selectable_options() );
					} elseif ( $type == 'file' ) {
						$admin_fields[] = Field::make( $type, $custom_field->get_meta(), $custom_field->get_name() )->set_value_type( 'url' );
					} elseif ( $custom_field->get_type() == 'number' ) {
						$admin_fields[] = Field::make( $type, $custom_field->get_meta(), $custom_field->get_name() )->set_attribute( 'type', 'number' );
					} elseif ( $custom_field->get_type() == 'password' ) {
						$admin_fields[] = Field::make( $type, $custom_field->get_meta(), $custom_field->get_name() )->set_attribute( 'type', 'password' );
					} else {
						$admin_fields[] = Field::make( $type, $custom_field->get_meta(), $custom_field->get_name() );
					}
				}
			}
		}

		wp_reset_postdata();

		Container::make( 'user_meta', esc_html__( 'Additional details' ) )
			->add_fields( $admin_fields );

	}

	/**
	 * Register registration fields settigns for the post type.
	 *
	 * @return void
	 */
	public function register_registration_fields_settings() {

		Container::make( 'post_meta', esc_html__( 'Main field settings' ) )
		->where( 'post_type', '=', 'pno_signup_fields' )
			->add_fields(
				array(

					Field::make( 'hidden', 'field_is_default' ),
					Field::make( 'hidden', 'field_priority' ),

					Field::make( 'html', 'psw_information_text' )
						->set_conditional_logic(
							array(
								'relation' => 'AND',
								array(
									'field'   => 'field_is_default',
									'value'   => 'password',
									'compare' => '=',
								),
							)
						)
						->set_html( '<p class="pno-field-is-default-notice">' . esc_html__( 'When the password field is disabled, a randomly generated password will be sent to the user via email.' ) . '</p>' ),

					Field::make( 'text', 'field_label', esc_html__( 'Custom form label' ) )
						->set_help_text( esc_html__( 'This text will be used as label within the registration forms. Leave blank to use the field title.' ) ),

					Field::make( 'text', 'field_placeholder', esc_html__( 'Placeholder' ) )
						->set_help_text( esc_html__( 'This text will appear within the field when empty. Leave blank if not needed.' ) ),

					Field::make( 'textarea', 'field_description', esc_html__( 'Field description' ) )
						->set_help_text( esc_html__( 'This is the text that appears as a description within the forms. Leave blank if not needed.' ) ),

					Field::make( 'checkbox', 'field_is_required', esc_html__( 'Set as required' ) )
						->set_conditional_logic(
							array(
								'relation' => 'AND',
								array(
									'field'   => 'field_is_default',
									'value'   => [ 'email', 'password' ],
									'compare' => 'NOT IN',
								),
							)
						)
						->set_help_text( esc_html__( 'Enable this option so the field must be filled before the form can be processed.' ) ),

					Field::make( 'html', 'crb_information_text' )
						->set_conditional_logic(
							array(
								'relation' => 'AND',
								array(
									'field'   => 'field_is_default',
									'value'   => 'username',
									'compare' => '=',
								),
							)
						)
						->set_html( '<p class="pno-field-is-default-notice">' . esc_html__( 'The email address will be used as username if during registration the username field is left blank when set as non required or the field is completely disabled.' ) . '</p>' ),

				)
			);

	}

}

new PNO_Custom_Fields;
