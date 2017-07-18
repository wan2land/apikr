Siot, Iamport
=============

[![Latest Stable Version](https://poser.pugx.org/apikr/siot-iamport/v/stable.svg)](https://packagist.org/packages/apikr/siot-iamport)
[![Latest Unstable Version](https://poser.pugx.org/apikr/siot-iamport/v/unstable.svg)](https://packagist.org/packages/apikr/siot-iamport)
[![Total Downloads](https://poser.pugx.org/apikr/siot-iamport/downloads.svg)](https://packagist.org/packages/apikr/siot-iamport)
[![License](https://poser.pugx.org/apikr/siot-iamport/license.svg)](https://packagist.org/packages/apikr/siot-iamport)

## Installation

```bash
composer require apikr/siot-iamport
```

- 웹사이트 : [www.iamport.kr](http://www.iamport.kr)
- API 페이지 : [api.iamport.kr](https://api.iamport.kr)

## How to use

```php
use Apikr\Siot\Iamport\Api;
use Apikr\Siot\Iamport\Configuration;
use GuzzleHttp\Client;

$api = new Api(new Client(), new Configuration([
    'impKey' => 'imp_apikey',
    'impSecret' => 'ekKoeW8RyKuT0zgaZsUtXXTLQ4AhPFW3ZGseDA6bkA5lamv9OqDMnxyeB9wqOsuO9W3Mx9YSJ4dTqJ3f',
]));
```

## 현재 지원하는 기능

- 비인증결제 관련 메서드
