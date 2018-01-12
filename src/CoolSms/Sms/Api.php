<?php
namespace Apikr\CoolSms\Sms;

use Apikr\CoolSms\Sms\Exception\RequestException;
use Apikr\CoolSms\Sms\Exception\SmsDeliveryException;
use Apikr\Api\Result;
use GuzzleHttp\Client;

class Api
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Apikr\CoolSms\Sms\Configuration */
    protected $config;

    /** @var string */
    protected $sendNumber;

    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @return \Apikr\Api\Result
     */
    public function senderList()
    {
        return $this->request("GET", "/senderid/1.2/list");
    }

    /**
     * @param string $phone
     * @return \Apikr\Api\Result
     */
    public function registerSender($phone)
    {
        return $this->request("POST", "/senderid/1.2/register", [
            'phone' => $this->escapeNumber($phone),
        ]);
    }

    /**
     * @param string $handleKey
     * @return \Apikr\Api\Result
     */
    public function verifySender($handleKey)
    {
        return $this->request("POST", "/senderid/1.2/verify", [
            'handle_key' => $handleKey,
        ]);
    }

    public function send($receiver, $text, $title = null, $sender = null)
    {
        try {
            $form = [
                'subject' => ($title ?: $this->config->title),
                'from' => $this->escapeNumber($sender ?: $this->config->sender),
                'to' => $this->escapeNumber($receiver),
                'text' => $text,
            ];
            return $this->request('POST', '/sms/2/send', $form);
        } catch (RequestException $e) {
            throw new SmsDeliveryException($receiver, $text, $e);
        }
    }

    /**
     * @return \Apikr\Api\Result
     */
    public function balance()
    {
        return $this->request('GET', '/sms/2/balance');
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $form
     * @return \Apikr\Api\Result
     */
    public function request($method, $uri, array $form = [])
    {
        $timestamp = time();
        $salt = uniqid();
        $signature = hash_hmac('md5', $timestamp.$salt, $this->config->secret);

        $form += [
            'api_key' => $this->config->apikey,
            'timestamp' => $timestamp,
            'salt' => $salt,
            'signature' => $signature,
        ];

        if (strtolower($method) === 'get') {
            $uri .= '?' . http_build_query($form);
            $response = $this->client->request($method, $this->config->getRequestUrl($uri));
        } else {
            $response = $this->client->request($method, $this->config->getRequestUrl($uri), [
                'form_params' => $form,
            ]);
        }
        $result = new Result(json_decode($response->getBody(), true));
        if (isset($result['error_count']) && $result['error_count'] > 0) {
            throw new RequestException('', $result['error_list'][0], $result);
        }
        return $result;
    }

    /**
     * @param string $phone
     * @return string
     */
    protected function escapeNumber($phone)
    {
        return preg_replace('~[^\\d]~', '', $phone);
    }
}
