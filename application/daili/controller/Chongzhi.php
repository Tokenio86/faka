<?php

namespace app\daili\controller;

use app\admin\model\Option as OptionModel;
use app\daili\model\Daili as Dailimodel;
use app\index\model\Notify as NotifyModel;
use app\admin\model\Recharge as RechargeModel;
class Chongzhi
{
    public function notifychongzhi()
    {
        $siteinfo = (new OptionModel())->getinfo();
        if ($siteinfo['paytype'] == 1) {
            ksort($_POST);
            reset($_POST);
            $codepay_key = $siteinfo['mzfkey'];
            $sign = '';
            foreach ($_POST as $key => $val) {
                if ($val == '' || $key == 'sign') {
                    continue;
                }
                if ($sign) {
                    $sign .= '&';
                }
                $sign .= "{$key}={$val}";
            }
            if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) {
                exit('fail');
            } else {
                $ddid = $_POST['param'];
                $money = $_POST['money'];
                $czinfo = (new RechargeModel())->rechargeinfo($ddid);
                if ($czinfo) {
                    $chongzhi = (new Dailimodel())->czmoney($czinfo['dailiid'], $money);
                    $updatacz = (new RechargeModel())->updatacz($ddid);
                    if ($chongzhi) {
                        return 'ok';
                    } else {
                        return 'no';
                    }
                } else {
                    return 'no';
                }
            }
        } else {
            if ($siteinfo['paytype'] == 0) {
                $notify_url = $siteinfo['siteurl'] . 'daili.php/chongzhi/notifychongzhi';
                $return_url = $siteinfo['siteurl'] . 'daili.php/index/returnchongzhi';
                $siteinfo = (new OptionModel())->getinfo();
                $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
                $alipayNotify = new NotifyModel($alipayinfo);
                $verify_result = $alipayNotify->verifyNotify();
                if ($verify_result) {
                    $ddid = $_GET['out_trade_no'];
                    $money = $_GET['money'];
                    if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                        $czinfo = (new RechargeModel())->rechargeinfo($ddid);
                        if ($czinfo) {
                            $updatacz = (new RechargeModel())->updatacz($ddid);
                            $chongzhi = (new Dailimodel())->czmoney($czinfo['dailiid'], $money);
                            echo 'success';
                        } else {
                            echo 'fail';
                        }
                    }
                } else {
                    echo 'fail';
                }
            }
        }
    }
}