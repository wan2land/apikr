<?php
namespace Apikr\SKPlanet\TMap;

use Apikr\Common\Result;
use Apikr\SKPlanet\TMap\Contracts\SpatialPoint;
use Apikr\SKPlanet\TMap\Exception\ApiException;
use GuzzleHttp\Client;

class Api
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Apikr\SKPlanet\TMap\Configuration */
    protected $config;

    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param \Apikr\SKPlanet\TMap\Contracts\SpatialPoint $origin
     * @param \Apikr\SKPlanet\TMap\Contracts\SpatialPoint $dest
     * @param array $options
     * @return \Apikr\Common\Result
     */
    public function getRoutes(SpatialPoint $origin, SpatialPoint $dest, array $options = [])
    {
        return $this->request('post', '/tmap/routes', $options + [
                'startX' => $origin->getSpatialLng(),
                'startY' => $origin->getSpatialLat(),
                'endX' => $dest->getSpatialLng(),
                'endY' => $dest->getSpatialLat(),
                'reqCoordType' => 'WGS84GEO',
                'resCoordType' => 'WGS84GEO',
                'searchOption' => Configuration::OPTION_SHORTEST,
            ]);
    }

    /**
     * @param string $address
     * @param array $options
     * @return \Apikr\Common\Result
     */
    public function geocodingFullAddress($address, array $options = [])
    {
        return $this->request('get', '/tmap/geo/fullAddrGeo', $options + [
                'coordType' => 'WGS84GEO',
                'format' => 'json',
                'fullAddr' => $address,
            ]);
    }

    /**
     * @param \Apikr\SKPlanet\TMap\Contracts\SpatialPoint $point
     * @param array $options
     * @return \Apikr\Common\Result
     */
    public function reverseGeocoding(SpatialPoint $point, array $options = [])
    {
        return $this->request('get', '/tmap/geo/reversegeocoding', $options + [
                'lat' => $point->getSpatialLat(),
                'lon' => $point->getSpatialLng(),
                'coordType' => 'WGS84GEO',
                'addressType' => 'A04',
            ]);
    }

    /**
     * @param string $address
     * @param string $type
     * @param array $options
     * @return \Apikr\Common\Result
     */
    public function convertAddress($address, $type = 'OtoN', array $options = [])
    {
        return $this->request('get', '/tmap/geo/convertAddress', $options + [
                'reqAdd' => $address,
                'searchTypCd' => $type,
                'reqMulti' => 'M',
                'resCoordType' => 'WGS84GEO',
                'addressType' => 'A04',
            ]);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $form
     * @return \Apikr\Common\Result
     */
    public function request($method, $path, array $form = [])
    {
        if (strtolower($method) === 'get') {
            $response = $this->client->request($method, $this->config->getRequestUrl($path, $form + ['version' => 1,]), [
                'headers' => [
                    'appKey' => $this->config->getApiKey(),
                ],
            ]);
        } else {
            $response = $this->client->request($method, $this->config->getRequestUrl($path, ['version' => 1,]), [
                'headers' => [
                    'appKey' => $this->config->getApiKey(),
                ],
                'form_params' => $form,
            ]);
        }
        $body = $response->getBody()->__toString();
        if (!$body) {
            throw new ApiException('처리중 에러가 발생하였습니다.', ApiException::CODE_NULL_RESPONSE);
        }
        $result = new Result(json_decode($body, true));
        if ($error = $result->search('error')) {
            throw new ApiException($error['message'], $error['code'], $result);
        }
        return $result;
    }
}
