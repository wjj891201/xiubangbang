<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default">
    <div class="panel-heading">
        咨询管理
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>客户电话</th>
                    <th>咨询时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($consult)) { foreach($consult as $index => $item) { ?>
                <tr>
                    <td><?php  echo $item['consultid'];?></td>
                    <td><?php  echo $item['phone'];?></td>
                    <td><?php  echo date('Y-m-d H：i:s',$item['addtime'])?></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-xs btn-edit" data-toggle="modal" data-target="#myModal" consultid="<?php  echo $item['consultid'];?>">查看详情</button>
                    </td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
        <?php  echo $page;?>
    </div>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">查看详情</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">电话</label>
                        <label for="firstname" style="text-align: left;padding-left: 15px;padding-right: 15px;" class="col-sm-2 control-label phone"></label>
                    </div>  
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">时间</label>
                        <label for="firstname" style="text-align: left;padding-left: 15px;padding-right: 15px;" class="col-sm-2 control-label addtime"></label>
                    </div> 
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">内容</label>
                        <label for="firstname" style="text-align: left;padding-left: 15px;padding-right: 15px;" class="col-sm-2 control-label content"></label>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>

<script>
    $(function () {
        $('.btn-edit').click(function () {
            var consultid = $(this).attr('consultid');
            $.get("<?php  echo $this->createWebUrl('consult')?>" + "&action=get&consultid=" + consultid, function (result) {
                var data = $.parseJSON(result);
                $(".phone").html(data.phone);
                $('.addtime').html(data.addtime);
                $('.content').html(data.content);
            });
        });
    });
</script>
