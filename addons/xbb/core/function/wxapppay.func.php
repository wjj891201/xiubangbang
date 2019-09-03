<?php

/**
 * 
 * @author JuChao<723829333@qq.com>
 * @date   2018-4-19 13:08:18
 * @copyright ©2010-2020 南京旭文网络科技有限公司
 * @license http://www.xuwenweb.com
 */
function wxapppay($data) {
    global $_W;
//    $setting = uni_setting($_W['uniacid'], array('payment'));
//    $wechat = $setting['payment']['wechat'];
//    $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
//    $row = pdo_fetch($sql, array(':acid' => $wechat['account']));
    $package = array();
    $package['appid'] = $data['appid'];
    $package['mch_id'] = $data['mchid'];
    $package['nonce_str'] = random(8);
    $package['body'] = $data['title'];
    $package['attach'] = $_W['uniacid'];
    $package['out_trade_no'] = $data['ordersn'];
    $package['openid'] = $data['openid'];
    $package['total_fee'] = $data['price'] * 100;
//    $package['total_fee'] = 1;
    $package['spbill_create_ip'] = CLIENT_IP;
    $package['time_start'] = date('YmdHis', TIMESTAMP);
    $package['time_expire'] = date('YmdHis', TIMESTAMP + 600);
    $package['notify_url'] = $_W['siteroot'] . 'addons/xw_wl/core/payment/wechat_notify.php';
    $package['trade_type'] = 'JSAPI';
    ksort($package, SORT_STRING);
    $string1 = '';
    foreach ($package as $key => $v) {
        if (empty($v)) {
            continue;
        }
        $string1 .= "{$key}={$v}&";
    }
    $string1 .= "key=" . $data['apikey'];
    $package['sign'] = strtoupper(md5($string1));
    $dat = array2xml($package);
    load()->func('communication');
    $response = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);
    if (is_error($response)) {
        message($response, '', 'error');
    }

    $objectxml = json_decode(json_encode(simplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    if ($objectxml['return_code'] == 'FAIL') {
        message($objectxml['return_msg'], '', 'error');
    }
    if ($objectxml['return_code'] == 'SUCCESS') {
        if ($objectxml['result_code'] == 'SUCCESS') {//如果这两个都为此状态则返回mweb_url，详情看‘统一下单’接口文档
            $wOpt = array();
            $wOpt['appId'] = $objectxml['appid'];
            $wOpt['timeStamp'] = time();
            $wOpt['nonceStr'] = $objectxml['nonce_str'];
            $wOpt['package'] = 'prepay_id=' . $objectxml['prepay_id'];
            $wOpt['signType'] = 'MD5';
            ksort($wOpt, SORT_STRING);
            $string = '';
            foreach ($wOpt as $key => $v) {
                $string .= $key . '=' . $v . '&';
            }
            $string .= 'key=' . $data['apikey'];
            $wOpt['paySign'] = strtoupper(md5($string));
        }
    }
    return $wOpt;
}
?>


