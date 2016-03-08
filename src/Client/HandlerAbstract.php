<?php

namespace Timiki\RpcClientCommon\Client;

use Timiki\RpcClientCommon\Client;

/**
 * Handler abstract class
 */
abstract class HandlerAbstract implements HandlerInterface
{
	/**
	 * Server instance
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * Set client
	 *
	 * @param Client $client Client object
	 * @return $this
	 */
	public function setClient(Client &$client)
	{
		if ($client instanceof Client) {
			$this->client = $client;
		}

		return $this;
	}

	/**
	 * Get client
	 *
	 * @return Client|null
	 */
	public function &getClient()
	{
		return $this->client;
	}

	/**
	 * Get client option value
	 *
	 * @param string $option Option name
	 * @param null $default Option default value
	 * @return mixed
	 */
	public function getOption($option, $default = null)
	{
		if ($this->client instanceof Client) {
			return $this->client->getOption($option, $default);
		}

		return $default;
	}

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
		// Handler call code
	}
}