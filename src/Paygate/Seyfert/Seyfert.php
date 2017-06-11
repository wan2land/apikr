<?php
namespace Apikr\Paygate\Seyfert;

use Apikr\Paygate\Seyfert\Models\Transaction;
use Closure;
use Apikr\Paygate\Seyfert\Contracts\MemberAware;
use Apikr\Paygate\Seyfert\Crypt\AesCtr;
use Apikr\Paygate\Seyfert\Models\Account;
use Apikr\Paygate\Seyfert\Models\Bank;
use Apikr\Paygate\Seyfert\Models\Member;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\SimpleCache\CacheInterface;

/*
 * API 어디까지 완성했는가..
 * 
 * [v] 완료
 * [X] 필요없음
 * [?] 어디에 사용하지
 * 
 * > Member Management (멤버 관리)
 *   [v] Assign Virtual Account(입금 가능한 가상계좌 할당) - /v5a/member/assignVirtualAccount/p2p?_method=PUT
 *   > Real bank account (실계좌, 출금용 계좌 관련)
 *     [v] Assign Real Bank Account(출금 가능한 실계좌 할당) - v5a/member/bnk?_method=POST
 *     [v] Check bank Existance (계좌 존재 여부 확인) - /v5/transaction/seyfert/checkbankexistence?_method=POST
 *     [X] Continue ARS trigger
 *     [v] Verify Account Owner Name(은행 계좌주 이름 검증) - /v5/transaction/seyfert/checkbankname?_method=POST
 *     [ ] Inquire Account Owner Name(은행 계좌주 이름 조회) - /v5/transaction/seyfert/inquireAccountOwnerName?_method=POST
 *     [v] Verify Account Owner(은행 계좌주 권한 검증) - /v5/transaction/seyfert/checkbankcode?_method=POST
 *   [v] Create Member(멤버 생성) - /v5a/member/createMember?_method=POST
 *   [ ] Create Member Information - /v5a/member/createMemInfo?_method=POST
 *   [X] Create Member with Bank Account(은행 계좌를 통한 멤버 생성) - /v5a/member/createMember?_method=POST
 *   [v] Create Member with Email(이메일을 통한 멤버 생성) - /v5a/member/createMember?_method=POST
 *   [v] Create Member with Mobile Phone (휴대폰 번호를 KEY값으로 하는 멤버의 생성) - /v5a/member/createMember?_method=POST
 *   [ ] Update All Information (멤버 정보의 수정) - /v5a/member/allInfo?_method=PUT
 *   [ ] Verify Email(이메일 검증) - /v5a/member/verify/email?_method=POST
 *   [ ] Create Member with Merchant's unique key (Merchant's unique key를 KEY값으로 하는 멤버의 생성) - /v5a/member/createMember?_method=POST
 * 
 * > Member Inquiry (멤버 조회)
 *   [X] Get Emails
 *   [X] Get Phones
 *   [v] Get Member Detail Information (멤버 상세 정보) - /v5a/member/privateInfo?_method=GET
 *   [v] Member Allinfo - /v5a/member/allInfo?_method=GET
 *   [v] Member Count (멤버 카운트) - /v5a/member/count?_method=GET

 * > Seyfert Transaction (세이퍼트 거래)
 *   [v] Inquire Seyfert Balance(세이퍼트 잔액 조회) - /v5/member/seyfert/inquiry/balance
 *   > Seyfert Pending Transfer (세이퍼트 펜딩 이체)
 *     [v] Seyfert Pending Transfer (세이퍼트 펜딩 이체)
 *     [ ] Seyfert Pending Transfer Release(세이퍼트 펜딩 해제)
 *     [ ] Seyfert Pending Cancel(세이퍼트 펜딩 이체 취소)
 *     [ ] Seyfert Pending with PreAuth (90일 선인증 거래)
 *   [v] Seyfert Withdraw(세이퍼트 출금) - /v5/transaction/seyfert/withdraw?_method=POST
 *   > Seyfert Transfer (세이퍼트 에스크로 이체)
 *     [ ] Seyfert Transfer (세이퍼트 에스크로 이체)
 *     [ ] Escrow Release(에스크로 해제)
 *     [ ] Seyfert Transfer Cancel(세이퍼트 에스크로 이체 취소)
 *   [ ] Seyfert Transfer Reserved
 *   [X] Seyfert Recurring Transfer(세이퍼트 자동 이체)
 *   [X] Seyfert Recurring Transfer Cancel(세이퍼트 자동 이체 취소)
 *   [X] Unlimited Reserved Transfer(무한 예약 이체)
 *   [X] Unlimited Reserved Transfer Cancel(무한 예약 이체 취소)
 *   [?] Send Money
 *   [X] Currency Exchange
 *   [ ] SMS MO(Mobile Originated)
 *   [ ] ARS
 *   [ ] KYC Payin (KYC 충전)
 *   [v] Seyfert List (세이퍼트 입출금 내역 조회)
 *   [ ] Daily Seyfert Balance (세이퍼트)
 *   [ ] Exchange-Send Money
 *   > Seyfert Virtual Account Transfer(세이퍼트 가상계좌 입금 결제 )
 *   > Seyfert Link Push Transaction (세이퍼트 광역 송금)
 *   [X] Member Follow (멤버 팔로우)
 *   [ ] 인증번호 재요청
 *
 * > Transaction Inquiry(거래 조회)
 *   [ ] Transaction Details (거래명세서)
 *   [ ] Transaction List (거래목록)
 * 
 */
class Seyfert
{
    const SEYFERT_DEV_HOST = 'https://stg5.paygate.net'; // 테스트 서버
    const SEYFERT_PROD_HOST = 'https://v5.paygate.net	'; // 실서버

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Apikr\Paygate\Seyfert\Configuration */
    protected $config;
    
    /** @var \Psr\SimpleCache\CacheInterface */
    protected $cache;

    public function __construct(Client $client, Configuration $config, CacheInterface $cache = null)
    {
        $this->client = $client;
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $phone
     * @return \Apikr\Paygate\Seyfert\Models\Member
     */
    public function createMember($name, $email = null, $phone = null)
    {
        if ($email && $phone) {
            $form = [
                'emailAddrss' => $email,
                'emailTp' => 'PERSONAL',
                'fullname' => $name,
                'nmLangCd' => 'ko',
                'phoneCntryCd' => 'KOR',
                'phoneNo' => $phone,
                'phoneTp' => 'MOBILE',
            ];
        } elseif ($email) {
            $form = [
                'keyTp' => 'EMAIL',
                'emailAddrss' => $email,
                'emailTp' => 'PERSONAL',
            ];
        } elseif ($phone) {
            $form = [
                'keyTp' => 'PHONE',
                'phoneNo' => $phone,
                'phoneCntryCd' => 'KOR',
            ];
        } else {
            throw new \RuntimeException("email, phone 둘 중 반드시 한개 이상은 입력하셔야 합니다.");
        }
        $result = $this->request("POST", '/v5a/member/createMember', $form);
        return new Member($result['data']['memGuid'], $name, $email, $phone);
    }
    
    /**
     * @param string $identifier
     * @return \Apikr\Paygate\Seyfert\Models\Member
     */
    public function getMember($identifier)
    {
        try {
            $result = $this->request("GET", '/v5a/member/privateInfo', [
                'dstMemGuid' => $identifier,
            ]);
            return new Member(
                $identifier,
                isset($result['data']['result']['namesList'][0]['fullname'])
                    ? $result['data']['result']['namesList'][0]['fullname'] : null,
                isset($result['data']['result']['emailsList'][0]['emailAddrss'])
                    ? $result['data']['result']['emailsList'][0]['emailAddrss'] : null,
                isset($result['data']['result']['phonesList'][0]['phoneNo'])
                    ? $result['data']['result']['phonesList'][0]['phoneNo'] : null
            );
        } catch (ClientException $e) {
            return null;
        }
    }
    
    /**
     * @return int
     */
    public function countMembers()
    {
        $result = $this->request("GET", '/v5a/member/count');
        return isset($result['data']['result']['totalCount']) ? $result['data']['result']['totalCount'] : 0;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return \Apikr\Paygate\Seyfert\Models\Member[]
     */
    public function retrieveMembers($page = 1, $limit = 10)
    {
        try {
            $result = $this->request("GET", '/v5a/member/allInfo', [
                'page' => $page,
                'limit' => $limit,
            ]);
            return array_map(function ($member) {
                return new Member(
                    $member['guid'],
                    $member['fullname'],
                    $member['emailAddrss'],
                    $member['phoneNo']
                );
            }, isset($result['data']['resultList']) ? $result['data']['resultList'] : []);
        } catch (ClientException $e) {
            $result = $this->getResultFromClientException($e);
            if (isset($result['data']['cdKey']) && $result['data']['cdKey'] === 'NO_MEMBER') {
                return [];
            }
            throw $e;
        }
    }

    /**
     * 세이퍼드 충전용 가상 계좌.
     * @param string $purpose
     * @return \Apikr\Paygate\Seyfert\Models\Bank[]
     */
    public function getBanksForVirtualAccount($purpose = 'p2p')
    {
        // $purpose = "p2p"; // p2p, payment, remit, bitcoin
        return $this->caching('seyfert.virtual_banks', function () use ($purpose) {
            $result = $this->request('GET', "/v5/code/listOf/availableVABanks/{$purpose}/charge");
            return array_map(function ($item) {
                return new Bank($item['cdNm'], $item['bankCode']);
            }, $result['data']);
        });
    }

    /**
     * 세이퍼드 환불용 가상계좌
     * @return \Apikr\Paygate\Seyfert\Models\Bank[]
     */
    public function getBanksForRealAccount()
    {
        return $this->caching('seyfert.real_banks', function () {
            $result = $this->request('GET', "/v5/code/listOf/banks");
            return array_map(function ($item) {
                return new Bank($item['cdNm'], $item['cdKey']);
            }, $result['data']);
        });
    }

    /**
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @param \Apikr\Paygate\Seyfert\Models\Account $account
     * @return bool
     */
    public function assignRealAccount(MemberAware $member, Account $account)
    {
        if (!$this->assignRealAccountOnly($member, $account)) return false;
        if (!$this->verifyRealAccountName($member)) return false;
        return $this->verifyAccountOwner($member);
    }

    /**
     * @internal
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @param \Apikr\Paygate\Seyfert\Models\Account $account
     * @return bool
     */
    public function assignRealAccountOnly(MemberAware $member, Account $account)
    {
        $result = $this->request("POST", "/v5a/member/bnk", [
            'dstMemGuid' => $member->getSeyfertMemberIdentifier(),
            'accntNo' => $account->getAccountNumber(),
            'bnkCd' => $account->getBank()->getCode(),
            'cntryCd' => 'KOR',
        ]);
        return $result['status'] === 'SUCCESS';
    }

    /**
     * @internal
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @return bool
     */
    public function verifyRealAccountName(MemberAware $member)
    {
        $result = $this->request("POST", "/v5/transaction/seyfert/checkbankname", [
            'dstMemGuid' => $member->getSeyfertMemberIdentifier(),
        ]);
        if ($result['data']['status'] === 'CHECK_BNK_NM_FINISHED') {
            return true;
        }
        return false;
    }

    /**
     * @internal 
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @return bool
     */
    public function verifyAccountOwner(MemberAware $member)
    {
        $result = $this->request("POST", "/v5/transaction/seyfert/checkbankcode", [
            'dstMemGuid' => $member->getSeyfertMemberIdentifier(),
        ]);
        if ($result['data']['status'] === 'CHECK_BNK_CD_FINISHED') { // 이미 검증완료 된 케이스
            return true;
        }
        if ($result['data']['status'] === 'VRFY_BNK_CD_SENDING_1WON') { // 1원 보냈어요!
            return true;
        }
        return false;
    }

    /**
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @return bool
     */
    public function hasBankAccount(MemberAware $member)
    {
        $result = $this->request("POST", '/v5/transaction/seyfert/checkbankexistence', [
            'dstMemGuid' => $member->getSeyfertMemberIdentifier(),
        ]);
        return $result['data']['status'] === 'CHECK_BNK_EXISTANCE_CHECKED'; // 실패시 CHECK_BNK_EXISTANCE_FAILED        
    }

    /**
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @param int $amount
     * @return bool
     */
    public function withdraw(MemberAware $member, $amount)
    {
        $result = $this->request("POST", '/v5/transaction/seyfert/withdraw', [
            'dstMemGuid' => $member->getSeyfertMemberIdentifier(),
            'amount' => $amount,
            'crrncy' => 'KRW',
        ]);
        return $result['data']['status'] === 'SFRT_WITHDRAW_REQ_TRYING';
    }

    /**
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $from
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $to
     * @param int $amount
     * @return bool
     */
    public function transfer(MemberAware $from, MemberAware $to, $amount)
    {
        $result = $this->request("POST", '/v5/transaction/seyfert/transferPending', [
            'srcMemGuid' => $from->getSeyfertMemberIdentifier(),
            'dstMemGuid' => $to->getSeyfertMemberIdentifier(),
            'amount' => $amount,
            'crrncy' => 'KRW',
        ]);
        return $result['data']['status'] === 'SFRT_TRNSFR_PND_TRYING';
    }

    /**
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @param \Apikr\Paygate\Seyfert\Models\Bank $bank
     * @return \Apikr\Paygate\Seyfert\Models\Account
     */
    public function createVirtualAccount(MemberAware $member, Bank $bank)
    {
        $cacheKey = 'seyfert.va.' . $bank->getCode() . '.' . $member->getSeyfertMemberIdentifier();
        if ($this->cache && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $result = $this->request("PUT", '/v5a/member/assignVirtualAccount/p2p', [
            'dstMemGuid' => $member->getSeyfertMemberIdentifier(),
            'bnkCd' => $bank->getCode(),
        ]);
        $account = new Account($bank, $result['data']['accntNo']);
        if ($this->cache) {
            $ttl = floor($result['data']['info']['expireDt'] / 1000) - time() - 10 * 60;
            $this->cache->set($cacheKey, $account, (int) $ttl);
        }
        return $account;
    }

    /**
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @return int
     */
    public function getBalanceMoney(MemberAware $member)
    {
        $result = $this->request("GET", '/v5/member/seyfert/inquiry/balance', [
            'dstMemGuid' => $member->getSeyfertMemberIdentifier(),
            'crrncy' => 'KRW',
        ]);
        return (int)(isset($result['data']['moneyPair']['amount']) ? $result['data']['moneyPair']['amount'] : 0);
    }

    /**
     * @param \Apikr\Paygate\Seyfert\Contracts\MemberAware $member
     * @param int $page
     * @param int $limit
     * @return array|\Apikr\Paygate\Seyfert\Models\Transaction[]
     */
    public function retrieveTransactions(MemberAware $member, $page = 1, $limit = 10)
    {
        $result = $this->request("GET", '/v5a/admin/seyfertList', [
            'dstMemGuid' => $member->getSeyfertMemberIdentifier(),
            'page' => $page,
            'limit' => $limit,
        ]);
        return array_map(function ($trans) {
            return new Transaction(
                $trans['tid'],
                $trans['trnsctnSt'],
                $trans['actcAmt'],
                $trans['actcRsltAmt'],
                floor($trans['createDt'] / 1000)
            );
        }, isset($result['data']['list']) ? $result['data']['list'] : []);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $form
     * @return array
     */
    protected function request($method, $uri, array $form = [])
    {
        $form = $form + [
                '_method' => $method,
                'reqMemGuid' => $this->config->getGuid(),
            ];

        $encReq = AesCtr::encrypt('&' . http_build_query($form), $this->config->getKeyp());
        $response = $this->client->request('GET', static::SEYFERT_DEV_HOST . $uri, [
            'query' => [
                '_method' => $method,
                'reqMemGuid' => $this->config->getGuid(),
                'encReq' => $encReq,
            ],
        ]);

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * @param \GuzzleHttp\Exception\ClientException $e
     * @return array
     */
    protected function getResultFromClientException(ClientException $e)
    {
        return json_decode($e->getResponse()->getBody()->__toString(), true);
    }

    /**
     * @param string $cacheKey
     * @param \Closure $resultHandler
     * @param int $ttl
     * @return mixed
     */
    protected function caching($cacheKey, Closure $resultHandler, $ttl = 1800)
    {
        if ($this->cache && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $result = $resultHandler();
        if ($this->cache) {
            $this->cache->set($cacheKey, $result, $ttl);
        }
        return $result;
    }
}
