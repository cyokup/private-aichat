<?php

namespace App\Helpers;

trait ApiResponse
{
    /**
     * 成功返回
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function successJson($data)
    {
        $retData['code'] = ResponseCode::SUCCESS_OK;
        if (gettype($data) == "string") {
            $retData['msg'] = $data;
            $retData['data'] = [];
        } else {
            $retData['msg'] = '请求成功';
            $retData['data'] = $data;
        }
        return response()->json($retData);
    }

    /**
     * 失败返回
     * @param $code
     * @param $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorJson($code, $msg, $data = [])
    {
        return response()->json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }
}
