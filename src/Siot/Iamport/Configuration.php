<?php
namespace Apikr\Siot\Iamport;

use Apikr\Api\ConfigurationAbstract;

/**
 * @property-read string $host
 * @property-read string $impKey
 * @property-read string $impSecret
 * @property-read string $debug
 * @property-read string $tokenCacheKey
 * @property-read \Psr\SimpleCache\CacheInterface $cache
 */
class Configuration extends ConfigurationAbstract
{
    /** @var array */
    protected $defaults = [
        'tokenCacheKey' => 'apikr.siot.iamport.token',
        'debug' => false,
    ];
    
    /** @var array */
    protected $required = [
        'impKey',
        'impSecret',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
        $this->assertRequired();
    }

    /**
     * @return string
     */
    public function getHostAttribute()
    {
        return 'https://api.iamport.kr';
    }
}
