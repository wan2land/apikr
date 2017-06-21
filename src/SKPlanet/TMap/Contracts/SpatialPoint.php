<?php
namespace Apikr\SKPlanet\TMap\Contracts;

interface SpatialPoint
{
    /**
     * @return float
     */
    public function getSpatialLng();

    /**
     * @return float
     */
    public function getSpatialLat();
}
