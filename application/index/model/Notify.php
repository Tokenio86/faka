<?php

namespace app\index\model;

function createLinkstring($para)
{
    $arg = "";
    while (list($key, $val) = each($para)) {
        $arg .= $key . "=" . $val . "&";
    }
    $arg = substr($arg, 0, count($arg) - 2);
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }
    return $arg;
}
function createLinkstringUrlencode($para)
{
    $arg = "";
    while (list($key, $val) = each($para)) {
        $arg .= $key . "=" . urlencode($val) . "&";
    }
    $arg = substr($arg, 0, count($arg) - 2);
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }
    return $arg;
}
function paraFilter($para)
{
    $para_filter = array();
    while (list($key, $val) = each($para)) {
        if ($key == "sign" || $key == "sign_type" || $val == "") {
            continue;
        } else {
            $para_filter[$key] = $para[$key];
        }
    }
    return $para_filter;
}
function argSort($para)
{
    ksort($para);
    reset($para);
    return $para;
}
function logResult($word = '')
{
    $fp = fopen("log.txt", "a");
    flock($fp, LOCK_EX);
    fwrite($fp, "执行日期：" . strftime("%Y%m%d%H%M%S", time()) . "\n" . $word . "\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}
function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '')
{
    if (trim($input_charset) != '') {
        $url = $url . "_input_charset=" . $input_charset;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $para);
    $responseText = curl_exec($curl);
    curl_close($curl);
    return $responseText;
}
function getHttpResponseGET($url, $cacert_url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);
    $responseText = curl_exec($curl);
    curl_close($curl);
    return $responseText;
}
function charsetEncode($input, $_output_charset, $_input_charset)
{
    $output = "";
    if (!isset($_output_charset)) {
        $_output_charset = $_input_charset;
    }
    if ($_input_charset == $_output_charset || $input == null) {
        $output = $input;
    } elseif (function_exists('mb_convert_encoding')) {
        $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
    } elseif (function_exists('iconv')) {
        $output = iconv($_input_charset, $_output_charset, $input);
    } else {
        die('sorry, you have no libs support for charset change.');
    }
    return $output;
}
function charsetDecode($input, $_input_charset, $_output_charset)
{
    $output = "";
    if (!isset($_input_charset)) {
        $_input_charset = $_input_charset;
    }
    if ($_input_charset == $_output_charset || $input == null) {
        $output = $input;
    } elseif (function_exists('mb_convert_encoding')) {
        $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
    } elseif (function_exists('iconv')) {
        $output = iconv($_input_charset, $_output_charset, $input);
    } else {
        die('sorry, you have no libs support for charset changes.');
    }
    return $output;
}
function md5Sign($prestr, $key)
{
    $prestr = $prestr . $key;
    return md5($prestr);
}
function md5Verify($prestr, $sign, $key)
{
    $prestr = $prestr . $key;
    $mysgin = md5($prestr);
    if ($mysgin == $sign) {
        return true;
    } else {
        return false;
    }
}
class Notify
{
    var $alipay_config;
    function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;
        $this->http_verify_url = $this->alipay_config['apiurl'] . 'api.php?';
    }
    function AlipayNotify($alipay_config)
    {
        $this->__construct($alipay_config);
    }
    function verifyNotify()
    {
        if (empty($_GET)) {
            return false;
        } else {
            $isSign = $this->getSignVeryfy($_GET, $_GET["sign"]);
            $responseTxt = 'true';
            if (preg_match('/true$/i', $responseTxt) && $isSign) {
                return true;
            } else {
                return false;
            }
        }
    }
    function verifyReturn()
    {
        if (empty($_GET)) {
            return false;
        } else {
            $isSign = $this->getSignVeryfy($_GET, $_GET["sign"]);
            $responseTxt = 'true';
            if (preg_match('/true$/i', $responseTxt) && $isSign) {
                return true;
            } else {
                return false;
            }
        }
    }
    function getSignVeryfy($para_temp, $sign)
    {
        $para_filter = paraFilter($para_temp);
        $para_sort = argSort($para_filter);
        $prestr = createLinkstring($para_sort);
        $isSgin = false;
        $isSgin = md5Verify($prestr, $sign, $this->alipay_config['key']);
        return $isSgin;
    }
    function getResponse($notify_id)
    {
        $transport = strtolower(trim($this->alipay_config['transport']));
        $partner = trim($this->alipay_config['partner']);
        $veryfy_url = '';
        if ($transport == 'https') {
            $veryfy_url = $this->https_verify_url;
        } else {
            $veryfy_url = $this->http_verify_url;
        }
        $veryfy_url = $veryfy_url . "partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
        return $responseTxt;
    }
}