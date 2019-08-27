<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default">
    <div class="panel-heading">
        轮播图片管理
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>轮播图片URL地址</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($picture)) { foreach($picture as $index => $item) { ?>
                <tr>
                    <td><?php  echo $item['picid'];?></td>
                    <td><?php  echo $item['imgurl'];?></td>
                    <td><button type="button" class="btn btn-primary btn-xs btn-edit" data-toggle="modal" data-target="#myModal" picid="<?php  echo $item['picid'];?>">编辑</button></td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 1030;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">轮播图片编辑</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" action="<?php  echo $this->createWebUrl('picture');?>&action=save" method="post">
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">轮播图片</label>
                        <div class="col-sm-10">
                            <?php  echo tpl_form_field_image('imgurl');?>
                            <input type="hidden" name='picid'/>
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
            var picid = $(this).attr('picid');
            $.get("<?php  echo $this->createWebUrl('picture')?>" + "&action=get&picid=" + picid, function (result) {
                var data = $.parseJSON(result);
                $("input[name='imgurl']").val(data.imgurl);
                $("input[name='picid']").val(data.picid);
                $('.img-responsive').attr('src', data.trueimgurl);
            });
        });
    });
</script>
