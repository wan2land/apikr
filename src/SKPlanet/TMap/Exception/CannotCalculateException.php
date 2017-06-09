<?php
namespace Apikr\SKPlanet\TMap\Exception;

use RuntimeException;

class CannotCalculateException extends RuntimeException
{
    const CODE_UNKNOWN = 0;
    const CODE_CLIENT_ERROR = 1;
    const CODE_SERVER_ERROR = 2;
    const CODE_NO_EXISTS_FEATURES = 3;
}
