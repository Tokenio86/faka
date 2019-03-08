<?php

namespace app\daili\model;

use think\Model;
class Goods extends Model
{
    protected $pk = 'id';
    protected $table = 'goods';
    public function goodinfo($goodid)
    {
        $res = $this->where(['id' => $goodid])->find();
        return $res;
    }
    public function update_sales($goodid, $sales)
    {
        $res = $this->where(['id' => $goodid])->find();
        $res2 = $this->where(['id' => $goodid])->update(['sales' => $sales + $res['sales']]);
    }
    public function getgood($abridge)
    {
        $res = $this->where(['abridge' => $abridge])->where(['status' => 0])->select();
        return $res;
    }
}