<?php
namespace Apikr\Paygate\Seyfert\Models;

class Bank
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $code;

    public function __construct($name, $code)
    {
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
