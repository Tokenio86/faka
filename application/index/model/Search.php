<?php

namespace app\index\model;

use think\Model;
class Search extends Model
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
    public function getYeskmAttr($value)
    {
        if ($value == '0') {
            return '未发卡';
        } else {
            return '已发卡';
        }
    }
    public function search($ddid, $page)
    {
        $ddinfo = $this->where(['ddid' => $ddid])->whereOr(['email' => $ddid])->whereOr(['payno' => $ddid])->order('id desc')->page($page, 5)->select();
        return $ddinfo;
    }
    public function countorder($ddid)
    {
        $count = $this->where(['ddid' => $ddid])->whereOr(['email' => $ddid])->whereOr(['payno' => $ddid])->order('id desc')->select();
        return $count;
    }
    public function getorderinfo($ddid)
    {
        $res = $this->where(['ddid' => $ddid])->find();
        return $res;
    }
}