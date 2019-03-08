<?php

namespace app\index\model;

use think\Model;
class Km extends Model
{
    protected $pk = 'id';
    protected $table = 'kami';
    public function getKahaoAttr($value)
    {
        if (!$value) {
            return '';
        } else {
            return $value . '&nbsp;';
        }
    }
    public function Kminfo($ddid)
    {
        $res = $this->where(['ddid' => $ddid])->select();
        return $res;
    }
    public function kucun_km($goodid)
    {
        $res3 = $this->where(['goodid' => $goodid])->where(['status' => 0])->select();
        return $res3;
    }
    public function update_km($kmid, $data)
    {
        $this->where(['id' => $kmid])->update(['status' => $data['status'], 'ddid' => $data['ddid'], 'time' => date("Y-m-d H:i:s")]);
    }
    public function paykucun_km($goodid)
    {
        $res = $this->where(['goodid' => $goodid])->where(['status' => 0])->select();
        return count($res);
    }
    public function yishoukami($ddid)
    {
        $res = $this->where(['ddid' => $ddid])->select();
        return count($res);
    }
}