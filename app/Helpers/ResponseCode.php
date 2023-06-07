<?php

namespace App\Helpers;

class ResponseCode
{
    // 请求正确且成功
    const SUCCESS_OK = 1000;
    // 参数错误
    const ERROR_PARAMETER = 1002;
    // 业务错误
    const ERROR_BUSINESS = 1003;
    // 内部错误
    const ERROR_INSIDE = 1004;
    // 认证失败
    const NO_AUTH = 1001;

}
