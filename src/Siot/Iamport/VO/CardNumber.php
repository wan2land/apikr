<?php
namespace Apikr\Siot\Iamport\VO;

use Apikr\Siot\Iamport\Contracts\CardNumberInterface;
use InvalidArgumentException;

class CardNumber implements CardNumberInterface
{
    /** @var string */
    protected $cardNumber;

    public function __construct($cardNumber)
    {
        $cardNumber = preg_replace("~[^0-9]~", '', $cardNumber);
        if (strlen($cardNumber) !== 16) {
            throw new InvalidArgumentException("invalid card number.");
        }
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCardNumber();
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return substr($this->cardNumber, 0, 4)
            . '-' . substr($this->cardNumber, 4, 4)
            . '-' . substr($this->cardNumber, 8, 4)
            . '-' . substr($this->cardNumber, 12, 4);
    }
} 
