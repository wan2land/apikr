<?php
namespace Apikr\Paygate\Seyfert\Models;

class Transaction
{
    /** @var string */
    protected $tid;

    /** @var string */
    protected $action;

    /** @var string */
    protected $variation;

    /** @var string */
    protected $amount;

    /** @var string */
    protected $createdAt;

    public function __construct($tid, $action, $variation, $amount, $createdAt)
    {
        $this->tid = $tid;
        $this->action = $action;
        $this->variation = $variation;
        $this->amount = $amount;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getVariation()
    {
        return $this->variation;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

