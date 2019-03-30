@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">标题</label>
                <div class="layui-input-block">
                    <input type="text" name="title" lay-verify="required" lay-errormsg="标题不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">跳转链接</label>
                <div class="layui-input-block">
                    <input type="text" name="url" lay-verify="required" lay-errormsg="跳转链接不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">单选框</label>
                <div class="layui-input-block" >
                    <input type="radio" name="type" value="1" title="所有用户" checked="" lay-filter="encrypt">
                    <input type="radio" name="type" value="2" title="指定用户" lay-filter="encrypt" id="type-2">
                    <input type="radio" name="type" value="3" title="指定标签" lay-filter="encrypt" id="type-3">
                </div>
            </div>

            <div class="layui-form-item" id="div_m_id" style="display: none">
                <label class="layui-form-label">用户id</label>
                <div class="layui-input-block">
                    <textarea name="m_id" id="" cols="50" rows="10" style="resize: none"></textarea>
                </div>
            </div>

            <div class="layui-form-item" id="div_user_type" style="display: none">
                <label class="layui-form-label">用户标签</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="m_type[]" value="1" title="服务商">
                    <input type="checkbox" name="m_type[]" value="2" title="店家">
                    <input type="checkbox" name="m_type[]" value="3" title="员工">
                    <input type="checkbox" name="m_type[]" value="4" title="普通用户">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">内容</label>
                <div class="layui-input-block">
                    <textarea name="content" id="" cols="50" rows="10" style="resize: none"></textarea>
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

            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('push/edit') }}",
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

            form.on('radio(encrypt)', function(data){
                var v = $(this).val()
                if(v == 1){
                    $('#div_m_id').hide();
                    $('#div_user_type').hide();
                }else if(v == 2){
                    $('#div_m_id').show();
                    $('#div_user_type').hide();
                }else{
                    $('#div_m_id').hide();
                    $('#div_user_type').show();
                }
            });

            //类型是指定用户 默认展开
            @if(isset($item['type']) && $item['type'] == 2)
                $('#div_m_id').show();
            @elseif(isset($item['type']) && $item['type'] == 3)
                $('#div_m_id').hide();
                $('#div_user_type').show();
            @endif

            //图片上传
            {!! \App\Libs\Upload::getPlupload() !!}
        });


        //表单回填
        @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>

@endsection
