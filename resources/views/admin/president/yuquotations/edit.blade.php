﻿@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post" onsubmit="return false">
            <div class="layui-form-item">
                <label class="layui-form-label">标题</label>
                <div class="layui-input-block">
                    <input type="text" name="title" lay-verify="required" lay-errormsg="标题不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">语言</label>
                <div class="layui-input-block">
                    <input type="radio" name="lang"  {{ isset($item['lang']) ? : 'checked' }} value="0" title="中文">
                    <input type="radio" name="lang"  value="1" title="英文">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">缩略图</label>
                <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image">选择图片</button>
                <a href="{{ isset($item['image']) ? $item['image'] : '' }}" target="_blank"><img src="{{ isset($item['image']) ? $item['image'] : '' }}" width="50" style="display: {{ isset($item['image']) ? : 'none' }};"></a>
                <input type="hidden" value="" name="image">
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">大图</label>
                <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_images">选择图片</button>
                <a href="{{ isset($item['image_big']) ? $item['image_big'] : '' }}" target="_blank"><img src="{{ isset($item['image_big']) ? $item['image'] : '' }}" width="50" style="display: {{ isset($item['image_big']) ? : 'none' }};"></a>
                <input type="hidden" value="" name="image_big">
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-inline">
                    <input type="text" name="position" lay-verify="number" lay-errormsg="排序只能是数字" autocomplete="off" class="layui-input" value="999">
                </div>
            </div>


            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">详情</label>
                <div class="layui-input-block">
                    <textarea name="desc" placeholder="请输入内容" class="layui-textarea"></textarea>
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
                    url : "{{ url('yuquotations/edit') }}",
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
            {!! \App\Libs\Upload::getPlupload('yuquotations') !!}
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
