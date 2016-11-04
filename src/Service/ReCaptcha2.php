<?php

namespace ZfServiceReCaptcha2\Service;

use Traversable;
use ZfServiceReCaptcha2\Service\ReCaptcha2\Response;
use ZfServiceReCaptcha2\Exception;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Stdlib\ArrayUtils;

class ReCaptcha2
{    
    /**
     * URI to the verify server
     *
     * @var string
     */
    const VERIFY_SERVER = 'https://www.google.com/recaptcha/api/siteverify';
    
    /**
     * Public key used when displaying the captcha
     *
     * @var string
     */
    protected $publicKey = null;

    /**
     * Private key used when verifying user input
     *
     * @var string
     */
    protected $privateKey = null;

    /**
     * Ip address used when verifying user input
     *
     * @var string
     */
    protected $ip = null;
    
    /**
     * Response from the verify server
     *
     * @var Response
     */
    protected $response = null;
    
    /**
     * Parameters for the script object
     *
     * @var array
     */
    protected $params = array(
        'onload' => null,
        'render' => 'onload',
        'hl'     => 'en'
    );
    
    /**
     * Attributes for div element
     *
     * @var array
     */
    protected $attributes = array(
        'class'            => 'g-recaptcha',
        'theme'            => 'light',
        'type'             => 'image',
        'tabindex'         => 0,
        'callback'         => null,
        'expired-callback' => null
    );


    /**
     * Class constructor
     *
     * @param string $publicKey
     * @param string $privateKey
     * @param array $params
     * @param array $options
     * @param string $ip
     * @param array|Traversable $params
     */
    public function __construct($publicKey = null, $privateKey = null,
                                $params = null, $attributes = null, $ip = null)
    {
        if ($publicKey !== null) {
            $this->setPublicKey($publicKey);
        }

        if ($privateKey !== null) {
            $this->setPrivateKey($privateKey);
        }

        if ($ip !== null) {
            $this->setIp($ip);
        } else {
            $remoteAddress = new RemoteAddress();
            $this->setIp($remoteAddress->getIpAddress());
        }

        if ($params !== null) {
            $this->setParams($params);
        }

        if ($attributes !== null) {
            $this->setAttributes($attributes);
        }
    }
    
    /**
     * Set the ip property
     *
     * @param string $ip
     * @return static
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get the ip property
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
    
    /**
     * Get the public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Set the public key
     *
     * @param string $publicKey
     * @return static
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get the private key
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Set the private key
     *
     * @param string $privateKey
     * @return static
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }
    
    /**
     * Get a single parameter
     *
     * @param string $key
     * @return mixed
     */
    public function getParam($key)
    {
        return $this->params[$key];
    }
    
    /**
     * Set a single parameter
     *
     * @param string $key
     * @param string $value
     * @return static
     */
    public function setParam($key, $value)
    {
        $key = strtolower($key);
        if (!array_key_exists($key, $this->params)) {
            return $this;
        }
        $this->params[$key] = $value;

        return $this;
    }
    
    /**
     * Get the parameter array
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Set parameters
     *
     * @param array|Zend_Config $params
     * @return static
     * @throws Exception\InvalidArgumentException
     */
    public function setParams($params)
    {
        if ($params instanceof Traversable) {
            $params = ArrayUtils::iteratorToArray($params);
        }
        
        if (!is_array($params)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of params; received "%s"',
                __METHOD__,
                (is_object($params) ? get_class($params) : gettype($params))
            ));
        }

        foreach ($params as $k => $v) {
            $this->setParam($k, $v);
        }

        return $this;
    }
    
    /**
     * Get a single attribute
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key];
    }
    
    /**
     * Set a single attribute
     *
     * @param string $key
     * @param string $value
     * @return static
     */
    public function setAttribute($key, $value)
    {
        $key = strtolower($key);
        if (!array_key_exists($key, $this->attributes)) {
            return $this;
        }
        $this->attributes[$key] = $value;

        return $this;
    }
    
    /**
     * Get attributes array
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Set attributes array
     *
     * @param array|Zend_Config $attributes
     * @return static
     * @throws Exception\InvalidArgumentException
     */
    public function setAttributes($attributes)
    {
        if ($attributes instanceof Traversable) {
            $attributes = ArrayUtils::iteratorToArray($attributes);
        }

        if (is_array($attributes)) {
            foreach ($attributes as $k => $v) {
                $this->setAttribute($k, $v);
            }
        } else {
            throw new Exception\InvalidArgumentException(
                'Expected array or Traversable object'
            );
        }

        return $this;
    }
    
    /**
     * Post a solution to the verify server
     *
     * @param string $responseField
     * @return HttpResponse
     * @throws Exception\InvalidArgumentException
     */
    protected function post($responseField)
    {
        if ($this->privateKey === null) {
            throw new Exception\InvalidArgumentException('Missing private key');
        }

        /* Fetch an instance of the http client */
        $httpClient = new HttpClient;
        $httpClient->resetParameters(true);

        $postParams = array(
            'response'   => $responseField,
            'secret'     => $this->getPrivateKey(),
            'remoteip'   => $this->getIp(),
        );

        /* Make the POST and return the response */
        $httpClient->setUri(self::VERIFY_SERVER)
                   ->setParameterPost($postParams)
                   ->setMethod(HttpRequest::METHOD_POST)
                   ->setEncType(HttpClient::ENC_FORMDATA);
        return $httpClient->send();
    }
    
    /**
     * Verify the user input
     *
     * This method calls up the post method and returns a Response object.
     *
     * @param string $challengeField
     * @param string $responseField
     * @return Response
     */
    public function verify($responseField)
    {
        if (empty($responseField)) {
            throw new Exception\InvalidArgumentException('Missing response field');
        }
        
        $response = $this->post($responseField);
        return new Response(null, null, $response);
    }
}