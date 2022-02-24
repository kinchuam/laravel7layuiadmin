<div class="layui-form-item">
    <label for="" class="layui-form-label">排序</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="sort" value="0">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" lay-vertype="tips">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">图片</label>
    <div class="layui-input-block">
        <div class="layui-upload" id="thumb"></div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">链接</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" placeholder="请输入链接" name="url">
    </div>
</div>

<div class="layui-form-item" pane="">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-block">
        <input type="radio" name="status" value="1" title="显示">
        <input type="radio" name="status" value="0" title="隐藏" checked>
    </div>
</div>
