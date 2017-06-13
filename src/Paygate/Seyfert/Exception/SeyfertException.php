<?php
namespace Apikr\Paygate\Seyfert\Exception;

use RuntimeException;

class SeyfertException extends RuntimeException
{
    // success but warning..
    const CODE_CHECK_BNK_CD_FINISHED = 201;

    // error
    const CODE_API_CLIENT_ERROR = 400;
    const CODE_API_SERVER_ERROR = 401;
    const CODE_CHECK_BNK_NM_DENIED = 402;
    const CODE_CHECK_BNK_NM_NEED_REVIEW = 403;
    
    // unknown
    const CODE_CHECK_BNK_NM_UNKNOWN = 501;
    const CODE_CHECK_BNK_CD_UNKNOWN = 502;
    const CODE_SFRT_TRNSFR_PND_UNKNOWN = 503;
}
