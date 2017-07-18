<?php
namespace Apikr\Siot\Iamport;

use Apikr\Api\Result;
use Apikr\Siot\Iamport\Contracts\TransactionInterface;
use Apikr\Siot\Iamport\Exception\IamportRequestException;
use Apikr\Siot\Iamport\VO\CardExpiry;
use Apikr\Siot\Iamport\VO\CardNumber;
use Apikr\Siot\Iamport\VO\Merchant;
use Apikr\Siot\Iamport\VO\MerchantIdentifier;
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

    public function testMakeUnauthPayment()
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
        $result = $this->api->makeUnauthPayment('apikr4028', new Merchant($merchantUid, 1000, '그냥결제'));
        static::assertInstanceOf(TransactionResult::class, $result);
        static::assertRegExp('~^imps_\d+$~', $result->getImpUid());

        usleep(500000);
        
        $result = $this->api->cancelPayment($result);
        static::assertInstanceOf(TransactionResult::class, $result);
        static::assertRegExp('~^imps_\d+$~', $result->getImpUid());
        static::assertEquals(1000, $result->search('response.cancel_amount'));
        static::assertEquals('그냥결제', $result->search('response.name'));
    }
    
    public function testScheduleUnauthPayment()
    {
        if (!isset($this->config['cardnumber'])) {
            static::markTestSkipped('test.config.php is null');
            return;
        }

        $uid1 = uniqid('apikr_merchant_');
        $uid2 = uniqid('apikr_merchant_');
        $uid3 = uniqid('apikr_merchant_');

        $result = $this->api->scheduleUnauthPayment(
            'apikr4028',
            [
                new Merchant($uid1, 500, '예약테스트1'),
                new Merchant($uid2, 600, '예약테스트2'),
                new Merchant($uid3, 1700, '예약테스트3'),
            ]
        );
        
        static::assertInstanceOf(Result::class, $result);
        static::assertEquals(['apikr4028', 'apikr4028', 'apikr4028'], $result->search('response[].customer_uid'));
        static::assertEquals([$uid1, $uid2, $uid3], $result->search('response[].merchant_uid'));
        static::assertEquals([500, 600, 1700], $result->search('response[].amount'));
        static::assertEquals(['예약테스트1', '예약테스트2', '예약테스트3'], $result->search('response[].name'));
        
        $result = $this->api->unscheduleUnauthPayment('apikr4028', [
            new MerchantIdentifier($uid1),
            new MerchantIdentifier($uid2),
            new MerchantIdentifier($uid3),
        ]);

        static::assertInstanceOf(Result::class, $result);
        static::assertEquals(['apikr4028', 'apikr4028', 'apikr4028'], $result->search('response[].customer_uid'));
        static::assertEquals([$uid1, $uid2, $uid3], $result->search('response[].merchant_uid'));
        static::assertEquals([500, 600, 1700], $result->search('response[].amount'));
    }
    
    public function testRetrieveUnauthCustomer()
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

        $result = $this->api->retrieveUnauthPayments('apikr4028', 1);
        static::assertInstanceOf(Result::class, $result);
    }
}
