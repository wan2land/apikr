<?php
namespace Apikr\ApiStore\Sms;

use Apikr\ApiStore\Sms\Exception\RequestException;
use Apikr\ApiStore\Sms\Exception\SmsDeliveryException;
use Apikr\Common\Result;
use GuzzleHttp\Client;

class Api
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Apikr\ApiStore\Sms\Configuration */
    protected $config;
    
    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @return \Apikr\Common\Result
     */
    public function senderList()
    {
        return $this->request("/sendnumber/list");
    }

    /**
     * @param string $phone
     * @return \Apikr\Common\Result
     */
    public function saveSender($phone)
    {
        return $this->request("/sendnumber/save", [
            'sendnumber' => $this->escapeNumber($phone),
        ]);
    }

    /**
     * @param string $receiver
     * @param string $text
     * @param string $title
     * @param string $sender
     * @return \Apikr\Common\Result
     */
    public function send($receiver, $text, $title = null, $sender = null)
    {
        try {
            return $this->request('/message/sms', [
                'dest_phone' => $this->escapeNumber($receiver),
                'send_phone' => $this->escapeNumber($sender ?: $this->config->sender),
                'subject' => $title ?: $this->config->title,
                'msg_body' => $text,
            ]);
        } catch (RequestException $e) {
            throw new SmsDeliveryException($receiver, $text, $e);
        }
    }

    /**
     * @param string $uri
     * @param array $form
     * @return \Apikr\Common\Result
     */
    public function request($uri, array $form = [])
    {
        $response = $this->client->request('POST', $this->config->getRequestUrl($uri), [
            'headers' => [
                'x-waple-authorization' => $this->config->apikey,
            ],
            'form_params' => $form,
        ]);
        $result = new Result(json_decode($response->getBody(), true));
        if ($result['result_code'] == 200) {
            return $result;
        }
        throw new RequestException('', $result['result_code'], $result);
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
