<?php

namespace Timiki\RpcClientCommon\Client\Handlers;

use Timiki\RpcClientCommon\Client\HandlerAbstract;
use Timiki\RpcClientCommon\Client\Response;
use Timiki\RpcClientCommon\Client\Http;

class Json extends HandlerAbstract
{
	/**
	 * Call method
	 *
	 * @param string $method Method name
	 * @param array $params Method params
	 * @param array $extra Methods extra params
	 * @param array $headers Headers for request
	 * @return Response
	 */
	public function call($method, array $params = [], array $extra = [], array $headers = [])
	{
		$headers['Content-Type'] = 'application/json';
		$id                      = isset($extra['id']) ? $extra['id'] : 0;

		/**
		 * Prepare cookie
		 */

		$cookieForRequest = array_key_exists('Cookie', $headers) ? $headers['Cookie'] : null;
		unset($headers['Cookie']);

		/**
		 * Prepare headers
		 */

		$headersForRequest = [];

		foreach ($headers as $name => $value) {
			if (is_array($value)) {
				$headersForRequest[] = $name.': '.implode(";", $value);
			} else {
				$headersForRequest[] = $name.': '.$value;
			}
		}

		/**
		 * Prepare body
		 */

		$bodyForRequest = json_encode(['jsonrpc' => '2.0', 'method' => $method, 'params' => $params, 'id' => $id]);

		/**
		 * Prepare cURL
		 */

		$curl = new Http();
		$curl->post($this->getClient()->getAddressForRequest(),$bodyForRequest, $headersForRequest, $cookieForRequest);



		// return new Response(json_decode($body), $info, 'json');
	}
}
