<?php

namespace Timiki\RpcClientCommon;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request as HttpClientRequest;
use Timiki\RpcClientCommon\Client\Response;

/**
 * Client class
 */
class Client
{
    /**
     * Server options
     *
     * @var array
     */
    protected $options = [
        'forwardHeaders' => [], // Forward headers array
        'forwardCookies' => [], // Forward cookies array
        'forwardIp'      => true, // Forward client ip to server
        'forwardLocale'  => true, // Forward client locale to server
    ];

    /**
     * Client type
     *
     * @var array
     */
    protected $type = 'json';
    protected $defaultType = 'json';
    /**
     * Server address
     *
     * @var array
     */
    protected $address = [];

    /**
     * Client locale
     *
     * @var array
     */
    protected $locale = 'en';

    /**
     * Create new client
     *
     * @param null|string|array $address
     * @param array             $options
     * @param string            $type
     * @param string            $locale
     */
    public function __construct($address = null, array $options = [], $type = 'json', $locale = 'en')
    {
        $this->addAddress($address);
        $this->setOptions($options);
        $this->setLocale($locale);
        $this->setType($type);
    }

    /**
     * Set type
     *
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * Set client locale
     *
     * @param $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

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
     * Set option value
     *
     * @param $option
     * @param $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        if (array_key_exists($option, $this->options)) {
            $this->options[$option] = $value;
        }

        return $this;
    }

    /**
     * Set multi options values.
     *
     * @param array $array
     * @return string|null
     */
    public function setOptions($array)
    {
        if (is_array($array)) {
            foreach ($array as $option => $value) {
                $this->setOption($option, $value);
            }
        }

        return $this;
    }

    /**
     * Get option value.
     *
     * @param      $option
     * @param null $default
     * @return array
     */
    public function getOption($option, $default = null)
    {
        if (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }

        return $default;
    }

    /**
     * Add server address
     *
     * @param      $address
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
    protected function getAddressForRequest()
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
     * @param array  $params
     * @param array  $extra
     * @return Response
     */
    public function call($method, array $params = [], array $extra = [])
    {
        /*
        | Create new Http Client
        */
        $httpClient       = new HttpClient();
        $request          = new \stdClass();
        $request->headers = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
        $request->body    = null;

        /*
         | Get  handler
         */

        if (class_exists('\\Timiki\\RpcClientCommon\\Client\\Handlers\\'.ucfirst(strtolower($this->getType())))) {
            $handlerClass = '\\Timiki\\RpcClientCommon\\Client\\Handlers\\'.ucfirst(strtolower($this->getType()));
            $handler      = new $handlerClass();
        } else {
            $handlerClass = '\\Timiki\\RpcClientCommon\\Client\\Handlers\\'.ucfirst(strtolower($this->defaultType));
            $handler      = new $handlerClass();
        }

        /*
        | Prepare handler
        */

        // Cookies
        if (class_exists('\Symfony\Component\HttpFoundation\Request')) {
            $cookies = $this->getOption('forwardCookies', []);
            if (is_array($cookies) and !empty($cookies)) {
                $cookiesToHeader       = [];
                $cookiesToHeaderString = '';
                foreach (\Symfony\Component\HttpFoundation\Request::createFromGlobals()->cookies->all() as $name => $values) {
                    if (in_array($name, $cookies)) {
                        $cookiesToHeader[$name] = $values;
                    }
                }
                foreach ($cookiesToHeader as $key => $value) {
                    $cookiesToHeaderString .= $key.'='.$value.'; ';
                }
                if (!empty($cookiesToHeaderString)) {
                    $request->headers['Cookie'] = $cookiesToHeaderString;
                }
            }
        }

        // Headers
        $headersForward = $this->getOption('forwardHeaders', []);
        if (is_array($headersForward) and !empty($headersForward)) {
            foreach (\Symfony\Component\HttpFoundation\Request::createFromGlobals()->headers->all() as $name => $values) {
                if (in_array($name, $headersForward)) {
                    $request->headers[$name] = $values;;
                }
            }
        }

        // Ip
        if ($this->getOption('forwardIp', false)) {
            if (class_exists('\Symfony\Component\HttpFoundation\Request')) {
                $request->headers['Client-Ip'] = \Symfony\Component\HttpFoundation\Request::createFromGlobals()->getClientIp();
            }
        }

        // Locale
        if ($this->getOption('forwardLocale', false)) {
            if ($this->getLocale() !== null) {
                $request->headers['Accept-Language'] = $this->getLocale();
            }
        }

        /*
        | Process handlers for request
        */
        $reflection = new  \ReflectionObject($handler);
        if ($reflection->hasMethod('request')) {
            $requestParams = $reflection->getMethod('request')->getParameters();
            $args          = [];
            foreach ($requestParams as $param) {
                if ($param->getName() === 'method') {
                    $args[] = &$method;
                }
                if ($param->getName() === 'params') {
                    $args[] = &$params;
                }
                if ($param->getName() === 'extra') {
                    $args[] = &$extra;
                }
                if ($param->getName() === 'httpRequest') {
                    $args[] = &$request;
                }
            }
            $reflection->getMethod('request')->invokeArgs($handler, $args);
        }

        /*
        | Send Http Request and give Http Response
        */

        $httpRequest  = new HttpClientRequest('POST', $this->getAddressForRequest(), (array)$request->headers, $request->body);
        $httpResponse = $httpClient->send($httpRequest, ['timeout' => 3600, 'connect_timeout' => 3600, 'verify' => false]);

        /*
        | Process handlers for response
        */

        $response = new Response();
        $response->setHttpRequest($httpRequest);
        $response->setHttpResponse($httpResponse);

        $reflection = new  \ReflectionObject($handler);
        if ($reflection->hasMethod('response')) {
            $requestParams = $reflection->getMethod('response')->getParameters();
            $args          = [];
            foreach ($requestParams as $param) {
                if ($param->getName() === 'method') {
                    $args[] = &$method;
                }
                if ($param->getName() === 'params') {
                    $args[] = &$params;
                }
                if ($param->getName() === 'extra') {
                    $args[] = &$extra;
                }
                if ($param->getName() === 'response') {
                    $args[] = &$response;
                }
                if ($param->getName() === 'httpResponse') {
                    $args[] = &$httpResponse;
                }
            }
            $reflection->getMethod('response')->invokeArgs($handler, $args);
        }

        return $response;
    }
}
