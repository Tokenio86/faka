<?php

namespace app\admin\model;

use think\Model;
class Login extends Model
{
    protected $pk = 'id';
    protected $table = 'options';
    public function login($data)
    {
        $userinfo = $this->where(['adminuser' => $data['username']])->where(['adminpass' => md5(md5($data['password']))])->find();
        if (!$userinfo) {
            return ['valid' => 0, 'msg' => '用户名或者密码不正确'];
        }
        session('username', $userinfo['adminuser']);
        return ['valid' => 1, 'msg' => '登陆成功'];
    }
}