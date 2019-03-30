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
                <label class="layui-form-label">副标题</label>
                <div class="layui-input-block">
                    <input type="text" name="sub_title" lay-verify="required" lay-errormsg="副标题不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">医生名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required" lay-errormsg="医生名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">医生简介</label>
                <div class="layui-input-block">
                    <textarea name="subject" id="" cols="50" rows="10"></textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">医生头像</label>
                <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image">选择图片</button>
                <a href="{{ isset($item['image']) ? $item['image'] : '' }}" target="_blank"><img src="{{ isset($item['image']) ? $item['image'] : '' }}" width="50" style="display: {{ isset($item['image']) ? : 'none' }};"></a>
                <input type="hidden" value="" name="image" lay-verify="required" lay-errormsg="请上传医生头像">
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">等级</label>
                <div class="layui-input-block">
                    <input type="text" name="level" lay-verify="number" lay-errormsg="医生等级不正确" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">h5地址</label>
                <div class="layui-input-block">
                    <input type="text" name="url" lay-verify="url" lay-errormsg="h5地址不正确" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">语言</label>
                <div class="layui-input-block">
                    <select name="lang" id="" class="radio-box">
                        @foreach($lang as $lk=>$lv)
                            <option value="{{ $lk }}">{{ $lv }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">是否推荐</label>
                <div class="layui-input-block">
                    <span class="radio-box">
                        <input type="radio" name="is_rem" value="1" title="是" checked>
                    </span>
                    <span class="radio-box">
                        <input type="radio" name="is_rem" value="0" title="否">
                    </span>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">详情</label>
                <div class="layui-input-block">
                    <div id="desc_id">
                        {!! isset($item['desc']) ? $item['desc'] : '' !!}
                    </div>
                    <textarea name="content" id="desc" style="display: none"></textarea>
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
    <script src="{{ URL::asset('admin/lib/wangeditor/wangEditor.min.js') }}" charset="utf-8"></script>
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
                    url : "{{ url('doctor_say/edit') }}",
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
