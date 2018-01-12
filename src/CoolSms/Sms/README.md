# Cool Sms, SMS

[![Latest Stable Version](https://poser.pugx.org/apikr/coolsms-sms/v/stable.svg)](https://packagist.org/packages/apikr/coolsms-sms)
[![Latest Unstable Version](https://poser.pugx.org/apikr/coolsms-sms/v/unstable.svg)](https://packagist.org/packages/apikr/coolsms-sms)
[![Total Downloads](https://poser.pugx.org/apikr/coolsms-sms/downloads.svg)](https://packagist.org/packages/apikr/coolsms-sms)
[![License](https://poser.pugx.org/apikr/coolsms-sms/license.svg)](https://packagist.org/packages/apikr/coolsms-sms)

공식페이지 : [www.coolsms.co.kr](https://www.coolsms.co.kr/)

```php
<?php

$httpClient = new GuzzleHttp\Client();

$conf = new Apikr\CoolSms\Sms\Configuration([
    'apikey' => 'abcdefghiklmnopqrstuvwxyz',
    'secret' => 'abcdef',
    'sender' => '발신번호',
]);
$sms = new Apikr\CoolSms\Sms\Api($httpClient, $conf);
```

## 발신자 번호 등록

```php
<?php
$result = $this->sms->registerSender('000-1234-1234');
$result['ars_number']; // 이 번호로 인증을 진행합니다.

// 그리고 다음 핸들키를 이용하여 활성화합니다.
$this->sms->verifySender($result['handle_key']); 
```

## 문자전송

```php
<?php
$result = $this->sms->send('000-1234-1234', '문자 가라 얍!');
```

## 문자 잔여량

```php
<?php
$result = $this->sms->balance(); // 한번에 가지고 오기
$result['cache']; // cache
$result['point']; // point
```

## Exception

- 모든 에러는 `\Apikr\CoolSms\Sms\Exception\RequestException` 클래스로 처리합니다.
- 문자 전송은 `\Apikr\CoolSms\Sms\Exception\SmsDeliveryException` 클래스로 처리합니다.

```
<?php
namespace Apikr\CoolSms\Sms\Exception;

class RequestException extends \RuntimeException
{
    const ERR_SERVER = -100;
    const ERR_INVALID_PARAM = -101;
    const ERR_AUTH_ERROR = -102;
    const ERR_WRONG_SENDER = -103;
    const ERR_DELIVERY_LIMIT_OR_TIME = -105;
    const ERR_INSUFFICIENT_REMAINING = -109;
    const ERR_RESERVED_DATETIME = -115;
    const ERR_REQUIRE_CHARGE = -201;
    const ERR_WRONG_IMAGE = -301;
    const ERR_UNKNOWN = -900;

    public function getResult();
}

class SmsDeliveryException extends RequestException
{
    public function getReceiver();
    public function getText();
}
```
