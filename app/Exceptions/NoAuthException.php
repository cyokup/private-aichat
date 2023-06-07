<?php

namespace App\Exceptions;

use App\Helpers\ResponseCode;
use Exception;

class NoAuthException extends Exception
{
    public function render()
    {
        return response(['code' => ResponseCode::NO_AUTH, 'msg' => $this->getMessage(), 'data' => []]);
    }

}
