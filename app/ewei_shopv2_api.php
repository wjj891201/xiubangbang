<?php
/*
 * 人人商城
 * www.nydzyc.com 源码网小程序连接接口，不要修改本文件任何内容，否则连接不正常
 */
define('IN_SYS', true);
require '../framework/bootstrap.inc.php';
load()->web('common');
$uniacid  =intval($_GPC['i']);
if(empty($uniacid)){
	die('Access Denied.');
}
$site = WeUtility::createModuleSite('ewei_shopv2');
$_GPC['c']='site';
$_GPC['a']='entry';
$_GPC['m']='ewei_shopv2';
$_GPC['do']='mobile';
$_W['uniacid'] = (int)$_GPC['i'];
$_W['acid'] = (int)$_GPC['i'];
if (!isset($_GPC['r'])){
    $_GPC['r']='app';
}else{
    $_GPC['r']='app.'.$_GPC['r'];
}
if(!is_error($site)) {
    $method = 'doMobileMobile';
    $site->uniacid = $uniacid ;
    $site->inMobile = true;
    if (method_exists($site, $method)) {
        $site->$method();
        exit;
    }
}

