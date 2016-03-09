<?php

namespace Timiki\RpcClientCommon\Client;

use Timiki\RpcClientCommon\Client;

/**
 * Handler interface
 */
interface HandlerInterface
{
	/**
	 * Set client
	 *
	 * @param Client $client Client object
	 * @return $this
	 */
	public function setClient(Client &$client);

	/**
	 * Get client
	 *
	 * @return Client|null
	 */
	public function &getClient();

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
	public function call($method, array $params = [], array $extra = [], array $headers = [], array $cookies = []);
}