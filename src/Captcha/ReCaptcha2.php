<?php

namespace ZfServiceReCaptcha2\Captcha;

use Zend\Captcha\AbstractAdapter;
use ZfServiceReCaptcha2\Service\ReCaptcha2 as ReCaptchaService;

class ReCaptcha2 extends AbstractAdapter
{
    /**#@+
     * Error codes
     */
    const MISSING_VALUE = 'missingValue';
    const ERR_CAPTCHA   = 'errCaptcha';
    const BAD_CAPTCHA   = 'badCaptcha';
    /**#@-*/
    
    /**
     * ReCaptcha response field name
     * @var string
     */
    const RESPONSE = 'g-recaptcha-response';
    
    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = [
        self::MISSING_VALUE => 'Missing captcha fields',
        self::ERR_CAPTCHA   => 'Failed to validate captcha',
        self::BAD_CAPTCHA   => 'Captcha value is wrong: %value%',
    ];
    
    /**
     * Recaptcha service object
     *
     * @var ReCaptchaService
     */
    protected $service;
    
    /**
     * Constructor
     *
     * @param  null|array|Traversable $options
     */
    public function __construct($options = null)
    {
        $this->setService(new ReCaptchaService());

        parent::__construct($options);

        if (!empty($options)) {
            if (array_key_exists('private_key', $options)) {
                $this->getService()->setPrivateKey($options['private_key']);
            }
            if (array_key_exists('public_key', $options)) {
                $this->getService()->setPublicKey($options['public_key']);
            }
        }
    }
    
    /**
     * Generate captcha
     *
     * @return string
     */
    public function generate()
    {
        return '';
    }

    /**
     * Validate captcha
     *
     * @see    \Zend\Validator\ValidatorInterface::isValid()
     * @param  mixed      $value
     * @param  array|null $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (!is_array($value) && !is_array($context)) {
            $this->error(self::MISSING_VALUE);
            return false;
        }

        if (!is_array($value) && is_array($context)) {
            $value = $context;
        }

        if (empty($value[self::RESPONSE])) {
            $this->error(self::MISSING_VALUE);
            return false;
        }

        $service = $this->getService();

        $res = $service->verify($value[self::RESPONSE]);

        if (!$res) {
            $this->error(self::ERR_CAPTCHA);
            return false;
        }

        if (!$res->isValid()) {
            $this->error(self::ERR_CAPTCHA);
            $service->setParam('error', self::ERR_CAPTCHA);
            return false;
        }

        return true;
    }
    
    /**
     * Set option
     *
     * If option is a service parameter, proxies to the service. The same
     * goes for any service options (distinct from service params)
     *
     * @param  string $key
     * @param  mixed $value
     * @return static
     */
    public function setOption($key, $value)
    {
        $service = $this->getService();
        $service->setParam($key, $value);
        $service->setAttribute($key, $value);
    }
    
    /**
     * Retrieve ReCaptcha service object
     *
     * @return ReCaptchaService
     */
    public function getService()
    {
        return $this->service;
    }
    
    /**
     * Set service object
     *
     * @param  ReCaptchaService $service
     * @return static
     */
    public function setService(ReCaptchaService $service)
    {
        $this->service = $service;
        return $this;
    }
    
    /**
     * Retrieve ReCaptcha Private key
     *
     * @return string
     */
    public function getPrivkey()
    {
        return $this->getService()->getPrivateKey();
    }
    
    /**
     * Set ReCaptcha Private key
     *
     * @param string $privkey
     * @return static
     */
    public function setPrivkey($privkey)
    {
        $this->getService()->setPrivateKey($privkey);
        return $this;
    }

    /**
     * Retrieve ReCaptcha Public key
     *
     * @return string
     */
    public function getPubkey()
    {
        return $this->getService()->getPublicKey();
    }
    
    /**
     * Set ReCaptcha public key
     *
     * @param string $pubkey
     * @return static
     */
    public function setPubkey($pubkey)
    {
        $this->getService()->setPublicKey($pubkey);
        return $this;
    }
    
    /**
     * Get helper name used to render captcha
     *
     * @return string
     */
    public function getHelperName()
    {
        return "captcha/recaptcha2";
    }
}