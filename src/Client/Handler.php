<?php

namespace Timiki\RpcClientCommon\Client;

use Timiki\RpcClientCommon\Client;

/**
 * Abstract client handler
 */
abstract class Handler
{

// Add process methods to your handler
//
//    /**
//     * Process request
//     */
//    public function request()
//    {
//    }
//
//    /**
//     * Process response
//     */
//    public function response()
//    {
//    }
//

    /**
     * Server instance
     *
     * @var Client
     */
    private $client;

    /**
     * Set server instance.
     *
     * @param $client
     * @return Handler
     */
    public function setClient(&$client)
    {
        if ($client instanceof Client) {
            $this->client = $client;
        }

        return $this;
    }

    /**
     * Get option value
     *
     * @param      $option
     * @param null $default
     * @return mixed
     */
    public function getOption($option, $default = null)
    {
        if ($this->client instanceof Client) {
            return $this->client->getOption($option, $default);
        }

        return null;
    }

    /**
     * Get server instance.
     *
     * @return Client|null
     */
    public function &getClient()
    {
        return $this->client;
    }
}
