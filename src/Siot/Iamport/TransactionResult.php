<?php
namespace Apikr\Siot\Iamport;

use Apikr\Api\Result;
use Apikr\Siot\Iamport\Contracts\TransactionInterface;

class TransactionResult extends Result implements TransactionInterface
{
    /**
     * @return string|null
     */
    public function getImpUid()
    {
        return $this->search('response.imp_uid');
    }
}
