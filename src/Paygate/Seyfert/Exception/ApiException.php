<?php
namespace Apikr\Paygate\Seyfert\Exception;

use Apikr\Api\Result;
use RuntimeException;

class ApiException extends RuntimeException
{
    // error
    const CODE_API_CLIENT_ERROR = 400;
    const CODE_API_SERVER_ERROR = 401;
    const CODE_CHECK_BNK_NM_DENIED = 402;
    const CODE_CHECK_BNK_NM_NEED_REVIEW = 403;
    
    // unknown
    const CODE_CHECK_BNK_NM_UNKNOWN = 501;
    const CODE_CHECK_BNK_CD_UNKNOWN = 502;
    const CODE_SFRT_TRNSFR_PND_UNKNOWN = 503;
    const CODE_SFRT_WITHDRAW_UNKNOWN = 504;
    const CODE_SFRT_TRNSFR_PND_RELEASED_UNKNOWN = 505;
    const CODE_SFRT_TRNSFR_PND_CANCELED_UNKNOWN = 506;

    /** @var \Apikr\Api\Result */
    protected $result;
    
    public function __construct($message, $code = 0, Result $result = null)
    {
        parent::__construct($message, $code);
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
