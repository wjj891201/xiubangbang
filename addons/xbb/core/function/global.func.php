<?php

defined('IN_IA') or exit('Access Denied');
ob_start();

//检查入驻到期时间
function checkEffective() {
    global $_W;
    $car = pdo_getall('xw_wl_car', array('uniacid' => $_W['uniacid'], 'status' => 2)); //短驳车
    foreach ($car as $k => $v) {
        if ($v['effective_time'] < $_W['timestamp']) {
            pdo_update('xw_wl_car', array('status' => 4, 'effective_time' => 0), array('id' => $v['id']));
        }
    }

    $ldp = pdo_getall('xw_wl_ldp', array('uniacid' => $_W['uniacid'], 'status' => 2)); //落地配
    foreach ($ldp as $k => $v) {
        if ($v['effective_time'] < $_W['timestamp']) {
            pdo_update('xw_wl_ldp', array('status' => 4, 'effective_time' => 0), array('id' => $v['id']));
        }
    }

    $store = pdo_getall('xw_wl_store', array('uniacid' => $_W['uniacid'], 'status' => 2)); //专线商户
    foreach ($store as $k => $v) {
        if ($v['effective_time'] < $_W['timestamp']) {
            pdo_update('xw_wl_store', array('status' => 4, 'effective_time' => 0), array('id' => $v['id']));
        }
    }
    return TRUE;
}

//判断等级
function checklevel($credit) {
    global $_W;
    $credit = floatval($credit);
    if ($credit < 100) {
        return 1;
    } elseif ($credit < 500) {
        return 2;
    } elseif ($credit < 1000) {
        return 3;
    } else {
        return 4;
    }
}

//判断推广是否到期
function checkExtendtime() {
    global $_W;
    $storeall = pdo_getall("xw_wl_store", array('status' => 2, 'extend' => 2));
    if ($storeall) {
        foreach ($storeall as $key => $value) {
            if ($value['extendtime'] < time()) {
                pdo_update("xw_wl_store", array('extend' => 1, 'type' => 0), array('id' => $value['id']));
            }
        }
    }
    return TRUE;
}

//获取具体地址经纬度
function getJwd($address) {
    $url = "http://apis.map.qq.com/ws/geocoder/v1/?address=" . $address . "&key=K6XBZ-IFPR3-OEX3J-YEWKY-UQNOJ-RQBDU";
    load()->func('communication');
    $response = ihttp_request($url, $data);
    $result = @json_decode($response['content'], true);
    $lng = $result['result']['location']['lng'];
    $lat = $result['result']['location']['lat'];
    $data = [$lng, $lat];
    return $data;
}

function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2) {
    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;
    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;
    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI / 180.0;
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $distance = $distance * $EARTH_RADIUS * 1000;
    if ($unit == 2) {
        $distance = $distance / 1000;
    }

    return round($distance, $decimal);
}

function getRandStr($length = 4) {
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

function saveWxappImg($filename, $filecode, $code) {
    global $_W;

    load()->func('file');
    if (empty($_FILES['file']['tmp_name'])) {
        $binaryfile = file_get_contents('php://input', 'r');
        if (!empty($binaryfile)) {
            mkdirs(ATTACHMENT_ROOT . '/temp');
            $tempfilename = random(5);
            $tempfile = ATTACHMENT_ROOT . '/temp/' . $tempfilename;
            if (file_put_contents($tempfile, $binaryfile)) {
                $imagesize = @getimagesize($tempfile);
                $imagesize = explode('/', $imagesize['mime']);
                $_FILES['file'] = array(
                    'name' => $tempfilename . '.' . $imagesize[1],
                    'tmp_name' => $tempfile,
                    'error' => 0,
                );
            }
        }
    }

    if (!empty($_FILES['file']['name'])) {

        if ($_FILES['file']['error'] != 0) {
            return error(-1, '上传失败，请重试！');
        }
        if (!file_is_image($_FILES['file']['name'])) {
            return error(-1, '上传失败, 请重试！');
        }
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $file_type = 'image/' . $ext;
        $file = file_upload($_FILES['file'], 'image', $filename);
        if (is_error($file)) {
            return error(-1, $file['message']);
        }
        $pathname = $file['path'];
        $fullname = ATTACHMENT_ROOT . '/' . $pathname;

        $file_succ = array(
            'name' => $_FILES['file']['name'],
            'path' => $pathname,
            'type' => $file_type,
            'filesize' => filesize($fullname),
            'filecode' => $filecode,
            'code' => $code,
        );

        setting_load('remote');
        if (!empty($_W['setting']['remote']['type'])) {
            file_remote_upload($pathname, false);
        }

        //压缩加水印
        modifyImg($fullname);

        return error(1, $file_succ);
    }
}

function modifyImg($imgurl) {
    global $_W;
    $filepath = imagecreatefromstring(file_get_contents($imgurl));
    list($fileWidth, $fileHight, $fileType) = getimagesize($imgurl);

    $maxWidth = 1000;
    if ($fileWidth > 1000) {
        //缩略图片
        $rate = $maxWidth / $fileWidth; //计算绽放比例
        $maxHeight = floor($fileHight * $rate); //计算出缩放后的高度
    } else {
        $maxWidth = $fileWidth; //计算绽放比例
        $maxHeight = $fileHight; //计算出缩放后的高度
    }

    $des_im = imagecreatetruecolor($maxWidth, $maxHeight); //创建一个缩放的画布
    imagecopyresized($des_im, $filepath, 0, 0, 0, 0, $maxWidth, $maxHeight, $fileWidth, $fileHight); //缩放
    //加水印logo
    $logo = IA_ROOT . "/attachment/images/xw_wl/logo.png";
    $logoImg = imagecreatefromstring(file_get_contents($logo));
    imagecopy($des_im, $logoImg, 0, 0, 0, 0, 123, 75);
 
      if ($fileType == 2) {
            imagejpeg($des_im, $imgurl); //保存到文件
        } else if ($fileType == 3) {
            imagepng($des_im, $imgurl);
        }
    imagedestroy($des_im);
    imagedestroy($logoImg);
    imagedestroy($filepath);
}
