<?php

namespace app\daili\controller;

use app\daili\model\Goods as Goodmodel;
use app\daili\model\Daili as Dailimodel;
use app\daili\model\Order as Ordermodel;
use app\admin\model\Option as OptionModel;
use app\daili\model\Category as CategoryModel;
use app\daili\model\Kami as KamiModel;
use app\daili\model\Alipaysubmit as SubmitModel;
use app\index\model\Notify as NotifyModel;
use app\admin\model\Recharge as RechargeModel;
use think\Db;
use think\Session;
class Index extends Common
{
    function _initialize()
    {
        $res = (new OptionModel())->getinfo();
        $this->assign('siteinfo', $res);
    }
    public function index()
    {
        $dailifinfo = (new Dailimodel())->dailiinfo(session('dailiid'));
        $daili_xiaofei = (new Ordermodel())->tkjl_count(session('dailiid'));
        $dl_xf = 0;
        $dl_xl = 0;
        foreach ($daili_xiaofei as $value) {
            if ($value['status'] == 1) {
                $dl_xf = $dl_xf + $value['price'];
                $dl_xl = $dl_xl + $value['count'];
            }
        }
        $this->assign('dailifinfo', $dailifinfo);
        $this->assign('dl_xf', $dl_xf);
        $this->assign('dl_xl', $dl_xl);
        return view();
    }
    public function catdata()
    {
        $cat = (new CategoryModel())->getcat();
        return json($cat);
    }
    public function gooddata($abridge)
    {
        $good = (new Goodmodel())->getgood($abridge);
        return json($good);
    }
    public function goodinfo($id)
    {
        $good = (new Goodmodel())->goodinfo($id);
        $kucun = (new KamiModel())->getkami($id);
        $goodinfo = ['id' => $good['id'], 'price' => $good['dailiprice'], 'kucun' => count($kucun), 'yuanjia' => $good['price']];
        return json($goodinfo);
    }
    public function pay()
    {
        $gdinfo = (new Goodmodel())->goodinfo($_POST['good']);
        $tatal_price = $gdinfo['dailiprice'] * $_POST['count'];
        $dlinfo = (new Dailimodel())->dailiinfo(session('dailiid'));
        if ($tatal_price > $dlinfo['money']) {
            $this->error('您的余额不足，请充值');
            exit;
        } else {
            $moneyinfo = (new Dailimodel())->money(session('dailiid'), $dlinfo['money'] - $tatal_price);
            $time = date("Y-m-d H:i:s");
            $orderid = 'D' . date("YmdHis") . mt_rand(1000, 9999);
            $orderinfo = ['ddid' => $orderid, 'goodid' => $_POST['good'], 'name' => $gdinfo['name'], 'price' => $tatal_price, 'count' => $_POST['count'], 'email' => $dlinfo['email'], 'type' => 'daili', 'status' => 1, 'time' => $time, 'daili' => session('dailiid')];
            $order = (new Ordermodel())->addorder($orderinfo);
            if ($order) {
                $kminfo = (new KamiModel())->getkami($_POST['good']);
                for ($i = 0; $i < $_POST['count']; $i++) {
                    $kmid = $kminfo[$i]['id'];
                    $km_update = (new KamiModel())->updatekami($kmid, $orderid);
                }
                $km_sales = (new Goodmodel())->update_sales($_POST['good'], $_POST['count']);
                header("Location:/daili.php/index/returninfo.html?ddid={$orderid}");
                exit;
            }
        }
    }
    public function returninfo($ddid)
    {
        $order = (new Ordermodel())->orderinfo($ddid);
        $km = (new KamiModel())->orderkm($ddid);
        $this->assign('order', $order);
        $this->assign('km', $km);
        return view();
    }
    public function tkjl($page)
    {
        $orderinfo = (new Ordermodel())->tkjl(session('dailiid'), $page);
        $tkjl_count = (new Ordermodel())->tkjl_count(session('dailiid'));
        $this->assign('orderinfo', $orderinfo);
        $this->assign('tkjl_count', count($tkjl_count));
        return view();
    }
    public function kminfo($ddid)
    {
        $kminfo = (new KamiModel())->orderkm($ddid);
        return json($kminfo);
    }
    public function loginout()
    {
        Session::clear();
        $this->success('退出成功', '/daili.php/index/index');
        exit;
    }
    public function xgmm()
    {
        if (request()->ispost()) {
            $res = (new Dailimodel())->dailiinfo(session('dailiid'));
            if (md5(md5($_POST['oldpass'])) != $res['password']) {
                $this->error('原密码错误请重新输入');
            } else {
                if (!$_POST['newpass']) {
                    $this->error('新密码不得为空');
                } else {
                    $res2 = (new Dailimodel())->updatedaili(session('dailiid'), $_POST['newpass']);
                    if ($res2) {
                        $this->success('修改成功', '/daili.php/index/xgmm.html');
                        exit;
                    } else {
                        $this->error('修改失败');
                    }
                }
            }
        }
        return view();
    }
    public function chongzhi()
    {
        if (request()->ispost()) {
            $siteinfo = (new OptionModel())->getinfo();
            $paytype = $_POST['paytype'];
            $dlid = $_POST['dailiid'];
            $money = $_POST['money'];
            $ddid = 'CZ' . date("YmdHis") . mt_rand(1000, 9999);
            $recharge = (new RechargeModel())->insertcz($dlid, $money, $ddid, $paytype);
            if ($siteinfo['paytype'] == 1) {
                $codepay_id = $siteinfo['mzfid'];
                $codepay_key = $siteinfo['mzfkey'];
                $ip = $_SERVER["REMOTE_ADDR"];
                $data = array("id" => $codepay_id, "pay_id" => $ip, "type" => $paytype, "price" => $money, "param" => $ddid, "notify_url" => $siteinfo['siteurl'] . 'daili.php/chongzhi/notifychongzhi', "return_url" => $siteinfo['siteurl'] . 'daili.php/index/returnchongzhi');
                ksort($data);
                reset($data);
                $sign = '';
                $urls = '';
                foreach ($data as $key => $val) {
                    if ($val == '' || $key == 'sign') {
                        continue;
                    }
                    if ($sign != '') {
                        $sign .= "&";
                        $urls .= "&";
                    }
                    $sign .= "{$key}={$val}";
                    $urls .= "{$key}=" . urlencode($val);
                }
                $query = $urls . '&sign=' . md5($sign . $codepay_key);
                $url = "http://api2.fateqq.com:52888/creat_order/?{$query}";
                header("Location:{$url}");
                exit;
            } else {
                if ($siteinfo['paytype'] == 0) {
                    $notify_url = $siteinfo['siteurl'] . 'daili.php/chongzhi/notifychongzhi';
                    $return_url = $siteinfo['siteurl'] . 'daili.php/index/returnchongzhi';
                    switch ($paytype) {
                        case '1':
                            $paytype = 'alipay';
                            break;
                        case '2':
                            $paytype = 'qqpay';
                            break;
                        case '3':
                            $paytype = 'wxpay';
                            break;
                        default:
                            $paytype = 'alipay';
                            break;
                    }
                    $parameter = array("pid" => trim($siteinfo['partner']), "type" => $paytype, "notify_url" => $notify_url, "return_url" => $return_url, "out_trade_no" => $ddid, "name" => '代理余额充值', "money" => $money, "sitename" => $siteinfo['sitename']);
                    $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
                    $alipaySubmit = new SubmitModel($alipayinfo);
                    $html_text = $alipaySubmit->buildRequestForm($parameter);
                    echo $html_text;
                }
            }
        }
        return view();
    }
    public function returnchongzhi()
    {
        $siteinfo = (new OptionModel())->getinfo();
        if ($siteinfo['paytype'] == 1) {
            ksort($_GET);
            reset($_GET);
            $codepay_key = $siteinfo['mzfkey'];
            $sign = '';
            foreach ($_GET as $key => $val) {
                if ($val == '' || $key == 'sign') {
                    continue;
                }
                if ($sign) {
                    $sign .= '&';
                }
                $sign .= "{$key}={$val}";
            }
            if (!$_GET['pay_no'] || md5($sign . $codepay_key) != $_GET['sign']) {
                exit('验证失败');
            } else {
                $money = $_GET['money'];
                $this->success("成功充值{$money}元", '/daili.php/index/index.html');
                exit;
            }
        } else {
            if ($siteinfo['paytype'] == 0) {
                $notify_url = $siteinfo['siteurl'] . 'daili.php/chongzhi/notifychongzhi';
                $return_url = $siteinfo['siteurl'] . 'daili.php/index/returnchongzhi';
                $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
                $alipayNotify = new NotifyModel($alipayinfo);
                $verify_result = $alipayNotify->verifyReturn();
                if ($verify_result) {
                    $money = $_GET['money'];
                    if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                    } else {
                        echo "trade_status=" . $_GET['trade_status'];
                    }
                    $this->success("成功充值{$money}元", '/daili.php/index/index.html');
                    exit;
                } else {
                    echo 'fail';
                }
            }
        }
    }
}