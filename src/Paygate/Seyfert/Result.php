<?php
namespace Apikr\Paygate\Seyfert;

use JmesPath\Env as JmesPath;

class Result
{
    /** @var array */
    protected $data;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @param string $expression
     * @return mixed
     */
    public function search($expression)
    {
        return JmesPath::search($expression, $this->toArray());
    }
}
