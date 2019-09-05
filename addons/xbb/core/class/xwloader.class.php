<?php

defined('IN_IA') or exit('Access Denied');

/**
 * 
 * 加载辅助类
 * 
 * @author Liuxudong <1659190447@qq.com>
 * @date   2017-2-13 15:32:50
 * @copyright ©2010-2020 南京旭文网络科技有限公司
 * @license http://www.xuwenweb.com
 */
function xwload() {
    static $xwloader;
    if (empty($xwloader)) {
        $xwloader = new xwloader();
    }
    return $xwloader;
}

class xwloader {

    private $cache = array();

    function func($name) {
        global $_W;
        if (isset($this->cache['xwfunc'][$name])) {
            return true;
        }
        $file = XW_CORE . 'function/' . $name . '.func.php';
        if (file_exists($file)) {
            include_once $file;
            $this->cache['xwfunc'][$name] = true;
            return true;
        } else {
            trigger_error('Invalid Helper Function ' . XW_CORE . 'function/' . $name . '.func.php', E_USER_ERROR);
            return false;
        }
    }

    function model($name) {
        global $_W;
        if (isset($this->cache['xwmodel'][$name])) {
            return true;
        }
        $file = XW_CORE . 'model/' . $name . '.mod.php';

        if (file_exists($file)) {
            include_once $file;
            $this->cache['xwmodel'][$name] = true;
            returntrue;
        } else {
            trigger_error('Invalid Model ' . XW_CORE . 'model/' . $name . '.mod.php', E_USER_ERROR);
            return false;
        }
    }

    function classs($name) {
        global $_W;
        if (isset($this->cache['xwclass'][$name])) {
            return true;
        }
        $file = XW_CORE . 'class/' . $name . '.class.php';
        if (file_exists($file)) {
            include_once $file;
            $this->cache['xwclass'][$name] = true;
            return true;
        } else {
            trigger_error('Invalid Class ' . XW_CORE . 'class/' . $name . '.class.php', E_USER_ERROR);
            return false;
        }
    }

    function web($name) {
        global $_W;
        if (isset($this->cache['xwweb'][$name])) {
            return true;
        }
        $file = XW_PATH . '/web/common/' . $name . '.func.php';
        if (file_exists($file)) {
            include_once $file;
            $this->cache['xwweb'][$name] = true;
            return true;
        } else {
            trigger_error('Invalid Web Helper ' . XW_PATH . '/web/common/' . $name . '.func.php', E_USER_ERROR);
            return false;
        }
    }

    function app($name) {
        global $_W;
        if (isset($this->cache['xwapp'][$name])) {
            return true;
        }
        $file = XW_PATH . '/app/common/' . $name . '.func.php';
        if (file_exists($file)) {
            include_once $file;
            $this->cache['xwapp'][$name] = true;
            return true;
        } else {
            trigger_error('Invalid App Function ' . XW_PATH . '/app/common/' . $name . '.func.php', E_USER_ERROR);
            return false;
        }
    }

}
