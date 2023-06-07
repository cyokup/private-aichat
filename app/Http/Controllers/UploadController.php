<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseCode;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * @api {POST} /api/upload/upload_file 上传文件
     * @apiGroup 其他
     * @apiParamExample {json} 参数
     * file  // 文件二进制流
     * @apiSuccessExample {json} 返回
     * url  文件访问路径
     * path  存储路径
     */
    public function uploadFile(Request $request)
    {
        $file = $request->file('file');
        //获取文件大小,单位B
        $fileSize = floor($file->getSize());
        if ($fileSize >= 50 * 1024 * 1024) {
            return $this->fail([ResponseCode::ERROR_BUSINESS, '文件必须小于50M']);
        }
        //过滤文件后缀
        $ext = $file->getClientOriginalExtension();
        if (in_array($ext, ['zip', 'rar', 'ppt', 'pptx', 'xls', 'xlsx', 'doc', 'docx', 'png', 'gif', 'jpg', 'jpeg'])) {
            // 保存目录
            $dir = 'files';
            // 文件名
            $fileName = time() . uniqid() . '.' . $ext;
            $file->storeAs($dir, $fileName, ['disk' => 'public']);
            $path = "/$dir/" . $fileName;
            $url = config('filesystems.disks.public.url') . $path;
            return $this->successJson(['url' => $url, 'path' => $path]);
        } else {
            return $this->errorJson(ResponseCode::ERROR_BUSINESS, '文件格式错误');
        }
    }

}
