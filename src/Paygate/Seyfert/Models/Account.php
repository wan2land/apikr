<?php
namespace Apikr\Paygate\Seyfert\Models;

class Account
{
    /** @var \Apikr\Paygate\Seyfert\Models\Bank */
    protected $bank;

    /** @var string */
    protected $accountNumber;

    /**
     * @param \Apikr\Paygate\Seyfert\Models\Bank $bank
     * @param string $accountNumber
     */
    public function __construct(Bank $bank, $accountNumber)
    {
        $this->bank = $bank;
        $this->accountNumber = str_replace("-", '', $accountNumber);
    }

    /**
     * @return \Apikr\Paygate\Seyfert\Models\Bank
     */
    public function getBank()
    {
        return $this->bank;
    }
    
    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function getFormattedAccountNumber()
    {
        if ($this->bank->getCode() === 'KIUP_003') {
            return substr($this->accountNumber, 0, 3) . '-' .
            substr($this->accountNumber, 3, 6) . '-' .
            substr($this->accountNumber, 9, 2) . '-' .
            substr($this->accountNumber, 11);
        }
    }
}
