@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">角色名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="title" lay-verify="required" lay-errormsg="名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            @foreach($role_right as $key => $menu_top)
                <input type="checkbox" lay-skin="primary" lay-filter="select_top" title="{{ $menus[$key] }}" value="select_{{ $key }}" data-top="{{ $key }}"><hr>
                @foreach($menu_top as $k => $menu_child)
                <fieldset class="layui-elem-field" id="select_{{ $key }}">
                    <legend><input type="checkbox" lay-skin="primary" lay-filter="select_child" title="{{ $menus[$k] }}" value="select_{{ $k }}" data-top="{{ $key }}" data-child="{{ $k }}"> </legend>
                    <div class="layui-field-box" id="select_{{ $k }}">
                        @foreach($menu_child as $val)
                            <div class="role_checkbox">
                                <input type="checkbox" name="right[{{ $key }}][{{ $k }}][]" lay-filter="right" title="{{ $val['title'] }}" value="{{ $val['right'] }}" data-top="{{ $key }}" data-child="{{ $k }}" @if (isset($item['right'][$key][$k]) && in_array($val['right'], $item['right'][$key][$k])) checked @endif >
                            </div>
                        @endforeach
                    </div>
                </fieldset>
                @endforeach
            @endforeach
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
                    url : "{{ url('admin_role/edit') }}",
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
                //检查全选状态
                check_right : function (top_id, child_id) {
                    var top_num   = $('#select_'+top_id+' [lay-filter="right"]').length;
                    var child_num   = $('#select_'+child_id+' [lay-filter="right"]').length;
                    var checked_top_num   = $('#select_'+top_id+' [lay-filter="right"]:checked').length;
                    var checked_child_num   = $('#select_'+child_id+' [lay-filter="right"]:checked').length;

                    if (top_num <= checked_top_num) {
                        $('[value="select_' + top_id + '"]').prop('checked',true);
                    } else {
                        $('[value="select_' + top_id + '"]').prop('checked',false);
                    }
                    if (child_num <= checked_child_num) {
                        $('[value="select_' + child_id + '"]').prop('checked',true);
                    } else {
                        $('[value="select_' + child_id + '"]').prop('checked',false);
                    }
                    form.render('checkbox');
                }
            }

            form.on('checkbox(select_top)', function(data){
                if(this.checked == true) {
                    $('#'+ data.value +' [type="checkbox"]').prop('checked',true);
                } else {
                    $('#'+ data.value +' [type="checkbox"]').prop('checked',false);
                }
                form.render('checkbox');
            });
            form.on('checkbox(select_child)', function(data){
                if(this.checked == true) {
                    $('#'+ data.value +' [type="checkbox"]').prop('checked',true);
                } else {
                    $('#'+ data.value +' [type="checkbox"]').prop('checked',false);
                }
                form.render('checkbox');
            });

            form.on('checkbox', function(data){
                my_function.check_right($(this).attr('data-top'), $(this).attr('data-child'));
            });

            $('[lay-filter="right"]').each(
                function(i) {
                    my_function.check_right($(this).attr('data-top'), $(this).attr('data-child'));
                }
            );
        });

        //表单回填
        @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>
@endsection
