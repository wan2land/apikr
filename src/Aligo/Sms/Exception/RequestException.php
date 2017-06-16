<?php
namespace Apikr\Aligo\Sms\Exception;

use Apikr\Common\Result;
use RuntimeException;
use Exception;

class RequestException extends RuntimeException
{
    const ERR_SERVER = -100;
    const ERR_INVALID_PARAM = -101;
    const ERR_AUTH_ERROR = -102;
    const ERR_WRONG_SENDER = -103;
    const ERR_DELIVERY_LIMIT_OR_TIME = -105;
    const ERR_INSUFFICIENT_REMAINING = -109;
    const ERR_RESERVED_DATETIME = -115;
    const ERR_REQUIRE_CHARGE = -201;
    const ERR_WRONG_IMAGE = -301;
    const ERR_UNKNOWN = -900;

    /** @var array */
    protected static $messages = [
        -100 => '서버에러',
        -101 => '필수입력 부적합',
        -102 => '인증 에러',
        -103 => '발신번호 인증 에러',
        -105 => '발송건수제한,발송시간 에러',
        -109 => '문자 잔여횟수 부족',
        -115 => '예약 시간 에러',
        -201 => '전송가능 건수 부족(충전잔액부족)',
        -301 => '이미지 입력오류',
        -900 => '알려지지 않은 에러',
    ];
    
    /** @var \Apikr\Common\Result */
    protected $result;

    /**
     * @param string $message
     * @param int $code
     * @param \Apikr\Common\Result $result
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
     * @return \Apikr\Common\Result
     */
    public function getResult()
    {
        return $this->result;
    }
}
