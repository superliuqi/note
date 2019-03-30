@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post" onsubmit="return false">
            <div class="layui-form-item">
                <label class="layui-form-label">规格名称</label>
                <div class="layui-input-block">
                    <input type="text" name="title" lay-verify="required" lay-errormsg="规格名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">上传图片</label>
                <div class="layui-input-block">
                    @foreach(\App\Models\Spec::TYPE_IMAGE_DESC as $id => $value)
                    <input type="radio" name="type" value="{{ $id }}" title="{{ $value }}" @if ($id == \App\Models\Spec::TYPE_IMAGE_OFF) checked @endif>
                    @endforeach
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">请选择分类</label>
                <div class="layui-input-block">
                    <select name="category_id" lay-verify="required" lay-errormsg="请选择分类">
                        <option value=""></option>
                        {!! $category !!}
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <input type="text" name="note" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-block">
                    <input type="text" name="position" lay-verify="number" lay-errormsg="排序只能是数字" autocomplete="off" class="layui-input" value="999">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="id" value="" />
                    <button class="layui-btn" lay-submit="" lay-filter="go">保存</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer')
    <script src="{{ URL::asset('admin/js/set_form.js') }}" charset="utf-8"></script>
    <script>
        layui.config({
            base: '/admin/js/',
            version: new Date().getTime()
        }).use(['form', 'global'], function(){
            var form = layui.form,
                layer = layui.layer,
                global = layui.global,
                $ = layui.$;
            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('spec/edit') }}",
                    data : data.field,
                    success : function(result) {
                        if ( result.code == 0 ) {
                            layer.msg('保存成功', {time: 1000}, function () {
                                global.layer_close();
                            });
                        } else {
                            layer.msg(result.msg);
                        }
                    },
                    error : function () {
                        layer.msg('操作失败，请刷新页面重试！');
                    }
                });
                return false;
            });
        });

        //表单回填
        @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>
@endsection
