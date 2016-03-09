<?php

namespace Timiki\RpcClientCommon\Client\Http;

/**
 * Http response
 */
class Response
{
	/**
	 * Protocol
	 *
	 * @var string
	 */
	protected $protocol;

	/**
	 * Version
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Code
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason_phrase;

	/**
	 * Headers
	 *
	 * @var array
	 */
	protected $headers;

	/**
	 * Body
	 *
	 * @var string
	 */
	protected $body;

	/**
	 * Raw message
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Info
	 *
	 * @var array
	 */
	protected $info = [];

	/**
	 * Http constructor
	 *
	 * @param string $message Raw http message
	 * @param array $info Info for request
	 */
	public function __construct($message, array $info = [])
	{
		$this->message = $message;
		$this->info    = $info;
		if (!empty(trim($message))) {
			$this->parseMessage($message);
		}
	}

	/**
	 * Parse a message into parts
	 *
	 * @param string $message Message to parse
	 * @return array
	 */
	protected function parseMessage($message)
	{
		$startLine = null;
		$headers   = [];
		$body      = '';

		// Iterate over each line in the message, accounting for line endings
		$lines = preg_split('/(\\r?\\n)/', $message, -1, PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0, $totalLines = count($lines); $i < $totalLines; $i += 2) {

			$line = $lines[$i];

			// If two line breaks were encountered, then this is the end of body
			if (empty($line)) {
				if ($i < $totalLines - 1) {
					$body = implode('', array_slice($lines, $i + 2));
				}
				break;
			}

			// Parse message headers
			if (!$startLine) {
				$startLine = explode(' ', $line, 3);
			} elseif (strpos($line, ':')) {
				$parts = explode(':', $line, 2);
				$key   = trim($parts[0]);
				$value = isset($parts[1]) ? trim($parts[1]) : '';
				if (!isset($headers[$key])) {
					$headers[strtolower($key)] = [$value];
				} elseif (!is_array($headers[strtolower($key)])) {
					$headers[strtolower($key)] = [$headers[strtolower($key)], $value];
				} else {
					$headers[strtolower($key)][] = $value;
				}
			}
		}

		$parts = [
			'start_line' => $startLine,
			'headers'    => $headers,
			'body'       => $body,
		];

		list($protocol, $version) = explode('/', trim($parts['start_line'][0]));

		$this->protocol      = $protocol;
		$this->version       = $version;
		$this->code          = $parts['start_line'][1];
		$this->headers       = $parts['headers'];
		$this->body          = $parts['body'];
		$this->reason_phrase = isset($parts['start_line'][2]) ? $parts['start_line'][2] : '';
	}

	/**
	 * Gets headers array
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Get header by name
	 *
	 * @param string $name Header name
	 * @param null|string|array $default Default header values if header not set
	 * @return array|null
	 */
	public function getHeader($name, $default = null)
	{
		if (array_key_exists(strtolower($name), $this->headers)) {
			return $this->headers[strtolower($name)];
		}
		return $default;
	}

	/**
	 * Get raw message
	 *
	 * @return string|null
	 */
	public function getRaw()
	{
		return $this->message;
	}

	/**
	 * Get body
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Get info
	 *
	 * @return array
	 */
	public function getInfo()
	{
		return $this->info;
	}
}

