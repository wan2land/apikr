<?php
namespace Apikr\Aligo\Sms;

use Apikr\Aligo\Sms\Exception\RequestException;
use Apikr\Aligo\Sms\Exception\SmsDeliveryException;
use Apikr\Common\Result;
use GuzzleHttp\Client;

class Api
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Apikr\Aligo\Sms\Configuration */
    protected $config;

    /** @var string */
    protected $sendNumber;

    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function send($receiver, $text, $title = null, $sender = null)
    {
        if (!$title) {
            $title = $this->config->title;
        }
        try {
            $form = [
                'sender' => $this->escapeNumber($sender ? $sender : $this->config->sender),
                'receiver' => $this->escapeNumber($receiver),
                'msg' => $text,
            ];
            if ($this->config->debug) {
                $form['testmode_yn'] = 'Y';
            }
            return $this->request('', $form);
        } catch (RequestException $e) {
            throw new SmsDeliveryException($receiver, $text, $e);
        }
    }

    /**
     * @return \Apikr\Common\Result
     */
    public function remain()
    {
        return $this->request('/remain/');
    }

    /**
     * @return int
     */
    public function remainSms()
    {
        return (int)$this->remain()['SMS_CNT'];
    }

    /**
     * @return int
     */
    public function remainLms()
    {
        return (int)$this->remain()['LMS_CNT'];
    }

    /**
     * @return int
     */
    public function remainMms()
    {
        return (int)$this->remain()['MMS_CNT'];
    }

    /**
     * @param string $uri
     * @param array $form
     * @return \Apikr\Common\Result
     */
    public function request($uri, array $form = [])
    {
        $response = $this->client->request('POST', $this->config->getRequestUrl($uri), [
            'form_params' => $form + [
                    'userid' => $this->config->id,
                    'key' => $this->config->apikey,
                ],
        ]);
        $result = new Result(json_decode($response->getBody(), true));
        if ($result['result_code'] == 1) {
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
