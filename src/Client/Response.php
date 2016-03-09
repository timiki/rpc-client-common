<?php

namespace Timiki\RpcClientCommon\Client;

use Timiki\RpcClientCommon\Client\Http\Response as HttpResponse;

/**
 * Client response
 */
class Response
{
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
	 * Response
	 *
	 * @var HttpResponse
	 */
	private $httpResponse;

	/**
	 * Response constructor
	 *
	 * @param mixed $result
	 * @param string $type
	 * @param HttpResponse $httpResponse
	 */
	public function __construct($result = null, $type = 'json', HttpResponse $httpResponse)
	{
		$this->result       = $result;
		$this->type         = $type;
		$this->httpResponse = $httpResponse;
	}

	/**
	 * Get http response object
	 *
	 * @return HttpResponse
	 */
	public function getHttpResponse()
	{
		return $this->httpResponse;
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

