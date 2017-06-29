<?php
namespace Apikr\SKPlanet\TMap\Exception;

class CannotCalculateException extends TMapException
{
    const CODE_UNKNOWN = 0;
    const CODE_CLIENT_ERROR = 1;
    const CODE_SERVER_ERROR = 2;
    const CODE_NO_EXISTS_FEATURES = 3;
}
