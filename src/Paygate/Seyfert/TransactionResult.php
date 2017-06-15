<?php
namespace Apikr\Paygate\Seyfert;

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
