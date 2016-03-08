<?php

namespace Timiki\RpcClientCommon\Client;

/**
 * Simple cURL client
 */
class Curl
{
	/**
	 * Call post request for url
	 *
	 * @param $url
	 * @param $body
	 * @param array $header
	 * @param null $cookie
	 * @return Response
	 */
	function post($url, $body, array $header = [], $cookie = null)
	{
		if ($curl = curl_init()) {

			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3600);
			curl_setopt($curl, CURLOPT_TIMEOUT, 3600);
			curl_setopt($curl, CURLOPT_HEADER, true);

			if ($cookie) {
				curl_setopt($curl, CURLOPT_COOKIE, $cookie);
			}

			if (count($header) > 0) {
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			}

			$out = curl_exec($curl);

			if (!curl_errno($curl)) {

				$info         = curl_getinfo($curl);
				$headers_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
				$headers_line = substr($out, 0, $headers_size);
				$body         = substr($out, $headers_size);
				$headers      = [];

				foreach (explode(PHP_EOL, $headers_line) as $value) {
					if (!empty(trim($value))) {
						$headers[] = $value;
					}
				}

				$startLine = explode(' ', array_shift($headers), 3);
				$headers   = $this->headers_from_lines($headers);


			}

			curl_close($curl);
		}
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

