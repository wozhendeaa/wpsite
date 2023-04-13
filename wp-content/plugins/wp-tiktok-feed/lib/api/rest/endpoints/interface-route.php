<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints;

/**
 * Route Interface
 */

interface Route {

	public function callback( \WP_REST_Request $request );

	public static function get_name();

	public static function get_rest_args();

	public static function get_rest_route();

	public static function get_rest_method();

	public function get_rest_permission();
}
