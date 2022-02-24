<div class="layui-form-item">
    <label for="" class="layui-form-label">父级</label>
    <div class="layui-input-block">
        <div id="parent"></div>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">名称</label>
    <div class="layui-input-block" >
        <input type="text" name="name" lay-verify="required" lay-vertype="tips" class="layui-input" placeholder="如：system.index">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">显示名称</label>
    <div class="layui-input-block" >
        <input type="text" name="display_name" lay-verify="required" lay-vertype="tips" class="layui-input" placeholder="如：系统管理">
    </div>
</div>

<div class="layui-form-item" pane="">
    <label class="layui-form-label">类型</label>
    <div class="layui-input-block">
        <input type="radio" name="genre" value="1" title="菜单" checked>
        <input type="radio" name="genre" value="2" title="按钮" >
    </div>
</div>

