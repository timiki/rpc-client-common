<?php

namespace Timiki\RpcClientCommon\Client\Handlers;

use Timiki\RpcClientCommon\Client\Handler;

class Json extends Handler
{
    /**
     * request
     *
     * @return mixed
     */
    public function request($httpRequest, $method, $params, $extra)
    {
        $id = 0;
        if (isset($extra['id'])) {
            $id = $extra['id'];
        }
        $jsonRequest                          = ['jsonrpc' => '2.0', 'method' => $method, 'params' => $params, 'id' => $id];
        $httpRequest->headers['Content-Type'] = 'application/json';
        $httpRequest->body                    = json_encode($jsonRequest);
    }

    /**
     * response
     *
     * @return mixed
     */
    public function response(\GuzzleHttp\Psr7\Response $httpResponse, \StdClass &$response)
    {
        $response = json_decode($httpResponse->getBody());
    }
}
