<?php
namespace Apikr\Siot\Iamport\VO;

use Apikr\Siot\Iamport\Contracts\MerchantIdentifierInterface;

class MerchantIdentifier implements MerchantIdentifierInterface
{
    /** @var string */
    protected $uid;

    public function __construct($uid)
    {
        $this->uid = $uid;
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantUid()
    {
        return $this->uid;
    }
} 
