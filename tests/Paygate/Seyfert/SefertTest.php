<?php

namespace Apikr\Paygate\Seyfert;

use Apikr\Api\Result;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase 
{
    /** @var \Apikr\Paygate\Seyfert\Api */
    protected $seyfert;
    
    /** @var array */
    protected $dataset;
    
    public function setUp()
    {
        if (!file_exists(__DIR__ . '/dataset.php')) {
            static::markTestSkipped('test dataset is null');
        }
        $this->dataset = require __DIR__ . '/dataset.php';
        $this->seyfert = new Api(new Client(), new Configuration($this->dataset));
    }
}
