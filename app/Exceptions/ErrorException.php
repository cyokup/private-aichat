<?php

namespace App\Exceptions;

use App\Helpers\ResponseCode;
use Exception;

class ErrorException extends Exception
{
    public function render()
    {
        return response(['code' => ResponseCode::ERROR_INSIDE, 'msg' => $this->getMessage(), 'data' => []]);
    }

}
