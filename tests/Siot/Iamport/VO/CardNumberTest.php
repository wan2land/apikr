<?php
namespace Apikr\Siot\Iamport\VO;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CardNumberTest extends TestCase
{
    public function testValidArgument()
    {
        $number = new CardNumber('1234-1234-1234-1234');
        static::assertEquals('1234-1234-1234-1234', $number->__toString());
        static::assertEquals('1234-1234-1234-1234', $number->getCardNumber());

        $number = new CardNumber('1234234534564567');
        static::assertEquals('1234-2345-3456-4567', $number->__toString());
        static::assertEquals('1234-2345-3456-4567', $number->getCardNumber());

        $number = new CardNumber('1234_2345_3456_4567');
        static::assertEquals('1234-2345-3456-4567', $number->__toString());
        static::assertEquals('1234-2345-3456-4567', $number->getCardNumber());
    }

    public function testInvalidArgument()
    {
        try {
            new CardNumber('1234-1234-1234-12');
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('invalid card number.', $e->getMessage());
        }
    }
}
