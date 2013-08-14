<?php

/**
 * Transifex API
 *
 * @since 1.0
 */
class Codepress_Transifex_API {

	private $api_url, $auth;

	function __construct() {

		$this->api_url = 'https://www.transifex.com/api/2/';
		$this->set_credentials();
	}

	/**
	 * Set credentials
	 *
	 * @since 1.0
	 */
	function set_credentials() {

		$credentials = get_option( 'cpti_options' );

		$username = isset( $credentials['username'] ) ? $credentials['username'] : '';
		$password = isset( $credentials['password'] ) ? $credentials['password'] : '';

		if ( $username && $password )
			$this->auth = $username . ':' . $password;
	}

	/**
	 * Verify credentials
	 *
	 * @since 1.0
	 */
	function verify_credentials() {

		// @todo: contact transifex how to verify credentials
		return true;
	}

	/**
	 * Connect API
	 *
	 * @since 1.0
	 *
	 * @param string $request API variable; e.g. projects
	 */
	function connect_api( $request = '' ) {

		$cache_id = md5( $request );

		$result = get_transient( $cache_id );

		if ( ! $result ) {
			$args = array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $this->auth )
				),
				'timeout' 	=> 3600,
				'sslverify' => false
			);

			$response 	= wp_remote_get( $this->api_url . $request, $args );
			$json 		= wp_remote_retrieve_body( $response );

			if ( $json ) {
				$result = json_decode( $json );

				set_transient( $cache_id, $result, 3600 ); // refresh cache each hour
			}
		}

		return $result;
	}
}