<?php

/**
 * 
 * @author JuChao<723829333@qq.com>
 * @date   2018-4-19 10:42:29
 * @copyright ©2010-2020 南京旭文网络科技有限公司
 * @license http://www.xuwenweb.com
 */
require dirname(__FILE__) . '/../../../../framework/bootstrap.inc.php';

$input = file_get_contents('php://input');


$bPreviousValue = libxml_disable_entity_loader(true);
$obj = isimplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
$data = json_decode(json_encode($obj), true);
libxml_disable_entity_loader($bPreviousValue);


$_W['uniacid'] = $_W['weid'] = intval($data['attach']);
$setting = uni_setting($_W['uniacid'], array('payment'));
//$fp = fopen(IA_ROOT . '/payment/wechat/test.json', 'w');
//fwrite($fp, json_encode($setting));
//fclose($fp);
if (($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS')) {
    $wechat = $setting['payment']['wechat'];
    ksort($data);
    $string1 = '';
    foreach ($data as $k => $v) {
        if ($v != '' && $k != 'sign') {
            $string1 .= "{$k}={$v}&";
        }
    }
    $sign = strtoupper(md5($string1 . "key={$wechat['signkey']}"));
    if ($sign == $data['sign']) {

        $ordersn = $data['out_trade_no'];
        $ordertype = substr($ordersn, 0, 3);
        $res = pdo_update("xw_wl_extend_order", array('status' => 2, 'paytime' => $_W['timestamp']), array('ordersn' => $ordersn));
        if ($res) {
            $orderinfo = pdo_get("xw_wl_extend_order", array('ordersn' => $ordersn));
            $storeinfo = pdo_get('xw_wl_store', array('openid' => $orderinfo['openid']));
            if ($storeinfo['extendtime'] < $_W['timestamp']) {
                $extendtime = strtotime("+" . $orderinfo['month'] . " month", $_W['timestamp']);
            } else {
                $extendtime = strtotime("+" . $orderinfo['month'] . " month", $storeinfo['extendtime']);
            }
            $data = array(
                'extend' => 2,
                'extendtime' => $extendtime,
                'type' => 1,
            );
            $re = pdo_update("xw_wl_store", $data, array('id' => $storeinfo['id']));
            if ($re) {
                $arr = array(
                    'uniacid' => $_W['uniacid'],
                    'openid' => $storeinfo['openid'],
                    'sid' => $storeinfo['id'],
                    'company' => $storeinfo['company'],
                    'type' => 1,
                    'price' => $orderinfo['price'],
                    'remark' => "付费推广" . $storeinfo['month'] . "个月，微信支付" . $orderinfo['price'] . "元",
                    'createtime' => $_W['timestamp'],
                );
                pdo_insert("xw_wl_extend_record", $arr);
            }
            echo('SUCCESS');
        }
    }
}
