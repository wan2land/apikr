<?php
namespace Apikr\SKTelecom\EventDay;

use InvalidArgumentException;

class Configuration
{
    /** @var string */
    public $apikey;

    /** @var array */
    protected static $required = [
        'apikey',
    ];
    
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $attribute) {
            $this->{$key} = $attribute;
        }
        foreach (static::$required as $required) {
            if (!isset($this->{$required})) {
                $keys = "'" . implode("', '", static::$required) . "'";
                throw new InvalidArgumentException("설정에서 {$keys}는 필수값입니다.");
            }
        }
    }
}
