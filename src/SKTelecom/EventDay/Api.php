<?php
namespace Apikr\SKTelecom\EventDay;

use Apikr\Api\Result;
use Apikr\SKTelecom\EventDay\Exception\EventDayRequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Api
{
    const TYPE_HOLIDAYS = 'h';
    const TYPE_ANNIVERSARY = 'a';
    const TYPE_24_SEASONS = 's';
    const TYPE_ETC_SEASONS = 't';
    const TYPE_PUBLIC_ANNIVERSARY = 'p';
    const TYPE_ALTER_HOLIDAYS = 'i';
    const TYPE_ETC = 'e';

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Apikr\SKTelecom\EventDay\Configuration */
    protected $config;
    
    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param string $type
     * @param string|int $year
     * @param string|int $month
     * @param string|int $day
     * @return \Apikr\Api\Result
     */
    public function getDays($type, $year = null, $month = null, $day = null)
    {
        return $this->request("/v1/eventday/days", [
            "type" => $type,
            "year" => $year,
            "month" => $month,
            "day" => $day,
        ]);
    }

    /**
     * @param string $uri
     * @param array $form
     * @return \Apikr\Api\Result
     */
    public function request($uri, array $form = [])
    {
        $fullUri = "https://apis.sktelecom.com" . $uri . "?" . http_build_query($form);
        try {
            $response = $this->client->request('GET', $fullUri, [
                'headers' => [
                    "Accept" => "application/json",
                    "TDCProjectKey" => $this->config->apikey,
                ]
            ]);
            $result = new Result(json_decode($response->getBody(), true));
            return $result;
        } catch (ClientException $e) {
            $errorResult = new Result(json_decode($e->getResponse()->getBody()->__toString(), true));
            throw new EventDayRequestException($errorResult["msg"], 0, $errorResult, $e);
        }
    }
}
