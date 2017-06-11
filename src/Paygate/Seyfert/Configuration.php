<?php
namespace Apikr\Paygate\Seyfert;

class Configuration
{
    const SEYFERT_DEV_HOST = 'https://stg5.paygate.net'; // 테스트 서버
    const SEYFERT_PROD_HOST = 'https://v5.paygate.net'; // 실서버

    /** @var string */
    protected $guid;
    
    /** @var string */
    protected $keyp;

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
    public function getGuid()
    {
        return $this->guid;
    }
    
    /**
     * @return string
     */
    public function getKeyp()
    {
        return $this->keyp;
    }


    /**
     * @param string $path
     * @param array $queries
     * @return string
     */
    public function getRequestUrl($path, array $queries = [])
    {
        $url = ($this->production ? static::SEYFERT_PROD_HOST : static::SEYFERT_DEV_HOST)
            . '/' . ltrim($path, '/');
        if (count($queries)) {
            $url .= '?' . http_build_query($queries);
        }
        return $url;
    }
}
