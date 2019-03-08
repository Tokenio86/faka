<?php

namespace app\daili\model;

use think\Model;
class Login extends Model
{
    protected $pk = 'id';
    protected $table = 'daili';
    protected $autoWriteTimestamp = 'timestamp';
    protected $updateTime = false;
    public function login($data)
    {
        $dailiinfo = $this->where(['username' => $data['username']])->where(['password' => md5(md5($data['password']))])->find();
        if (!$dailiinfo) {
            return ['valid' => 0, 'msg' => '用户名或者密码不正确'];
        }
        session('dailiuser', $dailiinfo['username']);
        session('dailiid', $dailiinfo['id']);
        return ['valid' => 1, 'msg' => '登陆成功'];
    }
    public function reg($username, $email)
    {
        $res = $this;
        $res->username = $username;
        $res->password = md5(md5($username));
        $res->email = $email;
        $res->money = 0;
        $res->save();
        return $res;
    }
    public function finddl($username)
    {
        $res = $this->where(['username' => $username])->find();
        return $res;
    }
}