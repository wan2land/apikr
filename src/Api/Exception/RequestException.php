<?php
namespace Apikr\Api\Exception;

use Apikr\Api\Result;
use RuntimeException;
use Exception;

class RequestException extends RuntimeException
{
    /** @var array */
    protected static $messages = [];

    /** @var string */
    protected static $unknownMessage = '알 수 없는 에러가 발생하였습니다.';
    
    /** @var \Apikr\Api\Result */
    protected $result;

    /**
     * @param string $message
     * @param int $code
     * @param \Apikr\Api\Result $result
     * @param \Exception $previous
     */
    public function __construct($message = '', $code = 0, Result $result = null, Exception $previous = null)
    {
        if (!$message) {
            $message = isset(static::$messages[$code]) ? static::$messages[$code] : static::$unknownMessage;
        }
        parent::__construct($message, $code, $previous);
        $this->result = $result;
    }

    /**
     * @return \Apikr\Api\Result
     */
    public function getResult()
    {
        return $this->result;
    }
}
