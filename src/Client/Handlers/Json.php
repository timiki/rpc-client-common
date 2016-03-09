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
	 * @param array $cookies Cookies for request
	 * @return Response
	 */
	public function call($method, array $params = [], array $extra = [], array $headers = [], array $cookies = [])
	{
		$headers['Content-Type'] = ['application/json'];
		$id                      = isset($extra['id']) ? $extra['id'] : 0;
		$body                    = json_encode(['jsonrpc' => '2.0', 'method' => $method, 'params' => $params, 'id' => $id]);
		$curl                    = new Http();
		$httpResponse            = $curl->post($this->getClient()->getAddressForRequest(), $body, $headers, $cookies, $extra);

		return new Response(json_decode($httpResponse->getBody()), 'json', $httpResponse);
	}
}
