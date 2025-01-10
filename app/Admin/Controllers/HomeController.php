<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        $userName = auth('admin')->user()->name;
        return $content
            ->title('后台管理系统')
            ->description('Backend Administration System')
            ->row('你好，' . $userName . '！')
            ->row('欢迎使用后台管理系统！');
    }
}
