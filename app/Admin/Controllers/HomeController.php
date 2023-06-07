<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('控制面板');
            $headers = ['项目', '数据'];
            $rows[] = ['用户名',Admin::user()->username];
            $content->row((new Box('数据总揽', new Table($headers, $rows)))->style('info')->solid());
        });
    }

}
