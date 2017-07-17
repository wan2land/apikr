<?php
namespace Apikr\ApiStore\Sms\Exception;

use Apikr\Api\Result;
use RuntimeException;
use Exception;

class RequestException extends RuntimeException
{
    /** @var array */
    protected static $messages = [
        100 => '잘못된 사용자입니다.',
        300 => '매개변수가 잘못되었습니다.',
        400 => '알수 없는 에러',
        500 => '발신번호 사전 등록제에 의한 미등록 차단 또는 중복등록',
        600 => '선불제 충전요금 부족',
    ];

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
            $message = isset(static::$messages[$code]) ? static::$messages[$code] : '';
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
