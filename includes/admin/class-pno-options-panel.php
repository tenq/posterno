<?php
/**
 * Handles registration of the an options panel for Posterno.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

namespace PNO;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The class that registers the Posterno's options panel.
 */
class OptionsPanel {

	/**
	 * Slug of the options panel.
	 *
	 * @var string
	 */
	public $slug = 'posterno-options';

	/**
	 * Holds registered settings tabs.
	 *
	 * @var array
	 */
	public $options_pages = [];

	/**
	 * Holds registered subsections tabs for each options page.
	 *
	 * @var array
	 */
	public $options_subsections = [];

	/**
	 * Hook into WordPress and get things started.
	 *
	 * @return void
	 */
	public function init() {

		$this->options_pages       = \pno_get_registered_settings_tabs();
		$this->options_subsections = \pno_get_registered_settings_tabs_sections();

		add_action( 'carbon_fields_register_fields', [ $this, 'register_settings' ] );

		add_filter( "cb_theme_options_{$this->slug}_container_file", [ $this, 'render' ] );
		if ( ! empty( $this->options_pages ) && is_array( $this->options_pages ) ) {
			foreach ( $this->options_pages as $page_id => $page_label ) {
				if ( $page_id !== 'general' ) {
					add_filter( "cb_theme_options_{$this->slug}[{$page_id}]_container_file", [ $this, 'render' ] );
				}
			}
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ], 100 );

	}

	/**
	 * Register the options panel.
	 *
	 * @return void
	 */
	public function register_settings() {

		$main_options_page = Container::make( 'custom_options', esc_html__( 'Posterno settings' ) )
			->set_page_menu_title( esc_html__( 'Posterno' ) )
			->set_datastore( new \PNO\Datastores\OptionsPanel() )
			->set_page_file( $this->slug )
			->set_page_parent( 'options-general.php' );

		$main_options_page_tabs = $this->get_settings_tabs( 'general' );

		foreach ( $main_options_page_tabs as $tab_id => $tab_label ) {

			$settings = isset( $this->get_registered_settings()[ $tab_id ] ) ? $this->get_registered_settings()[ $tab_id ] : [];

			$main_options_page->add_tab(
				esc_html( $tab_label ),
				$settings
			);

		}

		foreach ( $this->options_pages as $option_page_id => $option_page_label ) {
			if ( $option_page_id === 'general' ) {
				continue;
			}

			$sub_page = Container::make( 'custom_options', esc_html__( 'Posterno settings' ) )
				->set_page_menu_title( esc_html__( 'Posterno' ) )
				->set_datastore( new \PNO\Datastores\OptionsPanel() )
				->set_page_file( $this->slug . "[$option_page_id]" )
				->set_page_parent( $main_options_page );

			$sub_page_tabs = $this->get_settings_tabs( $option_page_id );
			if ( is_array( $sub_page_tabs ) ) {
				foreach ( $sub_page_tabs as $sub_page_tab_id => $sub_page_tab_label ) {
					$sub_page_settings = isset( $this->get_registered_settings()[ $sub_page_tab_id ] ) ? $this->get_registered_settings()[ $sub_page_tab_id ] : [];
					$sub_page->add_tab(
						esc_html( $sub_page_tab_label ),
						$sub_page_settings
					);
				}
			}
		}

	}

	/**
	 * Get settings sub sections for a specific options page.
	 *
	 * @param string $option_page_id the id of the options page.
	 * @return array|mixed
	 */
	private function get_settings_tabs( $option_page_id ) {
		return isset( $this->options_subsections[ $option_page_id ] ) ? $this->options_subsections[ $option_page_id ] : [];
	}

	/**
	 * Retrieve an array of all settings registered for posterno.
	 *
	 * @return array
	 */
	public function get_registered_settings() {

		$settings = [];

		$settings['pages']                  = $this->get_pages_settings();
		$settings['theme']                  = $this->get_theme_settings();
		$settings['login']                  = $this->get_login_settings();
		$settings['registration']           = $this->get_registration_settings();
		$settings['password_recovery_form'] = $this->get_password_recovery_form_settings();
		$settings['redirects']              = $this->get_redirects_settings();
		$settings['privacy']                = $this->get_privacy_settings();
		$settings['emails_settings']        = $this->get_emails_settings();

		$settings['profiles_settings']   = $this->get_profiles_settings();
		$settings['listings_settings']   = $this->get_listings_settings();
		$settings['listings_management'] = $this->get_listings_management_settings();
		$settings['listings_submission'] = $this->get_listings_submission_settings();
		$settings['listings_content']    = $this->get_listings_content_settings();
		$settings['listings_maps']       = $this->get_listings_map_settings();
		$settings['listings_redirects']  = $this->get_listings_redirects_settings();

		/**
		 * Filter: add/remove/modify settings registered for the Posterno's options panel.
		 *
		 * @param array $settings settings currently registered.
		 * @return array
		 */
		return apply_filters( 'pno_options_panel_settings', $settings );

	}

	/**
	 * Get pages settings.
	 *
	 * @return array
	 */
	private function get_pages_settings() {

		$settings = [];

		// Register general settings.
		$settings[] = Field::make( 'multiselect', 'login_page', esc_html__( 'Login page' ) )
			->set_help_text( esc_html__( 'Select the page where you have added the login form shortcode.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'password_page', esc_html__( 'Password recovery page' ) )
			->set_help_text( esc_html__( 'Select the page where you have added the password recovery form shortcode.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'registration_page', esc_html__( 'Registration page' ) )
			->set_help_text( esc_html__( 'Select the page where you have added the registration form shortcode.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'dashboard_page', esc_html__( 'Dashboard page' ) )
			->set_help_text( esc_html__( 'Select the page where you have added the dashboard shortcode.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'submission_page', esc_html__( 'Listing submission page' ) )
			->set_help_text( esc_html__( 'Select the page where you have added the listing submission form shortcode.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'editing_page', esc_html__( 'Listing editing page' ) )
			->set_help_text( esc_html__( 'Select the page where you have added the listing editing form shortcode.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'profile_page', esc_html__( 'Public profile page' ) )
			->set_help_text( esc_html__( 'Select the page where you have added the profile shortcode.' ) )
			->add_options( 'pno_get_pages' );

		return $settings;

	}

	/**
	 * Get theme settings.
	 *
	 * @return array
	 */
	private function get_theme_settings() {

		$settings = [];

		$settings[] = Field::make( 'checkbox', 'bootstrap_style', esc_html__( 'Include Bootstrap css' ) )
			->set_help_text( esc_html__( 'Posterno uses bootstrap 4 for styling all of the elements. Disable these options if your theme already makes use of bootstrap.' ) );

		$settings[] = Field::make( 'checkbox', 'bootstrap_script', esc_html__( 'Include Bootstrap scripts' ) )
			->set_help_text( esc_html__( 'Posterno uses bootstrap 4 for styling all of the elements. Disable these options if your theme already makes use of bootstrap.' ) );

		return $settings;

	}

	/**
	 * Get login settings.
	 *
	 * @return array
	 */
	private function get_login_settings() {

		$settings = [];

		$settings[] = Field::make( 'radio', 'login_method', __( 'Allow users to login with:' ) )
			->add_options( 'pno_get_login_methods' );

		$settings[] = Field::make( 'checkbox', 'login_show_registration_link', esc_html__( 'Show registration page link' ) )
			->set_help_text( esc_html__( 'Enable the option to display a link to the registration page within the login form.' ) );

		$settings[] = Field::make( 'checkbox', 'login_show_password_link', esc_html__( 'Show lost password link' ) )
			->set_help_text( esc_html__( 'Enable the option to display a link to the password recovery within the login form.' ) );

		return $settings;

	}

	/**
	 * Get registration settings.
	 *
	 * @return array
	 */
	private function get_registration_settings() {

		$settings = [];

		$settings[] = Field::make( 'checkbox', 'disable_username', __( 'Disable username during registration:' ) )
			->set_help_text( __( 'Enable this option to disable the username field within the registration form. The email address will be used as username for the new user.' ) );

		$settings[] = Field::make( 'checkbox', 'disable_password', __( 'Disable custom passwords during registration:' ) )
			->set_help_text( __( 'Enable this option to disable passwords within the registration form. A randomly generated password will be sent to the user.' ) );

		$settings[] = Field::make( 'checkbox', 'verify_password', __( 'Enable password confirmation:' ) )
			->set_help_text( __( 'Enable this option to add a password confirmation field within the registration form.' ) );

		$settings[] = Field::make( 'checkbox', 'strong_passwords', __( 'Require strong passwords:' ) )
			->set_help_text( __( 'Enable this option to require strong passwords for accounts.' ) );

		$settings[] = Field::make( 'checkbox', 'enable_role_selection', __( 'Allow role section:' ) )
			->set_help_text( __( 'Enable to allow users to select a user role on registration.' ) );

		$settings[] = Field::make( 'multiselect', 'allowed_roles', __( 'Allowed Roles:' ) )
			->set_help_text( __( 'Select which roles can be selected upon registration.' ) )
			->add_options( 'pno_get_roles' );

		$settings[] = Field::make( 'checkbox', 'enable_terms', __( 'Enable terms & conditions:' ) )
			->set_help_text( __( 'Enable to force users to agree to your terms before registering an account.' ) );

		$settings[] = Field::make( 'multiselect', 'terms_page', esc_html__( 'Terms Page:' ) )
			->set_help_text( esc_html__( 'Select the page that contains your terms.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'checkbox', 'login_after_registration', __( 'Login after registration:' ) )
			->set_help_text( __( 'Enable this option to automatically authenticate users after registration.' ) );

		$settings[] = Field::make( 'checkbox', 'registration_show_login_link', esc_html__( 'Show login link?' ) )
			->set_help_text( esc_html__( 'Enable the option to display a link to the login page within the registration form.' ) );

		$settings[] = Field::make( 'checkbox', 'registration_show_password_link', esc_html__( 'Show lost password link?' ) )
			->set_help_text( esc_html__( 'Enable the option to display a link to the password recovery within the registration form.' ) );

		return $settings;

	}

	/**
	 * Get password recovery form settings.
	 *
	 * @return array
	 */
	private function get_password_recovery_form_settings() {

		$settings = [];

		$settings[] = Field::make( 'checkbox', 'recovery_show_login_link', esc_html__( 'Show login link' ) )
			->set_help_text( esc_html__( 'Enable the option to display a link to the login page within the password recovery form.' ) );

		$settings[] = Field::make( 'checkbox', 'recovery_show_registration_link', esc_html__( 'Show registration page link' ) )
			->set_help_text( esc_html__( 'Enable the option to display a link to the registration page within the password recovery form.' ) );

		return $settings;

	}

	/**
	 * Get redirects settings.
	 *
	 * @return array
	 */
	private function get_redirects_settings() {

		$settings = [];

		$settings[] = Field::make( 'checkbox', 'redirect_wp_login', esc_html__( 'Redirect wp-login to frontend' ) )
			->set_help_text( esc_html__( 'Enable the option to redirect wp-login/wp-admin to the frontend login form.' ) );

		$settings[] = Field::make( 'multiselect', 'login_redirect', esc_html__( 'After login' ) )
			->set_help_text( esc_html__( 'Select the page where you want to redirect users after they login.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'logout_redirect', esc_html__( 'After logout' ) )
			->set_help_text( esc_html__( 'Select the page where you want to redirect users after they logout. If empty will return to your homepage.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'registration_redirect', esc_html__( 'After registration' ) )
			->set_help_text( esc_html__( 'Select the page where you want to redirect users after they register. If empty a message will be displayed instead.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'cancellation_redirect', esc_html__( 'After account cancellation' ) )
			->set_help_text( esc_html__( 'Select the page where you want to redirect users after they delete their account. If empty will return to your homepage.' ) )
			->add_options( 'pno_get_pages' );

		return $settings;

	}

	/**
	 * Get privacy settings.
	 *
	 * @return array
	 */
	private function get_privacy_settings() {

		$settings = [];

		$settings[] = Field::make( 'checkbox', 'allow_account_delete', esc_html__( 'Allow users to delete their own account' ) )
			->set_help_text( esc_html__( 'Enable the option to display a section within the dashboard for users to delete their own account.' ) );

		$settings[] = Field::make( 'checkbox', 'allow_data_request', esc_html__( 'Allow users to request a copy of their own data' ) )
			->set_help_text( esc_html__( 'Enable the option to allow the user to request an export of personal data from the dashboard page.' ) );

		$settings[] = Field::make( 'checkbox', 'allow_data_erasure', esc_html__( 'Allow users to request erasure of their own data' ) )
			->set_help_text( esc_html__( 'Enable the option to allow the user to request erasure of personal data from the dashboard page.' ) );

		return $settings;

	}

	/**
	 * Get emails settings.
	 *
	 * @return array
	 */
	private function get_emails_settings() {

		$settings = [];

		$settings[] = Field::make( 'text', 'from_name', esc_html__( 'From name:' ) )
			->set_help_text( esc_html__( 'The name emails are said to come from. This should probably be your site name.' ) );

		$settings[] = Field::make( 'text', 'from_email', esc_html__( 'From email:' ) )
			->set_help_text( esc_html__( 'This will act as the "from" and "reply-to" address.' ) );

		$settings[] = Field::make( 'select', 'email_template', __( 'Choose Options' ) )
			->set_help_text( esc_html__( 'Select the email template you wish to use for all emails sent by Posterno.' ) )
			->set_options( 'pno_get_email_templates' );

		$settings[] = Field::make( 'checkbox', 'disable_admin_password_recovery_email', __( 'Disable admin password recovery email:' ) )
			->set_help_text( __( 'Enable this option to stop receiving notifications when a new user resets his password.' ) );

		return $settings;

	}

	/**
	 * Get profiles settings.
	 *
	 * @return array
	 */
	private function get_profiles_settings() {

		$settings = [];

		$settings[] = Field::make( 'checkbox', 'allow_avatars', esc_html__( 'Custom avatars' ) )
			->set_help_text( esc_html__( 'Enable this option to allow users to upload custom avatars for their profiles.' ) );

		return $settings;

	}

	/**
	 * Get listings settings.
	 *
	 * @return array
	 */
	private function get_listings_settings() {

		$settings = [];

		$settings[] = Field::make( 'text', 'listings_per_page', esc_html__( 'Listings per page' ) )
			->set_help_text( esc_html__( 'Enter the amount of listings you wish to display.' ) );

		$settings[] = Field::make( 'select', 'listings_default_order', esc_html__( 'Order listings by' ) )
			->set_options( 'pno_get_listings_order_options' );

		$settings[] = Field::make( 'select', 'listings_layout', esc_html__( 'Default listings layout' ) )
			->set_options( 'pno_get_listings_layout_available_options' );

		$settings[] = Field::make( 'checkbox', 'listing_open_new_tab', esc_html__( 'Open internal listings links in new tab' ) )
			->set_help_text( esc_html__( 'Enable the option to open listings links in a new browser tab. ' ) );

		$settings[] = Field::make( 'radio', 'listing_date_format', esc_html__( 'Date format:' ) )
			->set_help_text( esc_html__( 'Choose how you want the published date for listings to be displayed on the front-end.' ) )
			->add_options(
				[
					'relative' => esc_html__( 'Relative to the current date (e.g., 1 day, 1 week, 1 month ago)' ),
					'default'  => esc_html__( 'Default date format as defined in Settings' ),
				]
			);

		return $settings;

	}

	/**
	 * Get listings management settings.
	 *
	 * @return array
	 */
	private function get_listings_management_settings() {

		$settings = [];

		$settings[] = Field::make( 'text', 'listings_per_page_dashboard', esc_html__( 'Listings per page in dashboard' ) )
			->set_help_text( esc_html__( 'Enter the amount of listings you wish to display when users are managing their listings.' ) );

		$settings[] = Field::make( 'checkbox', 'listing_allow_editing', esc_html__( 'Listings can be edited' ) )
			->set_help_text( esc_html__( 'Enable the option to allow users to edit their own listings.' ) );

		$settings[] = Field::make( 'checkbox', 'listing_allow_delete', esc_html__( 'Listings can be deleted' ) )
			->set_help_text( esc_html__( 'Enable the option to allow users to delete their own listings.' ) );

		$settings[] = Field::make( 'checkbox', 'listing_permanently_delete', esc_html__( 'Permanently delete listings' ) )
			->set_help_text( esc_html__( 'Enable the option to permanently delete listings from the database instead of trashing them.' ) );

		$settings[] = Field::make( 'checkbox', 'submission_allow_pendings_edit', esc_html__( 'Allow pending edits' ) )
			->set_help_text( esc_html__( 'Enable the option to allow editing of pending listings. Users can continue to edit pending listings until they are approved by an admin.' ) );

		$settings[] = Field::make( 'radio', 'submission_edit_moderated', esc_html__( 'Allow published edits' ) )
			->set_help_text( esc_html__( 'Choose whether published listings can be edited and if edits require admin approval. When moderation is required, the original listings will be unpublished while edits await admin approval.' ) )
			->set_options(
				[
					'no'            => esc_html__( 'Users cannot edit' ),
					'yes'           => esc_html__( 'Users can edit without admin approval' ),
					'yes_moderated' => esc_html__( 'Users can edit, but edits require admin approval' ),
				]
			);

		return $settings;

	}

	/**
	 * Get listing submission settings.
	 *
	 * @return array
	 */
	private function get_listings_submission_settings() {

		$settings = [];

		$settings[] = Field::make( 'multiselect', 'submission_requires_roles', __( 'Allowed roles' ) )
			->set_help_text( __( 'Select which roles can submit listings. Leave blank if not needed.' ) )
			->add_options( 'pno_get_roles' );

		$settings[] = Field::make( 'checkbox', 'submission_categories_associated', esc_html__( 'Categories to type association' ) )
			->set_help_text( esc_html__( 'Enable this option to allow selection of categories belonging to the chosen listing type during submission.' ) );

		$settings[] = Field::make( 'checkbox', 'submission_tags_associated', esc_html__( 'Tags to categories association' ) )
			->set_help_text( esc_html__( 'Enable this option to allow selection of tags belonging to the chosen listing categories during submission.' ) );

		$settings[] = Field::make( 'checkbox', 'submission_categories_sublevel', esc_html__( 'Display sub categories' ) )
			->set_help_text( esc_html__( 'Enable the option to display sub categories during selection.' ) );

		$settings[] = Field::make( 'checkbox', 'submission_region_sublevel', esc_html__( 'Allow child regions' ) )
			->set_help_text( esc_html__( 'Enable the option to display a parent to child hierarchy for the regions selector while submitting listings.' ) );

		$settings[] = Field::make( 'text', 'submission_categories_amount', esc_html__( 'How many categories?' ) )
			->set_help_text( esc_html__( 'Specify how many categories users can select for their listings (eg: 5). Leave blank if not needed.' ) );

		$settings[] = Field::make( 'text', 'submission_subcategories_amount', esc_html__( 'How many sub categories?' ) )
			->set_help_text( esc_html__( 'Specify how many sub categories users can select for their listings (eg: 5). Leave blank if not needed.' ) );

		$settings[] = Field::make( 'checkbox', 'submission_moderated', esc_html__( 'Moderate new listings' ) )
			->set_help_text( esc_html__( 'Enable the option to require admin approval of all new listing submissions.' ) );

		$settings[] = Field::make( 'text', 'submission_images_amount', esc_html__( 'How many images?' ) )
			->set_help_text( esc_html__( 'Specify the maximum amount of images your members can upload per listing.' ) );

		return $settings;

	}

	/**
	 * Get listings content settings.
	 *
	 * @return array
	 */
	private function get_listings_content_settings() {

		$settings = [];

		$settings[] = Field::make( 'multiselect', 'listings_social_profiles', __( 'Allowed social profiles' ) )
			->set_help_text( __( 'Select which social profiles to enable for listings.' ) )
			->add_options( 'pno_get_registered_social_media' );

		$settings[] = Field::make( 'checkbox', 'listing_image_placeholder', esc_html__( 'Show thumbnail placeholder' ) )
			->set_help_text( esc_html__( 'Enable the option to display a placeholder image when a listing does not have a thumbnail.' ) );

		$settings[] = Field::make( 'image', 'listing_image_placeholder_file', esc_html__( 'Custom placeholder image' ) )
			->set_conditional_logic(
				array(
					array(
						'field' => 'listing_image_placeholder',
						'value' => true,
					),
				)
			)
			->set_value_type( 'url' )
			->set_help_text( esc_html__( 'Upload a custom image if you wish to customize the default placeholder.' ) );

		return $settings;

	}

	/**
	 * Get listings maps settings.
	 *
	 * @return array
	 */
	private function get_listings_map_settings() {

		$settings = [];

		$settings[] = Field::make( 'radio', 'map_provider', esc_html__( 'Map provider' ) )
			->set_help_text( esc_html__( 'Select which maps provider to use across the website.' ) )
			->set_options( 'pno_get_registered_maps_providers' );

		$settings[] = Field::make( 'text', 'google_maps_api_key', esc_html__( 'Google Maps API Key' ) )
			->set_help_text( __( 'Google requires an API key to display maps. Acquire an API key from the <a href="https://developers.google.com/maps/documentation/geocoding/get-api-key">Google Maps API developer site.</a>' ) );

		$settings[] = Field::make( 'text', 'map_starting_lat', esc_html__( 'Starting address latitude' ) )
			->set_help_text( esc_html__( 'Pick a starting position for the map. Eg: 40.7484405' ) );

		$settings[] = Field::make( 'text', 'map_starting_lng', esc_html__( 'Starting address longitude' ) )
			->set_help_text( esc_html__( 'Pick a starting position for the map. Eg: -73.9944191' ) );

		$settings[] = Field::make( 'text', 'map_zoom', esc_html__( 'Starting map zoom level' ) )
			->set_help_text( esc_html__( 'Pick a starting zoom level for the map. Eg: 12' ) );

		return $settings;

	}

	/**
	 * Get listings redirects settings.
	 *
	 * @return array
	 */
	private function get_listings_redirects_settings() {

		$settings = [];

		$settings[] = Field::make( 'multiselect', 'listing_submission_redirect', esc_html__( 'After successful submission' ) )
			->set_help_text( esc_html__( 'Select the page where you wish to redirect users after submitting a listing. Leave blank to display a message only.' ) )
			->add_options( 'pno_get_pages' );

		$settings[] = Field::make( 'multiselect', 'listing_editing_redirect', esc_html__( 'After successful editing' ) )
			->set_help_text( esc_html__( 'Select the page where you wish to redirect users after editing a listing. Leave blank to display a message only.' ) )
			->add_options( 'pno_get_pages' );

		return $settings;

	}

	/**
	 * Render the options panel.
	 *
	 * @return string.
	 */
	public function render() {
		return PNO_PLUGIN_DIR . 'includes/admin/views/options-panel.php';
	}

	/**
	 * Load assets into the options panel.
	 *
	 * @return void
	 */
	public function assets() {

		$screen = get_current_screen();

		$screens = [ 'settings_page_posterno-options' ];

		foreach ( $this->options_pages as $page_id => $page_label ) {
			$screens[] = "admin_page_posterno-options[{$page_id}]";
		}

		if ( in_array( $screen->base, $screens ) ) {
			wp_enqueue_style( 'pno-options-panel', PNO_PLUGIN_URL . '/assets/css/admin/admin-settings-panel.min.css', false, PNO_VERSION );
		}

	}

}

( new OptionsPanel() )->init();