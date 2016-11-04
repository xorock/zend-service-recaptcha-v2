<?php

use ZfServiceReCaptcha2\Form\Element;
use ZfServiceReCaptcha2\Form\View\Helper\Captcha\ReCaptcha2;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'form_elements' => [
        'factories' => [
            Element\ReCaptcha2::class => Element\ReCaptcha2Factory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'captcha/recaptcha2'          => ReCaptcha2::class,
            'captcha_recaptcha2'          => ReCaptcha2::class,
            'captchaRecaptcha2'           => ReCaptcha2::class,
            'CaptchaRecaptcha2'           => ReCaptcha2::class,
            'formcaptcharecaptcha2'       => ReCaptcha2::class,
            'form_captcha_recaptcha2'     => ReCaptcha2::class,
            'formCaptchaRecaptcha2'       => ReCaptcha2::class,
            'FormCaptchaRecaptcha2'       => ReCaptcha2::class,
        ],
        'factories' => [
            ReCaptcha2::class => InvokableFactory::class,
        ],
    ],
];