@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
                <div class="layui-field-box">


                    <div class="layui-form-item">
                        <label class="layui-form-label">操作前</label>
                        <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image">选择图片</button>
                        <a href="{{ isset($item['image']) ? $item['image'] : '' }}" target="_blank"><img src="{{ isset($item['image']) ? $item['image'] : '' }}" width="50" style="display: {{ isset($item['image']) ? : 'none' }};"></a>
                        <input type="hidden" value="" name="image">
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">操作后</label>
                        <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image1">选择图片</button>
                        <a href="{{ isset($item['contrast_image']) ? $item['contrast_image'] : '' }}" target="_blank"><img src="{{ isset($item['contrast_image']) ? $item['contrast_image'] : '' }}" width="50" style="display: {{ isset($item['contrast_image']) ? : 'none' }};"></a>
                        <input type="hidden" value="" name="contrast_image">
                    </div>


                </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="doctor_id" value="" />
                    <button class="layui-btn" lay-submit="" lay-filter="go">保存</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer')
    <script src="{{ URL::asset('admin/js/set_form.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/lib/wangeditor/wangEditor.min.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/lib/plupload/plupload.full.min.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/lib/plupload/i18n/zh_CN.js') }}" charset="utf-8"></script>
    <script>
        layui.config({
            base: '/admin/js/',
            version: new Date().getTime()
        }).use(['form', 'global', 'laytpl'], function(){
            var form = layui.form,
                    layer = layui.layer,
                    global = layui.global,
                    laytpl = layui.laytpl,
                    goods_sku = layui.goods_sku,
                    $ = layui.$;

            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('doctor_case/edit') }}",
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

            //图片上传
            {!! \App\Libs\Upload::getPlupload('doctor_case') !!}

            //加载编辑器
            {!! \App\Libs\Editor::editorCreate() !!}

        });

        //表单回填
                @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>
@endsection
