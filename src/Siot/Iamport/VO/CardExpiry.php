<?php
namespace Apikr\Siot\Iamport\VO;

use Apikr\Siot\Iamport\Contracts\CardExpiryInterface;
use InvalidArgumentException;

class CardExpiry implements CardExpiryInterface
{
    /** @var string */
    protected $cardExpiry;

    public function __construct($cardExpiry)
    {
        if (!preg_match('~^\d{1,2}\/\d{2}$~', $cardExpiry) && !preg_match('~^\d{4}-?\d{2}$~', $cardExpiry)) {
            throw new InvalidArgumentException("invalid card expiry.");
        }
        if (strpos($cardExpiry, '/') !== false) {
            list($month, $year) = explode("/", $cardExpiry, 2);
            $this->cardExpiry = sprintf("20%02d%02d", $year, $month);
        } else {
            $cardExpiry = preg_replace("~[^0-9]~", '', $cardExpiry);
            $this->cardExpiry = $cardExpiry;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCardExpiry();
    }

    /**
     * @return string
     */
    public function getCardExpiry()
    {
        return substr($this->cardExpiry, 0, 4)
            . '-' . substr($this->cardExpiry, 4, 2);
    }
} 
