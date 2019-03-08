<?php

namespace app\daili\model;

use think\Model;
class Category extends Model
{
    protected $pk = 'id';
    protected $table = 'categories';
    public function getcat()
    {
        $res = $this->select();
        return $res;
    }
}