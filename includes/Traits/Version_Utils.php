<?php
/**
 * Trait WordPress\Plugin_Check\Traits\Version_Utils
 *
 * @package plugin-check
 */

namespace WordPress\Plugin_Check\Traits;

/**
 * Trait for version utilities.
 *
 * @since 1.4.0
 */
trait Version_Utils {

	/**
	 * Returns current major WordPress version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Stable WordPress version.
	 */
	protected function get_wordpress_stable_version(): string {
		$version = $this->get_latest_version_info( 'current' );

		// Strip off any -alpha, -RC, -beta suffixes.
		list( $version, ) = explode( '-', $version );

		if ( preg_match( '#^\d.\d#', $version, $matches ) ) {
			$version = $matches[0];
		}

		return $version;
	}

	/**
	 * Returns WordPress latest version.
	 *
	 * @since 1.4.0
	 *
	 * @return string WordPress latest version.
	 */
	protected function get_wordpress_latest_version(): string {
		$version = $this->get_latest_version_info( 'current' );

		return $version ?? get_bloginfo( 'version' );
	}

	/**
	 * Returns required PHP version.
	 *
	 * @since 1.4.0
	 *
	 * @return string Required PHP version.
	 */
	protected function get_wordpress_required_php_version(): string {
		$version = $this->get_latest_version_info( 'php_version' );

		return $version ?? $this->get_required_php_version();
	}

	/**
	 * Returns required MySQL version.
	 *
	 * @since 1.4.0
	 *
	 * @return string Required MySQL version.
	 */
	protected function get_wordpress_required_mysql_version(): string {
		$version = $this->get_latest_version_info( 'mysql_version' );

		return $version ?? $this->get_required_mysql_version();
	}

	/**
	 * Returns recommended PHP version.
	 *
	 * @since 1.4.0
	 *
	 * @return string Recommended PHP version.
	 */
	protected function get_recommended_php_version(): string {
		$recommended_version = '7.4'; // Default fallback recommended version.

		$version_details = wp_check_php_version();

		if ( is_array( $version_details ) && ! empty( $version_details['recommended_version'] ) ) {
			$recommended_version = $version_details['recommended_version'];
		}

		return $recommended_version;
	}

	/**
	 * Returns specific information.
	 *
	 * @since 1.4.0
	 *
	 * @param string $key The information key to retrieve.
	 * @return mixed The requested information.
	 */
	private function get_latest_version_info( string $key ) {
		$info = get_transient( 'wp_plugin_check_latest_version_info' );

		if ( false === $info ) {
			$response = wp_remote_get( 'https://api.wordpress.org/core/version-check/1.7/' );

			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$body = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( isset( $body['offers'] ) && ! empty( $body['offers'] ) ) {
					$info = reset( $body['offers'] );
					set_transient( 'wp_plugin_check_latest_version_info', $info, DAY_IN_SECONDS );
				}
			}
		}

		return array_key_exists( $key, $info ) ? $info[ $key ] : null;
	}

	/**
	 * Returns the current WordPress' required PHP version.
	 *
	 * @since 1.4.0
	 *
	 * @return string The current WordPress' required PHP version.
	 */
	private function get_required_php_version(): string {
		static $required_php_version;

		if ( ! isset( $required_php_version ) ) {
			require ABSPATH . WPINC . '/version.php';
		}

		return $required_php_version;
	}

	/**
	 * Returns the current WordPress' required MySQL version.
	 *
	 * @since 1.4.0
	 *
	 * @return string The current WordPress' required MySQL version.
	 */
	private function get_required_mysql_version(): string {
		static $required_mysql_version;

		if ( ! isset( $required_mysql_version ) ) {
			require ABSPATH . WPINC . '/version.php';
		}

		return $required_mysql_version;
	}
}
