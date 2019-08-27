<?php

defined('IN_IA') or exit('Access Denied');

class XbbModuleSite extends WeModuleSite
{

    /**
     * 轮播图添加、编辑、删除
     */
    public function doWebPicture()
    {
        global $_GPC, $_W;
        if ($_GPC['action'] == 'get')
        {
            $picture = pdo_get('xbb_picture', ['picid' => $_GPC['picid']]);
            $picture['trueimgurl'] = tomedia($picture['imgurl']);
            return json_encode($picture);
        }
        if ($_GPC['action'] == 'del')
        {
            $res = pdo_delete('xbb_picture', array('picid' => $_GPC['picid']));
            if ($res)
            {
                echo 'ok';
                exit;
            }
        }
        if ($_GPC['action'] == 'save')
        {
            if (!empty($_GPC['picid']))
            {
                //编辑
                $data = ['imgurl' => $_GPC['imgurl']];
                $result = pdo_update('xbb_picture', $data, ['picid' => $_GPC['picid']]);
                if (!empty($result))
                {
                    message('编辑成功', $this->createWebUrl('picture'), 'success');
                }
            }
            else
            {
                //添加
                $data = ['imgurl' => $_GPC['imgurl'], 'create_time' => time(), 'uniacid' => $_W['uniacid']];
                $result = pdo_insert('xbb_picture', $data);
                if (!empty($result))
                {
                    message('添加成功', $this->createWebUrl('picture'), 'success');
                }
            }
        }
        $picture = pdo_getall('xbb_picture', ['uniacid' => $_W['uniacid']]);
        load()->func('tpl');
        include $this->template('picture');
    }

    /**
     * 银行添加、编辑、删除
     */
    public function doWebBank()
    {
        global $_GPC, $_W;

        if ($_GPC['action'] == 'get')
        {
            $bank = pdo_get('xbb_bank', ['id' => $_GPC['id']]);
            return json_encode($bank);
        }
        if ($_GPC['action'] == 'del')
        {
            $result = pdo_delete('xbb_bank', ['id' => $_GPC['id']]);
            if ($result)
            {
                echo 'ok';
                exit;
            }
        }
        if ($_GPC['action'] == 'save')
        {
            $data = [
                'name' => $_GPC['name'],
                'uniacid' => $_W['uniacid']
            ];
            if (!empty($_GPC['id']))
            {
                $result = pdo_update('xbb_bank', $data, ['id' => $_GPC['id']]);
                if (!empty($result))
                {
                    message('编辑成功', $this->createWebUrl('bank'), 'success');
                }
            }
            else
            {
                $data['create_time'] = time();
                $result = pdo_insert('xbb_bank', $data);
                if (!empty($result))
                {
                    message('添加成功', $this->createWebUrl('bank'), 'success');
                }
            }
        }
        $sql = "select * from" . tablename('xbb_bank') . " where uniacid=" . $_W['uniacid'];
        $bank = pdo_fetchall($sql);
        // 分页开始
        $total = count($bank);
        $pageindex = max($_GPC['page'], 1);
        $pagesize = 10;
        $page = pagination($total, $pageindex, $pagesize);
        $p = ($pageindex - 1) * 2;
        $sql .= " order by id desc limit " . $p . " , " . $pagesize;
        $bank = pdo_fetchall($sql);
        // 分页結束
        load()->func('tpl');
        include $this->template('bank');
    }

    public function doWebConsult()
    {
        global $_GPC, $_W;
        if ($_GPC['action'] == 'get')
        {
            $consult = pdo_get('lv_suo_consult', ['consultid' => $_GPC['consultid']]);
            $consult['addtime'] = date('Y-m-d H:i:s', $consult['addtime']);
            return json_encode($consult);
        }
        $sql = "select * from" . tablename('lv_suo_consult') . " where uniacid=" . $_W['uniacid'];
        $consult = pdo_fetchall($sql);
        // 分页开始
        $total = count($consult);
        $pageindex = max($_GPC['page'], 1);
        $pagesize = 2;
        $page = pagination($total, $pageindex, $pagesize);
        $p = ($pageindex - 1) * 2;
        $sql .= " order by consultid desc limit " . $p . " , " . $pagesize;
        $consult = pdo_fetchall($sql);
        // 分页結束
        load()->func('tpl');
        include $this->template('consult');
    }

}