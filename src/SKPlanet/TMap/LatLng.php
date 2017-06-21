<?php
namespace Apikr\SKPlanet\TMap;

use Apikr\SKPlanet\TMap\Contracts\SpatialPoint;

class LatLng implements SpatialPoint 
{
    /** @var float */
    protected $lat;

    /** @var float */
    protected $lng;

    /**
     * @param float $lat
     * @param float $lng
     */
    public function __construct($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @return float
     */
    public function getSpatialLng()
    {
        return $this->lng;
    }

    /**
     * @return float
     */
    public function getSpatialLat()
    {
        return $this->lat;
    }
}
