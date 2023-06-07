<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;

    /**
     * 获取用户信息
     * @return mixed
     */
    public function getUser()
    {
        return request()->user();
    }

    /**
     * 获取用户id
     * @return mixed
     */
    public function getUserId()
    {
        return request()->user()->id;
    }

}
