<?php

defined('IN_IA') or exit('Access Denied');

/**
 * 
 * 框架自助加载类
 * 
 * @author Liuxudong <1659190447@qq.com>
 * @date   2017-2-13 15:32:50
 * @copyright ©2010-2020 南京旭文网络科技有限公司
 * @license http://www.xuwenweb.com
 */
function xw_autoLoad($class_name) {
    $file = XW_CORE . 'class/' . $class_name . '.class.php';
    if (is_file($file)) {
        require_once $file;
    }
    return false;
}
spl_autoload_register('xw_autoLoad');
