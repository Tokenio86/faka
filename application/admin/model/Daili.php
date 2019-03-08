<?php

namespace app\admin\model;

use think\Model;
class Daili extends Model
{
    protected $pk = 'id';
    protected $table = 'daili';
    protected $autoWriteTimestamp = 'timestamp';
    protected $updateTime = false;
    public function dailiinfo($page, $limit, $keyword)
    {
        if ($keyword != '') {
            $res = $this->where(['username' => $keyword])->whereOr(['id' => $keyword])->page($page, $limit)->select();
        } else {
            $res = $this->page($page, $limit)->select();
        }
        return $res;
    }
    public function deldaili($id)
    {
        $res = $this->where(['id' => $id])->delete();
        return $res;
    }
    public function daili($id)
    {
        $res = $this->where(['id' => $id])->find();
        return $res;
    }
    public function dlcount($keyword)
    {
        if ($keyword != '') {
            $res = $this->where(['username' => $keyword])->where(['id' => $keyword])->select();
        } else {
            $res = $this->select();
        }
        return count($res);
    }
    public function editdaili($data)
    {
        if (!$data['password']) {
            $res = $this->where(['id' => $data['id']])->update(['email' => $data['email'], 'money' => $data['money']]);
        } else {
            $res = $this->where(['id' => $data['id']])->update(['email' => $data['email'], 'money' => $data['money'], 'password' => md5(md5($data['password']))]);
        }
        return $res;
    }
    public function adddaili($data)
    {
        if (!$data['username'] || !$data['email']) {
            return ['vaild' => 0, 'msg' => '用户或者邮箱不得为空'];
            exit;
        }
        $dlif = $this->where(['username' => $data['username']])->find();
        if ($dlif) {
            return ['vaild' => 0, 'msg' => '用户名已存在'];
            exit;
        }
        $res = $this;
        $res->username = $data['username'];
        $res->password = md5(md5($data['username']));
        $res->email = $data['email'];
        $res->money = 0;
        $res->save();
        if ($res) {
            return ['vaild' => 1, 'msg' => '添加成功'];
            exit;
        } else {
            return ['vaild' => 0, 'msg' => '添加失败'];
            exit;
        }
    }
    public function jiakuan($dlid, $money)
    {
        $dlinfo = $this->where(['id' => $dlid])->find();
        $money = $dlinfo['money'] + $money;
        $jiakuan = $this->where(['id' => $dlid])->update(['money' => $money]);
        if ($jiakuan) {
            return '1';
        }
    }
}