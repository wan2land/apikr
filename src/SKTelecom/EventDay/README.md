SKTelecom, EventDay (공휴일)
============================

[![Latest Stable Version](https://poser.pugx.org/apikr/sktelecom-eventday/v/stable.svg)](https://packagist.org/packages/apikr/sktelecom-eventday)
[![Latest Unstable Version](https://poser.pugx.org/apikr/sktelecom-eventday/v/unstable.svg)](https://packagist.org/packages/apikr/sktelecom-eventday)
[![Total Downloads](https://poser.pugx.org/apikr/sktelecom-eventday/downloads.svg)](https://packagist.org/packages/apikr/sktelecom-eventday)
[![License](https://poser.pugx.org/apikr/sktelecom-eventday/license.svg)](https://packagist.org/packages/apikr/sktelecom-eventday)

## Installation

```bash
composer require apikr/sktelecom-eventday
```

- [공식페이지](https://developers.sktelecom.com/content/sktApi/view/?svcId=10072)

## 사용법

1. Workspace메뉴에서 신청합니다.
2. "TDCProject Key"를 복사합니다.
3. 다음과 같이 Api 객체를 선언합니다.

```php
<?php

$httpClient = new GuzzleHttp\Client();
$conf = new Apikr\SKTelecom\EventDay\Configuration([
    'apikey' => 'abcdefghiklmnopqrstuvwxyz',
]);
$eventDay = new Apikr\SKTelecom\EventDay\Api($httpClient, $conf);
```

## 공휴일 가져오기

```php
<?php
$result = $eventDay->getDays(Apikr\SKTelecom\EventDay\Api::TYPE_HOLIDAYS);
```

## Exception

- 모든 에러는 `\Apikr\SKTelecom\EventDay\Exception\EventDayRequestException` 클래스로 처리합니다.

```
<?php
namespace Apikr\SKTelecom\EventDay\Exception;

class EventDayRequestException extends \RuntimeException
{
    public function getResult();
}
```
