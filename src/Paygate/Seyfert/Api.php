<?php
namespace Apikr\Paygate\Seyfert;

use Apikr\Paygate\Seyfert\Crypt\AesCtr;
use Apikr\Paygate\Seyfert\Exception\SeyfertException;
use Apikr\Paygate\Seyfert\Models\Bank;
use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
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
 *   [v] Update All Information (멤버 정보의 수정) - /v5a/member/allInfo?_method=PUT
 *   [ ] Verify Email(이메일 검증) - /v5a/member/verify/email?_method=POST
 *   [ ] Create Member with Merchant's unique key (Merchant's unique key를 KEY값으로 하는 멤버의 생성) - /v5a/member/createMember?_method=POST
 * 
 * > Member Inquiry (멤버 조회)
 *   [X] Get Emails
 *   [X] Get Phones
 *   [v] Get Member Detail Information (멤버 상세 정보) - /v5a/member/privateInfo?_method=GET
 *   [ ] Member Allinfo - /v5a/member/allInfo?_method=GET
 *   [v] Member Count (멤버 카운트) - /v5a/member/count?_method=GET

 * > Seyfert Transaction (세이퍼트 거래)
 *   [v] Inquire Seyfert Balance(세이퍼트 잔액 조회) - /v5/member/seyfert/inquiry/balance
 *   > Seyfert Pending Transfer (세이퍼트 펜딩 이체)
 *     [v] Seyfert Pending Transfer (세이퍼트 펜딩 이체)
 *     [v] Seyfert Pending Transfer Release(세이퍼트 펜딩 해제)
 *     [v] Seyfert Pending Cancel(세이퍼트 펜딩 이체 취소)
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
class Api
{
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
     * @return \Apikr\Paygate\Seyfert\Result
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
            throw new InvalidArgumentException("email, phone 둘 중 반드시 한개 이상은 입력하셔야 합니다.");
        }
        $result = $this->request("POST", '/v5a/member/createMember', $form);
        return new Result($result);
    }

    /**
     * @param string $guid
     * @param array $attributes
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function updateMember($guid, array $attributes = [])
    {
        $form = [];
        if (array_key_exists('name', $attributes)) {
            $form['fullname'] = $attributes['name'];
            $form['nmLangCd'] = 'ko';
        }
        if (array_key_exists('email', $attributes)) {
            $form['emailAddress'] = $attributes['email'];
            $form['emailTp'] = 'PERSONAL';
        }
        if (array_key_exists('phone', $attributes)) {
            $form['phoneNo'] = $attributes['phone'];
            $form['phoneCntryCd'] = 'KOR';
        }
        if (count($form)) {
            $form['dstMemGuid'] = $guid;
            $result = $this->request("PUT", '/v5a/member/allInfo', $form);
            return new Result($result);
        }
        throw new InvalidArgumentException('attributes에는 적어도 name, email, phone 중 하나는 있어야 합니다.');
    }
    
    /**
     * @param string $guid
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function getMember($guid)
    {
        $result = $this->request("GET", '/v5a/member/privateInfo', [
            'dstMemGuid' => $guid,
        ]);
        return new Result($result);
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
     * 세이퍼드 충전용 가상 계좌.
     * 
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
     * 
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
     * @param string $guid
     * @param string $bankCode
     * @param string $accountNumber
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function assignRealAccount($guid, $bankCode, $accountNumber)
    {
        $this->assignRealAccountOnly($guid, $bankCode, $accountNumber);
        $this->verifyRealAccountName($guid);
        return $this->verifyAccountOwner($guid);
    }

    /**
     * @internal
     * @param string $guid
     * @param string $bankCode
     * @param string $accountNumber
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function assignRealAccountOnly($guid, $bankCode, $accountNumber)
    {
        $result = $this->request("POST", "/v5a/member/bnk", [
            'dstMemGuid' => $guid,
            'bnkCd' => $bankCode,
            'accntNo' => $accountNumber,
            'cntryCd' => 'KOR',
        ]);
        return new Result($result);
    }

    /**
     * @internal
     * @param string $guid
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function verifyRealAccountName($guid)
    {
        $result = $this->request("POST", "/v5/transaction/seyfert/checkbankname", [
            'dstMemGuid' => $guid,
        ]);
        if ($result['data']['status'] === 'CHECK_BNK_NM_FINISHED') {
            return new Result($result);
        } elseif ($result['data']['status'] === 'CHECK_BNK_NM_DENIED') {
            throw new SeyfertException(
                "예금주명 조회에 실패하였습니다.",
                SeyfertException::CODE_CHECK_BNK_NM_DENIED,
                $result
            );
        } elseif ($result['data']['status'] === 'CHECK_BNK_NM_NEED_REVIEW') {
            throw new SeyfertException(
                "예금주가 일치하지 않거나 예금주를 조회할 수 없습니다.",
                SeyfertException::CODE_CHECK_BNK_NM_NEED_REVIEW,
                $result
            );
        } else {
            throw new SeyfertException(
                "예금주명 조회 도중 에러({$result['data']['status']})가 발생하였습니다.",
                SeyfertException::CODE_CHECK_BNK_NM_UNKNOWN,
                $result
            );
        }
    }

    /**
     * @internal 
     * @param string $guid
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function verifyAccountOwner($guid)
    {
        $result = $this->request("POST", "/v5/transaction/seyfert/checkbankcode", [
            'dstMemGuid' => $guid,
        ]);
        // 1원 보냈어요! & 이미 검증완료 된 케이스
        if ($result['data']['status'] === 'VRFY_BNK_CD_SENDING_1WON' 
         || $result['data']['status'] === 'CHECK_BNK_CD_FINISHED') { // 이미 검증완료 된 케이스
            return new Result($result);
        }
        throw new SeyfertException(
            "계좌 조회 도중 에러({$result['data']['status']})가 발생하였습니다.",
            SeyfertException::CODE_CHECK_BNK_CD_UNKNOWN,
            $result
        );
    }

    /**
     * @param string $guid
     * @return bool
     */
    public function hasBankAccount($guid)
    {
        $result = $this->request("POST", '/v5/transaction/seyfert/checkbankexistence', [
            'dstMemGuid' => $guid,
        ]);
        return $result['data']['status'] === 'CHECK_BNK_EXISTANCE_CHECKED'; // 실패시 CHECK_BNK_EXISTANCE_FAILED        
    }

    /**
     * @param string $guid
     * @param int $amount
     * @return \Apikr\Paygate\Seyfert\TransactionResult
     */
    public function withdraw($guid, $amount)
    {
        $result = $this->request("POST", '/v5/transaction/seyfert/withdraw', [
            'dstMemGuid' => $guid,
            'amount' => $amount,
            'crrncy' => 'KRW',
        ]);
        if ($result['data']['status'] === 'SFRT_WITHDRAW_REQ_TRYING') {
            return new TransactionResult($result);
        }
        throw new SeyfertException(
            "세피어트 출금 도중 에러({$result['data']['status']})가 발생하였습니다.",
            SeyfertException::CODE_SFRT_WITHDRAW_UNKNOWN,
            $result
        );
    }

    /**
     * @param string $fromGuid
     * @param string $toGuid
     * @param int $amount
     * @return \Apikr\Paygate\Seyfert\TransactionResult
     */
    public function transferPending($fromGuid, $toGuid, $amount)
    {
        $result = $this->request("POST", '/v5/transaction/seyfert/transferPending', [
            'srcMemGuid' => $fromGuid,
            'dstMemGuid' => $toGuid,
            'amount' => $amount,
            'crrncy' => 'KRW',
        ]);
        if ($result['data']['status'] === 'SFRT_TRNSFR_PND_TRYING' || $result['data']['status'] === 'SFRT_TRNSFR_PND_AGRREED') {
            return new TransactionResult($result);
        }
        throw new SeyfertException(
            "전송 도중 알수 없는 에러({$result['data']['status']})가 발생하였습니다.",
            SeyfertException::CODE_SFRT_TRNSFR_PND_UNKNOWN,
            $result
        );
    }

    /**
     * @param string $tid
     * @return \Apikr\Paygate\Seyfert\TransactionResult
     */
    public function releasePending($tid)
    {
        $result = $this->request("POST", '/v5/transaction/pending/release', [
            'parentTid' => $tid,
        ]);
        if ($result['data']['status'] === 'SFRT_TRNSFR_PND_RELEASED') {
            return new TransactionResult($result);
        }
        throw new SeyfertException(
            "펜딩 헤제 도중 알수 없는 에러({$result['data']['status']})가 발생하였습니다.",
            SeyfertException::CODE_SFRT_TRNSFR_PND_RELEASED_UNKNOWN,
            $result
        );
    }

    /**
     * @param string $tid
     * @return \Apikr\Paygate\Seyfert\TransactionResult
     */
    public function cancelPending($tid)
    {
        $result = $this->request("POST", '/v5/transaction/seyfertTransferPending/cancel', [
            'parentTid' => $tid,
        ]);
        if ($result['data']['status'] === 'SFRT_TRNSFR_PND_CANCELED') {
            return new TransactionResult($result);
        }
        throw new SeyfertException(
            "펜딩 취소 도중 알수 없는 에러({$result['data']['status']})가 발생하였습니다.",
            SeyfertException::CODE_SFRT_TRNSFR_PND_CANCELED_UNKNOWN,
            $result
        );
    }

    /**
     * @param string $guid
     * @param string $bankCode
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function createVirtualAccount($guid, $bankCode)
    {
        $cacheKey = "seyfert.va.{$bankCode}.{$guid}";
        if ($this->cache && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $result = $this->request("PUT", '/v5a/member/assignVirtualAccount/p2p', [
            'dstMemGuid' => $guid,
            'bnkCd' => $bankCode,
        ]);
        $resultToReturn = new Result($result);
        if ($this->cache) {
            $ttl = floor($result['data']['info']['expireDt'] / 1000) - time() - 10 * 60;
            $this->cache->set($cacheKey, $resultToReturn, (int) $ttl);
        }
        return $resultToReturn;
    }

    /**
     * @param string $guid
     * @return int
     */
    public function getBalanceMoney($guid)
    {
        $result = $this->request("GET", '/v5/member/seyfert/inquiry/balance', [
            'dstMemGuid' => $guid,
            'crrncy' => 'KRW',
        ]);
        return (int)(isset($result['data']['moneyPair']['amount']) ? $result['data']['moneyPair']['amount'] : 0);
    }

    /**
     * @param string $guid
     * @param int $page
     * @param int $limit
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function retrieveTransactions($guid, $page = 1, $limit = 10)
    {
        $result = $this->request("GET", '/v5a/admin/seyfertList', [
            'dstMemGuid' => $guid,
            'page' => $page,
            'limit' => $limit,
        ]);
        return new Result($result);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $form
     * @return array
     */
    public function request($method, $path, array $form = [])
    {
        $form = $form + [
                '_method' => $method,
                'reqMemGuid' => $this->config->getGuid(),
            ];

        $encReq = AesCtr::encrypt('&' . http_build_query($form), $this->config->getKeyp());
        try {
            $response = $this->client->request('GET', $this->config->getRequestUrl($path), [
                'query' => [
                    '_method' => $method,
                    'reqMemGuid' => $this->config->getGuid(),
                    'encReq' => $encReq,
                ],
            ]);
        } catch (ClientException $e) {
            $result = json_decode($e->getResponse()->getBody(), true);
            if (isset($result['data']['cdDesc'])) {
                throw new SeyfertException($result['data']['cdDesc'] . "..", SeyfertException::CODE_API_CLIENT_ERROR);
            }
            throw $e;
        }
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
