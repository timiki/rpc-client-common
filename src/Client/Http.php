<?php

namespace Timiki\RpcClientCommon\Client;

use Timiki\RpcClientCommon\Client\Http\Response;

/**
 * Simple cURL client
 */
class Http
{
	/**
	 * Call post request for url
	 *
	 * @param string $url Request url
	 * @param string $body Request body
	 * @param array $headers Headers array
	 * @param array $cookies Cookies array
	 * @param array $extra Extra array
	 * @return Response
	 */
	function post($url, $body, array $headers = [], array $cookies = [], $extra = [])
	{
		if ($curl = curl_init()) {

			/**
			 * Prepare headers
			 */

			$headersForRequest = [];
			unset($headers['Cookie']);

			foreach ($headers as $name => $value) {
				if (is_array($value)) {
					$headersForRequest[] = $name . ': ' . implode(";", $value);
				} else {
					$headersForRequest[] = $name . ': ' . $value;
				}
			}

			/**
			 * Prepare cookie
			 */

			$cookieForRequest = '';

			foreach ($cookies as $name => $value) {
				if (empty($cookieForRequest)) {
					$cookieForRequest = $name . '=' . $value;
				} else {
					$cookieForRequest .= '; ' . $name . '=' . $value;
				}
			}

			$curlOptions = [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST           => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_CONNECTTIMEOUT => 3600,
				CURLOPT_TIMEOUT        => 3600,
				CURLOPT_HEADER         => true,
			];

			if (array_key_exists('curl', $extra)) {
				$curlOptions = array_replace($curlOptions, $extra['curl']);
			}

			$curlOptions[CURLOPT_URL]        = $url;
			$curlOptions[CURLOPT_POSTFIELDS] = $body;

			if ($cookieForRequest) {
				$curlOptions[CURLOPT_COOKIE] = $cookieForRequest;
			}
			if ($headersForRequest) {
				$curlOptions[CURLOPT_HTTPHEADER] = $headersForRequest;
			}

			curl_setopt_array($curl, $curlOptions);
			$out = curl_exec($curl);

			if (!curl_errno($curl)) {
				$response = new Response($out, curl_getinfo($curl));
				curl_close($curl);
			} else {
				$response = new Response('', []);
				curl_close($curl);
			}

			return $response;
		}

		return new Response('', []);
	}

	/**
	 * Parses an array of header lines into an associative array of headers.
	 *
	 * @param array $lines Header lines array of strings in the following format: "Name: Value"
	 * @return array
	 */
	function headers_from_lines($lines)
	{
		$headers = [];

		foreach ($lines as $line) {
			$parts                      = explode(':', $line, 2);
			$headers[trim($parts[0])][] = isset($parts[1])
				? trim($parts[1])
				: null;
		}

		return $headers;
	}
}

