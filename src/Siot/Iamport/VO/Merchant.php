<?php
namespace Apikr\Siot\Iamport\VO;

use Apikr\Siot\Iamport\Contracts\MerchantInterface;

class Merchant implements MerchantInterface 
{
    /** @var string */
    protected $uid;
    
    /** @var int */
    protected $amount;
    
    /** @var string */
    protected $name;

    /** @var array */
    protected $options;

    public function __construct($uid, $amount, $name, array $options = [])
    {
        $this->uid = $uid;
        $this->amount = (int) $amount;
        $this->name = $name;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantUid()
    {
        return $this->uid;
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantOptions()
    {
        return $this->options;
    }
} 
