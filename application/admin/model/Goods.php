<?php

namespace app\admin\model;

use think\Model;
class Goods extends Model
{
    protected $pk = 'id';
    protected $table = 'goods';
    public function getStatusAttr($value)
    {
        $status = [0 => '出售中', 1 => '已下架'];
        return $status[$value];
    }
    public function goods($page, $limit, $keyword)
    {
        if ($keyword != '') {
            $res = $this->where('name', 'like', '%' . $keyword . '%')->page($page, $limit)->select();
        } else {
            $res = $this->page($page, $limit)->select();
        }
        return $res;
    }
    public function goodcount($keyword)
    {
        if ($keyword != '') {
            $res = $this->where('name', 'like', '%' . $keyword . '%')->select();
        } else {
            $res = $this->select();
        }
        return count($res);
    }
    public function addgood($data)
    {
        if (floatval($data['price']) != 0) {
            if ($data['image'] == '') {
                $data['image'] = "__STATIC__/images/noimg.jpg";
            }
            $res = $this->insert(['name' => $data['name'], 'abridge' => $data['abridge'], 'price' => $data['price'], 'dailiprice' => $data['dailiprice'], 'images' => $data['image'], 'sort' => $data['sort'], 'Introduction' => $data['Introduction'], 'status' => $data['status'], 'mansl' => $data['mansl'], 'yhprice' => $data['yhprice']]);
            if ($res) {
                return ['valid' => 1, 'msg' => '添加成功'];
            } else {
                return ['valid' => 0, 'msg' => '添加失败'];
            }
        } else {
            return ['valid' => 0, 'msg' => '价格设置不正确'];
        }
    }
    public function delgood($id)
    {
        $res = $this->where(['id' => $id])->delete();
        if ($res) {
            return '1';
        } else {
            return '2';
        }
    }
    public function editgood($id)
    {
        $res = $this->where(['id' => $id])->find();
        return $res;
    }
    public function posteditgood($data)
    {
        $res = $this->where(['id' => $data['id']])->update(['name' => $data['name'], 'abridge' => $data['abridge'], 'images' => $data['image'], 'dailiprice' => $data['dailiprice'], 'price' => $data['price'], 'sort' => $data['sort'], 'Introduction' => $data['Introduction'], 'status' => $data['status'], 'mansl' => $data['mansl'], 'yhprice' => $data['yhprice']]);
        if ($res) {
            return ['valid' => 1, 'msg' => '修改成功'];
        } else {
            return ['valid' => 0, 'msg' => '修改失败'];
        }
    }
    public function getgoods()
    {
        $res = $this->select();
        return $res;
    }
    public function delcat_good($abridge)
    {
        $res = $this->where(['abridge' => $abridge])->find();
        return $res;
    }
    public function find_good($id)
    {
        $res = $this->where(['id' => $id])->find();
        return $res;
    }
}