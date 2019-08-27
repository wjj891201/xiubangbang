<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default" style="margin-top: 20px;">
    <div class="panel-heading">
        银行管理
    </div>
    <div class="panel-body">
        <a href="#" class="btn btn-primary btn-add" data-toggle="modal" data-target="#myModal" style="margin-bottom: 30px;">
            <i class="glyphicon glyphicon-plus"></i>添加银行
        </a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center" width="40%">名称</th>
                    <th class="text-center" width="30%">添加时间</th>
                    <th class="text-center" width="30%">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($bank)) { foreach($bank as $index => $item) { ?>
                <tr>
                    <td class="text-center"><?php  echo $item['name'];?></td>
                    <td class="text-center"><?php  echo date('Y-m-d H:i:s',$item['create_time'])?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-primary btn-xs btn-edit" data-toggle="modal" data-target="#myModal" id="<?php  echo $item['id'];?>">编辑</button>
                        <!--                        <a href="<?php  echo $this->createWebUrl('bank')?>&action=del&id=<?php  echo $item['id'];?>" class="btn btn-danger btn-xs">刪除</a>-->
                        <button type="button" class="btn btn-danger btn-xs btn-del" id="<?php  echo $item['id'];?>">删除</button>
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
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" action="<?php  echo $this->createWebUrl('bank');?>&action=save" method="post">
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" placeholder="请输入银行名称">
                            <input type="hidden" name='id'/>
                        </div>
                    </div>  
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-danger">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>

<script>
    $(function () {
        $('.btn-add').click(function () {
            $('#myModalLabel').html('银行添加');
            $("input[name='name']").val('');
            $("input[name='id']").val('');
        });
        $('.btn-edit').click(function () {
            $('#myModalLabel').html('银行编辑');
            var id = $(this).attr('id');
            $.get("<?php  echo $this->createWebUrl('bank')?>" + "&action=get&id=" + id, function (result) {
                var data = $.parseJSON(result);
                $("input[name='name']").val(data.name);
                $("input[name='id']").val(data.id);

            });
        });
        $('.btn-del').click(function () {
            var id = $(this).attr('id');
            var obj = $(this);
            layer.confirm('确认要删除吗？', {icon: 3, title: '提示'}, function (index) {
                $.get("<?php  echo $this->createWebUrl('bank')?>" + "&action=del&id=" + id, function (result) {
                    if (result == 'ok') {
                        obj.parents("tr").remove();
                        layer.msg('已成功删除!', {icon: 1, time: 1000});
                    }
                });
            });
        });
    });
</script>
