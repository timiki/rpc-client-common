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
	 * Http constructor
	 *
	 * @param $message
	 */
	public function __construct($message)
	{
		$this->parseMessage($message);
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
					$headers[$key] = $value;
				} elseif (!is_array($headers[$key])) {
					$headers[$key] = [$headers[$key], $value];
				} else {
					$headers[$key][] = $value;
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
}

