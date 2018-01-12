<?php
namespace Apikr\CoolSms\Sms\Exception;

use Apikr\Api\Result;
use RuntimeException;
use Exception;

class RequestException extends RuntimeException
{
    /** @var array */
    protected static $messages = [
        1010 => '필수 입력 값 미입력',
        1020 => '등록된 계정이 아니거나 패스워드가 틀림',
        1021 => '해당 메시지가 없음',
        1022 => '해당 그룹이 없음',
        1023 => '해당 이미지가 없음',
        1024 => '서버 오류',
        1025 => '이미지 입력되었으나 타입이 MMS가 아님',
        1026 => '중복 수신번호',
        1030 => '잔액 부족',
        1061 => '사용자에 의해 수신거부 됨(080무료수신거부)',
        1062 => '발신번호 미등록',
        2000 => '정상 접수',
        2100 => '(예약) 대기중',
        2160 => '예약 취소',
        2230 => '잔액 부족',
        2254 => '스팸처리(쿨에스엠에스 게이트웨이)',
        3000 => '이통사로 접수 완료(정상)',
        3032 => '미가입자',
        3040 => '전송시간 초과',
        3041 => '단말기 busy',
        3042 => '음영지역',
        3043 => '단말기 Power off',
        3044 => '단말기 메시지 저장갯수 초과',
        3045 => '단말기 일시 서비스 정지',
        3046 => '기타 단말기 문제',
        3047 => '착신거절',
        3049 => 'Format Error',
        3050 => 'SMS서비스 불가 단말기',
        3051 => '착신측의 호불가 상태',
        3052 => '이통사 서버 운영자 삭제',
        3053 => '서버 메시지 Que Full',
        3054 => '스팸처리(이통사)',
        3055 => 'SPAM, nospam.or.kr 에 등록된 번호',
        3056 => '전송실패(무선망단)',
        3057 => '전송실패(무선망->단말기단)',
        3058 => '전송경로 없음',
        3059 => '변작된 발신번호.',
        4000 => '수신완료',
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
