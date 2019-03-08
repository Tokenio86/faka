<?php

namespace app\admin\model;

use think\Model;
class Recharge extends Model
{
    protected $pk = 'id';
    protected $table = 'recharge';
    public function getStatusAttr($value)
    {
        $status = [1 => '<svg class="icon" aria-hidden="true">
        <use xlink:href="#icon-yifukuan"></use>
        </svg>', 0 => '<svg class="icon" aria-hidden="true">
        <use xlink:href="#icon-chaoshiweifukuan"></use>
        </svg>'];
        return $status[$value];
    }
    public function getTypeAttr($value)
    {
        if ($value == 'alipay') {
            return '<svg class="icon" aria-hidden="true">
                    <use xlink:href="#icon-zhifubao"></use>
                    </svg>';
        } else {
            if ($value == 'qqpay') {
                return '<svg class="icon" aria-hidden="true">
                 <use xlink:href="#icon-QQqianbaozhifu"></use>
                 </svg>';
            } else {
                if ($value == 'wxpay') {
                    return '<svg class="icon" aria-hidden="true">
                 <use xlink:href="#icon-weixinzhifu"></use>
                 </svg>';
                } else {
                    if ($value == 'daili') {
                        return '<svg class="icon" aria-hidden="true">
                 <use xlink:href="#icon-dailishang"></use>
                 </svg>';
                    }
                }
            }
        }
    }
    public function rechargedata($page, $limit, $keyword)
    {
        if ($keyword != '') {
            $resrecharge = $this->where(['dailiid' => $keyword])->order('id desc')->page($page, $limit)->select();
        } else {
            $resrecharge = $this->order('id desc')->page($page, $limit)->select();
        }
        return $resrecharge;
    }
    public function rechargecount($keyword)
    {
        if ($keyword != '') {
            $res = $this->where(['dailiid' => $keyword])->select();
        } else {
            $res = $this->select();
        }
        return count($res);
    }
    public function insertcz($dlid, $money, $ddid, $paytype)
    {
        switch ($paytype) {
            case '1':
                $paytype = 'alipay';
                break;
            case '2':
                $paytype = 'qqpay';
                break;
            case '3':
                $paytype = 'wxpay';
                break;
        }
        $this->insert(['id' => $ddid, 'dailiid' => $dlid, 'money' => $money, 'type' => $paytype, 'time' => date("Y-m-d H:i:s"), 'status' => 0]);
    }
    public function rechargeinfo($ddid)
    {
        $res = $this->where(['id' => $ddid])->where(['status' => 0])->find();
        return $res;
    }
    public function updatacz($ddid)
    {
        $this->where(['id' => $ddid])->update(['status' => 1]);
    }
    public function delcz($oper)
    {
        if ($oper == 'czjlwfk') {
            $cz_tatal = $this->where(['status' => 0])->select();
            foreach ($cz_tatal as $val) {
                $b = substr($val['time'], 0, 10);
                $c = date('Y-m-d');
                if ($b != $c) {
                    $clearcz = $this->where(['id' => $val['id']])->delete();
                }
            }
            return 'OK';
        } else {
            $cz_tatal = $this->select();
            foreach ($cz_tatal as $val) {
                $clearcz = $this->where(['id' => $val['id']])->delete();
            }
            return 'OK';
        }
    }
}