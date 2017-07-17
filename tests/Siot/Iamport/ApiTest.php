<?php
namespace Apikr\Siot\Iamport;

use Apikr\Api\Result;
use Apikr\Siot\Iamport\VO\CardExpiry;
use Apikr\Siot\Iamport\VO\CardNumber;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    /** @var array */
    protected $config = [];

    /** @var \Apikr\Siot\Iamport\Api */
    protected $api;

    public function setUp()
    {
        if (file_exists(__DIR__ . '/test.config.php')) {
            $this->config = require __DIR__ . '/test.config.php';
        }
        $this->api = new Api(new Client(), new Configuration([
            'impKey' => 'imp_apikey',
            'impSecret' => 'ekKoeW8RyKuT0zgaZsUtXXTLQ4AhPFW3ZGseDA6bkA5lamv9OqDMnxyeB9wqOsuO9W3Mx9YSJ4dTqJ3f',
        ]));
    }
    
    public function testCreateToken()
    {
        $token = $this->api->createToken();
        static::assertRegExp('/^[a-f0-9]{40}$/', $token);
    }

    public function testCreateSubscribeCustomer()
    {
        if (!isset($this->config['cardnumber'])) {
            static::markTestSkipped('test.config.php is null');
            return;
        }
        $result = $this->api->createSubscribeCustomer(
            'apikr4028',
            new CardNumber($this->config['cardnumber']),
            new CardExpiry($this->config['expiry']),
            $this->config['birthday'],
            $this->config['pwd2digit']
        );
        static::assertInstanceOf(Result::class, $result);
    }
}
