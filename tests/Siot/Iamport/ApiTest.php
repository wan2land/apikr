<?php
namespace Apikr\Siot\Iamport;

use Apikr\Api\Result;
use Apikr\Siot\Iamport\Contracts\TransactionInterface;
use Apikr\Siot\Iamport\Exception\IamportRequestException;
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

    public function testCRUDSubscribeCustomer()
    {
        if (!isset($this->config['cardnumber'])) {
            static::markTestSkipped('test.config.php is null');
            return;
        }

        $result = $this->api->saveUnauthPaymentCustomer(
            'apikr4028',
            new CardNumber($this->config['cardnumber']),
            new CardExpiry($this->config['expiry']),
            $this->config['birthday'],
            $this->config['pwd2digit']
        );

        static::assertInstanceOf(Result::class, $result);
        static::assertEquals('apikr4028', $result->search('response.customer_uid'));
        $cardName = $result->search('response.card_name'); 
        
        $result = $this->api->getUnauthPaymentCustomer('apikr4028');
        static::assertInstanceOf(Result::class, $result);
        static::assertEquals('apikr4028', $result->search('response.customer_uid'));
        static::assertEquals($cardName, $result->search('response.card_name'));

        $result = $this->api->removeUnauthPaymentCustomer('apikr4028');
        static::assertInstanceOf(Result::class, $result);
        static::assertEquals('apikr4028', $result->search('response.customer_uid'));
        static::assertEquals($cardName, $result->search('response.card_name'));

        try {
            $this->api->getUnauthPaymentCustomer('apikr4028');
            static::fail();
        } catch (IamportRequestException $e) {
            static::assertEquals('요청하신 customer_uid(apikr4028)로 등록된 정보를 찾을 수 없습니다.', $e->getMessage());
        }
    }

    public function testMakeUnauthPaying()
    {
        if (!isset($this->config['cardnumber'])) {
            static::markTestSkipped('test.config.php is null');
            return;
        }

        $this->api->saveUnauthPaymentCustomer(
            'apikr4028',
            new CardNumber($this->config['cardnumber']),
            new CardExpiry($this->config['expiry']),
            $this->config['birthday'],
            $this->config['pwd2digit']
        );

        $merchantUid = uniqid('apikr_merchant_');
        $result = $this->api->makeUnauthPayment('apikr4028', $merchantUid, 1000, '그냥결제');
        static::assertInstanceOf(TransactionResult::class, $result);
        static::assertRegExp('~^imps_\d+$~', $result->getImpUid());

        usleep(500000);
        
        $result = $this->api->cancelPayment($result);
        static::assertInstanceOf(TransactionResult::class, $result);
        static::assertRegExp('~^imps_\d+$~', $result->getImpUid());
        static::assertEquals(1000, $result->search('response.cancel_amount'));
        static::assertEquals('그냥결제', $result->search('response.name'));
    }
}
