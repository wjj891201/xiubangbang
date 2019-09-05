<?php

defined('IN_IA') or exit('Access Denied');

class XbbModuleWxapp extends WeModuleWxapp
{

    /**
     * 获取电话和openid
     */
    public function doPageGetopenid()
    {
        global $_GPC, $_W;
        require 'core/class/PHP/wXBizDataCrypt.php';

        $APPID = $_W['account']['key'];
        $AppSecret = $_W['account']['secret'];

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$APPID}&secret={$AppSecret}&js_code={$_GPC['code']}&grant_type=authorization_code";
        $result = file_get_contents($url);
        $arr = json_decode($result, true);
        $session_key = $arr['session_key'];
        $openid = $arr['openid'];

        $pc = new \WXBizDataCrypt($APPID, $session_key);
        $iv = $_GPC['iv'];
        $encryptedData = $_GPC['encryptedData'];
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        if ($errCode == 0)
        {
            $info = json_decode($data, true);
            $this->result(0, '获取电话和openid', ['mobile' => $info['purePhoneNumber'], 'openid' => $openid]);
        }
        else
        {
            $this->result(1, '获取电话和openid失败');
        }
    }

    /**
     * 微信用户
     */
    public function doPageEditmember()
    {
        global $_W, $_GPC;
        $data = [
            'nickname' => $_GPC['nickname'],
            'avatar' => $_GPC['avatar'],
            'openid' => $_GPC['openid'],
            'addtime' => $_W['timestamp'],
            'uniacid' => $_W['uniacid']
        ];
        $member = pdo_get('xbb_member', ['openid' => $_GPC['openid']]);
        if (!$member)
        {
            pdo_insert('xbb_member', $data);
        }
        $info = pdo_get('xbb_member', ['openid' => $_GPC['openid']]);
        return $this->result(0, 'success', ['user' => $info]);
    }

    /**
     * 首页轮播图片
     */
    public function doPagePicture()
    {
        global $_GPC, $_W;
        $picture = pdo_getall('xbb_picture', ['uniacid' => $_W['uniacid']]);
        foreach ($picture as $key => $vo)
        {
            $picture[$key]['imgurl'] = tomedia($vo['imgurl']);
        }
        $this->result(0, '获取成功', $picture);
    }

    /**
     * 银行
     */
    public function doPageBank()
    {
        global $_GPC, $_W;
        $bank = pdo_getall('xbb_bank', ['uniacid' => $_W['uniacid']]);
        $this->result(0, '获取成功', $bank);
    }

    /**
     * 服务项目
     */
    public function doPageProject()
    {
        global $_GPC, $_W;
        $project = pdo_getall('xbb_project', ['uniacid' => $_W['uniacid']]);
        $this->result(0, '获取成功', $project);
    }

    /**
     * 申请
     */
    public function doPageApply()
    {
        global $_GPC, $_W;
        $data = [
            'name' => $_GPC['name'],
            'telphone' => $_GPC['telphone'],
            'address' => $_GPC['address'],
            'identity_photo' => $_GPC['identity_photo'],
            'addtime' => time(),
            'uniacid' => $_W['uniacid'],
        ];
        $result = pdo_insert('xbb_apply', $data);
        if (!empty($result))
        {
            $this->result(0, '提交成功', ['status' => 'success']);
        }
    }

    /**
     * 上传图片
     */
    public function doPageUploadimg()
    {
        global $_GPC, $_W;
        load()->func('file');
        $result = file_upload($_FILES['file'], 'image');
        $this->result(0, '上传成功', $result);
    }

    /**
     * 优质解答
     */
//    public function doPageAnswer()
//    {
//        global $_GPC, $_W;
//        $sql = "select * from" . tablename('lv_suo_answer') . " where uniacid=" . $_W['uniacid'];
//        $answer = pdo_fetchall($sql);
//        // 分页开始
//        $total = count($answer);
//        $pageindex = max($_GPC['page'], 1);
//        $pagesize = 2;
//        $page = pagination($total, $pageindex, $pagesize);
//        $p = ($pageindex - 1) * 2;
//        $sql .= " order by answerid desc limit " . $p . " , " . $pagesize;
//        $answer = pdo_fetchall($sql);
//        // 分页結束
//        $this->result(0, $total, $answer);
//    }
//    public function doPageConsult()
//    {
//        global $_GPC, $_W;
//        $data = [
//            'content' => $_GPC['content'],
//            'phone' => $_GPC['phone'],
//            'addtime' => time(),
//            'uniacid' => $_W['uniacid'],
//        ];
//        $result = pdo_insert('lv_suo_consult', $data);
//        if (!empty($result))
//        {
//            $this->result(0, '提交成功', ['status' => 'success']);
//        }
//    }
}
