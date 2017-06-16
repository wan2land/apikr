<?php
namespace Apikr\Aligo\Sms\Exception;

class SmsDeliveryException extends RequestException
{
    /** @var string */
    protected $receiver;
    
    /** @var string */
    protected $text;
    
    /**
     * @param string $receiver
     * @param string $text
     * @param \Apikr\Aligo\Sms\Exception\RequestException $previous
     */
    public function __construct($receiver, $text, RequestException $previous = null)
    {
        parent::__construct($previous->getMessage(), $previous->getCode(), $previous->getResult(), $previous);
        $this->receiver = $receiver;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
