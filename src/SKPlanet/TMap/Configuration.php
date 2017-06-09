<?php
namespace Apikr\SKPlanet\TMap;

class Configuration
{
    const HOST_PROD = 'https://apis.skplanetx.com'; // production

    const OPTION_TRAFFIC_RECOMMEND = 0; // 교통최적 + 추천
    const OPTION_TRAFFIC_FREE = 1; // 교통최적 + 무료우선
    const OPTION_TRAFFIC_NEWBIE = 3; // 교통최적 + 초보
    const OPTION_SHORTEST = 10; // 최단거리

    /** @var string */
    protected $apiKey;
    
    /** @var bool */
    protected $production = false;
    
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $attribute) {
            $this->{$key} = $attribute;
        }
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return boolean
     */
    public function isProduction()
    {
        return $this->production;
    }

    /**
     * @param string $path
     * @param array $queries
     * @return string
     */
    public function getRequestUrl($path, array $queries = [])
    {
        $url = static::HOST_PROD . '/' . ltrim($path, '/');
        if (count($queries)) {
            $url .= '?' . http_build_query($queries);
        }
        return $url;
    }
}
