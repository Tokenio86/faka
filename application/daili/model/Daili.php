<?php

namespace app\daili\model;

use think\Model;
class Daili extends Model
{
    protected $pk = 'id';
    protected $table = 'daili';
    public function dailiinfo($dailiid)
    {
        $res = $this->where(['id' => $dailiid])->find();
        return $res;
    }
    public function money($dailiid, $money)
    {
        $res = $this->where(['id' => $dailiid])->update(['money' => $money]);
    }
    public function czmoney($dailiid, $money)
    {
        $res = $this->where(['id' => $dailiid])->find();
        $res2 = $this->where(['id' => $dailiid])->update(['money' => $money + $res['money']]);
        return $res2;
    }
    public function updatedaili($dailiid, $password)
    {
        $res = $this->where(['id' => $dailiid])->update(['password' => md5(md5($password))]);
        return $res;
    }
}