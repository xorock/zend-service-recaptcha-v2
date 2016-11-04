<?php

namespace ZfServiceReCaptcha2\Form\View\Helper\Captcha;

use ZfServiceReCaptcha2\Captcha\ReCaptcha2 as RecaptchaComponent;
use ZfServiceReCaptcha2\Exception\InvalidArgumentException;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormInput;

class ReCaptcha2 extends FormInput
{
    /**
     * URI to the secure API
     *
     * @var string
     */
    const API_SECURE_SERVER = 'https://www.google.com/recaptcha/api';
    
    /**
     * Default ReCaptcha class name
     * 
     * @var string
     */
    const CAPTCHA_CLASS_NAME = 'g-recaptcha';
    
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }
    
    public function render(ElementInterface $element)
    {
        $html = $this->renderCaptchaElement($element->getCaptcha());
        $this->renderHeadScript($element->getCaptcha()->getService()->getParams());
        return $html;
    }
    
    /**
     * Add JS for ReCaptcha element
     * 
     * @return string
     */
    public function renderHeadScript(array $params)
    {
        $host = self::API_SECURE_SERVER;

        $langPart = '?hl=en';
        if (!empty($params['hl'])) {
            $langPart = '?hl=' . urlencode($params['hl']);
        }
        
        $renderPart = '&render=onload';
        if (!empty($params['render'])) {
            $renderPart = '&render=' . urlencode($params['render']);
        }
        
        $onloadPart = '';
        if (!empty($params['onload'])) {
            $onloadPart = '&onload=' . urlencode($params['onload']);
        }
        
        $file = "{$host}.js{$langPart}{$renderPart}{$onloadPart}";
        
        $this->view->headScript()->appendFile($file, null, array('async' => true, 'defer' => true));
    }
    
    /**
     * Prepare ReCaptcha element
     * 
     * @param RecaptchaComponent $captcha
     * @return string
     * @throws InvalidArgumentException
     */
    public function renderCaptchaElement(RecaptchaComponent $captcha)
    {
        if (empty($captcha->getPubkey())) {
            throw new InvalidArgumentException('Missing public key');
        }
        
        $captchaService = $captcha->getService();
        
        $captchaPattern = '<div %s></div>';
        $captchaAttributes = $this->createAttributesString(array(
            'class' => $captchaService->getAttribute('class'),
            'data-sitekey' => $captcha->getPubkey(),
            'data-theme' => $captchaService->getAttribute('theme'),
            'data-type' => $captchaService->getAttribute('type'),
            'data-tabindex' => $captchaService->getAttribute('tabindex'),
            'data-callback' => $captchaService->getAttribute('callback'),
            'data-expired-callback' => $captchaService->getAttribute('expired-callback')
        ));
        
        $captchaElement = sprintf($captchaPattern, $captchaAttributes);
        return $captchaElement;
    }
}