<?php

namespace Timiki\RpcClientCommon\Client;

use GuzzleHttp\Psr7\Request as HttpRequest;
use GuzzleHttp\Psr7\Response as HttpResponse;

/**
 * Client response
 */
class Response
{
    /**
     * Http request
     *
     * @var HttpRequest
     */
    private $request;

    /**
     * Http response
     *
     * @var HttpResponse
     */
    private $response;

    /**
     * Result
     *
     * @var mixed
     */
    private $result;

    /**
     * Set http request.
     *
     * @param HttpRequest $request
     * @return Response
     */
    public function setHttpRequest(HttpRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get http request.
     *
     * @return HttpRequest
     */
    public function getHttpRequest()
    {
        return $this->request;
    }

    /**
     * Set http response.
     *
     * @param HttpResponse $response
     * @return Response
     */
    public function setHttpResponse(HttpResponse $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get http response.
     *
     * @return HttpResponse
     */
    public function getHttpResponse()
    {
        return $this->response;
    }

    /**
     * Set result.
     *
     * @param mixed $result
     * @return Response
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result.
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}

