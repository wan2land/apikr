<?php
namespace Apikr\Paygate\Seyfert;

use Apikr\Api\Result;

class TransactionResult extends Result 
{
    /**
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->search('data.tid');
    }
}
