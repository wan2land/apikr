<?php
namespace Apikr\Siot\Iamport\VO;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CardExpiryTest extends TestCase
{
    public function testValidArgument()
    {
        $expiry = new CardExpiry('2017-11');
        static::assertEquals('2017-11', $expiry->__toString());
        static::assertEquals('2017-11', $expiry->getCardExpiry());

        $expiry = new CardExpiry('201711');
        static::assertEquals('2017-11', $expiry->__toString());
        static::assertEquals('2017-11', $expiry->getCardExpiry());

        $expiry = new CardExpiry('01/22');
        static::assertEquals('2022-01', $expiry->__toString());
        static::assertEquals('2022-01', $expiry->getCardExpiry());

        $expiry = new CardExpiry('1/22');
        static::assertEquals('2022-01', $expiry->__toString());
        static::assertEquals('2022-01', $expiry->getCardExpiry());
    }

    public function testInvalidArgument()
    {
        try {
            new CardExpiry('122');
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('invalid card expiry.', $e->getMessage());
        }
        try {
            new CardExpiry('01//22');
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('invalid card expiry.', $e->getMessage());
        }
    }
}
