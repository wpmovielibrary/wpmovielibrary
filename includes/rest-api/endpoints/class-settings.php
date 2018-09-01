<?php
/**
 * REST API:Settings Controller class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest\endpoints;

use WP_Error;
use WP_REST_Server;
use WP_REST_Controller;

/**
 * Core class to access settings via the REST API.
 *
 * @see WP_REST_Controller
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Settings extends WP_REST_Controller {

	/**
	 * Plugin's registered settings fields.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $setting_fields = null;

	private $additional_settings = null;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct() {

		$this->namespace = 'wpmoly/v1';
		$this->rest_base = 'settings';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args' => array(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/schema', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_fields_schema' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args' => array(),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/validate', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'validate_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args' => array(),
			),
		) );

	}

	/**
	 * Checks if a given request has access to read and manage settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return boolean True if the request has read access for the item, otherwise false.
	 */
	public function get_item_permissions_check( $request ) {

		return current_user_can( 'manage_options' );
	}

	/**
	 * Retrieves the settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return array|WP_Error Array on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {

		$options  = $this->get_registered_options();
		$response = array();

		foreach ( $options as $name => $args ) {

			/**
			 * Prefix option name.
			 *
			 * @since 3.0.0
			 *
			 * @param string $name Option name.
			 */
			$option_name = apply_filters( 'wpmoly/filter/settings/option/name', $args['option_name'] );

			$response[ $name ] = get_option( $option_name, $args['schema']['default'] );

			/*
			 * Because get_option() is lossy, we have to
			 * cast values to the type they are registered with.
			 */
			$response[ $name ] = $this->prepare_value( $response[ $name ], $name, $args['schema'], $request );
		}

		return $response;
	}

	/**
	 * Validate a specific setting.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return array|WP_Error Array on success, or WP_Error object on failure.
	 */
	public function validate_item( $request ) {

		$settings = array();

		$setting_fields = $this->get_registered_settings();

		foreach ( $setting_fields as $name => $args ) {
			if ( ! empty( $request[ $name ] ) ) {
				$settings[ $name ] = $this->validate_value( $request[ $name ], $request, $name );
			}
		}

		$response = rest_ensure_response( $settings );

		return $response;
	}

	/**
	 * Prepares a value for output based off a schema array.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param mixed $value  Value to prepare.
	 * @param array $schema Schema to match.
	 *
	 * @return mixed The prepared value.
	 */
	protected function prepare_value( $value, $name, $schema, $request ) {

		$value = $this->sanitize_value( $value, $request, $name, $schema );

		// If the value is not valid by the schema, set the value to null. Null
		// values are specifcally non-destructive so this will not cause overwriting
		// the current invalid value to null.
		if ( is_wp_error( rest_validate_value_from_schema( $value, $schema ) ) ) {
			return null;
		}

		return rest_sanitize_value_from_schema( $value, $schema );
	}

	/**
	 * Retrieves the settings fields schema.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return array|WP_Error Array on success, or WP_Error object on failure.
	 */
	public function get_fields_schema( $request ) {

		$setting_fields = $this->get_registered_settings();

		$settings = array();

		foreach ( $setting_fields as $name => $args ) {

			$args = wp_parse_args( $args, array(
				'type'        => null,
				'title'       => '',
				'label'       => '',
				'description' => '',
				'page'        => '',
				'group'       => '',
				'parent'      => '',
				'options'     => array(),
				'default'     => '',
				'validate_callback' => '',
			) );

			$setting = array(
				'name'        => $name,
				'type'        => $args['type'],
				'title'       => $args['title'],
				'label'       => $args['label'],
				'description' => $args['description'],
				'default'     => $args['default'],
				'validate'    => ! empty( $args['validate_callback'] ),
			);

			if ( ! empty( $args['page'] ) ) {
				$setting['page'] = $args['page'];
			} elseif ( ! empty( $args['group'] ) ) {
				$setting['group'] = $args['group'];
			}

			if ( ! empty( $args['parent'] ) ) {
				$setting['parent'] = $args['parent'];
			}

			if ( ! empty( $args['options'] ) ) {
				$setting['options'] = $args['options'];
			}

			$settings[] = (object) $setting;
		}

		$response  = rest_ensure_response( $settings );

		$response->header( 'X-WP-Total', (int) count( $settings ) );
		$response->header( 'X-WP-TotalPages', 1 );

		return $response;
	}

	/**
	 * Updates settings for the settings object.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return array|WP_Error Array on success, or error object on failure.
	 */
	public function update_item( $request ) {

		$options = $this->get_registered_options();
		$params  = $request->get_params();

		foreach ( $options as $name => $args ) {

			if ( ! array_key_exists( $name, $params ) ) {
				continue;
			}

			/** Filter documented in includes/rest-api/controllers/class-settings.php */
			$option_name = apply_filters( 'wpmoly/filter/settings/option/name', $name );

			/*
			 * A null value for an option would have the same effect as
			 * deleting the option from the database, and relying on the
			 * default value.
			 */
			if ( is_null( $request[ $name ] ) ) {

				/*
				 * A null value is returned in the response for any option
				 * that has a non-scalar value.
				 *
				 * To protect clients from accidentally including the null
				 * values from a response object in a request, we do not allow
				 * options with values that don't pass validation to be updated to null.
				 * Without this added protection a client could mistakenly
				 * delete all options that have invalid values from the
				 * database.
				 */
				if ( is_wp_error( rest_validate_value_from_schema( get_option( $option_name, false ), $args['schema'] ) ) ) {
					return new WP_Error(
						'rest_invalid_stored_value', sprintf( __( 'The %s property has an invalid stored value, and cannot be updated to null.' ), $name ), array( 'status' => 500 )
					);
				}

				delete_option( $option_name );
			} else {
				update_option( $option_name, $request[ $name ] );
			}
		}

		return $this->get_item( $request );
	}

	/**
	 * Retrieves all the plugin's registered settings fields.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @return array Array of registered settings fields.
	 */
	protected function get_registered_settings() {

		if ( ! is_null( $this->setting_fields ) ) {
			return $this->setting_fields;
		}

		if ( empty( $this->setting_fields ) ) {

			/**
			 * Filter the default setting fields for REST API access.
			 *
			 * @since 3.0.0
			 *
			 * @param array $setting_fields Empty array.
			 */
			$this->setting_fields = apply_filters( 'wpmoly/filter/rest/registered/setting/fields', $this->setting_fields );
		}

		return $this->setting_fields;
	}

	/**
	 * Retrieves additional settings fields.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @return array Array of registered settings fields.
	 */
	protected function get_additional_settings( $registered_settings = array() ) {

		if ( empty( $this->additional_settings ) ) {

			/**
			 * Filter the additional setting fields for REST API access.
			 *
			 * @since 3.0.0
			 *
			 * @param array $additional_settings Empty array.
			 */
			$this->additional_settings = apply_filters( 'wpmoly/filter/rest/registered/additional/settings', $this->additional_settings );
		}

		$registered_settings = array_merge( $registered_settings, $this->additional_settings );

		return $registered_settings;
	}

	/**
	 * Retrieves all the plugin's options.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @return array Array of registered options.
	 */
	protected function get_registered_options() {

		$rest_options = array();

		$setting_fields = $this->get_registered_settings();
		$setting_fields = $this->get_additional_settings( $setting_fields );

		foreach ( $setting_fields as $name => $args ) {

			$args = wp_parse_args( $args, array(
				'type'        => null,
				'title'       => '',
				'label'       => '',
				'description' => '',
				'default'     => null,
				'options'     => array(),
			) );

			/**
			 * Prefix option name.
			 *
			 * @since 3.0.0
			 *
			 * @param string $name Option name.
			 */
			$option_name = apply_filters( 'wpmoly/unfilter/settings/option/name', $name );

			$rest_args = array(
				'name'        => $option_name,
				'option_name' => $option_name,
				'schema' => array(
					'type'        => $args['type'],
					'title'       => $args['title'],
					'description' => empty( $args['label'] ) ? '' : $args['label'],
					'default'     => $args['default'],
				),
				'sanitize_callback' => isset( $args['sanitize_callback'] ) ? $args['sanitize_callback'] : null,
				'validate_callback' => isset( $args['validate_callback'] ) ? $args['validate_callback'] : null,
			);

			if ( ! empty( $args['options'] ) ) {
				$rest_args['schema']['enum'] = array_keys( $args['options'] );
			}

			/*
			* Whitelist the supported types for settings, as we don't want invalid types
			* to be updated with arbitrary values that we can't do decent sanitizing for.
			*/
			if ( ! in_array( $rest_args['schema']['type'], array( 'number', 'integer', 'string', 'boolean', 'array', 'object' ), true ) ) {
				continue;
			}

			$rest_options[ $rest_args['name'] ] = $rest_args;
		}

		return $rest_options;
	}

	/**
	 * Retrieves the plugin's settings schema, conforming to JSON Schema.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		$options = $this->get_registered_options();

		$schema = array(
			'$schema'    => 'http://json-schema.org/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => array(),
		);

		foreach ( $options as $option_name => $option ) {
			$schema['properties'][ $option_name ] = $option['schema'];
			$schema['properties'][ $option_name ]['arg_options'] = array(
				'sanitize_callback' => $option['sanitize_callback'],
				'validate_callback' => $option['validate_callback'],
			);
		}

		return $schema;
	}

	/**
	 *
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param  mixed           $value The value for the setting.
	 * @param  WP_REST_Request $request The request object.
	 * @param  string          $param The parameter name.
	 *
	 * @return boolean
	 */
	public function validate_value( $value, $request, $param ) {

		$is_valid = false;

		$options = $this->get_registered_options();
		if ( empty( $options[ $param ]['validate_callback'] ) ) {
			return null;
		}

		$callback = $options[ $param ]['validate_callback'];
		if ( is_callable( $callback ) ) {
			$is_valid = call_user_func_array( $callback, array( $value, $request, $param ) );
		}

		return $is_valid;
	}

	/**
	 * Custom sanitize callback used for all options to allow the use of 'null'.
	 *
	 * By default, the schema of settings will throw an error if a value is set to
	 * `null` as it's not a valid value for something like "type => string". We
	 * provide a wrapper sanitizer to whitelist the use of `null`.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param mixed           $value   The value for the setting.
	 * @param WP_REST_Request $request The request object.
	 * @param string          $param   The parameter name.
	 * @param array           $schema  The parameter schema.
	 *
	 * @return mixed|WP_Error
	 */
	public function sanitize_value( $value, $request, $param, $schema ) {

		if ( is_null( $value ) ) {
			return $value;
		}

		return rest_parse_request_arg( $value, $request, $param );
	}
}
