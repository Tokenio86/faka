<?php

namespace app\admin\model;

use think\Model;
class Category extends Model
{
    protected $pk = 'id';
    protected $table = 'categories';
    public function category($page, $limit, $keyword)
    {
        if ($keyword != '') {
            $catinfo = $this->where('name', 'like', '%' . $keyword . '%')->whereOr('abridge', 'like', '%' . $keyword . '%')->page($page, $limit)->select();
        } else {
            $catinfo = $this->page($page, $limit)->select();
        }
        return $catinfo;
    }
    public function categorycount($keyword)
    {
        if ($keyword != '') {
            $catinfo = $this->where('name', 'like', '%' . $keyword . '%')->whereOr('abridge', 'like', '%' . $keyword . '%')->select();
        } else {
            $catinfo = $this->select();
        }
        return count($catinfo);
    }
    public function addcat($data)
    {
        if (!$data['name'] || !$data['abridge']) {
            return ['valid' => 0, 'msg' => '分类名或分类简称未填写'];
        }
        $res = $this->insert(['name' => $data['name'], 'abridge' => $data['abridge'], 'image' => $data['image'], 'sort' => $data['sort']]);
        if ($res) {
            return ['valid' => 1, 'msg' => '添加成功'];
        } else {
            return ['valid' => 0, 'msg' => '添加失败'];
        }
    }
    public function delcat($id)
    {
        $res = $this->where(['id' => $id])->delete();
        if ($res) {
            return '1';
        } else {
            return '2';
        }
    }
    public function editcat($id)
    {
        $res = $this->where(['id' => $id])->find();
        return $res;
    }
    public function posteditcat($data)
    {
        $res = $this->where(['id' => $data['id']])->update(['name' => $data['name'], 'abridge' => $data['abridge'], 'image' => $data['image'], 'sort' => $data['sort']]);
        if ($res) {
            return ['valid' => 1, 'msg' => '修改成功'];
        } else {
            return ['valid' => 0, 'msg' => '修改失败'];
        }
    }
    public function getcat()
    {
        $res = $this->select();
        return $res;
    }
}