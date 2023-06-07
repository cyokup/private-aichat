<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\ItemContentAction;
use App\Models\Item;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class ItemController extends AdminController
{

    protected $title = '项目管理';

    /**
     * 表格布局和数据输出（首页显示数据）
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Item());
        $grid->actions(function ($actions) {
            $actions->add(new ItemContentAction());
            $actions->disableView();
        });
        $grid->model()->orderBy('id', 'desc');
        $grid->id('ID')->sortable();
        $grid->title("标题");
        $grid->headimgurl("头像")->image("", 50, 50);
        $grid->column('chat_url', 'Chat链接')->link();
        $grid->column('status', '状态')->editable('select', [0 => '禁用', 1 => '启用']);
        $grid->remark("备注");
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
        $form = new Form(new Item());
        $form->text("title", "标题")->rules('required', ['required' => '必填']);;
        $form->select('status', '状态')->options([0 => '禁用', 1 => '启用'])->default(1)->rules('required', ['required' => '必填']);;
        $form->image("headimgurl", "头像")->move('images')->uniqueName()->rules('required', ['required' => '必填']);;
        $form->text("remark", "备注");
        $form->hidden('admin_id')->value(Admin::user()->id);
        if ($form->isCreating()) {
            $form->hidden('code')->value(randStr(8));
        }
        return $form;
    }
}
