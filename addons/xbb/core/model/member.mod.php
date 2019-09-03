<?php

defined('IN_IA') or exit('Access Denied');

/**
 * 获取用户信息
 * @param type $openid
 * @return type
 */
function getMember($openid = '') {
    global $_W;
    $member = pdo_get('xw_wl_member', array('openid' => $openid));
    return $member;
}