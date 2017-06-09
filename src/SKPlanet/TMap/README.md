APIKR - SK Planet, T-Map
======

## 사용법

```php
use Apikr\SKPlanet\TMap\TMap;
use Apikr\SKPlanet\TMap\Configuration;
use GuzzleHttp\Client;

$api = new TMap(new Client(), new Configuration([
    'apiKey' => 'c2e0150d-a4ba-391e-89ad-d8f74f167432',
]));
```

## 지원하는 메서드

```php
// 거리 구하기
// 반환값의 단위는 미터(m) 입니다.
$api->getDistance(
    new LatLng('37.55510690', '126.97069110'), // 서울역
    new LatLng('35.87143540', '128.60144500'), // 대구
    [
        'searchOption' => Configuration::OPTION_SHORTEST, // 생략가능
    ]
); // return 277922

// 경유지 있는 거리 구하기
// 반환값의 단위는 미터(m) 입니다.
// 다음 경우 구간이 3개이기 때문에 3개로 값을 반환합니다.
$api->getDistances([
    new LatLng('37.55510690', '126.97069110'), // 서울역
    new LatLng('37.54053970', '127.07185000'), // 건국대병원
    new LatLng('36.32598610', '127.42067930'), // 대전
    new LatLng('35.87143540', '128.60144500'), // 대구
], [
    'searchOption' => Configuration::OPTION_SHORTEST,
]); // return [11767, 157973, 137077]
```
