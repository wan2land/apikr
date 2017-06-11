API KR
======

항상 똑같은거 반복해서 사용하는 API들, 한곳에 묶어 보고 싶었습니다.

개인이 사용중인거 있으시다면 조금만 정리해서 올려주세요.

 - PHP 소스이고, 버전은 [지원하는 PHP 버전](https://en.wikipedia.org/wiki/PHP)을 사용합니다. 현재는 PHP 5.6 이상입니다. 
 - HTTP REST Api 경우는 반드시 Guzzle HTTP를 사용합니다.
 - 네임스페이스는 `Apikr\회사이름\서비스이름`으로 해주세요.
 
## 제공하는 API 목록

- [SK Planet, T Map](src/SKPlanet/TMap/) : 지도 서비스
- [Paygate, Seyfert](src/Paygate/Seyfert/) : 핀테크 플랫폼
