
<div class="layui-form-item">
    <label for="" class="layui-form-label">父级</label>
    <div class="layui-input-block">
        <div id="category"></div>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">名称</label>
    <div class="layui-input-block" >
        <input type="text" name="name" lay-verify="required" lay-vertype="tips" class="layui-input" placeholder="如：system.index">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">图标</label>
    <div class="layui-input-block">
        <input type="hidden" name="icon" class="icon">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">路由</label>
    <div class="layui-input-block" >
        <input class="layui-input" type="text" name="route" placeholder="如：admin.member" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">权限</label>
    <div class="layui-input-block">
        <div id="permission"></div>
    </div>
</div>

<div class="layui-form-item" pane="">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-block">
        <input type="radio" name="status" value="1" title="显示">
        <input type="radio" name="status" value="0" title="隐藏" checked>
    </div>
</div>

