@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post" onsubmit="return false">
            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block">
                    <input type="text" name="username" lay-verify="required" lay-errormsg="用户名不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" id="password" lay-verify="password" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">重复密码</label>
                <div class="layui-input-block">
                    <input type="password" name="repeat_password" lay-verify="resspaword" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">选择用户组</label>
                <div class="layui-input-block">
                    <select name="group_id" lay-verify="required" lay-errormsg="请选择用户组">
                        <option value=""></option>
                        @foreach($group as $value)
                            <option value="{{ $value['id'] }}">{{ $value['title'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">头像</label>
                <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image">选择图片</button>
                <a href="{{ isset($item['headimg']) ? $item['headimg'] : '' }}" target="_blank"><img src="{{ isset($item['headimg']) ? $item['headimg'] : '' }}" width="50" style="display: {{ isset($item['headimg']) ? : 'none' }};"></a>
                <input type="hidden" value="" name="headimg" lay-verify="required" lay-errormsg="头像不能为空">
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">昵称</label>
                <div class="layui-input-block">
                    <input type="text" name="nick_name" lay-verify="required" lay-errormsg="昵称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="full_name" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">电话</label>
                <div class="layui-input-block">
                    <input type="text" name="tel" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">邮箱</label>
                <div class="layui-input-block">
                    <input type="text" name="email" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">性别</label>
                <div class="layui-input-block">
                    @foreach(\App\Models\MemberProfile::SEX_DESC as $key => $value)
                        <input type="radio" name="sex" value="{{ $key }}" title="{{ $value }}" lay-verify="required" @if ($key == 0) checked @endif>
                    @endforeach
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">地区</label>
                <div class="layui-input-inline">
                    <select name="prov_id" lay-filter="prov_id">

                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="city_id" lay-filter="city_id">
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="area_id" lay-filter="area_id">
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">用户类型</label>
                <div class="layui-input-inline">
                    <select name="type" lay-filter="type">
                        <option value="0">请选择</option>
                        @foreach(\App\Models\MemberProfile::USER_TYPE as $key => $val) {
                        <option value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="depart_id" lay-filter="depart_id">
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
    <script src="{{ URL::asset('admin/lib/plupload/plupload.full.min.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/lib/plupload/i18n/zh_CN.js') }}" charset="utf-8"></script>
    <script>
        layui.config({
            base: '/admin/js/',
            version: new Date().getTime()
        }).use(['form', 'global'], function(){
            var form = layui.form,
                layer = layui.layer,
                global = layui.global,
                $ = layui.$;

            //自定义验证规则
            form.verify({
                @if (!$item)
                password: [
                    /^[\S]{6,20}$/,
                    '密码必须6到20位，且不能出现空格'
                ],
                @endif
                resspaword:function(value){
                    var pass = $('input[name="password"]').val();
                    if(value != pass){
                        return '两次密码不一致';
                    }
                }
            });

            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('member/edit') }}",
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

            //自定义函数
            var my_function = {
                //获取菜单
                get_area : function (select_name, parent_id = 0, default_id = 0) {
                    $.ajax({
                        type : "GET",
                        url : "{{ url('area') }}",
                        data : {parent_id:parent_id},
                        success : function(result) {
                            if (result.code==0) {
                                var html = '<option value="">请选择</option>';
                                $.each(result.data, function (index, value) {
                                    html += '<option value="' + value.id + '"';
                                    if (value.id == default_id) {
                                        html += 'selected';
                                    }
                                    html += '>' + value.name + '</option>';
                                })
                                $('[name="' + select_name + '"]').html(html);
                                form.render('select');
                            }
                        },
                        error : function () {
                            layer.msg('操作失败，请刷新页面重试！');
                        }
                    });
                },
                //获取部门
                department : function (default_id = 0) {
                    $.ajax({
                        type : "GET",
                        url : "{{ url('member/department') }}",
                        data : {},
                        success : function(result) {
                            if (result.code==0) {
                                var html = '<option value="0">请选择</option>';
                                $.each(result.data, function (index, value) {
                                    html += '<option value="' + value.id + '"';
                                    if (value.id == default_id) {
                                        html += 'selected';
                                    }
                                    html += '>' + value.title + '</option>';
                                })
                                $('[name="depart_id"]').html(html);
                                form.render('select');
                            }
                        },
                        error : function () {
                            layer.msg('操作失败，请刷新页面重试！');
                        }
                    });
                }
            }
            //监听省市区选择
            form.on('select(prov_id)', function(data){
                my_function.get_area('city_id', data.value);
            });
            form.on('select(city_id)', function(data){
                my_function.get_area('area_id', data.value);
            });

            //省市区数据回填
            @if (isset($item['prov_id']))
            my_function.get_area('prov_id', 0, {{ $item['prov_id'] }});
            my_function.get_area('city_id', {{ $item['prov_id'] }}, {{ $item['city_id'] }});
            my_function.get_area('area_id', {{ $item['city_id'] }}, {{ $item['area_id'] }});
            @else
            my_function.get_area('prov_id');
            @endif

            //监听用户角色选择
            form.on('select(type)', function(data){
                if (data.value == {{ \App\Models\MemberProfile::USER_MEMBER }}) {
                    my_function.department();
                } else {
                    var html = '<option value="0">请选择</option>';
                    $('[name="depart_id"]').html(html);
                    form.render('select');
                }
            });
            @if (isset($item['depart_id']) && $item['type'] == \App\Models\MemberProfile::USER_MEMBER)
            my_function.department({{ $item['depart_id'] }});
            @endif

            //图片上传
            {!! \App\Libs\Upload::getPlupload('member') !!}
        });

        //表单回填
        @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>
@endsection
