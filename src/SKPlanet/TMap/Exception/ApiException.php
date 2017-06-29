<?php
namespace Apikr\SKPlanet\TMap\Exception;

use Apikr\Common\Result;

class ApiException extends TMapException
{
    const CODE_NULL_RESPONSE = 1;
    const CODE_BAD_REQUEST = 1100;

    /** @var \Apikr\Common\Result */
    protected $result;

    public function __construct($message, $code = 0, Result $result = null)
    {
        parent::__construct($message, $code);
        $this->result = $result;
    }
}
