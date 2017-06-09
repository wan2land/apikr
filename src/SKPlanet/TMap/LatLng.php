<?php
namespace Apikr\SKPlanet\TMap;

class LatLng
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
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }
}
