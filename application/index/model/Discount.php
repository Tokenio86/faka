<?php

namespace app\index\model;

use think\Model;
class Discount extends Model
{
    protected $pk = 'id';
    protected $table = 'discount';
    public function getStatusAttr($value)
    {
        $status = [1 => '<span class="layui-badge">已使用</span>', 0 => '<span class="layui-badge layui-bg-green">未使用</span>'];
        return $status[$value];
    }
    public function discountinfo($quanma)
    {
        $res = $this->where(['quanma' => $quanma])->where(['status' => 0])->find();
        return $res['discount'];
    }
    public function updateDiscount($quanma, $ddid)
    {
        $res = $this->where(['quanma' => $quanma])->update(['status' => 1, 'ddid' => $ddid]);
    }
    public function Discountdata($page, $limit, $keyword)
    {
        if ($keyword != '') {
            $Discountdata = $this->where(['quanma' => $keyword])->order('id desc')->page($page, $limit)->select();
        } else {
            $Discountdata = $this->order('id desc')->page($page, $limit)->select();
        }
        return $Discountdata;
    }
    public function DiscountCount($keyword)
    {
        if ($keyword != '') {
            $res = $this->where(['quanma' => $keyword])->select();
        } else {
            $res = $this->select();
        }
        return count($res);
    }
    public function delDiscountCount($id)
    {
        $res = $this->where(['id' => $id])->delete();
        if ($res) {
            return '1';
        } else {
            return '2';
        }
    }
    public function addDiscount($data)
    {
        for ($i = 0; $i < $data['shuliang']; $i++) {
            $quanma = date("YmdHis") . mt_rand(10000, 99999);
            $quanma = substr(md5($quanma), 8, 16);
            $res = $this->insert(['quanma' => $quanma, 'status' => 0, 'discount' => $data['discount']]);
        }
        return $res;
    }
}