<?php
namespace Apikr\Paygate\Seyfert\Models;

use Apikr\Paygate\Seyfert\Contracts\MemberAware;

class Member implements MemberAware
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $name;

    /** @var string */
    protected $email;

    /** @var string */
    protected $phone;

    public function __construct($identifier, $name = null, $email = null, $phone = null)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getSeyfertMemberIdentifier()
    {
        return $this->identifier;
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
