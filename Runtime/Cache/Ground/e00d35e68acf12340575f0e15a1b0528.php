<?php if (!defined('THINK_PATH')) exit();?><form id="form" action="<?php echo U('Setting/blockadd');?>" method="post" role="form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo ($title); ?>-<span class="text-success"><?php echo ($info["name"]); ?></span></h4>
    </div>

    <div class="modal-body">
        <div class="form-group">
            <label for="name">场地名称：</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="填写场地名称">
        </div>
        <div class="form-group">
            <label for="remark">场地备注：</label>
            <input type="text" name="remark" class="form-control" id="remark" placeholder="填写场地备注">
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="items_id" value="<?php echo ($items_id); ?>" />
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">关闭</button>
        <button type="submit" id="submit" class="btn btn-primary">确认提交</button>
    </div>
</form>
<script src="/Public/Static/jquery.validate.min.js"></script>
<script>
    $('#submit').on('click', function () {
        var $btn = $(this).button('loading');
        $("#form").validate({
//        errorPlacement:function(error,element) {
//            error.appendTo(element.parent('div').prev('label'));
//        },
            rules: {
                name: "required"
            },
            messages: {
                name: "填写场地名称"
            },
            showErrors: function (errorMap, errorList) {
                if (this.numberOfInvalids() > 0) {
                    $btn.button('reset');
                }
            }
        });
    })
</script>