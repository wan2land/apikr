<?php
namespace Apikr\Paygate\Seyfert\Exception;

use Apikr\Paygate\Seyfert\Result;
use Exception;
use RuntimeException;

class SeyfertException extends RuntimeException
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

    /** @var array */
    protected $result;
    
    public function __construct($message, $code = 0, array $result = [])
    {
        parent::__construct($message, $code);
        $this->result = $result;
    }

    /**
     * @return \Apikr\Paygate\Seyfert\Result
     */
    public function getResult()
    {
        return new Result($this->result);
    }
}
