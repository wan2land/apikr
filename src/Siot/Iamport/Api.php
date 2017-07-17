<?php
namespace Apikr\Siot\Iamport;

use Apikr\Api\Result;
use Apikr\Siot\Iamport\Contracts\CardExpiryInterface;
use Apikr\Siot\Iamport\Contracts\CardNumberInterface;
use Apikr\Siot\Iamport\Exception\IamportRequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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
     * @param string $customerUid
     * @param \Apikr\Siot\Iamport\Contracts\CardNumberInterface $cardNumber
     * @param \Apikr\Siot\Iamport\Contracts\CardExpiryInterface $expiry
     * @param string $birth
     * @param string|null $pwd2digit
     * @param array $options
     * @return \Apikr\Api\Result
     */
    public function createSubscribeCustomer(
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
     * @return string
     */
    public function createToken()
    {
        if ($this->cache->has($this->config->tokenCacheKey)) {
            return $this->cache->get($this->config->tokenCacheKey);
        }
        $result = $this->request('POST', '/users/getToken', [
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
     * @param string $uri
     * @param array $form
     * @param bool $auth
     * @return \Apikr\Api\Result
     */
    public function request($method, $uri, array $form = [], $auth = true)
    {
        $headers = [];
        if ($auth) {
            $headers['Authorization'] = $this->createToken();
        }
        try {
            $response = $this->client->request($method, $this->config->getRequestUrl($uri), [
                'json' => $form,
                'headers' => $headers,
            ]);
            $result = Result::createFromResponse($response);
            if ($result->search('code') == -1) {
                throw new IamportRequestException($result->search('message'), $result->search('code'), $result);
            }
            return $result;
        } catch (ClientException $e) {
            $result = Result::createFromResponse($e->getResponse());
            throw new IamportRequestException($result->search('message'), $result->search('code'), $result, $e);
        }
    }
}
