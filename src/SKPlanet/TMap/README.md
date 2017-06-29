SK Planet, T-Map
================

공식페이지 : [developers.skplanetx.com/apidoc/kor/tmap](https://developers.skplanetx.com/apidoc/kor/tmap)

## 사용법

```php
use Apikr\SKPlanet\TMap\Api;
use Apikr\SKPlanet\TMap\TMap;
use Apikr\SKPlanet\TMap\Configuration;
use GuzzleHttp\Client;

$api = new Api(new Client(), new Configuration([
    'apiKey' => 'c2e0150d-a4ba-391e-89ad-d8f74f167432',
])); // API 를 직접 요청하는 클래스
$tmap = new TMap($api);
```

## 지원하는 메서드

### 거리구하기

거리를 구할 때 사용합니다. 매개변수로서 `Apikr\SKPlanet\TMap\LatLng` 클래스를 통해 요청해도 되지만,
`Apikr\SKPlanet\TMap\Contracts\SpatialPoint` 인터페이스를 구현한 클래스를 사용해도 됩니다. 반환값은 정수형(`int`)이며 단위는 미터(m)입니다.

**Example.**

```php
$tmap->getDistance(
    new LatLng('37.55510690', '126.97069110'), // 서울역
    new LatLng('35.87143540', '128.60144500'), // 대구
    [
        'searchOption' => Configuration::OPTION_SHORTEST, // 생략가능
    ]
); // return 277922
```

### 경유지를 포함한 거리구하기

거리를 구할 때 사용합니다. 매개변수로서 `Apikr\SKPlanet\TMap\LatLng` 클래스를 통해 요청해도 되지만,
`Apikr\SKPlanet\TMap\Contracts\SpatialPoint` 인터페이스를 구현한 클래스를 사용해도 됩니다. 반환값은 정수형의 배열(`int[]`)이며 각각의 단위는 미터(m)입니다.

**Example.**

```php
$tmap->getDistances([
    new LatLng('37.55510690', '126.97069110'), // 서울역
    new LatLng('37.54053970', '127.07185000'), // 건국대병원
    new LatLng('36.32598610', '127.42067930'), // 대전
    new LatLng('35.87143540', '128.60144500'), // 대구
], [
    'searchOption' => Configuration::OPTION_SHORTEST,
]); // return [11767, 157973, 137077]
```

### 지오코딩

주소를 Lat, Lng로 변경합니다. 매개변수로 주소를 받습니다.
신길/구길 주소 둘다 사용가능합니다. 반환값은 `Apikr\SKPlanet\TMap\LatLng` 객체를 반환합니다.

**Example.**

```php
$result = $tmap->geocoding("서울 중구 명동길 14 7층"); // 신길주소

$result->getSpatialLat(); // 37.563411
$result->getSpatialLng(); // 126.982886
```

### 역지오코딩

Lat, Lng를 주소로 변경합니다. 매개변수로서 `Apikr\SKPlanet\TMap\LatLng` 클래스를 통해 요청해도 되지만,
`Apikr\SKPlanet\TMap\Contracts\SpatialPoint` 인터페이스를 구현한 클래스를 사용해도 됩니다.

**Example.**

```php
$tmap->reverseGeocoding(new LatLng('37.563411', '127.061217')); // 서울특별시 동대문구 한천로6길 36
```
