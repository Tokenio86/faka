<?php

namespace app\admin\model;

use think\Model;
class Option extends Model
{
    protected $pk = 'id';
    protected $table = 'options';
    public function getinfo()
    {
        $res = $this->where(['id' => 1])->find();
        return $res;
    }
}