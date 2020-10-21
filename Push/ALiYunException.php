<?php
/**
 * Created by PhpStorm.
 * User: feelop
 * Date: 2018/6/1
 * Time: 15:45
 */

namespace Ucar\Push;

class ALiYunException extends \Exception
{

    private $type;

    public function __construct($msg, $code)
    {
        $this->type = $code;
        parent::__construct($msg, $this->getErrorCode($code));
    }

    private function getErrorCode($code)
    {
        return [
                'OK' => 1,
                'isv.RAM_PERMISSION_DENY' => -1,
                'isv.OUT_OF_SERVICE' => -2,
                'isv.PRODUCT_UN_SUBSCRIPT' => -3,
                'isv.PRODUCT_UNSUBSCRIBE' => -4,
                'isv.ACCOUNT_NOT_EXISTS' => -5,
                'isv.ACCOUNT_ABNORMAL' => -6,
                'isv.SMS_TEMPLATE_ILLEGAL' => -7,
                'isv.SMS_SIGNATURE_ILLEGAL' => -8,
                'isv.INVALID_PARAMETERS' => -9,
                'isv.SYSTEM_ERROR' => -10,
                'isv.MOBILE_NUMBER_ILLEGAL' => -11,
                'isv.MOBILE_COUNT_OVER_LIMIT' => -12,
                'isv.TEMPLATE_MISSING_PARAMETERS' => -13,
                'isv.BUSINESS_LIMIT_CONTROL' => -14,
                'isv.INVALID_JSON_PARAM' => -15,
                'isv.BLACK_KEY_CONTROL_LIMIT' => -16,
                'isv.PARAM_LENGTH_LIMIT' => -17,
                'isv.PARAM_NOT_SUPPORT_URL' => -18,
                'isv.AMOUNT_NOT_ENOUGH' => -19,
            ][$code] ?? 0;
    }

    public function getErrorType()
    {
        return $this->type;
    }
}