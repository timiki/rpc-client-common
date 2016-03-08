<?php

namespace Timiki\RpcClientCommon\Client;

/**
 * Client response
 */
class Response
{
	/**
	 * Response info
	 *
	 * @var array
	 */
	protected $info;

	/**
	 * Response type
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Response result
	 *
	 * @var mixed
	 */
	private $result;

	/**
	 * Response constructor
	 *
	 * @param mixed $result
	 * @param array $info
	 * @param string $type
	 */
	public function __construct($result = null, array $info = [], $type = 'json')
	{
		$this->result = $result;
		$this->info   = $info;
		$this->type   = $type;
	}

	/**
	 * Get info
	 *
	 * @param null|string $name
	 * @return array|null|string
	 */
	public function getInfo($name = null)
	{
		if ($name === null) {
			return $this->info;
		}
		if (array_key_exists($name, $this->info)) {
			return $this->info[$name];
		}

		return null;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get result
	 *
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->result;
	}
}

