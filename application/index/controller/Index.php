<?php

namespace app\index\controller;

use think\Db;
use think\Controller;
use think\View;
use app\index\model\Item as ItemModel;
use app\index\model\Alipaysubmit as SubmitModel;
use app\index\model\Notify as NotifyModel;
use app\index\model\Search as SearchModel;
use app\index\model\Km as KmModel;
use app\index\model\Order as OrderModel;
use app\index\model\Option as OptionModel;
use app\index\model\Category as CategoryModel;
use app\index\model\Link as LinkModel;
use app\admin\model\Article as ArticleModel;
use app\index\model\Discount as DiscountModel;
class Index extends Controller
{
    function _initialize()
    {

        $res = (new OptionModel())->getinfo();
        $this->assign('siteinfo', $res);
    }
    public function index()
    {
        $res2 = (new CategoryModel())->getcat();
        $res = (new ItemModel())->itemall();
        $link = (new LinkModel())->getlink();
        $this->assign('res', $res);
        $this->assign('res2', $res2);
        $this->assign('link', $link);
        return view();
    }
    public function item($abridge)
    {
        $abridge = xss_clean($abridge);
        $res = (new ItemModel())->item($abridge);
        $this->assign('res', $res);
        $catname = (new CategoryModel())->getcatname($abridge);
        $this->assign('title', $catname['name']);
        return view();
    }
    public function trade($id)
    {
        $goodinfo = (new ItemModel())->getgoodinfo($id);
        if ($goodinfo['status'] == 1) {
            $this->error('此商品已下架');
            exit;
        }
        $kucun = (new KmModel())->kucun_km($id);
        $this->assign('goodinfo', $goodinfo);
        $this->assign('kucun', count($kucun));
        return view();
    }
    public function pay()
    {
        $siteinfo = (new OptionModel())->getinfo();
        $ddid = date("YmdHis") . mt_rand(10000, 99999);
        $tkcount = xss_clean($_POST['count']);
        $email = xss_clean($_POST['email']);
        if (preg_match('/[\\x{4e00}-\\x{9fa5}]/u', $email) > 0 || !$email) {
            $this->error('联系方式不得有中文或空格');
            exit;
        }
        if ($siteinfo['mailon'] == 1) {
            if (!preg_match("/([\\w\\-]+\\@[\\w\\-]+\\.[\\w\\-]+)/", $email)) {
                $this->error('无效的email邮箱');
                exit;
            }
        }
        if ($siteinfo['maxsl'] != 0) {
            if ($tkcount > $siteinfo['maxsl']) {
                $this->error('超过单笔限制最大购买数量' . $siteinfo['maxsl'] . '件');
                exit;
            }
        }
        $pay_kmkucun = (new KmModel())->paykucun_km($_POST['goodid']);
        if ($tkcount > $pay_kmkucun) {
            $this->error('库存不足');
            exit;
        }
        $out_trade_no = $ddid;
        $res = (new ItemModel())->getgoodinfo($_POST['goodid']);
        if ($tkcount >= $res['mansl'] && $res['mansl'] && $res['yhprice']) {
            $res['price'] = $res['yhprice'];
        }
        $name = $res['name'];
        $money = $res['price'] * $tkcount;
        $quanma = xss_clean($_POST['input-yhq']);
        if ($quanma) {
            $yhq = (new DiscountModel())->discountinfo($quanma);
            if ($yhq) {
                $money = round($money * $yhq, 2);
                $updateyhq = (new DiscountModel())->updateDiscount($quanma, $out_trade_no);
            } else {
                $this->error('优惠券不存在!');
                exit;
            }
        }
        $type = $_POST['type'];
        $res = (new OrderModel())->insertorder(['ddid' => $out_trade_no, 'name' => $name, 'price' => $money, 'type' => $type, 'status' => 0, 'time' => date("Y-m-d H:i:s"), 'goodid' => $_POST['goodid'], 'email' => $email, 'count' => $_POST['count']]);
        if ($siteinfo['paytype'] == 1) {
            $codepay_id = $siteinfo['mzfid'];
            $token = $siteinfo['mzftoken'];
            $codepay_key = $siteinfo['mzfkey'];
            $ip = $_SERVER["REMOTE_ADDR"];
            switch ($type) {
                case 'alipay':
                    $paytype = 1;
                    break;
                case 'qqpay':
                    $paytype = 2;
                    break;
                case 'wxpay':
                    $paytype = 3;
                    break;
                default:
                    $paytype = 1;
                    break;
            }
            $data = array("id" => $codepay_id, "pay_id" => $ip, "type" => $paytype, "price" => $money, "param" => $out_trade_no, "notify_url" => $siteinfo['siteurl'] . 'index/notify', "return_url" => $siteinfo['siteurl'] . 'index/returninfo');
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
            header("Location:http://api2.fateqq.com:52888/creat_order/?{$query}");
            exit;
        } else {
            $notify_url = $siteinfo['siteurl'] . 'index/notify';
            $return_url = $siteinfo['siteurl'] . 'index/returninfo';
            $parameter = array("pid" => trim($siteinfo['partner']), "type" => $type, "notify_url" => $notify_url, "return_url" => $return_url, "out_trade_no" => $out_trade_no, "name" => $name, "money" => $money, "sitename" => $siteinfo['sitename']);
            $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
            $alipaySubmit = new SubmitModel($alipayinfo);
            $html_text = $alipaySubmit->buildRequestForm($parameter);
            echo $html_text;
        }
    }
    public function notify()
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
                exit('验证失败');
            } else {
                $out_trade_no = $_POST['param'];
                $pay_no = $_POST['pay_no'];
                $res = (new OrderModel())->updateorder($out_trade_no, 'status', 1);
                $res2 = (new OrderModel())->orderinfo($out_trade_no);
                $yscount = (new KmModel())->yishoukami($out_trade_no);
                $res2['count'] = $res2['count'] - $yscount;
                if ($res2['yeskm'] == 0) {
                    echo 'ok';
                    $res3 = (new KmModel())->kucun_km($res2['goodid']);
                    for ($i = 0; $i < $res2['count']; $i++) {
                        $kamiid = $res3[$i]['id'];
                        $kahao = $res3[$i]['kahao'];
                        $mima = $res3[$i]['mima'];
                        $updata_km = (new KmModel())->update_km($kamiid, ['status' => 1, 'ddid' => $out_trade_no]);
                    }
                    $kmstatus = (new OrderModel())->updateorder($out_trade_no, 'yeskm', 1);
                    $putpayno = (new OrderModel())->updatepayno($out_trade_no, $pay_no);
                    $kmsales = (new ItemModel())->update_sales($res2['goodid'], $res2['count']);
                    $kmdd = (new KmModel())->Kminfo($out_trade_no);
                    if ($siteinfo['mailon'] == 1) {
                        $res2 = (new OrderModel())->orderinfo($out_trade_no);
                        $kmemail = "";
                        foreach ($kmdd as $val) {
                            $kmemail = $kmemail . $val['kahao'] . '&nbsp;&nbsp;&nbsp;' . $val['mima'] . '<br>';
                        }
                        $emailcontent = <<<EOT
                    <div class="emailpaged" style="-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;background-position: center center;background-repeat: no-repeat;">
        <div class="emailcontent" style="width:100%;max-width:720px;text-align: left;margin: 0 auto;padding-bottom: 20px">
            <div class="emailtitle">
                <h1 style="color:#fff;background: #51a0e3;line-height:70px;font-size:24px;font-weight:normal;padding-left:40px;margin:0">
                    {$siteinfo['sitename']}-请查收您的卡密信息
                </h1>
                <div class="emailtext" style="background:#fff;padding:20px 32px 40px;">
                
                    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-top:1px solid #eee;border-left:1px solid #eee;color:#6e6e6e;font-size:16px;font-weight:normal">
                            <thead><tr><th colspan="2" style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;background:#f8f8f8;">订单信息</th></tr></thead>
                            <tbody>
\t\t\t\t\t\t\t    <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">订单编号</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$res2['ddid']}</td>
                                </tr>
\t\t\t\t\t\t\t    <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center">商品名称</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$res2['name']}</td>
                                </tr>
\t\t\t\t\t\t\t    <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;">订单价格</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$res2['price']}元</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;">购买数量</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$res2['count']}件</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;">卡密信息</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$kmemail}</td>
                                </tr>                    
\t\t\t\t\t\t\t</tbody>
                    </table>
                    <p style="color: #6e6e6e;font-size:13px;line-height:24px;">使用中若有售后问题请联系客服QQ:{$siteinfo['qq']}</p>  
                </div>
               
            </div>
        </div>
    </div>
EOT;
                        sendEmail(['webtitle' => $siteinfo['sitename'], 'emailhost' => $siteinfo['emailhost'], 'emailport' => $siteinfo['emailport'], 'emailuser' => $siteinfo['emailuser'], 'emailpass' => $siteinfo['emailpass'], 'user_email' => $res2['email'], 'content' => $emailcontent]);
                    }
                } else {
                    $kmdd = (new KmModel())->Kminfo($out_trade_no);
                }
            }
        } else {
            if ($siteinfo['paytype'] == 0) {
                $notify_url = $siteinfo['siteurl'] . 'index/notify';
                $return_url = $siteinfo['siteurl'] . 'index/returninfo';
                $siteinfo = (new OptionModel())->getinfo();
                $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
                $alipayNotify = new NotifyModel($alipayinfo);
                $verify_result = $alipayNotify->verifyNotify();
                if ($verify_result) {
                    $out_trade_no = $_GET['out_trade_no'];
                    $trade_no = $_GET['trade_no'];
                    $trade_status = $_GET['trade_status'];
                    $type = $_GET['type'];
                    if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                        $res = (new OrderModel())->updateorder($out_trade_no, 'status', 1);
                        $res2 = (new OrderModel())->orderinfo($out_trade_no);
                        $yscount = (new KmModel())->yishoukami($out_trade_no);
                        $res2['count'] = $res2['count'] - $yscount;
                        if ($res2['yeskm'] == 0) {
                            $res3 = (new KmModel())->kucun_km($res2['goodid']);
                            for ($i = 0; $i < $res2['count']; $i++) {
                                $kamiid = $res3[$i]['id'];
                                $kahao = $res3[$i]['kahao'];
                                $mima = $res3[$i]['mima'];
                                $updata_km = (new KmModel())->update_km($kamiid, ['status' => 1, 'ddid' => $out_trade_no]);
                            }
                            $kmstatus = (new OrderModel())->updateorder($out_trade_no, 'yeskm', 1);
                            $kmsales = (new ItemModel())->update_sales($res2['goodid'], $res2['count']);
                            $kmdd = (new KmModel())->Kminfo($out_trade_no);
                            if ($siteinfo['mailon'] == 1) {
                                $res2 = (new OrderModel())->orderinfo($out_trade_no);
                                $kmemail = "";
                                foreach ($kmdd as $val) {
                                    $kmemail = $kmemail . $val['kahao'] . '&nbsp;&nbsp;&nbsp;' . $val['mima'] . '<br>';
                                }
                                $emailcontent = <<<EOT
                    <div class="emailpaged" style="-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;background-position: center center;background-repeat: no-repeat;">
        <div class="emailcontent" style="width:100%;max-width:720px;text-align: left;margin: 0 auto;padding-bottom: 20px">
            <div class="emailtitle">
                <h1 style="color:#fff;background: #51a0e3;line-height:70px;font-size:24px;font-weight:normal;padding-left:40px;margin:0">
                    {$siteinfo['sitename']}-请查收您的卡密信息
                </h1>
                <div class="emailtext" style="background:#fff;padding:20px 32px 40px;">
                
                    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-top:1px solid #eee;border-left:1px solid #eee;color:#6e6e6e;font-size:16px;font-weight:normal">
                            <thead><tr><th colspan="2" style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;background:#f8f8f8;">订单信息</th></tr></thead>
                            <tbody>
\t\t\t\t\t\t\t    <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">订单编号</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$res2['ddid']}</td>
                                </tr>
\t\t\t\t\t\t\t    <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center">商品名称</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$res2['name']}</td>
                                </tr>
\t\t\t\t\t\t\t    <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;">订单价格</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$res2['price']}元</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;">购买数量</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$res2['count']}件</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;">卡密信息</td>
                                    <td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">{$kmemail}</td>
                                </tr>                    
\t\t\t\t\t\t\t</tbody>
                    </table>
                    <p style="color: #6e6e6e;font-size:13px;line-height:24px;">使用中若有售后问题请联系客服QQ:{$siteinfo['qq']}</p>  
                </div>
               
            </div>
        </div>
    </div>
EOT;
                            }
                        } else {
                            $kmdd = (new KmModel())->Kminfo($out_trade_no);
                        }
                    }
                    echo 'success';
                } else {
                    echo 'fail';
                }
            }
        }
    }
    public function returninfo()
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
                $out_trade_no = $_GET['param'];
                $this->success('付款成功,正在跳转订单页面', "/search.html?ddid={$out_trade_no}&page=1");
            }
        } else {
            if ($siteinfo['paytype'] == 0) {
                $notify_url = $siteinfo['siteurl'] . 'index/notify';
                $return_url = $siteinfo['siteurl'] . 'index/returninfo';
                $alipayinfo = array('notify_url' => $notify_url, 'return_url' => $return_url, 'partner' => $siteinfo['partner'], 'key' => $siteinfo['key'], 'sign_type' => strtoupper('MD5'), 'input_charset' => strtolower('utf-8'), 'transport' => 'http', 'apiurl' => $siteinfo['payapi']);
                $alipayNotify = new NotifyModel($alipayinfo);
                $verify_result = $alipayNotify->verifyReturn();
                if ($verify_result) {
                    $out_trade_no = $_GET['out_trade_no'];
                    $trade_no = $_GET['trade_no'];
                    $trade_status = $_GET['trade_status'];
                    $type = $_GET['type'];
                    if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                    } else {
                        echo "trade_status=" . $_GET['trade_status'];
                    }
                    $this->success('付款成功,正在跳转订单页面', "/search.html?ddid={$out_trade_no}&page=1");
                } else {
                    echo 'fail';
                }
            }
        }
    }
    public function search($ddid, $page = 1)
    {
        $ddid = xss_clean($ddid);
        $page = xss_clean($page);
        if (!$ddid) {
            $this->error('请输入订单号或联系方式');
            exit;
        }
        $orderinfo = (new SearchModel())->search($ddid, $page);
        $count = (new SearchModel())->countorder($ddid);
        $kminfo = (new KmModel())->Kminfo($ddid);
        if (!$count) {
            $this->error('没有该订单号的购买记录');
            exit;
        }
        $this->assign('orderinfo', $orderinfo);
        $this->assign('count', count($count));
        return view();
    }
    public function fenge()
    {
        if (request()->ispost()) {
            $char = implode("", $_POST);
            echo '<pre>';
            $arr = explode("\r\n", $char);
            for ($i = 0; $i < count($arr); $i++) {
                $arr[$i] = explode(" ", $arr[$i]);
            }
            print_r($arr);
        }
        return view();
    }
    public function kminfo($ddid)
    {
        $kminfo = (new KmModel())->Kminfo($ddid);
        return json($kminfo);
    }
    public function article($id)
    {
        $res = (new ArticleModel())->getart($id);
        $this->assign('res', $res);
        return view();
    }
    public function getDiscounInfo($quanma)
    {
        $res = (new DiscountModel())->discountinfo($quanma);
        return $res;
    }
}