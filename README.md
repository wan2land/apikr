API KR
======

[![Latest Stable Version](https://poser.pugx.org/apikr/apikr/v/stable.svg)](https://packagist.org/packages/apikr/apikr)
[![Latest Unstable Version](https://poser.pugx.org/apikr/apikr/v/unstable.svg)](https://packagist.org/packages/apikr/apikr)
[![Total Downloads](https://poser.pugx.org/apikr/apikr/downloads.svg)](https://packagist.org/packages/apikr/apikr)
[![License](https://poser.pugx.org/apikr/apikr/license.svg)](https://packagist.org/packages/apikr/apikr)

항상 똑같은거 반복해서 사용하는 API들, 한곳에 묶어 보고 싶었습니다.

## 제공하는 API 목록

- [SK Planet, T Map](src/SKPlanet/TMap/) : 지도 서비스
- [SK Telecom, 공휴일(Event Day)](src/SKTelecom/EventDay/) : 공휴일
- [Paygate, Seyfert](src/Paygate/Seyfert/) : 핀테크 플랫폼
- [Aligo, SMS](src/Aligo/Sms) : SMS 서비스
- [API Store, SMS](src/ApiStore/Sms) : SMS 서비스
- [시옷, 아임포트(Iamport)](src/Siot/Iamport) : 결제 서비스

## 설치하기 (Installation)

API KR은 [컴포저(Composer)](https://getcomposer.org/)를 통해서 설치할 수 있습니다.

```sh
composer require apikr/apikr
```

## 기여하기 (Contribution)

개인이 사용중인거 있으시다면 조금만 정리해서 올려주세요.

 - PHP 소스이고, [안전한 PHP 버전](https://en.wikipedia.org/wiki/PHP)을 사용합니다. 현재는 PHP 5.6 이상입니다. 
 - HTTP REST Api 경우는 반드시 Guzzle HTTP를 사용합니다.
 - 네임스페이스는 `Apikr\회사이름\서비스이름`으로 해주세요.
 - 현재 베타버전을 사용하고 있습니다. 기존의 API가 변경된 경우 Minor 버전(0.x.0)이 올라가고,
   기능이 추가되거나 버그가 수정된 것은 Fix 버전(0.0.x)이 올라갑니다.
