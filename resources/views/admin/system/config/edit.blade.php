@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">名称</label>
                <div class="layui-input-block">
                    <input type="text" name="title" lay-verify="required" lay-errormsg="名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">参数key名称</label>
                <div class="layui-input-block">
                    <input type="text" name="key_name" lay-verify="required" lay-errormsg="参数key名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">参数值</label>
                <div class="layui-input-block">
                    <input type="text" name="value" lay-verify="required" lay-errormsg="参数值不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">类型</label>
                <div class="layui-input-block">
                    <input type="radio" name="input_type" value="text" title="单行文本框" checked="">
                    <input type="radio" name="input_type" value="textarea" title="多行文本">
                    <input type="radio" name="input_type" value="radio" title="单选框">
                    <input type="radio" name="input_type" value="select" title="下拉框">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">选择参数值</label>
                <div class="layui-input-block">
                    <textarea name="select_value" class="layui-textarea"></textarea>
                    <div class="layui-form-mid layui-word-aux">请输入参数值，每行一个</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">tab名称</label>
                <div class="layui-input-block">
                    <select name="tab_name" lay-verify="required" lay-errormsg="请选择tab名称">
                        @foreach(\App\Models\Config::TAB_NAME as $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-block">
                    <input type="text" name="position" value="999" lay-verify="number" lay-errormsg="排序不只能是数字" autocomplete="off" class="layui-input">
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
                    url : "{{ url('config/edit') }}",
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
    </script>
@endsection
