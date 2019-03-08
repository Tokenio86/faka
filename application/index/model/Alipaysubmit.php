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
class Alipaysubmit
{
    var $alipay_config;
    function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;
        $this->alipay_gateway_new = $this->alipay_config['apiurl'] . 'submit.php?';
    }
    function AlipaySubmit($alipay_config)
    {
        $this->__construct($alipay_config);
    }
    function buildRequestMysign($para_sort)
    {
        $prestr = createLinkstring($para_sort);
        $mysign = md5Sign($prestr, $this->alipay_config['key']);
        return $mysign;
    }
    function buildRequestPara($para_temp)
    {
        $para_filter = paraFilter($para_temp);
        $para_sort = argSort($para_filter);
        $mysign = $this->buildRequestMysign($para_sort);
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = strtoupper(trim($this->alipay_config['sign_type']));
        return $para_sort;
    }
    function buildRequestParaToString($para_temp)
    {
        $para = $this->buildRequestPara($para_temp);
        $request_data = createLinkstringUrlencode($para);
        return $request_data;
    }
    function buildRequestForm($para_temp)
    {
        $para = $this->buildRequestPara($para_temp);
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . $this->alipay_gateway_new . "_input_charset=" . trim(strtolower($this->alipay_config['input_charset'])) . "'>";
        while (list($key, $val) = each($para)) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml = $sHtml . "<input type='submit'></form>";
        $sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }
}