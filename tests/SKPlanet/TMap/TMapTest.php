<?php
namespace Apikr\SKPlanet\TMap;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
    /** @var \Apikr\SKPlanet\TMap\TMap $tmap*/
    protected $tmap;

    public function setUp()
    {
        $this->tmap = new TMap(new Client(), new Configuration([
            'apiKey' => 'c2e0150d-a4ba-391e-89ad-d8f74f167432',
        ]));
    }

    public function testGetDistance()
    {
        $actual = $this->tmap->getDistance(
            new LatLng('37.55510690', '126.97069110'), // 서울역
            new LatLng('35.87143540', '128.60144500'), // 대구
            [
                'searchOption' => Configuration::OPTION_SHORTEST,
            ]
        );

        static::assertTrue(is_int($actual));

        // 오차범위 10%
        static::assertGreaterThan(277922 * 0.9, $actual);
        static::assertLessThan(277922 * 1.9, $actual);
    }

    public function testCalculateMany()
    {
        $actual = $this->tmap->getDistances([
            new LatLng('37.55510690', '126.97069110'), // 서울역
            new LatLng('37.54053970', '127.07185000'), // 건국대병원
            new LatLng('36.32598610', '127.42067930'), // 대전
            new LatLng('35.87143540', '128.60144500'), // 대구
        ], [
            'searchOption' => Configuration::OPTION_SHORTEST,
        ]);

        static::assertTrue(is_array($actual));
        static::assertEquals(3, count($actual));

        static::assertGreaterThan(11767 * 0.9, $actual[0]);
        static::assertLessThan(11767 * 1.9, $actual[0]);
        
        static::assertGreaterThan(157973 * 0.9, $actual[1]);
        static::assertLessThan(157973 * 1.9, $actual[1]);

        static::assertGreaterThan(137077 * 0.9, $actual[2]);
        static::assertLessThan(137077 * 1.9, $actual[2]);
    }
}
