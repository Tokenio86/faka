<?php

namespace app\daili\controller;

use think\Controller;
use app\daili\model\Login as LoginModel;
use app\admin\model\Option as OptionModel;
use app\daili\model\Alipaysubmit as SubmitModel;
use app\index\model\Notify as NotifyModel;
class Login extends Controller
{
    function _initialize()
    {
        $res = (new OptionModel())->getinfo();
        $this->assign('siteinfo', $res);
    }
    public function login()
    {
        if (request()->ispost()) {
            $res = (new LoginModel())->login($_POST);
            if ($res['valid']) {
                $this->success($res['msg'], '/daili.php/index/index');
                exit;
            } else {
                $this->error($res['msg']);
                exit;
            }
        }
        return view();
    }
    public function reg()
    {
        if (request()->ispost()) {
            $username = xss_clean($_POST['username']);
            $email = xss_clean($_POST['email']);
            $paytype = $_POST['paytype'];
            if ($username == "" || $email == "") {
                $this->error('输入不规范,请检查');
                exit;
            } else {
                $finddl = (new LoginModel())->finddl($username);
                if ($finddl) {
                    $this->error('代理已存在');
                    exit;
                } else {
                    $siteinfo = (new OptionModel())->getinfo();
                    $un_pw = $username;
                    $email = $email;
                    if ($siteinfo['paytype'] == 1) {
                        $codepay_id = $siteinfo['mzfid'];
                        $codepay_key = $siteinfo['mzfkey'];
                        $ip = $_SERVER["REMOTE_ADDR"];
                        $data = array("id" => $codepay_id, "pay_id" => $ip, "type" => $paytype, "price" => $siteinfo['opdl'], "param" => $un_pw . "--" . $email, "notify_url" => $siteinfo['siteurl'] . 'daili.php/login/regnotify', "return_url" => $siteinfo['siteurl'] . 'daili.php/login/regreturn');
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
                            $notify_url = $siteinfo['siteurl'] . 'daili.php/login/regnotify';
                            $return_url = $siteinfo['siteurl'] . 'daili.php/login/regreturn';
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
                            $parameter = array("pid" => trim($siteinfo['partner']), "type" => $paytype, "notify_url" => $notify_url, "return_url" => $return_url, "out_trade_no" => $un_pw . "--" . $email, "name" => '代理账户开通', "money" => $siteinfo['opdl'], "sitename" => $siteinfo['sitename']);
                            $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
                            $alipaySubmit = new SubmitModel($alipayinfo);
                            $html_text = $alipaySubmit->buildRequestForm($parameter);
                            echo $html_text;
                        }
                    }
                }
            }
        }
        return view();
    }
    public function regnotify()
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
                $dlinfo = explode("--", $_POST['param']);
                $username = $dlinfo[0];
                $email = $dlinfo[1] . '@qq.com';
                $regdl = (new LoginModel())->reg($username, $email);
                if ($regdl) {
                    exit('success');
                } else {
                    exit('no');
                }
            }
        } else {
            if ($siteinfo['paytype'] == 0) {
                $notify_url = $siteinfo['siteurl'] . 'daili.php/login/regnotify';
                $return_url = $siteinfo['siteurl'] . 'daili.php/login/regreturn';
                $siteinfo = (new OptionModel())->getinfo();
                $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
                $alipayNotify = new NotifyModel($alipayinfo);
                $verify_result = $alipayNotify->verifyNotify();
                if ($verify_result) {
                    $dailiid = $_GET['out_trade_no'];
                    $money = $_GET['money'];
                    if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                        $dlinfo = explode("--", $_GET['out_trade_no']);
                        $username = $dlinfo[0];
                        $email = $dlinfo[1] . '@qq.com';
                        $regdl = (new LoginModel())->reg($username, $email);
                    }
                    echo 'success';
                } else {
                    echo 'fail';
                }
            }
        }
    }
    public function regreturn()
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
                exit('fail');
            } else {
                $this->success('开通成功,正在返回登录页面', 'daili/index/index');
                exit;
            }
        } else {
            if ($siteinfo['paytype'] == 0) {
                $notify_url = $siteinfo['siteurl'] . 'daili/login/regnotify';
                $return_url = $siteinfo['siteurl'] . 'daili/login/regreturn';
                $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
                $alipayNotify = new NotifyModel($alipayinfo);
                $verify_result = $alipayNotify->verifyReturn();
                if ($verify_result) {
                    $money = $_GET['money'];
                    if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                    } else {
                        echo "trade_status=" . $_GET['trade_status'];
                    }
                    $this->success('开通成功,正在返回登录页面', 'daili/index/index');
                    exit;
                } else {
                    echo 'fail';
                }
            }
        }
    }
}