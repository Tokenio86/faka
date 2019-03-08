<?php

namespace app\admin\model;

use think\Model;
class Option extends Model
{
    protected $pk = 'id';
    protected $table = 'options';
    public function option($data)
    {
        $optioninfo = $this->where(['id' => 1])->where(['adminpass' => md5(md5($data['oldpwd']))])->find();
        if ($optioninfo == null) {
            return ['valid' => 0, 'msg' => '原密码输入不正确'];
        } else {
            if ($data['newpwd'] != $data['newpwd1']) {
                return ['valid' => 0, 'msg' => '两次密码输入不一致'];
            } else {
                $this->where(['id' => 1])->update(['adminpass' => md5(md5($data['newpwd']))]);
                return ['valid' => 1, 'msg' => '密码修改成功'];
            }
        }
    }
    public function getinfo()
    {
        $res = $this->where(['id' => 1])->find();
        return $res;
    }
    public function updateinfo($data)
    {
        $res = $this->where(['id' => 1])->update(['sitename' => $data['sitename'], 'siteurl' => $data['siteurl'], 'qq' => $data['qq'], 'logoimg' => $data['logoimg'], 'alipay' => $data['alipay'], 'wxpay' => $data['wxpay'], 'qqpay' => $data['qqpay'], 'mailon' => $data['mailon'], 'emailhost' => $data['emailhost'], 'emailport' => $data['emailport'], 'emailuser' => $data['emailuser'], 'emailpass' => $data['emailpass'], 'tongji' => $data['tongji'], 'gonggao' => $data['gonggao'], 'goodgg' => $data['goodgg'], 'indexmode' => $data['indexmode'], 'opdl' => $data['opdl'], 'keywords' => $data['keywords'], 'description' => $data['description'], 'maxsl' => $data['maxsl']]);
        if ($res) {
            return '1';
        } else {
            return '0';
        }
    }
    public function updatepay($data)
    {
        $res = $this->where(['id' => 1])->update(['partner' => $data['partner'], 'key' => $data['key']]);
        if ($res) {
            return '1';
        } else {
            return '0';
        }
    }
}