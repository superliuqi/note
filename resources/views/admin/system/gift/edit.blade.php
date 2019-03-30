@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">礼物名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required" lay-errormsg="礼物名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">图片</label>
                <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image">选择图片</button>
                <a href="{{ isset($item['image']) ? $item['image'] : '' }}" target="_blank"><img src="{{ isset($item['image']) ? $item['image'] : '' }}" width="50" style="display: {{ isset($item['image']) ? : 'none' }};"></a>
                <input type="hidden" value="" name="image">
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">gif图</label>
                <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="gif_image">选择图片</button>
                <a href="{{ isset($item['gif_image']) ? $item['gif_image'] : '' }}" target="_blank"><img src="{{ isset($item['gif_image']) ? $item['gif_image'] : '' }}" width="50" style="display: {{ isset($item['gif_image']) ? : 'none' }};"></a>
                <input type="hidden" value="{{ isset($item['gif_image']) ? ($item['gif_image']) : '' }}" name="gif_image">
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">小gif图</label>
                <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="small_gif_image">选择图片</button>
                <a href="{{ isset($item['small_gif_image']) ? $item['small_gif_image'] : '' }}" target="_blank"><img src="{{ isset($item['small_gif_image']) ? $item['small_gif_image'] : '' }}" width="50" style="display: {{ isset($item['small_gif_image']) ? : 'none' }};"></a>
                <input type="hidden" value="{{ isset($item['small_gif_image']) ? ($item['small_gif_image']) : '' }}" name="small_gif_image">
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">美币个数</label>
                <div class="layui-input-block">
                    <input type="text" name="amount" lay-verify="number" lay-errormsg="美币个数有误" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">单位</label>
                <div class="layui-input-block">
                    <input type="text" name="unit" lay-verify="required" lay-errormsg="单位填写有误" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">触发额外动画效果个数</label>
                <div class="layui-input-block">
                    <input type="text" name="is_gif_additional" lay-verify="number" lay-errormsg="个数填写有误" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">类型</label>
                <div class="layui-input-block">
                    <span class="radio-box">
                        <input type="radio" name="type" value="1" title="小礼物" checked>
                    </span>
                    <span class="radio-box">
                        <input type="radio" name="type" value="2" title="大礼物">
                    </span>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <span class="radio-box">
                        <input type="radio" name="status" value="1" title="正常" checked>
                    </span>
                    <span class="radio-box">
                        <input type="radio" name="status" value="2" title="下架">
                    </span>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-block">
                    <input type="text" name="position" lay-verify="number" lay-errormsg="排序只能是数字" autocomplete="off" class="layui-input" value="">
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
                    url : "{{ url('gift/edit') }}",
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
            {!! \App\Libs\Upload::getPlupload() !!}
        });

        //表单回填
        @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>
@endsection
