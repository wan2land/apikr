<?php
namespace Apikr\Paygate\Seyfert\Contracts;

interface MemberAware
{
    /**
     * @return string
     */
    public function getSeyfertMemberIdentifier();
}
