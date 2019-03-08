<?php

namespace app\index\model;

use think\Model;
use app\index\model\Km as KmModel;
class Item extends Model
{
    protected $pk = 'id';
    protected $table = 'goods';
    public function getSalesAttr($value)
    {
        if ($value < 10000) {
            return $value;
        } else {
            $value = $value / 10000;
            $value = round($value, 2) . '万';
            return $value;
        }
    }
    public function item($abridge)
    {
        $goodinfo = $this->where(['abridge' => $abridge])->where(['status' => 0])->order('sort asc')->select();
        return $goodinfo;
    }
    public function itemall()
    {
        $goodinfo = $this->where(['status' => 0])->order('sort asc')->select();
        $data = [];
        for ($i = 0; $i < count($goodinfo); $i++) {
            $kucun = (new KmModel())->kucun_km($goodinfo[$i]['id']);
            if (count($kucun) < 10000) {
                $kucun = count($kucun);
            } else {
                $kucun = count($kucun) / 10000;
                $kucun = round($kucun, 2) . '万';
            }
            $data[$i]['id'] = $goodinfo[$i]['id'];
            $data[$i]['name'] = $goodinfo[$i]['name'];
            $data[$i]['images'] = $goodinfo[$i]['images'];
            $data[$i]['sales'] = $goodinfo[$i]['sales'];
            $data[$i]['price'] = $goodinfo[$i]['price'];
            $data[$i]['abridge'] = $goodinfo[$i]['abridge'];
            $data[$i]['kucun'] = $kucun;
        }
        return $data;
    }
    public function getgoodinfo($id)
    {
        $res = $this->where(['id' => $id])->find();
        return $res;
    }
    public function update_sales($goodid, $sales)
    {
        $res = $this->where(['id' => $goodid])->find();
        $newsales = intval($res->getData('sales')) + $sales;
        $res2 = $this->where(['id' => $goodid])->update(['sales' => $newsales]);
    }
}