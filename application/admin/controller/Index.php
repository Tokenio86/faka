<?php

namespace app\admin\controller;

use app\admin\model\Category as CategoryModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Kami as KamiModel;
use app\admin\model\Order as OrderModel;
use app\admin\model\Option as OptionModel;
use app\admin\model\Links as LinksModel;
use app\admin\model\Daili as DailiModel;
use app\admin\model\Recharge as RechargeModel;
use app\admin\model\Article as ArticleModel;
use app\index\model\Discount as DiscountModel;
use think\Db;
use think\Session;
class Index extends Common
{
    function _initialize()
    {
        $res = (new OptionModel())->getinfo();
        $this->assign('siteinfo', $res);
    }
    public function welcome()
    {
        $order = (new OrderModel())->getorderinfo();
        $this->assign('order', count($order));
        $category = (new CategoryModel())->getcat();
        $this->assign('category', count($category));
        $good = (new GoodsModel())->getgoods();
        $this->assign('good', count($good));
        $kami = (new KamiModel())->getkckm();
        $this->assign('kami', count($kami));
        $todyorder = 0.0;
        $todybuyok = 0.0;
        $todymoney = 0.0;
        $totalmoney = 0.0;
        foreach ($order as $val) {
            $b = substr($val['time'], 0, 10);
            $c = date('Y-m-d');
            if ($b == $c) {
                $todyorder++;
            }
            if ($val->getData('status') == 1 && $b == $c) {
                $todybuyok++;
                $todymoney = $todymoney + $val['price'];
            }
            if ($val->getData('status') == 1) {
                $totalmoney = $totalmoney + $val['price'];
            }
        }
        $this->assign('todyorder', $todyorder);
        $this->assign('todybuyok', $todybuyok);
        $this->assign('todymoney', round($todymoney, 2));
        $this->assign('totalmoney', round($totalmoney, 2));
        return view();
    }
    public function index()
    {
        return view();
    }
    public function loginout()
    {
        Session::clear();
        $this->success('退出成功', "/" . Config('admin_url'));
    }
    public function category()
    {
        return view();
    }
    public function categorydata($page, $limit, $keyword = '')
    {
        $res = (new CategoryModel())->category($page, $limit, $keyword);
        $res = json_decode(json_encode($res), true);
        $rescount = (new CategoryModel())->categorycount($keyword);
        $data = $data = ['code' => 0, 'msg' => '', 'count' => $rescount, 'data' => []];
        foreach ($res as $value) {
            $data['data'][] = ['id' => $value['id'], 'name' => $value['name'], 'image' => '<img src="' . $value['image'] . '" onclick="lookimg(this)">', 'abridge' => $value['abridge'], 'sort' => $value['sort']];
        }
        return json($data);
    }
    public function addcat()
    {
        if (request()->ispost()) {
            $res = (new CategoryModel())->addcat($_POST);
            if ($res['valid']) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/category");
            } else {
                $this->error($res['msg']);
            }
        }
        return view();
    }
    public function delcat($id)
    {
        $catinfo = (new CategoryModel())->editcat($id);
        $goodinfo = (new GoodsModel())->delcat_good($catinfo['abridge']);
        if ($goodinfo) {
            echo '0';
        } else {
            $res = (new CategoryModel())->delcat($id);
            echo $res;
        }
    }
    public function editcat($id)
    {
        $res = (new CategoryModel())->editcat($id);
        $this->assign('res', $res);
        return view();
    }
    public function posteditcat()
    {
        if (request()->ispost()) {
            $res = (new CategoryModel())->posteditcat($_POST);
            if ($res) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/category");
            } else {
                $this->error($res['msg']);
            }
        }
    }
    public function goods()
    {
        $rescount = 2;
        $this->assign('rescount', $rescount);
        return view();
    }
    public function gooddata($page, $limit, $keyword = '')
    {
        $res = (new GoodsModel())->goods($page, $limit, $keyword);
        $res = json_decode(json_encode($res), true);
        $rescount = (new GoodsModel())->goodcount($keyword);
        $data = ['code' => 0, 'msg' => '', 'count' => $rescount, 'data' => []];
        foreach ($res as $value) {
            $kmtatal = (new KamiModel())->kmtatal($value['id']);
            $kucun = 0;
            foreach ($kmtatal as $kmval) {
                if ($kmval->getData('status') == 0) {
                    $kucun++;
                }
            }
            $data['data'][] = ['id' => $value['id'], 'name' => $value['name'], 'abridge' => $value['abridge'], 'price' => $value['price'], 'dailiprice' => $value['dailiprice'], 'status' => $value['status'], 'kucun' => $kucun, 'sales' => $value['sales']];
        }
        return json($data);
    }
    public function addgood()
    {
        if (request()->post()) {
            $res = (new GoodsModel())->addgood($_POST);
            if ($res) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/goods");
            } else {
                $this->error($res['msg']);
            }
        }
        $fenlei = (new CategoryModel())->getcat();
        $this->assign('fenlei', $fenlei);
        return view();
    }
    public function delgood($id)
    {
        $kami = (new KamiModel())->find_km($id);
        if ($kami) {
            echo '0';
        } else {
            $res = (new GoodsModel())->delgood($id);
            echo $res;
        }
    }
    public function editgood($id)
    {
        $res = (new GoodsModel())->editgood($id);
        $this->assign('res', $res);
        $fenlei = (new CategoryModel())->getcat();
        $this->assign('fenlei', $fenlei);
        $this->assign('status', $res->getData('status'));
        return view();
    }
    public function posteditgood()
    {
        if (request()->ispost()) {
            $res = (new GoodsModel())->posteditgood($_POST);
            if ($res) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/goods");
            } else {
                $this->error($res['msg']);
            }
        }
    }
    public function kami()
    {
        $res = (new GoodsModel())->getgoods();
        $this->assign('res', $res);
        return view();
    }
    public function kamidata($page, $limit, $id = '', $status = '')
    {
        $res = (new KamiModel())->kami($page, $limit, $id, $status);
        $res = json_decode(json_encode($res), true);
        $rescount = (new KamiModel())->kamicount($id, $status);
        $data = ['code' => 0, 'msg' => '', 'count' => $rescount, 'data' => []];
        foreach ($res as $value) {
            $kmname = (new GoodsModel())->find_good($value['goodid']);
            $data['data'][] = ['id' => $value['id'], 'name' => $kmname['name'], 'kahao' => $value['kahao'], 'mima' => $value['mima'], 'status' => $value['status']];
        }
        return json($data);
    }
    public function addkami()
    {
        if (request()->ispost()) {
            $res = (new KamiModel())->addkami($_POST);
            if ($res) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/addkami");
            } else {
                $this->error($res['msg']);
            }
        }
        $good = (new GoodsModel())->getgoods();
        $this->assign('good', $good);
        return view();
    }
    public function delkami($id)
    {
        $res = (new KamiModel())->delkami($id);
        return $res;
    }
    public function kamiinfo($id)
    {
        $kami = (new KamiModel())->find_km_id($id);
        $good = (new GoodsModel())->find_good($kami['goodid']);
        return '卡密编号：' . $kami['id'] . '<br>所属商品：' . $good['name'] . '<br>卡号信息：' . $kami['kahao'] . '<br>密码信息：' . $kami['mima'] . '<br>卡密状态：' . $kami['status'] . '<br>所属订单：' . $kami['ddid'] . '<br>发卡时间：' . $kami['time'];
    }
    public function order()
    {
        return view();
    }
    public function orderdata($page, $limit, $keyword = '')
    {
        $res = (new OrderModel())->ordata($page, $limit, $keyword);
        $odcount = (new OrderModel())->ordercount($keyword);
        $res = json_decode(json_encode($res), true);
        $data = ['code' => 0, 'msg' => '', 'count' => $odcount, 'data' => []];
        foreach ($res as $value) {
            $data['data'][] = ['ddid' => $value['ddid'], 'name' => $value['name'], 'time' => $value['time'], 'count' => $value['count'], 'email' => $value['email'], 'type' => $value['type'], 'status' => $value['status']];
        }
        return json($data);
    }
    public function option()
    {
        if (request()->ispost()) {
            $update = (new OptionModel())->updateinfo($_POST);
            if ($update) {
                $this->success('修改成功', "/" . Config('admin_url') . "/index/option");
            } else {
                $this->error('修改失败');
            }
        }
        $res = (new OptionModel())->getinfo();
        $this->assign('res', $res);
        return view();
    }
    public function delsold($id)
    {
        $res = (new KamiModel())->delsold($id);
        echo $res;
    }
    public function delkm($id)
    {
        $res = (new KamiModel())->delkm($id);
        if ($res == '1') {
            $this->success('清空成功', "/" . Config('admin_url') . "/index/goods.html");
        } else {
            $this->error('清空失败');
        }
    }
    public function apipay()
    {
        if (request()->ispost()) {
            $update = (new OptionModel())->updatepay($_POST);
            if ($update) {
                $this->success('修改成功', "/" . Config('admin_url') . "/index/apipay.html");
            } else {
                $this->error('修改失败');
            }
        }
        $res = (new OptionModel())->getinfo();
        $paytype = '';
        if ($res['paytype'] == 0) {
            $paytype = '易支付';
        } else {
            if ($res['paytype'] == 1) {
                $paytype = '码支付';
            }
        }
        $this->assign('res', $res);
        $this->assign('paytype', $paytype);
        return view();
    }
    public function password()
    {
        if (request()->ispost()) {
            $res = (new OptionModel())->option($_POST);
            if ($res['valid']) {
                Session::clear();
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/password");
            } else {
                $this->error($res['msg']);
            }
        }
        return view();
    }
    public function links()
    {
        return view();
    }
    public function linkdata($page, $limit, $keyword = '')
    {
        $link = (new LinksModel())->getlink($page, $limit, $keyword);
        $linkcount = (new LinksModel())->linkcount($keyword);
        $data = ['code' => 0, 'msg' => '', 'count' => $linkcount, 'data' => []];
        foreach ($link as $value) {
            $data['data'][] = ['id' => $value['id'], 'sitename' => $value['sitename'], 'siteurl' => $value['siteurl'], 'sort' => $value['sort']];
        }
        return json($data);
    }
    public function addlink()
    {
        if (request()->ispost()) {
            $res = (new LinksModel())->addlinks($_POST);
            if ($res['valid']) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/addlink");
            } else {
                $this->error($res['msg']);
            }
        }
        return view();
    }
    public function dellink($id)
    {
        $res = (new LinksModel())->dellink($id);
        if ($res) {
            return '1';
        } else {
            return '0';
        }
    }
    public function editlink($id)
    {
        $res = (new LinksModel())->findlink($id);
        if (request()->ispost()) {
            $res = (new LinksModel())->editlink($_POST);
            if ($res['valid']) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/links");
            } else {
                $this->error($res['msg']);
            }
        }
        $this->assign('link', $res);
        return view();
    }
    public function daili()
    {
        return view();
    }
    public function dailidata($page, $limit, $keyword = '')
    {
        $dlinfo = (new DailiModel())->dailiinfo($page, $limit, $keyword);
        $dlcount = (new DailiModel())->dlcount($keyword);
        $data = ['code' => 0, 'msg' => '', 'count' => $dlcount, 'data' => []];
        foreach ($dlinfo as $value) {
            $data['data'][] = ['id' => $value['id'], 'username' => $value['username'], 'email' => $value['email'], 'money' => $value['money'], 'level' => $value['level'], 'time' => $value['create_time']];
        }
        return json($data);
    }
    public function deldaili($dlid)
    {
        $deldaili = (new DailiModel())->deldaili($dlid);
        if ($deldaili) {
            return '1';
        } else {
            return '2';
        }
    }
    public function editdaili($id)
    {
        if (request()->ispost()) {
            $dlinfo = (new DailiModel())->editdaili($_POST);
            if ($dlinfo) {
                $this->success('修改成功', "/" . Config('admin_url') . "/index/daili.html");
            } else {
                $this->error('修改失败');
            }
        }
        $dlinfo = (new DailiModel())->daili($id);
        $this->assign('dlinfo', $dlinfo);
        return view();
    }
    public function adddaili()
    {
        if (request()->ispost()) {
            $add = (new DailiModel())->adddaili($_POST);
            if ($add['vaild']) {
                $this->success($add['msg'], "/" . Config('admin_url') . "/index/daili.html");
            } else {
                $this->error($add['msg']);
            }
        }
        return view();
    }
    public function pldelkm($data)
    {
        $data = json_decode($data, true);
        if (!$data) {
            return '没有选中任何记录';
        }
        $res = (new KamiModel())->pldelekm($data);
        if ($res == '1') {
            return '删除成功';
        }
    }
    public function dljiakuan($dlid, $money)
    {
        $jiakuan = (new DailiModel())->jiakuan($dlid, $money);
        if ($jiakuan == '1') {
            return '已经成功给编号为' . $dlid . '的代理加款' . $money . '元';
        }
    }
    public function clear()
    {
        return view();
    }
    public function cleardata($oper, $time)
    {
        if ($oper == 'wfk' && ($time = 'none')) {
            $res = (new OrderModel())->cleardata($oper);
        } elseif ($oper == 'zdsj') {
            $time = explode(" - ", $time);
            $res = (new OrderModel())->zdsjdel(strtotime($time[0]), strtotime($time[1]));
        } elseif ($oper == 'czjlwfk' && ($time = 'none')) {
            $res = (new RechargeModel())->delcz($oper);
        } elseif ($oper == 'clearcz' && ($time = 'none')) {
            $res = (new RechargeModel())->delcz($oper);
        }
        return $res;
    }
    public function orderdetail($ddid)
    {
        $orderinfo = (new OrderModel())->findorder($ddid);
        return '订单编号：' . $orderinfo['ddid'] . '<br>商品编号：' . $orderinfo['goodid'] . '<br>商品名称：' . $orderinfo['name'] . '<br>订单金额：' . $orderinfo['price'] . '<br>购买数量：' . $orderinfo['count'] . '<br>买家信息：' . $orderinfo['email'] . '<br>支付方式：' . $orderinfo['type'] . '<br>订单状态：' . $orderinfo['status'] . '<br>下单时间：' . $orderinfo['time'] . '<br>是否发卡：' . $orderinfo['yeskm'] . '<br>卡密信息：' . '<a target="_blank" href="/search.html?ddid=' . $orderinfo['ddid'] . '&page=1">点此查看</a>' . '<br>代理编号：' . $orderinfo['daili'] . '<br>流水编号：' . $orderinfo['payno'];
    }
    public function export($id, $status)
    {
        $res = (new KamiModel())->export($id, $status);
        $txt = '';
        foreach ($res as $value) {
            $txt .= $value['kahao'] . ' ' . $value['mima'] . "\r\n";
        }
        return $txt;
    }
    public function uploadimg()
    {
        $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
        $max_file_size = 2000000;
        $destination_folder = Config('imgdir');
        $watermark = 0;
        $watertype = 1;
        $waterposition = 1;
        $waterstring = "";
        $waterimg = "";
        $imgpreview = 1;
        $imgpreviewsize = 1 / 2;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!is_uploaded_file($_FILES["file"]["tmp_name"])) {
                echo "图片不存在!";
                exit;
            }
            $file = $_FILES["file"];
            if ($max_file_size < $file["size"]) {
                echo "文件太大!";
                exit;
            }
            if (!in_array($file["type"], $uptypes)) {
                echo "文件类型不符!" . $file["type"];
                exit;
            }
            if (!file_exists($destination_folder)) {
                mkdir($destination_folder);
            }
            $filename = $file["tmp_name"];
            $image_size = getimagesize($filename);
            $pinfo = pathinfo($file["name"]);
            $ftype = $pinfo['extension'];
            $destination = $destination_folder . time() . "." . $ftype;
            if (file_exists($destination) && $overwrite != true) {
                echo "同名文件已经存在了";
                exit;
            }
            if (!move_uploaded_file($filename, $destination)) {
                echo "移动文件出错";
                exit;
            }
            $pinfo = pathinfo($destination);
            $fname = $pinfo["basename"];
            if ($watermark == 1) {
                $iinfo = getimagesize($destination, $iinfo);
                $nimage = imagecreatetruecolor($image_size[0], $image_size[1]);
                $white = imagecolorallocate($nimage, 255, 255, 255);
                $black = imagecolorallocate($nimage, 0, 0, 0);
                $red = imagecolorallocate($nimage, 255, 0, 0);
                imagefill($nimage, 0, 0, $white);
                switch ($iinfo[2]) {
                    case 1:
                        $simage = imagecreatefromgif($destination);
                        break;
                    case 2:
                        $simage = imagecreatefromjpeg($destination);
                        break;
                    case 3:
                        $simage = imagecreatefrompng($destination);
                        break;
                    case 6:
                        $simage = imagecreatefromwbmp($destination);
                        break;
                    default:
                        die('不支持的文件类型');
                        exit;
                }
                imagecopy($nimage, $simage, 0, 0, 0, 0, $image_size[0], $image_size[1]);
                imagefilledrectangle($nimage, 1, $image_size[1] - 15, 80, $image_size[1], $white);
                switch ($watertype) {
                    case 1:
                        imagestring($nimage, 2, 3, $image_size[1] - 15, $waterstring, $black);
                        break;
                    case 2:
                        $simage1 = imagecreatefromgif("xplore.gif");
                        imagecopy($nimage, $simage1, 0, 0, 0, 0, 85, 15);
                        imagedestroy($simage1);
                        break;
                }
                switch ($iinfo[2]) {
                    case 1:
                        imagejpeg($nimage, $destination);
                        break;
                    case 2:
                        imagejpeg($nimage, $destination);
                        break;
                    case 3:
                        imagepng($nimage, $destination);
                        break;
                    case 6:
                        imagewbmp($nimage, $destination);
                        break;
                }
                imagedestroy($nimage);
                imagedestroy($simage);
            }
            if ($imgpreview == 1) {
                $imginfo = ['code' => 0, 'msg' => '', 'data' => ['src' => $destination]];
                return json($imginfo);
            }
        }
    }
    public function article()
    {
        return view();
    }
    public function articledata($page, $limit, $keyword = '')
    {
        $res = (new ArticleModel())->artdata($page, $limit, $keyword);
        $res = json_decode(json_encode($res), true);
        $rescount = (new ArticleModel())->artcount($keyword);
        $data = ['code' => 0, 'msg' => '', 'count' => $rescount, 'data' => []];
        foreach ($res as $value) {
            $data['data'][] = ['id' => $value['id'], 'title' => $value['title'], 'time' => $value['time']];
        }
        return json($data);
    }
    public function addart()
    {
        if (request()->ispost()) {
            $res = (new ArticleModel())->addart($_POST);
            if ($res['valid']) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/article.html");
            } else {
                $this->error($res['msg']);
            }
        }
        return view();
    }
    public function delart($id)
    {
        $res = (new ArticleModel())->delart($id);
        return $res;
    }
    public function editart($id)
    {
        if (request()->ispost()) {
            $res = (new ArticleModel())->editart($_POST);
            if ($res['valid']) {
                $this->success($res['msg'], "/" . Config('admin_url') . "/index/article.html");
            } else {
                $this->error($res['msg']);
            }
        }
        $res = (new ArticleModel())->getart($id);
        $this->assign('res', $res);
        return view();
    }
    public function recharge()
    {
        return view();
    }
    public function rechargedata($page, $limit, $keyword = '')
    {
        $res = (new RechargeModel())->rechargedata($page, $limit, $keyword);
        $res = json_decode(json_encode($res), true);
        $rescount = (new RechargeModel())->rechargecount($keyword);
        $data = ['code' => 0, 'msg' => '', 'count' => $rescount, 'data' => []];
        foreach ($res as $value) {
            $data['data'][] = ['id' => $value['id'], 'dailiid' => $value['dailiid'], 'money' => $value['money'], 'type' => $value['type'], 'time' => $value['time'], 'status' => $value['status']];
        }
        return json($data);
    }
    public function youhuiquan()
    {
        return view();
    }
    public function youhuiquandata($page, $limit, $keyword = '')
    {
        $res = (new DiscountModel())->Discountdata($page, $limit, $keyword);
        $res = json_decode(json_encode($res), true);
        $rescount = (new DiscountModel())->DiscountCount($keyword);
        $data = ['code' => 0, 'msg' => '', 'count' => $rescount, 'data' => []];
        foreach ($res as $value) {
            $data['data'][] = ['id' => $value['id'], 'quanma' => $value['quanma'], 'status' => $value['status'], 'ddid' => $value['ddid'], 'discount' => $value['discount']];
        }
        return json($data);
    }
    public function delDiscount($id)
    {
        $res = (new DiscountModel())->delDiscountCount($id);
        return $res;
    }
    public function addyouhuiquan()
    {
        if (request()->ispost()) {
            $res = (new DiscountModel())->addDiscount($_POST);
            if ($res) {
                $this->success('完全O鸡巴K了', "/" . Config('admin_url') . "/index/addyouhuiquan.html");
            } else {
                $this->error('GG了');
            }
        }
        return view();
    }
}