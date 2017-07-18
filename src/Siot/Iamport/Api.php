<?php
namespace Apikr\Siot\Iamport;

use Apikr\Api\Result;
use Apikr\Siot\Iamport\Contracts\CardExpiryInterface;
use Apikr\Siot\Iamport\Contracts\CardNumberInterface;
use Apikr\Siot\Iamport\Contracts\MerchantIdentifierInterface;
use Apikr\Siot\Iamport\Contracts\MerchantInterface;
use Apikr\Siot\Iamport\Contracts\TransactionInterface;
use Apikr\Siot\Iamport\Exception\IamportRequestException;
use Apikr\Siot\Iamport\VO\MerchantIdentifier;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;

class Api
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Psr\SimpleCache\CacheInterface */
    protected $cache;
    
    /** @var \Apikr\Siot\Iamport\Configuration */
    protected $config;

    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
        $this->cache = $config->cache ?: new ArrayCache();
    }

    /**
     * @param \Apikr\Siot\Iamport\Contracts\TransactionInterface $transaction
     * @param array $options
     * @return \Apikr\Siot\Iamport\TransactionResult
     */
    public function cancelPayment(TransactionInterface $transaction, array $options = [])
    {
        $result = $this->request('post', '/payments/cancel', $options + [
            'imp_uid' => $transaction->getImpUid(),
        ]);
        return new TransactionResult($result->toArray());
    }
    
    /**
     * @param string $customerUid
     * @param \Apikr\Siot\Iamport\Contracts\CardNumberInterface $cardNumber
     * @param \Apikr\Siot\Iamport\Contracts\CardExpiryInterface $expiry
     * @param string $birth
     * @param string|null $pwd2digit
     * @param array $options
     * @return \Apikr\Api\Result
     */
    public function saveUnauthPaymentCustomer(
        $customerUid,
        CardNumberInterface $cardNumber,
        CardExpiryInterface $expiry,
        $birth,
        $pwd2digit = null,
        array $options = []
    ) {
        return $this->request('post', "/subscribe/customers/{$customerUid}", $options + [
            'card_number' => $cardNumber->getCardNumber(),
            'expiry' => $expiry->getCardExpiry(),
            'birth' => $birth,
            'pwd_2digit' => $pwd2digit,
        ]);
    }

    /**
     * @param string $customerUid
     * @return \Apikr\Api\Result
     */
    public function getUnauthPaymentCustomer($customerUid)
    {
        return $this->request('get', "/subscribe/customers/{$customerUid}");
    }

    /**
     * @param string $customerUid
     * @return \Apikr\Api\Result
     */
    public function removeUnauthPaymentCustomer($customerUid)
    {
        return $this->request('delete', "/subscribe/customers/{$customerUid}");
    }

    /**
     * @param string $customerUid
     * @param \Apikr\Siot\Iamport\Contracts\MerchantInterface $merchant
     * @param array $options
     * @return \Apikr\Siot\Iamport\TransactionResult
     */
    public function makeUnauthPayment($customerUid, MerchantInterface $merchant, array $options = [])
    {
        $formData = $options + $merchant->getMerchantOptions() + [
            'customer_uid' => $customerUid,
            'merchant_uid' => $merchant->getMerchantUid(),
            'amount' => (int)$merchant->getMerchantAmount(),
            'name' => $merchant->getMerchantName(),
        ];
        $result = $this->request('post', "/subscribe/payments/again", $formData);
        return new TransactionResult($result->toArray());
    }
    
    /**
     * @param string $customerUid
     * @param int $page
     * @return \Apikr\Api\Result
     */
    public function retrieveUnauthPayments($customerUid, $page = 1)
    {
        return $this->request('get', "/subscribe/customers/{$customerUid}/payments", [
            'page' => (int) $page,
        ]);
    }

    /**
     * @param string $customerUid
     * @param \Apikr\Siot\Iamport\Contracts\MerchantInterface[] $merchants
     * @param \DateTime|int $scheduledAt
     * @param array $options
     * @return \Apikr\Api\Result
     */
    public function scheduleUnauthPayment($customerUid, array $merchants, $scheduledAt = 0, array $options = [])
    {
        $scheduledAt = max($this->normalizeSchedule($scheduledAt), time() + 300); // default value is 5 minutes
        $formData = $options
            + [
                'customer_uid' => $customerUid,
                'schedules' => array_map(function (MerchantInterface $merchant) use ($scheduledAt) {
                    return $merchant->getMerchantOptions() + [
                        'schedule_at' => $scheduledAt,
                        'merchant_uid' => $merchant->getMerchantUid(),
                        'amount' => (int) $merchant->getMerchantAmount(),
                        'name' => $merchant->getMerchantName(),
                    ];
                }, $merchants),
            ];
        return $this->request('post', '/subscribe/payments/schedule', $formData);
    }

    /**
     * @param $customerUid
     * @param \Apikr\Siot\Iamport\Contracts\MerchantIdentifierInterface[] $merchantIds
     * @param array $options
     * @return \Apikr\Api\Result
     */
    public function unscheduleUnauthPayment($customerUid, array $merchantIds, array $options = [])
    {
        $formData = $options
            + [
                'customer_uid' => $customerUid,
                'merchant_uid' => array_map(function (MerchantIdentifierInterface $merchant) {
                    return $merchant->getMerchantUid();
                }, $merchantIds),
            ];
        return $this->request('post', '/subscribe/payments/unschedule', $formData);
    }

    /**
     * @return string
     */
    public function createToken()
    {
        if ($this->cache->has($this->config->tokenCacheKey)) {
            return $this->cache->get($this->config->tokenCacheKey);
        }
        $result = $this->request('post', '/users/getToken', [
            'imp_key' => $this->config->impKey,
            'imp_secret' => $this->config->impSecret,
        ], false);
        $this->cache->set(
            $this->config->tokenCacheKey,
            $result->search('response.access_token'),
            $result->search('response.expired_at')
        );
        return $result->search('response.access_token');
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $formData
     * @param bool $auth
     * @return \Apikr\Api\Result
     */
    public function request($method, $path, array $formData = [], $auth = true)
    {
        $headers = [];
        if ($auth) {
            $headers['Authorization'] = $this->createToken();
        }
        try {
            $uri = $this->config->host . $path;
            $options = [
                'headers' => $headers,
            ];
            if (count($formData)) {
                if (strtolower($method) === 'get') {
                    $uri .= '?' . http_build_query($formData);
                } else {
                    $options['json'] = $formData;
                }
            }
            $response = $this->client->request($method, $uri, $options);
            $result = Result::createFromResponse($response);
            if ($result->search('code') != 0) {
                throw new IamportRequestException($result->search('message'), $result->search('code'), $result);
            }
            return $result;
        } catch (ClientException $e) {
            $result = Result::createFromResponse($e->getResponse());
            throw new IamportRequestException($result->search('message'), $result->search('code'), $result, $e);
        }
    }

    /**
     * @param \DateTime|int $schedule
     * @return int
     */
    protected function normalizeSchedule($schedule)
    {
        if ($schedule instanceof \DateTime) {
            return (int) $schedule->format('U');
        } elseif (!is_numeric($schedule)) {
            throw new InvalidArgumentException(sprintf('schedule date must be an integer or a DateTime, "%s" given', is_object($schedule) ? get_class($schedule) : gettype($schedule)));
        }
        return (int) $schedule;
    }
}
