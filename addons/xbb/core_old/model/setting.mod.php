<?php

function getSetting() {
    global $_W;
    $base = pdo_get('xw_wl_setting', array('uniacid' => $_W['uniacid']));
    return $base;
}
