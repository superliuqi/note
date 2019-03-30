@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">权限名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="title" lay-verify="required" lay-errormsg="名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">菜单栏目</label>
                <div class="layui-input-inline">
                    <select name="menu_top" lay-filter="menu_top" lay-verify="required" lay-errormsg="请选择菜单栏目">

                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="menu_child" lay-filter="menu_child" lay-verify="required" lay-errormsg="请选择菜单栏目">
                    </select>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">权限码</label>
                <div class="layui-input-block">
                    <textarea name="right" lay-verify="required" lay-errormsg="权限码不能为空" class="layui-textarea"></textarea>
                    <div class="layui-form-mid layui-word-aux">请输入权限码，每行一个</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">快捷选择</label>
                <div class="layui-input-inline">
                    <select name="url_controller" lay-filter="url_controller">
                        <option value="">请选择</option>
                        @foreach($url_arr as $key => $val)
                            <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="url_action" lay-filter="url_action">
                    </select>
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
                    url : "{{ url('admin_role_right/edit') }}",
                    data : data.field,
                    success : function(result) {
                        if (result.code == 0) {
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

            //自定义函数
            var my_function = {
                //获取菜单
                get_menu : function (select_name, parent_id = 0, default_id = 0) {
                    $.ajax({
                        type : "GET",
                        url : "{{ url('admin_role_right/get_menu') }}",
                        data : {parent_id:parent_id},
                        success : function(result) {
                            if (result.code==0) {
                                var html = '<option value="">请选择</option>';
                                $.each(result.data, function (index, value) {
                                    html += '<option value="' + value.id + '"';
                                    if (value.id == default_id) {
                                        html += 'selected';
                                    }
                                    html += '>' + value.title + '</option>';
                                })
                                $('[name="' + select_name + '"]').html(html);
                                form.render('select');
                            }
                        },
                        error : function () {
                            layer.msg('操作失败，请刷新页面重试！');
                        }
                    });
                }
            }
            form.on('select(menu_top)', function(data){
                my_function.get_menu('menu_child', data.value);
            });

            //快捷选择
            form.on('select(url_controller)', function(data){
                url_arr = @json($url_arr);
                url_key = data.value;
                var html = '<option value="">请选择</option>';
                var action_arr = url_arr[url_key];
                for ( var i = 0; i <action_arr.length; i++){
                    html += '<option value="' + action_arr[i] + '">' + action_arr[i] + '</option>';
                }
                $('[name="url_action"]').html(html);
                form.render('select');
            });
            //快捷选择完成给文本框赋值
            form.on('select(url_action)', function(data){
                right = $('[name="right"]').val();
                newline = '';
                if (right.length > 0) {
                    newline = '\n';
                }
                $('[name="right"]').val(right + newline + data.value);
            });

            //数据回填
            @if ($item)
            my_function.get_menu('menu_top', 0, {{ $item['menu_top'] }});
            my_function.get_menu('menu_child', {{ $item['menu_top'] }}, {{ $item['menu_child'] }});
            @else
            my_function.get_menu('menu_top');
            @endif
        });

        //表单回填
        @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>
@endsection
