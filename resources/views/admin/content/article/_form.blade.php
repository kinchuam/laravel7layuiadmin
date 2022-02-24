<div class="layui-form-item">
    <label class="layui-form-label">分类</label>
    <div class="layui-input-block">
        <select name="category_id">
            <option value="0">默认</option>
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">排序</label>
    <div class="layui-input-block">
        <input class="layui-input" placeholder="请输入排序" type="number" name="sort" value="0">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label must">标题</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" placeholder="请输入标题" name="title" lay-verify="required" lay-vertype="tips" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">描述</label>
    <div class="layui-input-block">
        <textarea name="desc" placeholder="请输入内容" class="layui-textarea"></textarea>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">缩略图</label>
    <div class="layui-input-block">
        <div class="layui-upload" id="thumb"></div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label must">内容</label>
    <div class="layui-input-block">
        <textarea  class="layui-textarea" id="content" name="content"></textarea>
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
