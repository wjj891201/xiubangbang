<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<a href="#" class="btn btn-success btn-add" data-toggle="modal" data-target="#myModal">
    <i class="glyphicon glyphicon-plus"></i>添加优质解答
</a>
<div class="panel panel-default" style="margin-top: 20px;">
    <div class="panel-heading">
        优质解答
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>类型</th>
                    <th>电话号码</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($answer)) { foreach($answer as $index => $item) { ?>
                <tr>
                    <td><?php  echo $item['answerid'];?></td>
                    <td><?php  echo $item['type'];?></td>
                    <td><?php  echo $item['phone'];?></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-xs btn-edit" data-toggle="modal" data-target="#myModal" answerid="<?php  echo $item['answerid'];?>">编辑</button>
                        <a href="<?php  echo $this->createWebUrl('answer')?>&action=del&answerid=<?php  echo $item['answerid'];?>" class="btn btn-danger btn-xs">刪除</a>
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
                <h4 class="modal-title" id="myModalLabel">优质解答编辑/添加</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" action="<?php  echo $this->createWebUrl('answer');?>&action=save" method="post">
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">案件类型</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="type" placeholder="请输入案件类型">
                            <input type="hidden" name='answerid'/>
                        </div>
                    </div>  
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">电话号码</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="phone" placeholder="请输入电话号码">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">案件内容</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content"></textarea>
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
        $('.btn-edit').click(function () {
            var answerid = $(this).attr('answerid');
            $.get("<?php  echo $this->createWebUrl('answer')?>" + "&action=get&answerid=" + answerid, function (result) {
                var data = $.parseJSON(result);
                $("input[name='type']").val(data.type);
                $("input[name='phone']").val(data.phone);
                $("input[name='answerid']").val(data.answerid);
                $('textarea').html(data.content);

            });
        });
        $('.btn-add').click(function () {
            $("input[name='type']").val('');
            $("input[name='phone']").val('');
            $("input[name='answerid']").val('');
            $('textarea').html('');
        });
    });
</script>
