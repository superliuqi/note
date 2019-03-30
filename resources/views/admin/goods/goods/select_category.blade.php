@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post" onsubmit="return false">
            <div class="layui-form-item">
                <label class="layui-form-label">请选择分类</label>
                <div class="layui-input-inline">
                    <select name="category" lay-filter="category">
                        <option value="">请选择分类</option>
                        {!! $category !!}
                    </select>
                </div>
                <button class="layui-btn" lay-submit="" lay-filter="go">确定</button>
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
                var category_id = $('[name="category"]').val();
                if (!category_id) {
                    layer.msg('请选择分类');
                    return false;
                }
                window.location.href='{{ url('goods/add') }}?category_id=' + category_id;
                return false;
            });
        });
    </script>
@endsection
