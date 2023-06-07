<?php

namespace App\Admin\Actions;

use App\Jobs\ContentToDetail;
use App\Models\ItemContent;
use Encore\Admin\Actions\Action;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemContentImport extends Action
{
    protected $selector = '.import-post';

    public function handle(Request $request)
    {
        $itemId = $request->get('item_id');
        if (empty($itemId)) {
            return $this->response()->error('项目ID不存在');
        }
        $file = $request->file('file');
        if (!($request->hasFile('file') && $file->isValid())) {
            return $this->response()->error('文件不存在或无效');
        }
        // 单位KB
        $img_size = round(($file->getSize() / 1024), 2);
        if ($img_size >= 5 * 1024) {
            return $this->response()->error('文件必须小于5M');
        }
        // 文件后缀
        $ext = $file->getClientOriginalExtension();
        if (!in_array($ext, ['txt', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'pdf'])) {
            return $this->response()->error("只允许上传txt,doc,docx,csv,xls,xlsx,pdf后缀的文件");
        }
        $fileName = date('YmdHis') . '_' . randStr(3) . '.' . $ext;
        $dir = 'files';
        $file->storeAs($dir, $fileName, ['disk' => 'public']);
        $itemContent = ItemContent::create([
            'admin_id' => Admin::user()->id,
            'item_id' => $itemId,
            'title' => $file->getClientOriginalName(),
            'path' => $dir . '/' . $fileName,
            'ext' => $ext,
            'size' => $img_size,
            'status' => 0
        ]);
        dispatch((new ContentToDetail($itemContent)));
        return $this->response()->success('导入完成！')->refresh();
    }

    public function form()
    {
        $this->file('file', '请选择文件');
        $this->hidden('item_id', '项目id')->value(\request('item_id'));
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-post">导入资料</a>
HTML;
    }
}
