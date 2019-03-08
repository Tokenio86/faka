<?php

namespace app\admin\model;

use think\Model;
class Links extends Model
{
    protected $pk = 'id';
    protected $table = 'links';
    public function addlinks($data)
    {
        $res = $this->insert(['sitename' => $data['sitename'], 'siteurl' => $data['siteurl'], 'sort' => $data['sort']]);
        return ['valid' => 1, 'msg' => '添加成功'];
    }
    public function editlink($data)
    {
        $res = $this->where(['id' => $data['id']])->update(['sitename' => $data['sitename'], 'siteurl' => $data['siteurl'], 'sort' => $data['sort']]);
        return ['valid' => 1, 'msg' => '修改成功'];
    }
    public function getlink($page, $limit, $keyword)
    {
        if ($keyword != '') {
            $res = $this->where(['id' => $keyword])->whereOr(['sitename' => $keyword])->page($page, $limit)->select();
        } else {
            $res = $this->page($page, $limit)->select();
        }
        return $res;
    }
    public function linkcount($keyword)
    {
        if ($keyword != '') {
            $res = $this->where(['id' => $keyword])->whereOr(['sitename' => $keyword])->select();
        } else {
            $res = $this->select();
        }
        return count($res);
    }
    public function dellink($id)
    {
        $res = $this->where(['id' => $id])->delete();
        return $res;
    }
    public function findlink($id)
    {
        $res = $this->where(['id' => $id])->find();
        return $res;
    }
}