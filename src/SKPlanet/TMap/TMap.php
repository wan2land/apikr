<?php
namespace Apikr\SKPlanet\TMap;

use Apikr\SKPlanet\TMap\Contracts\SpatialPoint;
use Apikr\SKPlanet\TMap\Exception\CannotCalculateException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use InvalidArgumentException;

class TMap
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
     * @return int
     * @throws \Apikr\SKPlanet\TMap\Exception\CannotCalculateException
     */
    public function getDistance(SpatialPoint $origin, SpatialPoint $dest, array $options = [])
    {
        $result = $this->getRoutes($origin, $dest, $options);
        if (!isset($result['features'])) {
            throw new CannotCalculateException('features 값이 없습니다.', CannotCalculateException::CODE_NO_EXISTS_FEATURES);
        }
        if (isset($result['features'][0]['properties']['totalDistance'])) {
            return $result['features'][0]['properties']['totalDistance'];
        }
        $totalDistance = 0;
        foreach ($result['features'] as $feature) {
            $totalDistance += isset($feature['properties']['distance']) ? $feature['properties']['distance'] : 0;
        }
        return $totalDistance;
    }

    /**
     * @param \Apikr\SKPlanet\TMap\Contracts\SpatialPoint[] $points
     * @param array $options
     * @return int[]
     * @throws \Apikr\SKPlanet\TMap\Exception\CannotCalculateException
     */
    public function getDistances(array $points, array $options = [])
    {
        $this->assertSpatialPointList(__METHOD__, $points);

        $origin = array_shift($points);
        $dest = array_pop($points);

        $result = $this->getRoutes($origin, $dest, [
            'passList' => implode('_', array_map(function (SpatialPoint $point) {
                return $point->getSpatialLng() . "," . $point->getSpatialLat();
            }, $points)),
        ] + $options);

        if (!isset($result['features'])) {
            throw new CannotCalculateException('features 값이 없습니다.', CannotCalculateException::CODE_NO_EXISTS_FEATURES);
        }

        $distances = [];
        $key = 'N';
        foreach ($result['features'] as $feature) {
            if ($pointType = isset($feature['properties']['pointType']) ? $feature['properties']['pointType'] : null) {
                if ($pointType === 'E') break;
                if ($pointType !== 'N') {
                    $key = $pointType;
                }
            }
            if (!array_key_exists($key, $distances)) {
                $distances[$key] = 0;
            }
            $distances[$key] += isset($feature['properties']['distance']) ? $feature['properties']['distance'] : 0;
        }
        return array_values($distances);
    }

    /**
     * @param \Apikr\SKPlanet\TMap\Contracts\SpatialPoint $origin
     * @param \Apikr\SKPlanet\TMap\Contracts\SpatialPoint $dest
     * @param array $options
     * @return array
     */
    public function getRoutes(SpatialPoint $origin, SpatialPoint $dest, array $options = [])
    {
        try {
            $response = $this->client->post($this->config->getRequestUrl('/tmap/routes', ['version' => 1,]), [
                'headers' => [
                    'appKey' => $this->config->getApiKey(),
                ],
                'form_params' => $options + [
                    'startX' => $origin->getSpatialLng(),
                    'startY' => $origin->getSpatialLat(),
                    'endX' => $dest->getSpatialLng(),
                    'endY' => $dest->getSpatialLat(),
                    'reqCoordType' => 'WGS84GEO',
                    'resCoordType' => 'WGS84GEO',
                    'searchOption' => Configuration::OPTION_SHORTEST,
                ],
            ]);
            return json_decode($response->getBody()->__toString(), true);
        } catch (ClientException $e) { // 40x ERROR
            throw $e;
        } catch (ServerException $e) { // 50x ERROR
            throw $e;
        }
    }

    /**
     * @param string $methodName
     * @param array $points
     */
    protected function assertSpatialPointList($methodName, array $points = [])
    {
        $className = static::class;
        foreach ($points as $key => $point) {
            if (!$point instanceof SpatialPoint) {
                $argumentType = SpatialPoint::class;
                $index = $key + 1;
                throw new InvalidArgumentException("Argument {$index} passed to {$className}::{$methodName}() must be of the type {$argumentType}");
            }
        }
        if (count($points) > 5) {
            throw new InvalidArgumentException("Arguments passed to {$className}::{$methodName}() must be array less than 5");
        }
        if (count($points) < 2) {
            throw new InvalidArgumentException("Arguments passed to {$className}::{$methodName}() must be array more than 2");
        }
    }
}
