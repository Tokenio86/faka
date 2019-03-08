<?php

namespace app\daili\model;

use think\Model;
class Order extends Model
{
    protected $pk = 'id';
    protected $table = 'order';
    public function addorder($orderinfo)
    {
        $res = $this->insert(['ddid' => $orderinfo['ddid'], 'goodid' => $orderinfo['goodid'], 'name' => $orderinfo['name'], 'price' => $orderinfo['price'], 'count' => $orderinfo['count'], 'email' => $orderinfo['email'], 'type' => $orderinfo['type'], 'status' => $orderinfo['status'], 'time' => $orderinfo['time'], 'daili' => $orderinfo['daili'], 'yeskm' => 1]);
        return $res;
    }
    public function orderinfo($ddid)
    {
        $res = $this->where(['ddid' => $ddid])->find();
        return $res;
    }
    public function tkjl($dailiid, $page)
    {
        $res = $this->where(['daili' => $dailiid])->order('id desc')->page($page, 5)->select();
        return $res;
    }
    public function tkjl_count($dailiid)
    {
        $res = $this->where(['daili' => $dailiid])->select();
        return $res;
    }
}