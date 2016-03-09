<?php

namespace Timiki\RpcClientCommon;

use Timiki\RpcClientCommon\Client\Response;
use Timiki\RpcClientCommon\Client\HandlerInterface;

/**
 * Client class
 */
class Client
{
	/**
	 * Client type
	 *
	 * @var array
	 */
	protected $type = 'json';

	/**
	 * Server address
	 *
	 * @var array
	 */
	protected $address = [];

	/**
	 * Client default headers
	 *
	 * @var array
	 */
	protected $headers = [];

	/**
	 * Client default cookies
	 *
	 * @var array
	 */
	protected $cookies = [];

	/**
	 * Client locale
	 *
	 * @var array
	 */
	protected $locale = 'en';

	/**
	 * Create new client
	 *
	 * @param null|string|array $address RPC address string or array
	 * @param string $type RPC client type
	 * @param array $headers Headers array
	 * @param array $cookies Cookies array
	 * @param string $locale Locale name
	 */
	public function __construct($address = null, $type = 'json', array $headers = [], array $cookies = [], $locale = 'en')
	{
		$this->setHeader('User-Agent', 'RPC client');
		$this->addAddress($address);
		$this->setLocale($locale);
		$this->setHeaders($headers);
		$this->setCookies($cookies);
		$this->type = $type;
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
	 * Set client header
	 *
	 * @param string $name Header name
	 * @param string|array $value Header value
	 * @param bool $replace
	 * @return $this
	 */
	public function setHeader($name, $value, $replace = false)
	{
		if (!is_array($value)) {
			$value = [$value];
		}
		if (array_key_exists($name, $this->headers)) {
			if ($replace) {
				$this->headers[$name] = $value;
			} else {
				$this->headers[$name] = array_merge($this->headers[$name], $value);
			}
		} else {
			$this->headers[$name] = $value;
		}
		return $this;
	}

	/**
	 * Set client headers by array
	 *
	 * @param array $headers Headers array
	 * @return $this
	 */
	public function setHeaders(array $headers)
	{
		foreach ($headers as $name => $value) {
			$this->setHeader($name, $value);
		}

		return $this;
	}

	/**
	 * Get headers
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Set client cookie
	 *
	 * @param string $name Cookie name
	 * @param string $value Cookie value
	 * @return $this
	 */
	public function setCookie($name, $value)
	{
		$this->cookies[$name] = $value;

		return $this;
	}

	/**
	 * Set client cookies by array
	 *
	 * @param array $cookies Cookies
	 * @return $this
	 */
	public function setCookies(array $cookies)
	{
		foreach ($cookies as $name => $value) {
			$this->setCookie($name, $value);
		}

		return $this;
	}

	/**
	 * Get client cookies
	 *
	 * @return array
	 */
	public function getCookies()
	{
		return $this->cookies;
	}

	/**
	 * Set client locale
	 *
	 * @param $locale
	 * @return $this
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;
		$this->setHeader('Accept-Language', $locale);
		return $this;
	}

	/**
	 * Get client locale
	 *
	 * @return string|null
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * Add server address
	 *
	 * @param string|array $address
	 * @param bool $append
	 * @return $this
	 */
	public function addAddress($address, $append = true)
	{
		if ($append === false) {
			$this->address = [];
		}
		if (is_array($address)) {
			foreach ($address as $path) {
				$this->addAddress($path);
			}
		}
		if (is_string($address)) {
			$this->address[] = $address;
		}

		return $this;
	}

	/**
	 * Get server address for request
	 *
	 * @return string|null
	 */
	public function getAddressForRequest()
	{
		if (count($this->address) === 0) {
			return null;
		}
		if (count($this->address) === 1) {
			return $this->address[0];
		}

		return $this->address[rand(0, count($this->address))];
	}

	/**
	 * Get server address list
	 *
	 * @return array
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * Call request
	 *
	 * @param string $method
	 * @param array $params
	 * @param array $extra
	 * @return Response
	 */
	public function call($method, array $params = [], array $extra = [])
	{
		/**
		 * Handler
		 * @var HandlerInterface $handler
		 */

		if (class_exists('\\Timiki\\RpcClientCommon\\Client\\Handlers\\' . ucfirst(strtolower($this->getType())))) {
			$handlerClass = '\\Timiki\\RpcClientCommon\\Client\\Handlers\\' . ucfirst(strtolower($this->getType()));
			$handler      = new $handlerClass();
		} else {
			$handlerClass = '\\Timiki\\RpcClientCommon\\Client\\Handlers\\Json';
			$handler      = new $handlerClass();
		}

		$handler->setClient($this);

		/**
		 * Prepare headers
		 */

		$headers     = $this->getHeaders();
		$callHeaders = [];
		if (array_key_exists('headers', $extra)) {
			$callHeaders = (array)$extra['headers'];
			unset($extra['headers']);
		}

		foreach ($callHeaders as $name => $value) {
			if (!is_array($value)) {
				$value = [$value];
			}
			if (array_key_exists($name, $headers)) {
				$headers[$name] = array_merge($headers, $value);
			} else {
				$headers[$name] = $value;
			}
		}

		/**
		 * Prepare cookies
		 */
		$cookies     = $this->getCookies();
		$callCookies = [];
		if (array_key_exists('cookies', $extra)) {
			$callCookies = (array)$extra['cookies'];
			unset($extra['cookies']);
		}

		foreach ($callCookies as $name => $value) {
			$cookies[$name] = $value;
		}

		/**
		 * Call handler
		 */

		return $handler->call($method, $params, $extra, $headers, $cookies);
	}
}
