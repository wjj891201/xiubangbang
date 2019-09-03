<?php

require IA_ROOT . '/addons/xbb/core/function/global.func.php';

//上传图片
if ($module_do == 'savefile')
{
    global $_W, $_GPC;
    $code = getRandStr(4);
    if ($_GPC['path'])
    {
        $filename = $_GPC['path'];
    }
    else
    {
        $filename = $code . $_GPC['openid'];
    }
    $filepath = 'images/xbb/apply/';
    $result = saveWxappImg($filepath . $filename, $filename, $code);
    if ($result['errno'] < 0)
    {
        return $this->result(-1, $result['message']);
    }
    return $this->result(0, '上传成功', $result);
}
