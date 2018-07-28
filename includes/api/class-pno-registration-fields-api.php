<?php
/**
 * Registers a custom rest api controller for the registration fields.
 *
 * @package     posterno
 * @copyright   Copyright (c) 2018, Pressmodo, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The class that register a new rest api controller to handle registration fields.
 */
class PNO_Registration_Fields_Api extends PNO_REST_Controller {

	/**
	 * WP REST API namespace/version.
	 *
	 * @var string
	 */
	protected $namespace = 'posterno/v1/custom-fields';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'registration';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'pno_signup_fields';

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/' . $this->rest_base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Get registration fields.
	 *
	 * @return void
	 */
	public function get_items( $request ) {

		$args = [
			'post_type'              => $this->post_type,
			'posts_per_page'         => 100,
			'nopaging'               => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'meta_key'               => '_field_priority',
			'orderby'                => 'meta_value_num',
			'order'                  => 'ASC',
		];

		$fields = new WP_Query( $args );
		$data   = [];

		if ( empty( $fields ) ) {
			return rest_ensure_response( $data );
		}

		if ( is_array( $fields->get_posts() ) && ! empty( $fields->get_posts() ) ) {
			foreach ( $fields->get_posts() as $post ) {
				$response = $this->prepare_item_for_response( $post, $request );
				$data[]   = $this->prepare_response_for_collection( $response );
			}
		}

		return rest_ensure_response( $data );

	}

	/**
	 * Matches the post data to the schema we want.
	 *
	 * @param WP_Post $post The comment object whose response is being prepared.
	 */
	public function prepare_item_for_response( $post, $request ) {

		$post_data = array();
		$schema    = $this->get_item_schema();
		$field     = new PNO_Registration_Field( $post->ID );

		// We are also renaming the fields to more understandable names.
		if ( isset( $schema['properties']['id'] ) ) {
			$post_data['id'] = (int) $post->ID;
		}
		if ( isset( $schema['properties']['name'] ) ) {
			$post_data['name'] = $field->get_name();
		}
		if ( isset( $schema['properties']['label'] ) ) {
			$post_data['label'] = $field->get_label();
		}
		if ( isset( $schema['properties']['meta'] ) ) {
			$post_data['meta'] = $field->get_meta();
		}
		if ( isset( $schema['properties']['priority'] ) ) {
			$post_data['priority'] = (int) $field->get_priority();
		}
		if ( isset( $schema['properties']['default'] ) ) {
			$post_data['default'] = (bool) $field->is_default_field();
		}
		if ( isset( $schema['properties']['type'] ) ) {
			$post_data['type']          = $field->get_type();
			$post_data['type_nicename'] = $field->get_type_nicename();
		}
		if ( isset( $schema['properties']['description'] ) ) {
			$post_data['description'] = $field->get_description();
		}
		if ( isset( $schema['properties']['placeholder'] ) ) {
			$post_data['placeholder'] = $field->get_placeholder();
		}
		if ( isset( $schema['properties']['required'] ) ) {
			$post_data['required'] = (bool) $field->is_required();
		}

		return rest_ensure_response( $post_data );
	}

	/**
	 * Get the registration field schema, conforming to JSON Schema.
	 *
	 * @param WP_REST_Request $request
	 * @return array
	 */
	public function get_item_schema() {

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( 'Unique identifier for the object.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'name'        => array(
					'description' => __( 'The name for the object.' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'label'       => array(
					'description' => __( 'The optional label for the profile field used within forms.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'meta'        => array(
					'description' => __( 'The user meta key for the field used to store users information.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'priority'    => array(
					'description' => __( 'The priority number assigned to the field used to defined the order within forms.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'default'     => array(
					'description' => __( 'Flag to determine if the field is a default field.' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'type'        => array(
					'description' => __( 'Field type.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'description' => array(
					'description' => __( 'Field description.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'placeholder' => array(
					'description' => __( 'Field placeholder.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'required'    => array(
					'description' => __( 'Flag to determine if the field is required when displayed within forms.' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		return $schema;

	}

}
