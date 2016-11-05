<?php

namespace ZfServiceReCaptcha2\Form\Element;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfServiceReCaptcha2\Captcha\ReCaptcha2 as CaptchaReCaptcha2;

class ReCaptcha2Factory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->has('config') ? $container->get('config') : [];
        
        $captchaOptions = [
            'class' => CaptchaReCaptcha2::class
        ];
        
        $captchaConfig = isset($config['zfservicerecaptcha2']['recaptcha'])
            ? $config['zfservicerecaptcha2']['recaptcha']
            : null;

        if (is_array($captchaConfig) && array_key_exists('options', $captchaConfig)) {
            $captchaOptions += $captchaConfig;
        }
        
        $captchaElement = new ReCaptcha2();
        $captchaElement->setName(CaptchaReCaptcha2::RESPONSE);
        $captchaElement->setCaptcha($captchaOptions);
        
        return $captchaElement;
    }
}