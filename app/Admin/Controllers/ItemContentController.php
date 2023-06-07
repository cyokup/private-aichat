<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\ItemContentImport;
use App\Models\ItemContent;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class ItemContentController extends AdminController
{

    protected $title = '资料管理';

    /**
     * 表格布局和数据输出（首页显示数据）
     * @return Grid
     */
    protected function grid()
    {
        $itemId = request('item_id');
        $grid = new Grid(new ItemContent());
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ItemContentImport());
        });
        $grid->header(function ($query) {
            $html = "1.导入支持：txt、doc、docx、csv、xls、xlsx、pdf后缀的文件<br/>";
            $html .= "2.单文件最大5M,1M=1024KB";
            return $html;
        });
        $grid->model()->where('item_id', $itemId)->orderBy('id', 'desc');
        $grid->id('ID')->sortable();
        $grid->title("标题");
        $grid->size("存储(KB)");
        $grid->column('status', '状态')->display(function ($status) {
            $array = [0 => '未处理', 1 => '处理中', 2 => '处理完成', 3 => '处理失败'];
            return $array[$status] ?? '';
        });
        $grid->remark("备注")->editable();
        $grid->created_at("创建时间");
        $grid->filter(function ($filter) {
            $filter->like('title', '标题');
        });
        return $grid;
    }

    /**
     * 表单布局和数据输出（创建，编辑单条数据）
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ItemContent());
        $form->text("title", "标题");
        $form->textarea("content", "内容");
        $form->hidden('admin_id')->value(Admin::user()->id);
        $form->submitted(function (Form $form) {
            $form->content = preg_replace("/\s/", "", $form->content);
        });
        return $form;
    }
}
