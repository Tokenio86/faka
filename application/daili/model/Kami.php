<?php

namespace app\daili\model;

use think\Model;
class Kami extends Model
{
    protected $pk = 'id';
    protected $table = 'kami';
    public function getkami($goodid)
    {
        $res = $this->where(['goodid' => $goodid])->where(['status' => 0])->select();
        return $res;
    }
    public function updatekami($kmid, $orderid)
    {
        $res = $this->where(['id' => $kmid])->update(['status' => 1, 'ddid' => $orderid, 'time' => date("Y-m-d H:i:s")]);
    }
    public function orderkm($ddid)
    {
        $res = $this->where(['ddid' => $ddid])->select();
        return $res;
    }
}