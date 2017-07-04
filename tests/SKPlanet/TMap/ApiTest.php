<?php
namespace Apikr\SKPlanet\TMap;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    /** @var \Apikr\SKPlanet\TMap\Api $tmap*/
    protected $tmap;

    public function setUp()
    {
        $this->tmap = new Api(new Client(), new Configuration([
            'apiKey' => 'c2e0150d-a4ba-391e-89ad-d8f74f167432', // for testing api key.
        ]));
    }
    
    public function testGeocodingFullAddress()
    {
        $result = $this->tmap->geocodingFullAddress("서울 중구 명동길 14 7층");

        static::assertEquals('37.563411', $result->search('coordinateInfo.coordinate[0].newLat'));
        static::assertEquals('126.982886', $result->search('coordinateInfo.coordinate[0].newLon'));
    }
    
    public function testReverseGeocoding()
    {
        $result = $this->tmap->reverseGeocoding(new LatLng('37.563411', '126.982886'));
        
        static::assertEquals('서울특별시 중구 명동길 14 Noon Square', $result->search('addressInfo.fullAddress'));
    }

    public function testConvertAddress()
    {
        $result = $this->tmap->convertAddress("충남 천안시 성거읍 신월리 370-6");

        static::assertEquals('충남', $result->search('ConvertAdd.upperDistName'));
        static::assertEquals('천안시 서북구', $result->search('ConvertAdd.middleDistName'));
        static::assertEquals('성거읍', $result->search('ConvertAdd.legalLowerDistName'));
        static::assertEquals('봉주로', $result->search('ConvertAdd.newAddressList.newAddress[0].roadName'));
        static::assertEquals('97', $result->search('ConvertAdd.newAddressList.newAddress[0].bldNo1'));
        static::assertEquals('0', $result->search('ConvertAdd.newAddressList.newAddress[0].bldNo2'));
    }
}
