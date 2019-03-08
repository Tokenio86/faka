<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Login as LoginModel;
use app\admin\model\Option as OptionModel;
class Login extends Controller
{
    function _initialize()
    {
        $res = (new OptionModel())->getinfo();
        $this->assign('siteinfo', $res);
    }
    public function login()
    {
        if (request()->ispost()) {
            $res = (new LoginModel())->login($_POST);
            if ($res['valid']) {
                $this->success($res['msg'], '/' . Config('admin_url') . '/index/index');
                exit;
            } else {
                $this->error($res['msg']);
                exit;
            }
        }
        return view();
    }
}