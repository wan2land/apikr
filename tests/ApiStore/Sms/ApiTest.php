<?php
namespace Apikr\ApiStore\Sms;

use Apikr\Api\Result;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    /** @var array */
    protected $config;
    
    /** @var \Apikr\ApiStore\Sms\Api */
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
        static::assertEquals('OK', $result['result_message']);
        static::assertEquals('200', $result['result_code']);
    }

    public function testSendLms()
    {
        $text = sprintf("%s, 기~인메세지, ", date("n월 j일 H시 i분")) . str_repeat("우아앙", 50);
        $result = $this->sms->send($this->config['testreceiver'], $text, '제목 있음!!!');

        static::assertInstanceOf(Result::class, $result);
        static::assertEquals('OK', $result['result_message']);
        static::assertEquals('200', $result['result_code']);
    }
    
    public function testSenderList()
    {
        $result = $this->sms->senderList();

        static::assertInstanceOf(Result::class, $result);
        foreach ($result->search('numberList[*].sendnumber') as $sender) {
            static::assertRegExp('~^\d+$~', $sender); // only number
        }
    }
}
