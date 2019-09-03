<?php

defined('IN_IA') or exit('Access Denied');

class XbbModuleWxapp extends WeModuleWxapp
{
//    public function doPageCode()
//    {
//        global $_GPC, $_W;
//        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$_W['account']['key']}&secret={$_W['account']['secret']}&js_code={$_GPC['code']}&grant_type=authorization_code";
//        $result = file_get_contents($url);
//        $result = json_decode($result, true);
//        $data = $result;
//        $this->result(0, '获取openid', $data);
//    }

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
