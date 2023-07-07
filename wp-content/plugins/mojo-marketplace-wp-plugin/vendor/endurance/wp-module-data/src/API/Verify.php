<?php

namespace Endurance\WP\Module\Data\API;

use Endurance\WP\Module\Data\HubConnection;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Response;

/**
 * REST API controller for verifying a hub connection attempt
 */
class Verify extends WP_REST_Controller {

	/**
	 * Instance of HubConnection class
	 *
	 * @var HubConnection
	 */
	public $hub;

	/**
	 * Constructor.
	 *
	 * @param HubConnection $hub Instance of the hub connection manager
	 * @since 4.7.0
	 */
	public function __construct( HubConnection $hub ) {
		$this->hub       = $hub;
		$this->namespace = 'bluehost/v1/data';
		$this->rest_base = 'verify';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<token>[a-f0-9]{32})',
			array(
				'args' => array(
					'token' => array(
						'description' => __( 'Connection verification token.' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
			)
		);

	}

	/**
	 * Returns a verification of the supplied connection token
	 *
	 * @since 1.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$valid  = $this->hub->verify_token( $request['token'] );
		$status = ( $valid ) ? 200 : 401;

		$response = new WP_REST_Response(
			array(
				'token' => $request['token'],
				'valid' => $valid,
			),
			$status
		);

		return $response;
	}

	/**
	 * No authentication required for this endpoint
	 *
	 * @since 1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error
	 */
	public function get_items_permissions_check( $request ) {
		return true;
	}
}
