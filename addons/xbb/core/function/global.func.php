<?php

defined('IN_IA') or exit('Access Denied');
ob_start();
/**
 * 
 * 框架所需公用函数
 * 
 * @author Liuxudong <1659190447@qq.com>
 * @date   2017-2-13 15:32:50
 * @copyright ©2010-2020 南京旭文网络科技有限公司
 * @license http://www.xuwenweb.com
 */

/**
 * 生成web端使用的url
 * 
 * @global type $_W
 * @global type $_GPC
 * @param type $segment
 * @param type $params
 * @return type
 */
function web_url($segment, $params = array()) {
    global $_W, $_GPC;
    session_start();
    list($do, $op) = explode('/', $segment);
    $url = $_W['siteroot'] . 'web/index.php?c=site&a=entry&m=' . XW_NAME;
    if (!empty($do)) {
        $url .= "&do={$do}";
    }
    if (!empty($op)) {
        $url .= "&op={$op}";
    }
    if (!empty($params)) {
        $queryString = http_build_query($params, '', '&');
        $url .= '&' . $queryString;
    }
    return $url;
}

/**
 * 生成移动端使用的url地址
 * 
 * @global type $_W
 * @param type $segment
 * @param type $params
 * @return type
 */
function mobile_url($segment, $params = array()) {
    global $_W;
    list($do, $op) = explode('/', $segment);
    $url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=' . XW_NAME;
    if (!empty($do)) {
        $url .= "&do={$do}";
    }
    if (!empty($op)) {
        $url .= "&op={$op}";
    }
    if (!empty($params)) {
        $queryString = http_build_query($params, '', '&');
        $url .= '&' . $queryString;
    }
    return $url;
}

/**
 * 安全处理手机号码
 * 
 * @param type $mobile
 * @return type
 */
function mobile_mask($mobile) {
    return substr($mobile, 0, 3) . '****' . substr($mobile, 7);
}

/**
 * debug错误信息
 * @param type $value
 */
function xw_debug($value) {
    echo "<br><pre>";
    print_r($value);
    echo "</pre>";
    exit;
}

/**
 * 日志记录
 * 
 * @param type $message
 * @param type $data
 */
function xw_log($message, $data = '') {
    if ($data) {
        pdo_insert('core_text', array('content' => iserializer($data)));
        $text_id = pdo_insertid();
    }
    $log = array('errno' => 0, 'message' => $message, 'text_id' => intval($text_id), 'createtime' => TIMESTAMP, 'ip' => CLIENT_IP);
    pdo_insert('core_error_log', $log);
}

/**
 * api调用日志
 * 
 * @param type $message
 * @param type $data
 */
function api_log($message, $data = '') {
    if (DEVELOPMENT && ((CURRENT_IP && CURRENT_IP == CLIENT_IP) || CURRENT_IP == '')) {
        if ($data) {
            $message .= ' -> ';
            if (is_resource($data)) {
                $message .= '资源文件';
            } elseif (gettype($data) == 'object' || is_array($data)) {
                $message .= iserializer($data);
            } else {
                $message .= $data;
            }
        }
        $filename = IA_ROOT . '/data/logs/api-log-' . date('Ymd', TIMESTAMP) . '.' . $_GET['platform'] . '.txt';
        if (!file_exists($filename)) {
            load()->func('file');
            mkdirs(dirname($filename));
        }
        file_put_contents($filename, $message . PHP_EOL . PHP_EOL, FILE_APPEND);
    }
}

/**
 * 生成密码
 * 
 * @param type $password
 * @param type $salt
 * @return type
 */
function pwd_hash($password, $salt) {
    return md5("{$password}-{$salt}-{$GLOBALS['_W']['config']['setting']['authkey']}");
}

/**
 * 判断是否在微信环境
 * 
 * @return boolean
 */
function is_weixin() {
    if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false) {
        return false;
    }
    return true;
}

/**
 * 删除表情
 * 
 * @param type $text
 * @return type
 */
function removeEmoji($text) {
    $clean_text = "";
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);

    return $clean_text;
}

/**
 * 模板处理
 * 
 * @global type $_W
 * @param type $filename
 * @param type $flag
 * @return string
 */
function xw_template($filename, $flag = '') {
    global $_W;
    $name = XW_NAME;
    if (defined('IN_SYS')) {
        $template = $_W['xwsetting']['templat']['webview'];
        if (empty($template)) {
            $template = "default";
        }
        $source = IA_ROOT . "/addons/{$name}/web/view/{$template}/{$filename}.html";
        $compile = IA_ROOT . "/data/tpl/web/{$name}/web/view/{$template}/{$filename}.tpl.php";
        if (!is_file($source)) {
            $source = IA_ROOT . "/addons/{$name}/web/view/default/{$filename}.html";
        }
    } else {
        $template = $_W['xwsetting']['templat']['mobileview'];
        if (empty($template)) {
            $template = "default";
        }
        $source = IA_ROOT . "/addons/{$name}/mobile/view/{$template}/{$filename}.html";
        $compile = IA_ROOT . "/data/tpl/mobile/{$name}/mobile/view/{$template}/{$filename}.tpl.php";
        if (!is_file($source)) {
            $source = IA_ROOT . "/addons/{$name}/mobile/view/default/{$filename}.html";
        }
    }
    if (!is_file($source)) {
        exit("Error: template source '{$filename}' is not exist!!!");
    }
    if (!is_file($compile) || filemtime($source) > filemtime($compile)) {
        xw_template_compile($source, $compile, true);
    }
    if ($flag == TEMPLATE_FETCH) {
        extract($GLOBALS, EXTR_SKIP);
        ob_end_flush();
        ob_clean();
        ob_start();
        include $compile;
        $contents = ob_get_contents();
        ob_clean();
        return $contents;
    } else if ($flag == 'template') {
        extract($GLOBALS, EXTR_SKIP);
        return $compile;
    } else {
        return $compile;
    }
}

/**
 * 模板编译
 * 
 * @param type $from
 * @param type $to
 * @param type $inmodule
 */
function xw_template_compile($from, $to, $inmodule = false) {
    $path = dirname($to);
    if (!is_dir($path)) {
        load()->func('file');
        mkdirs($path);
    }
    $content = xw_template_parse(file_get_contents($from), $inmodule);
    if (IMS_FAMILY == 'x' && !preg_match('/(footer|header)+/', $from)) {
        $content = str_replace('微擎', '系统', $content);
    }
    file_put_contents($to, $content);
}

/**
 * 标签处理
 * 
 * @param type $str
 * @param type $inmodule
 * @return string
 */
function xw_template_parse($str, $inmodule = false) {
    $str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
    $str = preg_replace('/{template\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite || ' . intval($inmodule) . ') ? (include $this->template($1, TEMPLATE_INCLUDEPATH)) : (include template($1, TEMPLATE_INCLUDEPATH));?>', $str);

    $str = preg_replace('/{php\s+(.+?)}/', '<?php $1?>', $str);
    $str = preg_replace('/{if\s+(.+?)}/', '<?php if($1) { ?>', $str);
    $str = preg_replace('/{else}/', '<?php } else { ?>', $str);
    $str = preg_replace('/{else ?if\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
    $str = preg_replace('/{\/if}/', '<?php } ?>', $str);
    $str = preg_replace('/{loop\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
    $str = preg_replace('/{loop\s+(\S+)\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
    $str = preg_replace('/{\/loop}/', '<?php } } ?>', $str);
    $str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)}/', '<?php echo $1;?>', $str);
    $str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\[\]\'\"\$]*)}/', '<?php echo $1;?>', $str);
    $str = preg_replace('/{url\s+(\S+)}/', '<?php echo url($1);?>', $str);
    $str = preg_replace('/{url\s+(\S+)\s+(array\(.+?\))}/', '<?php echo url($1, $2);?>', $str);
    $str = preg_replace_callback('/<\?php([^\?]+)\?>/s', "template_addquote", $str);
    $str = preg_replace('/{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)}/s', '<?php echo $1;?>', $str);
    $str = str_replace('{##', '{', $str);
    $str = str_replace('##}', '}', $str);
    $str = "<?php defined('IN_IA') or exit('Access Denied');?>" . $str;
    return $str;
}

function xw_template_addquote($matchs) {
    $code = "<?php {$matchs[1]}?>";
    $code = preg_replace('/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\](?![a-zA-Z0-9_\-\.\x7f-\xff\[\]]*[\'"])/s', "['$1']", $code);
    return str_replace('\\\"', '\"', $code);
}

$my_scenfiles = array();

function my_scandir($dir) {
    global $my_scenfiles;
    if ($handle = opendir($dir)) {
        while (($file = readdir($handle)) !== false) {
            if ($file != ".." && $file != ".") {
                if (is_dir($dir . "/" . $file)) {
                    my_scandir($dir . "/" . $file);
                } else {
                    $my_scenfiles[] = $dir . "/" . $file;
                }
            }
        }
        closedir($handle);
    }
}

/**
 * 费用格式化
 * 
 * @param type $currency
 * @param type $decimals
 * @return string
 */
function currency_format($currency, $decimals = 2) {
    $currency = floatval($currency);
    if (empty($currency)) {
        return '0.00';
    }
    $currency = number_format($currency, $decimals);
    $currency = str_replace(',', '', $currency);
    return $currency;
}

/**
 * 对象和数组互转
 * 
 * @param type $array
 * @return type
 */
function object_array($array) {
    if (is_object($array)) {
        $array = (array) $array;
    } if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

/**
 * 信息提示
 * 
 * @global type $_W
 * @param type $msg
 * @param type $redirect
 * @param type $type
 */
function xw_message($msg, $redirect = '', $type = '') {
    global $_W;
    if ($redirect == 'refresh') {
        $redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
    } elseif ($redirect == 'close') {
        $redirect = 'wx.closeWindow()';
        $close = 1;
    } elseif (!empty($redirect) && !strexists($redirect, 'http://')) {
        $urls = parse_url($redirect);
        $redirect = $_W['siteroot'] . 'mobile/index.php?' . $urls['query'];
    }
    $type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'info';

    if ($_W['isajax'] || $type == 'ajax') {
        $vars = array();
        $vars['message'] = $msg;
        $vars['redirect'] = $redirect;
        $vars['type'] = $type;
        exit(json_encode($vars));
    }
    if (empty($msg) && !empty($redirect)) {
        header('location: ' . $redirect);
    }
    $label = $type;
    if ($type == 'error') {
        $label = 'danger';
    }
    if ($type == 'ajax' || $type == 'sql') {
        $label = 'warning';
    }
    if (defined('IN_API')) {
        exit($msg);
    }
    include xw_template('common/message', TEMPLATE_INCLUDEPATH);
    exit();
}

function xw_json($status = 1, $return = null) {
    $ret = array('status' => $status);
    if ($return) {
        $ret['result'] = $return;
    }
    die(json_encode($ret));
}

/**
 * 公众号消息提醒
 * 
 * @global type $_W
 * @param type $openid
 * @param type $msg
 * @param type $url
 * @param type $account
 * @return type
 */
function sendCustomNotice($openid, $msg, $url = '', $account = null) {
    global $_W;
    if (!$account) {
        load()->model('account');
        $acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account_wechats') . " WHERE `uniacid`=:uniacid LIMIT 1", array(':uniacid' => $_W['uniacid']));
        $account = WeAccount::create($acid);
    }
    if (!$account) {
        return;
    }
    $content = "";
    if (is_array($msg)) {
        foreach ($msg as $key => $value) {
            if (!empty($value['title'])) {
                $content .= $value['title'] . ":" . $value['value'] . "\n";
            } else {
                $content .= $value['value'] . "\n";
                if ($key == 0) {
                    $content .= "\n";
                }
            }
        }
    } else {
        $content = $msg;
    }
    if (!empty($url)) {
        $content .= "<a href='{$url}'>点击查看详情</a>";
    }
    return $account->sendCustomNotice(array("touser" => $openid, "msgtype" => "text", "text" => array('content' => urlencode($content))));
}

/**
 * 关键字规则判断
 * 
 * @global type $_W
 * @param type $key
 * @return type
 */
function keyExist($key = '') {
    global $_W;

    if (empty($key)) {
        return NULL;
    }

    $keyword = pdo_fetch('SELECT rid FROM ' . tablename('rule_keyword') . ' WHERE content=:content and uniacid=:uniacid limit 1 ', array(':content' => trim($key), ':uniacid' => $_W['uniacid']));

    if (!empty($keyword)) {
        $rule = pdo_fetch('SELECT * FROM ' . tablename('rule') . ' WHERE id=:id and uniacid=:uniacid limit 1 ', array(':id' => $keyword['rid'], ':uniacid' => $_W['uniacid']));

        if (!empty($rule)) {
            return $rule;
        }
    }
}

/**
 * 操作记录日志
 * 
 * @global type $_W
 * @param type $user
 * @param type $describe
 * @param type $view_url
 * @param type $data
 */
function oplog($user, $describe, $view_url, $data) {
    global $_W;
    $data = array('user' => $user, 'uniacid' => $_W['uniacid'], 'describe' => $describe, 'view_url' => $view_url, 'data' => $data, 'ip' => CLIENT_IP, 'createtime' => TIMESTAMP);
    pdo_insert("xw_weishang_oplog", $data);
}

/**
 * 一天开始
 * 
 * @return type
 */
function today_start($t = 0) {
    if (empty($t)) {
        $t = time();
    }

    $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
    return $start;
}

/**
 * 一天结束
 * 
 * @return type
 */
function today_end($t = 0) {
    if (empty($t)) {
        $t = time();
    }
    $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
    return $end;
}

/**
 * 求两个已知经纬度之间的距离,单位为米
 * @param lng1,lng2 经度
 * @param lat1,lat2 纬度
 * @return float 距离，单位米
 * */
function getdistance($lng1, $lat1, $lng2, $lat2) {
    //将角度转为狐度
    $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return number_format($s / 1000, 1);
}

/**
 * 字符串截取
 * */
function substr_text($str, $start = 0, $length, $charset = "utf-8") {
    if (function_exists("mb_substr")) {
        return mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    return $slice;
}

/**
 * 下载媒体素材图片
 * @param type $media_id
 * @param type $filepath
 * @param type $filename
 * @return type
 */
function downloadFromWxServer($media_id, $filepath, $filename = '') {
    if (empty($filename)) {
        $filename = getRandStr(16);
    }
    load()->classs('weixin.account');
    $access_token = WeAccount :: token();
    $contentType['image/gif'] = '.gif';
    $contentType['image/jpeg'] = '.jpeg';
    $contentType['image/png'] = '.png';
    $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $media_id;
    $data = ihttp_get($url);
    $filetype = $data['headers']['Content-Type'];
    $filesize = $data['headers']['Content-Length'];
    $file_succ = array();
    if ($filetype && $contentType[$filetype] && $filesize > 0) {
        $fullfilepath = $filepath . $filename . $contentType[$filetype];
        load()->func('file');
        $wr = file_write($fullfilepath, $data['content']);
        if ($wr) {
//            $srcfile = $desfile = ATTACHMENT_ROOT . '/' . $fullfilepath;
//            $image_size = getimagesize($srcfile);
//            $width = intval($image_size[0] * 0.99);
//            $result = file_image_thumb($srcfile, $desfile, $width);
//            if (isset($result['errno']) && $result['errno'] == -1) {
//                return error(-1, $result['message']);
//            } else {
//                $file_succ = array('filename' => $filename . $contentType[$filetype], 'filepath' => $fullfilepath, 'filetype' => $filetype, 'filesize' => $filesize);
//                return error(1, $file_succ);
//            }
            $file_succ = array('filename' => $filename . $contentType[$filetype], 'filepath' => $fullfilepath, 'filetype' => $filetype, 'filesize' => $filesize);
            return error(1, $file_succ);
        } else {
            return error(-1, $wr);
        }
    } else {
        return error(-1, '接口异常，下载文件失败');
    }
}

/**
 * 获取随机数
 * @param type $length
 * @return string
 */
function getRandStr($length = 8) {
    $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $keys = array_rand($chars, $length);
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[$keys[$i]];
    }
    return $password;
}
