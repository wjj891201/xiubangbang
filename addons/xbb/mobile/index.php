<?php

defined('IN_IA') or exit('Access Denied');
/**
 * 手机端入口
 * @author JuChao<723829333@qq.com>
 * @date   2018-6-5 15:04:42
 * @copyright ©2010-2020 南京旭文网络科技有限公司
 * @license http://www.xuwenweb.com
 */



$controller = $_GPC['do'];
$op = $_GPC['op'];

if (empty($controller)) {
    $_GPC['do'] = $controller = 'index';
}

if (empty($op)) {
    $_GPC['op'] = $op = 'index';
}

$file = XW_MOBILE . 'controller/' . $controller . '.ctrl.php';
if (!file_exists($file)) {
    trigger_error("访问的文件 {$file} 不存在.", E_USER_WARNING);
}
require $file;
