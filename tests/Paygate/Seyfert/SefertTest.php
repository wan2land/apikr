<?php

namespace Apikr\Paygate\Seyfert;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase 
{
    /** @var \Apikr\Paygate\Seyfert\Api */
    protected $seyfert;
    
    /** @var array */
    protected $dataset;
    
    public function setUp()
    {
        if (!file_exists(__DIR__ . '/dataset.php')) {
            static::markTestSkipped('test dataset is null');
        }
        $this->dataset = require __DIR__ . '/dataset.php';
        $this->seyfert = new Api(new Client(), new Configuration($this->dataset));
    }

    public function testCreateMember()
    {
        $result = $this->seyfert->createMember('테스트', 'wan2land@gmail.com', '01049243213');
        // $guid = $result->search('data.memGuid');
        static::assertInstanceOf(Result::class, $result);
    }
    
    public function testGetBanksForVirtualAccount()
    {
        $banks = $this->seyfert->getBanksForVirtualAccount();
    }

    public function testGetBanksForRealAccount()
    {
        $banks = $this->seyfert->getBanksForRealAccount();
    }

    public function testCreateVirtualAccount()
    {
        $banks = $this->seyfert->getBanksForVirtualAccount();

        $account = $this->seyfert->createVirtualAccount($this->dataset['memguid'], $banks[0]);

        static::assertEquals($account->getAccountNumber(), $account->getAccountNumber());
    }
    
    public function testHasRealAccount()
    {

        static::assertTrue($this->seyfert->hasBankAccount($this->dataset['memguid']));
    }
    
    public function testAssignRealAccount()
    {
////        /** @var \Apikr\Paygate\Seyfert\Models\Bank $bank */
//        $bank = $this->seyfert->getBanksForRealAccount()[0];
//        if (!$bank) {
//            static::fail('!!');
//        }
//        $result = $this->seyfert->assignRealAccount($this->dataset['memguid'], new Account($bank, 'some real account'));
//        static::assertTrue($result);
    }
    
    public function testGetBalanceMoney() 
    {
        static::assertTrue(is_int($this->seyfert->getBalanceMoney($this->dataset['memguid'])));
    }

    public function testRetrieveTransactions()
    {
        $trans = $this->seyfert->retrieveTransactions($this->dataset['memguid']);
        
//        foreach ($trans as $tran) {
//            static::assertInstanceOf(Transaction::class, $tran);
//        }
    }

    /**
     * d
     */
    public function testTransfer()
    {
//        $fr = new Member('GcyhPSbKhZBJLdF45HFDs4');
//        $to = new Member('HsXUbJvEbrc485SB43odeo');
//        $result = $this->seyfert->transferPending($fr, $to, 500);

        $result = $this->seyfert->cancelPending('tai0h7');
        print_r($result->getTransactionId());
        print_r($result);
    }
}
