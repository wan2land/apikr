<?php

namespace Apikr\Paygate\Seyfert;

use Apikr\Paygate\Seyfert\Models\Bank;
use Apikr\Paygate\Seyfert\Models\Member;
use Apikr\Paygate\Seyfert\Models\Transaction;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Simple\ApcuCache;

class SeyfertTest extends TestCase 
{
    /** @var \Apikr\Paygate\Seyfert\Seyfert */
    protected $seyfert;
    
    /** @var array */
    protected $dataset;
    
    public function setUp()
    {
        if (!file_exists(__DIR__ . '/dataset.php')) {
            static::markTestSkipped('test dataset is null');
        }
        $this->dataset = require __DIR__ . '/dataset.php';
        $this->seyfert = new Seyfert(new Client(), new Configuration($this->dataset), new ApcuCache());
    }

    public function testCreateMember()
    {
        $member = $this->seyfert->createMember('테스트', 'wan2land@gmail.com', '01049243213');
        static::assertInstanceOf(Member::class, $member);
    }
    
    public function testGetBanksForVirtualAccount()
    {
        $banks = $this->seyfert->getBanksForVirtualAccount();
        foreach ($banks as $bank) {
            static::assertInstanceOf(Bank::class, $bank);
        }
    }

    public function testGetBanksForRealAccount()
    {
        $banks = $this->seyfert->getBanksForRealAccount();
        foreach ($banks as $bank) {
            static::assertInstanceOf(Bank::class, $bank);
        }
    }

    public function testCreateVirtualAccount()
    {
        $member = new Member($this->dataset['memguid']);
        $banks = $this->seyfert->getBanksForVirtualAccount();

        $account = $this->seyfert->createVirtualAccount($member, $banks[0]);

        static::assertEquals($account->getAccountNumber(), $account->getAccountNumber());
    }
    
    public function testHasRealAccount()
    {
        $member = new Member($this->dataset['memguid']);

        static::assertTrue($this->seyfert->hasBankAccount($member));
    }
    
    public function testAssignRealAccount()
    {
//        $member = new Member($this->dataset['memguid']);
//        /** @var \Apikr\Paygate\Seyfert\Models\Bank $bank */
//        $bank = $this->seyfert->getBanksForRealAccount()[0];
//        if (!$bank) {
//            static::fail('!!');
//        }
//        $result = $this->seyfert->assignRealAccount($member, new Account($bank, 'some real account'));
//        static::assertTrue($result);
    }
    
    public function testGetBalanceMoney() 
    {
        $member = new Member($this->dataset['memguid']);
        static::assertTrue(is_int($this->seyfert->getBalanceMoney($member)));
    }

    public function testRetrieveTransactions()
    {
        $member = new Member($this->dataset['memguid']);
        $trans = $this->seyfert->retrieveTransactions($member);
        
        foreach ($trans as $tran) {
            static::assertInstanceOf(Transaction::class, $tran);
        }
    }
}
