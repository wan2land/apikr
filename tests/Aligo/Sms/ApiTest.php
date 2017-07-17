<?php
namespace Apikr\Aligo\Sms;

use Apikr\Api\Result;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    /** @var array */
    protected $config;
    
    /** @var \Apikr\Aligo\Sms\Api */
    protected $sms;

    public function setUp()
    {
        if (!file_exists(__DIR__ . '/test.config.php')) {
            static::markTestSkipped('test.config.php is null');
        }
        $this->config = require __DIR__ . '/test.config.php';
        $this->sms = new Api(new Client(), new Configuration($this->config));
    }
    
    public function testSendSms()
    {
        $text = sprintf("%s, 짧은메세지", date("n월 j일 H시 i분"));
        $result = $this->sms->send($this->config['testreceiver'], $text);

        static::assertInstanceOf(Result::class, $result);
        static::assertEquals('SMS', $result['msg_type']);
    }

    public function testSendLms()
    {
        $text = sprintf("%s, 기~인메세지, ", date("n월 j일 H시 i분")) . str_repeat("우아앙", 50);
        $result = $this->sms->send($this->config['testreceiver'], $text, '제목');

        static::assertInstanceOf(Result::class, $result);
        static::assertEquals('LMS', $result['msg_type']);
    }
    
    public function testRemain()
    {
        $result = $this->sms->remain();
        
        static::assertInstanceOf(Result::class, $result);

        static::assertTrue(is_int($this->sms->remainSms()));
        static::assertTrue(is_int($this->sms->remainLms()));
        static::assertTrue(is_int($this->sms->remainMms()));
    }
}
