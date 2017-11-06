<?php
namespace Apikr\SKTelecom\EventDay;

use Apikr\Api\Result;
use Apikr\SKTelecom\EventDay\Exception\EventDayRequestException;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    /** @var array */
    protected $config;
    
    /** @var \Apikr\SKTelecom\EventDay\Api */
    protected $sms;

    public function setUp()
    {
        if (!file_exists(__DIR__ . '/test.config.php')) {
            static::markTestSkipped('test.config.php is null');
        }
        $this->config = require __DIR__ . '/test.config.php';
        $this->sms = new Api(new Client(), new Configuration([
            'apikey' => $this->config['apikey'], 
        ]));
    }
    
    public function testGetDays()
    {
        $result = $this->sms->getDays(Api::TYPE_HOLIDAYS);
        
        static::assertInstanceOf(Result::class, $result);
        foreach ($result->search('results[*]') as $date) {
            static::assertTrue(array_key_exists('year', $date));
            static::assertTrue(array_key_exists('month', $date));
            static::assertTrue(array_key_exists('day', $date));
            static::assertTrue(array_key_exists('type', $date));
            static::assertTrue(array_key_exists('name', $date));
        }
    }

    public function testGetDaysError()
    {
        try {
            $this->sms->getDays("?");
            static::fail();
        } catch (EventDayRequestException $e) {
            static::assertEquals("C001", $e->getResult()->search("code"));
        }
    }
}
