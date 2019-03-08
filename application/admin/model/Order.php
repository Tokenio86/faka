<?php

namespace app\admin\model;

use think\Model;
use app\admin\model\Kami as KamiModel;
class Order extends Model
{
    protected $pk = 'id';
    protected $table = 'order';
    public function getStatusAttr($value)
    {
        $status = [1 => '<svg class="icon" aria-hidden="true">
                       <use xlink:href="#icon-yifukuan"></use>
                       </svg>', 0 => '<svg class="icon" aria-hidden="true">
                       <use xlink:href="#icon-chaoshiweifukuan"></use>
                       </svg>'];
        return $status[$value];
    }
    public function getYeskmAttr($value)
    {
        $status = [1 => '已发卡', 0 => '未发卡'];
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
    public function getKahaoAttr($value)
    {
        if ($value == null) {
            return '无';
        } else {
            return $value;
        }
    }
    public function getMimaAttr($value)
    {
        if ($value == null) {
            return '无';
        } else {
            return $value;
        }
    }
    public function ordata($page, $limit, $keyword)
    {
        if ($keyword != '') {
            $resorder = $this->where(['ddid' => $keyword])->whereOr(['email' => $keyword])->order('id desc')->page($page, $limit)->select();
        } else {
            $resorder = $this->order('id desc')->page($page, $limit)->select();
        }
        return $resorder;
    }
    public function ordercount($keyword)
    {
        if ($keyword != '') {
            $res = $this->where(['ddid' => $keyword])->whereOr(['email' => $keyword])->select();
        } else {
            $res = $this->select();
        }
        return count($res);
    }
    public function cleardata($oper)
    {
        if ($oper == 'wfk') {
            $order_tatal = $this->where(['status' => 0])->select();
            foreach ($order_tatal as $val) {
                $b = substr($val['time'], 0, 10);
                $c = date('Y-m-d');
                if ($b != $c) {
                    $clearwfk = $this->where(['ddid' => $val['ddid']])->delete();
                }
            }
            return 'OK';
        }
    }
    public function getorderinfo()
    {
        $res = $this->select();
        return $res;
    }
    public function findorder($ddid)
    {
        $res = $this->where(['ddid' => $ddid])->find();
        return $res;
    }
    public function zdsjdel($time, $timeend)
    {
        $res = $this->select();
        foreach ($res as $val) {
            $t = $val['time'];
            $t = strtotime($t);
            if ($t >= $time && $t <= $timeend) {
                $this->where(['ddid' => $val['ddid']])->delete();
                $res2 = (new KamiModel())->ddiddelkm($val['ddid']);
            }
        }
        return '清理成功';
    }
}