<?php

namespace app\index\model;

use think\Model;
class Link extends Model
{
    protected $pk = 'id';
    protected $table = 'links';
    public function getlink()
    {
        $res = $this->order('sort asc')->select();
        return $res;
    }
}