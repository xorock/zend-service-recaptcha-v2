<?php

namespace ZfServiceReCaptcha2\Service\ReCaptcha2;

use Zend\Http\Response as HttpResponse;
use Zend\Json\Decoder;

class Response
{
    /**
     * Status
     *
     * true if the response is valid or false otherwise
     *
     * @var boolean
     */
    protected $status = null;

    /**
     * Error codes
     *
     * The error codes if the status is false. The different error codes can be found in the
     * recaptcha API docs.
     *
     * @var array
     */
    protected $errorCodes = array();

    /**
     * Class constructor used to construct a response
     *
     * @param string $status
     * @param array $errorCodes
     * @param Response $httpResponse If this is set the content will override $status and $errorCode
     */
    public function __construct($status = null, array $errorCodes = null, HttpResponse $httpResponse = null)
    {
        if ($status !== null) {
            $this->setStatus($status);
        }

        if ($errorCodes !== null) {
            $this->setErrorCodes($errorCodes);
        }

        if ($httpResponse !== null) {
            $this->setFromHttpResponse($httpResponse);
        }
    }

    /**
     * Set the status
     *
     * @param string $status
     * @return static
     */
    public function setStatus($status)
    {
        $this->status = (bool) $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Alias for getStatus()
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->getStatus();
    }

    /**
     * Set the error codes
     *
     * @param array $errorCodes
     * @return static
     */
    public function setErrorCodes(array $errorCodes)
    {
        $this->errorCodes = $errorCodes;

        return $this;
    }

    /**
     * Get the error codes
     *
     * @return array
     */
    public function getErrorCodes()
    {
        return $this->errorCodes;
    }

    /**
     * Populate this instance based on a Response object
     *
     * @param HttpResponse $response
     * @return static
     */
    public function setFromHttpResponse(HttpResponse $response)
    {
        $body = Decoder::decode($response->getBody());

        // Default status and error code
        $status = false;
        $errorCodes = array();

        if (!empty($body->success) && is_bool($body->success)) {
            $status = $body->success;
        }

        if (!empty($body->{'error-codes'}) && is_array($body->{'error-codes'})) {
            $errorCodes = $body->{'error-codes'};
        }

        $this->setStatus($status);
        $this->setErrorCodes($errorCodes);

        return $this;
    }
}