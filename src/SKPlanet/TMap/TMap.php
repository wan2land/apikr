<?php
namespace Apikr\SKPlanet\TMap;

use Apikr\SKPlanet\TMap\Contracts\SpatialPoint;
use Apikr\SKPlanet\TMap\Exception\CannotCalculateException;
use Apikr\SKPlanet\TMap\Exception\NotFoundGeocodingException;
use InvalidArgumentException;

class TMap
{
    /** @var \Apikr\SKPlanet\TMap\Api */
    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
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
        $result = $this->api->getRoutes($origin, $dest, $options);
        if (!isset($result['features'])) {
            throw new CannotCalculateException('features 값이 없습니다.', CannotCalculateException::CODE_NO_EXISTS_FEATURES);
        }
        if ($totalDistance = $result->search('features[0].properties.totalDistance')) {
            return $totalDistance;
        }
        $totalDistance = 0;
        foreach ($result->search('features[*].properties.distance') as $distance) {
            $totalDistance += $distance;
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

        $result = $this->api->getRoutes($origin, $dest, [
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
     * @param string $address
     * @return \Apikr\SKPlanet\TMap\Contracts\SpatialPoint
     */
    public function geocoding($address)
    {
        $result = $this->api->geocodingFullAddress($address);
        if ($result->search('coordinateInfo.coordinate | length(@)') > 0) {
            $coordinate = $result->search('coordinateInfo.coordinate[0]');
            if (isset($coordinate['lat']) && isset($coordinate['lon']) && $coordinate['lat'] && $coordinate['lon']) {
                return new LatLng($coordinate['lat'], $coordinate['lon']);
            }
            if (isset($coordinate['newLat']) && isset($coordinate['newLon']) && $coordinate['newLat'] && $coordinate['newLon']) {
                return new LatLng($coordinate['newLat'], $coordinate['newLon']);
            }
        }
        throw new NotFoundGeocodingException($address, "다음 주소({$address})를 찾을 수 없습니다.");
    }

    /**
     * @param \Apikr\SKPlanet\TMap\Contracts\SpatialPoint $point
     * @return string
     */
    public function reverseGeocoding(SpatialPoint $point)
    {
        $result = $this->api->reverseGeocoding($point);
        return trim($result->search('addressInfo.fullAddress'));
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
