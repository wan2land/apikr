<?php
namespace Apikr\CoolSms\Sms;

use Apikr\Api\Result;
use Apikr\CoolSms\Sms\Exception\RequestException;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    /** @var array */
    protected $config;
    
    /** @var \Apikr\CoolSms\Sms\Api */
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
    }

    public function testBalance()
    {
        $result = $this->sms->balance();
        
        static::assertInstanceOf(Result::class, $result);
    }
}
