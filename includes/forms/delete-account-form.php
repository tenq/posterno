<?php
/**
 * Handle the account delete form.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

namespace PNO\Forms;

use PNO\Form;
use PNO\Forms;
use PNO\Form\Field\CheckboxField;
use PNO\Form\Field\DropdownField;
use PNO\Form\Field\DropzoneField;
use PNO\Form\Field\EditorField;
use PNO\Form\Field\EmailField;
use PNO\Form\Field\FileField;
use PNO\Form\Field\ListingCategoryField;
use PNO\Form\Field\ListingLocationField;
use PNO\Form\Field\ListingOpeningHoursField;
use PNO\Form\Field\ListingTagsField;
use PNO\Form\Field\MultiCheckboxField;
use PNO\Form\Field\MultiSelectField;
use PNO\Form\Field\NumberField;
use PNO\Form\Field\PasswordField;
use PNO\Form\Field\RadioField;
use PNO\Form\Field\SocialProfilesField;
use PNO\Form\Field\TermSelectField;
use PNO\Form\Field\TextAreaField;
use PNO\Form\Field\TextField;
use PNO\Form\Field\URLField;
use PNO\Form\Rule\NotEmpty;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Handle the Posterno's account delete form.
 */
class DeleteAccountForm extends Forms {

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->form_name    = 'delete_account_form';
		$this->submit_label = esc_html__( 'Delete account' );
		parent::__construct();
	}

	/**
	 * Define the list of fields for the form.
	 *
	 * @return array
	 */
	public function get_fields() {

		$fields = array(
			'current_password' => new PasswordField(
				'current_password',
				[
					'label'       => esc_html__( 'Current password' ),
					'description' => esc_html__( 'Enter your current password to confirm cancellation of your account.' ),
					'required'    => true,
					'rules'       => [
						new NotEmpty(),
					],
				]
			),
		);

		/**
		 * Allow developers to customize the account delete form fields.
		 *
		 * @param array $fields the list of fields.
		 * @return array list of fields.
		 */
		return apply_filters( 'pno_delete_account_form_fields', $fields );

	}

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hook() {
		add_action( 'wp_loaded', [ $this, 'process' ] );
		add_shortcode( 'pno_delete_account_form', [ $this, 'shortcode' ] );
	}

	/**
	 * Form shortcode.
	 *
	 * @return string
	 */
	public function shortcode() {

		ob_start();

		if ( is_user_logged_in() ) {

			$user = wp_get_current_user();

			posterno()->templates
				->set_template_data(
					[
						'form'         => $this->form,
						'submit_label' => $this->submit_label,
						'title'        => esc_html__( 'Delete your account' ),
					]
				)
				->get_template_part( 'form' );

		}

		return ob_get_clean();

	}

	/**
	 * Process the form.
	 *
	 * @throws \Exception When password verification fails.
	 * @throws \Exception When form fails.
	 * @return void
	 */
	public function process() {
		try {

			//phpcs:ignore
			if ( empty( $_POST[ 'submit_' . $this->form->get_name() ] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST[ "{$this->form->get_name()}_nonce" ], "verify_{$this->form->get_name()}_form" ) ) {
				return;
			}

			if ( ! isset( $_POST[ $this->form->get_name() ] ) ) {
				return;
			}

			$this->form->bind( $_POST[ $this->form->get_name() ] );

			if ( $this->form->is_valid() ) {

				$user               = wp_get_current_user();
				$values             = $this->form->get_data();
				$submitted_password = $values['current_password'];

				$user = wp_get_current_user();

				if ( $user instanceof \WP_User && wp_check_password( $submitted_password, $user->data->user_pass, $user->ID ) && is_user_logged_in() ) {

					wp_logout();

					require_once( ABSPATH . 'wp-admin/includes/user.php' );

					wp_delete_user( $user->ID );

					$redirect_to = pno_get_option( 'cancellation_redirect' );

					if ( is_array( $redirect_to ) && isset( $redirect_to['value'] ) && ! empty( $redirect_to['value'] ) ) {
						wp_safe_redirect( get_permalink( $redirect_to['value'] ) );
						exit;
					} else {
						wp_safe_redirect( home_url() );
						exit;
					}
				} else {
					throw new \Exception( __( 'The password you entered is incorrect. Your account has not been deleted.' ) );
				}

			}
		} catch ( \Exception $e ) {
			$this->form->set_processing_error( $e->getMessage() );
			return;
		}

	}

}

add_action(
	'init', function () {
		( new DeleteAccountForm() )->hook();
	}, 30
);
