API Store, SMS
==============

- [공식페이지](https://www.apistore.co.kr/api/apiViewPrice.do?service_seq=151)

## 사용법

1. 서비스를 신청합니다.
2. 관리자에서 "마이홈" 메뉴에 들어갑니다.
3. "API Store Key"를 복사합니다.
4. 다음과 같이 Api 객체를 선언합니다.

```php
<?php

$httpClient = new GuzzleHttp\Client();
$conf = new Apikr\ApiStore\Sms\Configuration([
    'id' => 'serviceid',
    'apikey' => 'abcdefghiklmnopqrstuvwxyz',
    'sender' => '발신번호',
]);
$sms = new Apikr\ApiStore\Sms\Api($httpClient, $conf);
```

5. 발신번호를 등록합니다.

```php
<?php
$sms->saveSender("1588-xxxx"); // 설정에 넣은 sender 값을 넣습니다.
$result = $sms->senderList(); // 조회
foreach ($result->search('numberList[*].sendnumber') as $sender) {
    echo $sender, "\n"; // 등록된 번호 조회. JmesPath 를 사용합니다.
}
```

6. 문자를 전송합니다.

## 문자전송

```php
<?php
$result = $this->sms->send('000-1234-1234', '문자 가라 얍!');
```

## Exception

- 모든 에러는 `\Apikr\ApiStore\Sms\Exception\RequestException` 클래스로 처리합니다.
- 문자 전송은 `\Apikr\ApiStore\Sms\Exception\SmsDeliveryException` 클래스로 처리합니다.

```
<?php
namespace Apikr\ApiStore\Sms\Exception;

class RequestException extends \RuntimeException
{
    public function getResult();
}

class SmsDeliveryException extends RequestException
{
    public function getReceiver();
    public function getText();
}
```
