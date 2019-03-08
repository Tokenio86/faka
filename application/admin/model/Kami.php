<?php

namespace app\admin\model;

use think\Model;
class Kami extends Model
{
    protected $pk = 'id';
    protected $table = 'kami';
    public function getStatusAttr($value)
    {
        $status = [1 => '<span class="layui-badge">已售出</span>', 0 => '<span class="layui-badge layui-bg-green">库存中</span>'];
        return $status[$value];
    }
    public function kamicount($id, $status)
    {
        if ($id == '' && $status == '') {
            $res = $this->select();
        } else {
            if ($status == '2') {
                if ($id == 'all') {
                    $res = $this->select();
                } else {
                    $res = $this->where(['goodid' => $id])->select();
                }
            } elseif ($status == '0') {
                if ($id == 'all') {
                    $res = $this->where(['status' => 0])->select();
                } else {
                    $res = $this->where(['goodid' => $id])->where(['status' => 0])->select();
                }
            } else {
                if ($id == 'all') {
                    $res = $this->where(['status' => 1])->select();
                } else {
                    $res = $this->where(['goodid' => $id])->where(['status' => 1])->select();
                }
            }
        }
        return count($res);
    }
    public function kami($page, $limit, $id, $status)
    {
        if ($id == '' && $status == '') {
            $res = $this->page($page, $limit)->select();
        } else {
            if ($status == '2') {
                if ($id == 'all') {
                    $res = $this->page($page, $limit)->select();
                } else {
                    $res = $this->where(['goodid' => $id])->page($page, $limit)->select();
                }
            } elseif ($status == '0') {
                if ($id == 'all') {
                    $res = $this->where(['status' => 0])->page($page, $limit)->select();
                } else {
                    $res = $this->where(['goodid' => $id])->where(['status' => 0])->page($page, $limit)->select();
                }
            } else {
                if ($id == 'all') {
                    $res = $this->where(['status' => 1])->page($page, $limit)->select();
                } else {
                    $res = $this->where(['goodid' => $id])->where(['status' => 1])->page($page, $limit)->select();
                }
            }
        }
        return $res;
    }
    public function addkami($data)
    {
        $data['km'] = explode("\r\n", $data['km']);
        if (isset($data['kmcq']) && $data['kmcq'] == 'on') {
            $data['km'] = array_unique($data['km']);
        }
        for ($j = 0; $j < count($data['km']); $j++) {
            $data['km'][$j] = explode(" ", $data['km'][$j]);
            if (count($data['km'][$j]) == 1) {
                if (!$data['km'][$j][0]) {
                    continue;
                }
                $res = $this->insert(['goodid' => $data['goodid'], 'mima' => $data['km'][$j][0], 'status' => 0]);
            } else {
                if (!$data['km'][$j][0] && !$data['km'][$j][1]) {
                    continue;
                }
                $res = $this->insert(['goodid' => $data['goodid'], 'kahao' => $data['km'][$j][0], 'mima' => $data['km'][$j][1], 'status' => 0]);
            }
        }
        return ['valid' => 1, 'msg' => '添加成功'];
    }
    public function delkami($id)
    {
        $res = $this->where(['id' => $id])->delete();
        if ($res) {
            return '0';
        } else {
            return '1';
        }
    }
    public function delsold($id)
    {
        $res = $this->where(['goodid' => $id])->where(['status' => 1])->delete();
        if ($res) {
            return '已清空';
        } else {
            return '清空失败';
        }
    }
    public function delkm($id)
    {
        $res = $this->where(['goodid' => $id])->delete();
        if ($res) {
            return '1';
        } else {
            return '2';
        }
    }
    public function pldelekm($data)
    {
        foreach ($data as $val) {
            $this->where(['id' => $val['id']])->delete();
        }
        return '1';
    }
    public function getkckm()
    {
        $res = $this->where(['status' => 0])->select();
        return $res;
    }
    public function kmtatal($id)
    {
        $res = $this->where(['goodid' => $id])->select();
        return $res;
    }
    public function find_km($id)
    {
        $res = $this->where(['goodid' => $id])->find();
        return $res;
    }
    public function find_km_id($id)
    {
        $res = $this->where(['id' => $id])->find();
        return $res;
    }
    public function ddiddelkm($ddid)
    {
        $this->where(['ddid' => $ddid])->delete();
    }
    public function export($id, $status)
    {
        if ($id == 'all' && $status == '2') {
            $res = $this->select();
        } elseif ($id != 'all' && $status == '2') {
            $res = $this->where(['goodid' => $id])->select();
        } elseif ($id == 'all' && $status != '2') {
            $res = $this->where(['status' => $status])->select();
        } elseif ($id != 'all' && $status != '2') {
            $res = $this->where(['goodid' => $id])->where(['status' => $status])->select();
        }
        return $res;
    }
}