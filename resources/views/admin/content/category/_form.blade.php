<div class="layui-form-item">
    <label class="layui-form-label">排序</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="sort" value="0">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" lay-vertype="tips" >
    </div>
</div>

<div class="layui-form-item" pane="">
    <label class="layui-form-label">是否显示</label>
    <div class="layui-input-block">
        <input type="radio" name="status" value="1" title="是" >
        <input type="radio" name="status" value="0" title="否" checked>
    </div>
</div>

