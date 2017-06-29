<?php
namespace Apikr\SKPlanet\TMap\Exception;

use Exception;

class NotFoundGeocodingException extends TMapException
{
    /** @var string */
    protected $address;

    public function __construct($address, $message = '', $code = 0, Exception $previous = null)
    {
        $this->address = $address;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
}
