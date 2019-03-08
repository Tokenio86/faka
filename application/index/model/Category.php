<?php

namespace app\index\model;

use think\Model;
class Category extends Model
{
    protected $pk = 'id';
    protected $table = 'categories';
    public function getcat()
    {
        $res = $this->order('sort asc')->select();
        return $res;
    }
    public function getcatname($abridge)
    {
        $res = $this->where(['abridge' => $abridge])->find();
        return $res;
    }
}