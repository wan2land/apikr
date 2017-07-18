<?php
namespace Apikr\Siot\Iamport\Contracts;

interface MerchantInterface extends MerchantIdentifierInterface
{
    /**
     * @return int
     */
    public function getMerchantAmount();

    /**
     * @return string
     */
    public function getMerchantName();

    /**
     * @return array
     */
    public function getMerchantOptions();
} 
