<?php

namespace app\index\model;

use think\Model;
class Order extends Model
{
    protected $pk = 'id';
    protected $table = 'order';
    public function orderinfo($ddid)
    {
        $res = $this->where(['ddid' => $ddid])->find();
        return $res;
    }
    public function updateorder($ddid, $ziduan, $value)
    {
        $this->where(['ddid' => $ddid])->update([$ziduan => $value]);
    }
    public function insertorder($data)
    {
        $this->insert(['ddid' => $data['ddid'], 'name' => $data['name'], 'price' => $data['price'], 'type' => $data['type'], 'status' => 0, 'time' => $data['time'], 'goodid' => $data['goodid'], 'email' => $data['email'], 'count' => $data['count']]);
    }
    public function updatepayno($ddid, $pay_no)
    {
        $this->where(['ddid' => $ddid])->update(['payno' => $pay_no]);
    }
}