@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">广告名称</label>
                <div class="layui-input-block">
                    <input type="text" name="title" lay-verify="required" lay-errormsg="广告名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">图片</label>
                <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image">选择图片</button>
                <a href="{{ isset($item['image']) ? $item['image'] : '' }}" target="_blank"><img src="{{ isset($item['image']) ? $item['image'] : '' }}" width="50" style="display: {{ isset($item['image']) ? : 'none' }};"></a>
                <input type="hidden" value="" name="image">
                建议尺寸{{ $adv_group['width'] }}px × {{ $adv_group['height'] }}px
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">跳转类型</label>
                <div class="layui-input-block">
                    @foreach(\App\Models\Adv::TARGET_TYPE_DESC as $key => $value)
                    <input type="radio" name="target_type" value="{{ $key }}" title="{{ $value }}" lay-verify="required" @if ($key == \App\Models\Adv::TARGET_TYPE_URL) checked @endif>
                    @endforeach
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">url或id</label>
                <div class="layui-input-block">
                    <input type="text" name="target_value" lay-verify="required" lay-errormsg="url或id不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-block">
                    <input type="text" name="position" lay-verify="number" lay-errormsg="排序只能是数字" autocomplete="off" class="layui-input" value="999">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">开始时间</label>
                <div class="layui-input-block">
                    <input type="text" name="start_at" id="start_at" lay-verify="required" lay-errormsg="开始时间不能为空" autocomplete="off" class="layui-input" readonly>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">结束时间</label>
                <div class="layui-input-block">
                    <input type="text" name="end_at" id="end_at" lay-verify="required"  lay-errormsg="结束时间不能为空" autocomplete="off" class="layui-input" readonly>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="group_id" value="">
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
        }).use(['form', 'global', 'laydate'], function(){
            var form = layui.form,
                layer = layui.layer,
                global = layui.global,
                $ = layui.$,
                laydate = layui.laydate;

            //日期
            laydate.render({
                elem: '#start_at',
                type: 'datetime'
            });
            laydate.render({
                elem: '#end_at',
                type: 'datetime'
            });

            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('adv/edit') }}",
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
            {!! \App\Libs\Upload::getPlupload('adv') !!}
        });

        //表单回填
        @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>
@endsection
