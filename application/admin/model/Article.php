<?php

namespace app\admin\model;

use think\Model;
class Article extends Model
{
    protected $pk = 'id';
    protected $table = 'article';
    public function artdata($page, $limit, $keyword)
    {
        if ($keyword != '') {
            $res = $this->where('title', 'like', '%' . $keyword . '%')->whereOr('content', 'like', '%' . $keyword . '%')->page($page, $limit)->select();
        } else {
            $res = $this->page($page, $limit)->select();
        }
        return $res;
    }
    public function artcount($keyword)
    {
        if ($keyword != '') {
            $res = $this->where('title', 'like', '%' . $keyword . '%')->whereOr('content', 'like', '%' . $keyword . '%')->select();
        } else {
            $res = $this->select();
        }
        return count($res);
    }
    public function addart($data)
    {
        if (!$data['content'] || !$data['title']) {
            return ['valid' => 0, 'msg' => '标题或内容不得为空'];
        }
        $res = $this->insert(['title' => $data['title'], 'time' => date("Y-m-d H:i:s"), 'content' => $data['content']]);
        if ($res) {
            return ['valid' => 1, 'msg' => '添加成功'];
        } else {
            return ['valid' => 0, 'msg' => '添加失败'];
        }
    }
    public function delart($id)
    {
        $res = $this->where(['id' => $id])->delete();
        if ($res) {
            return '删除成功';
        } else {
            return '删除失败';
        }
    }
    public function getart($id)
    {
        $res = $this->where(['id' => $id])->find();
        return $res;
    }
    public function editart($data)
    {
        $res = $this->where(['id' => $data['id']])->update(['title' => $data['title'], 'content' => $data['content']]);
        if ($res) {
            return ['valid' => 1, 'msg' => '修改成功'];
        } else {
            return ['valid' => 0, 'msg' => '修改失败'];
        }
    }
}