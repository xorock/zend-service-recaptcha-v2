#Zend Framework 3 integration with Google ReCaptcha v2

Provides [Google ReCaptcha v2](https://www.google.com/recaptcha/intro/index.html) integration for
[Zend Framework 3](https://github.com/zendframework/zendframework).

## Installation

Install this library using composer:

```bash
$ composer require xorock/zend-service-recaptcha-v2
```

Then add `ZfServiceReCaptcha2` to Your module config under the `modules` key.

## Using Zend Framework Captcha element

```php
use Zend\Form\Element\Captcha;
use ZfServiceReCaptcha2\Captcha\ReCaptcha2;

$this->add([
    'type'       => Captcha::class,
    'name'       => 'g-recaptcha-response', // name is required for element to be validated
    'options'    => [
        'label' => 'Please answer question',
        'captcha' => [
            'class' => ReCaptcha2::class,
            'options' => [
                'hl' => 'en', // english is set by deafult, this line is not required
                'theme' => 'light', // see options below
                'callback' => '', // callback function, etc.
                'public_key'  => 'Generated public key',
                'private_key' => 'Generated private key'
            ],
        ],
    ],
]);
```

## Options

Form element allows two different type of options: params and attributes. Both refer to https://developers.google.com/recaptcha/docs/display configuration options.
Parameters are published inside 'script' tag, while the attributes referes to 'div.g-recaptcha element'. By default they are defined as:

```php
/**
 * Parameters for the script object
 *
 * @var array
 */
protected $_params = array(
    'onload' => null,
    'render' => 'onload',
    'hl'     => 'en'
);
    
/**
 * Attributes for div element
 *
 * @var array
 */
protected $_attributes = array(
    'class'            => 'g-recaptcha',
    'theme'            => 'light',
    'type'             => 'image',
    'tabindex'         => 0,
    'callback'         => null,
    'expired-callback' => null
);
```

## ReCaptcha2 form element

ZfServiceReCaptcha2 component also comes with a predefined element `ReCaptcha2`, which extends built-in `\Zend\Form\Element\Captcha`.
By default, it consumes following config:

```php
return [
    'zfservicerecaptcha2' => [
        'recaptcha' => [
            'options' => [
                // Captcha options
                'hl' => 'en',
                'public_key'  => 'Generated public key',
                'private_key' => 'Generated private key'
            ],
        ]
    ]
];
```

It is convenient to set Your ReCaptcha keys and other options in general application configuration.
You can use this element by simply defining

```php
use ZfServiceReCaptcha2\Form\Element\ReCaptcha2;

$this->add([
    'type'       => ReCaptcha2::class,
    // Field name is defined by factory
    // 'name'       => 'g-recaptcha-response', 
    'options'    => [
        'label' => 'Please answer question',
    ],
]);
```
