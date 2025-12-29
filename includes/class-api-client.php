<?php
/**
 * API client for the Vulnz Agent plugin, contains methods to interact with the external API.
 *
 * @package Vulnz_Agent
 */

namespace Vulnz_Agent;

// Block direct access.
defined( 'ABSPATH' ) || die();

/**
 * Class Api_Client
 */
class Api_Client {

	/**
	 * The API URL.
	 *
	 * @var string
	 */
	private $api_url;

	/**
	 * The API key.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->api_url = get_option_or_constant( VULNZ_API_URL, 'VULNZ_AGENT_API_URL', DEFAULT_VULNZ_API_URL );
		$this->api_key = get_option_or_constant( VULNZ_API_KEY, 'VULNZ_AGENT_API_KEY', '' );
	}

	/**
	 * Check if the API is available.
	 *
	 * @return boolean
	 */
	public function is_available(): bool {
		if ( empty( $this->api_url ) || empty( $this->api_key ) ) {
			return false;
		}

		if ( ! filter_var( $this->api_url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		$scheme = \wp_parse_url( $this->api_url, PHP_URL_SCHEME );
		$host   = \wp_parse_url( $this->api_url, PHP_URL_HOST );

		// Require HTTPS to avoid leaking credentials over plaintext.
		if ( 'https' !== $scheme ) {
			return false;
		}

		if ( ! is_string( $host ) || '' === $host ) {
			return false;
		}

		// Block obvious local/loopback/private targets to reduce SSRF risk.
		if ( 'localhost' === $host || '127.0.0.1' === $host ) {
			return false;
		}

		if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
			// If it's an IP, disallow private/reserved ranges.
			if ( ! filter_var( $host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Create or update a website.
	 *
	 * @param string     $domain The domain name.
	 * @param array|null $body   The request body.
	 *
	 * @return boolean
	 */
	public function create_or_update_website( string $domain, ?array $body = array() ): bool {
		$success            = false;
		$does_website_exist = false;

		if ( ! filter_var( $domain, FILTER_VALIDATE_DOMAIN, array( 'flags' => FILTER_FLAG_HOSTNAME ) ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Vulnz Agent] Invalid domain name provided.' );
			}
		} elseif ( ! $this->is_available() ) {
			// API not available because of bad settings.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Vulnz Agent] API client is not properly configured.' );
			}
		} else {
			$headers = array(
				'X-Api-Key'    => $this->api_key,
				'Content-Type' => 'application/json; charset=utf-8',
			);

			$get_url = sprintf( '%s/api/websites/%s', $this->api_url, $domain );
			$get_response = \wp_remote_get(
				$get_url,
				array(
					'headers' => $headers,
					'timeout' => API_REQUEST_TIMEOUT,
				)
			);

			if ( \is_wp_error( $get_response ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Vulnz Agent] API GET request failed: ' . $get_response->get_error_message() );
				}
			} elseif ( empty( ( $response_code = \wp_remote_retrieve_response_code( $get_response ) ) ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Vulnz Agent] Empty response code from API.' );
				}
			} elseif ( 404 === $response_code ) {
				// Website not found. Create it now.
				$post_url      = sprintf( '%s/api/websites', $this->api_url );
				$post_response = \wp_remote_post(
					$post_url,
					array(
						'headers' => $headers,
						'body'    => \wp_json_encode( array_merge( array( 'domain' => $domain ), $body ?? array() ) ),
						'timeout' => API_REQUEST_TIMEOUT,
					)
				);

				if ( \is_wp_error( $post_response ) ) {
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log( '[Vulnz Agent] Failed to create website: ' . $post_response->get_error_message() );
					}
				} elseif ( ! in_array( \wp_remote_retrieve_response_code( $post_response ), array( 200, 201 ), true ) ) {
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log( '[Vulnz Agent] Failed to create website: HTTP ' . \wp_remote_retrieve_response_code( $post_response ) );
					}
				} else {
					$does_website_exist = true;
				}
			} elseif ( 200 !== $response_code ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Vulnz Agent] Unexpected response code: HTTP ' . $response_code );
				}
			} else {
				// Unexpected response code.
				$does_website_exist = true;
			}
		}

		if ( $does_website_exist ) {
			$put_url = sprintf( '%s/api/websites/%s', $this->api_url, $domain );

			$put_response = \wp_remote_request(
				$put_url,
				array(
					'method'  => 'PUT',
					'headers' => $headers,
					'body'    => \wp_json_encode( $body ),
					'timeout' => API_REQUEST_TIMEOUT,
				)
			);

			if ( \is_wp_error( $put_response ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Vulnz Agent] Failed to update website: ' . $put_response->get_error_message() );
				}
			} elseif ( 200 !== \wp_remote_retrieve_response_code( $put_response ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Vulnz Agent] Failed to update website: HTTP ' . \wp_remote_retrieve_response_code( $put_response ) );
				}
			} else {
				$success = true;
			}
		}

		if ( ! empty( $cache_key = get_website_cache_key( $domain ) ) ) {
			delete_transient( $cache_key );
		}

		return $success;
	}

	/**
	 * Get a website by domain.
	 *
	 * @param string $domain The domain name.
	 *
	 * @return ?array The website data, or null on failure.
	 */
	public function get_website( string $domain ): ?array {
		$website_data = null;

		$cache_key = get_website_cache_key( $domain );

		if ( WEBSITE_DATA_CACHE_TTL > 0 && ! empty( $cache_key ) ) {
			$website_data = get_transient( $cache_key );
		}

		if ( WEBSITE_DATA_CACHE_TTL && ! empty( $website_data ) ) {
			// Cache hit.
		} elseif ( ! filter_var( $domain, FILTER_VALIDATE_DOMAIN, array( 'flags' => FILTER_FLAG_HOSTNAME ) ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Vulnz Agent] Invalid domain name provided.' );
			}
		} elseif ( ! $this->is_available() ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Vulnz Agent] API client is not properly configured.' );
			}
		} else {
			$headers = array(
				'X-Api-Key'    => $this->api_key,
				'Content-Type' => 'application/json; charset=utf-8',
			);

			$get_url      = sprintf( '%s/api/websites/%s', $this->api_url, $domain );
			$get_response = \wp_remote_get(
				$get_url,
				array(
					'headers' => $headers,
					'timeout' => API_REQUEST_TIMEOUT,
				)
			);

			if ( \is_wp_error( $get_response ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Vulnz Agent] API request failed: ' . $get_response->get_error_message() );
				}
			} elseif ( 200 !== \wp_remote_retrieve_response_code( $get_response ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Vulnz Agent] API returned HTTP ' . \wp_remote_retrieve_response_code( $get_response ) );
				}
			} else {
				$body = \wp_remote_retrieve_body( $get_response );
				$data = \json_decode( $body, true );
				if ( is_array( $data ) ) {
					$website_data = $data;

					// Sort plugins: vulnerable ones first, then by title or slug.
					if ( isset( $website_data['wordpress-plugins'] ) && is_array( $website_data['wordpress-plugins'] ) ) {
						usort(
							$website_data['wordpress-plugins'],
							function ( $a, $b ) {
								// First, sort by vulnerability status (vulnerable first).
								$vuln_diff = ( $b['has_vulnerabilities'] ?? false ) <=> ( $a['has_vulnerabilities'] ?? false );
								if ( 0 !== $vuln_diff ) {
									return $vuln_diff;
								}

								// If vulnerability status is the same, sort by configured order.
								if ( 'slug' === PLUGIN_SORT_ORDER ) {
									return strcasecmp( $a['slug'] ?? '', $b['slug'] ?? '' );
								}

								// Default: sort by title.
								return strcasecmp( $a['title'] ?? '', $b['title'] ?? '' );
							}
						);
					}

					if ( WEBSITE_DATA_CACHE_TTL > 0 && ! empty( $cache_key ) ) {
						set_transient( $cache_key, $website_data, WEBSITE_DATA_CACHE_TTL );
					}
				}
			}
		}

		if ( ! is_array( $website_data ) && ! is_null( $website_data ) ) {
			$website_data = null;
		}

		return $website_data;
	}
}
