@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
                <div class="layui-field-box">


                    <div class="layui-form-item">
                        <label class="layui-form-label">活动名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" lay-verify="required" lay-errormsg="活动名称不能为空" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-block">
                            <input type="radio" name="type" {{ isset($item['type']) ? : 'checked' }}  value="1" title="满减">
                            <input type="radio" name="type"  value="2" title="折扣">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">优惠券金额/折扣(折扣券只能在0-100)</label>
                        <div class="layui-input-block">
                            <input type="text" name="amount" lay-verify="required" lay-errormsg="金额不能为空" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">订单起用金额</label>
                        <div class="layui-input-block">
                            <input type="text" name="use_price" lay-verify="required" lay-errormsg="起用金额不能为空" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">兑换所需积分</label>
                        <div class="layui-input-block">
                            <input type="text" name="point" lay-verify="required" lay-errormsg="所需积分不能为空" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">允许使用的商品(填写商品id，多个之间逗号隔开)</label>
                        <div class="layui-input-block">
                            <input type="text" name="goods_ids" lay-verify="required" lay-errormsg="商品不能为空" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">缩略图</label>
                        <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image">选择图片</button>
                        <a href="{{ isset($item['image']) ? $item['image'] : '' }}" target="_blank"><img src="{{ isset($item['image']) ? $item['image'] : '' }}" width="50" style="display: {{ isset($item['image']) ? : 'none' }};"></a>
                        <input type="hidden" value="" name="image">
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">开始日期</label>
                        <div class="layui-input-block">
                            <input type="text" id="start_time" name="start_time" lay-verify="required" lay-errormsg="开始日期不能为空" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">结束日期</label>
                        <div class="layui-input-block">
                            <input type="text" id="end_time" name="end_time" lay-verify="required" lay-errormsg="结束日期不能为空" autocomplete="off" class="layui-input">
                        </div>
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
        }).use(['form', 'global', 'laytpl' ,'laydate'], function(){
            var form = layui.form,
                    layer = layui.layer,
                    global = layui.global,
                    laytpl = layui.laytpl,
                    goods_sku = layui.goods_sku,
                    $ = layui.$;
                    laydate = layui.laydate;

            laydate.render({
                elem: '#start_time'
                ,lang: 'cn'
                ,type:'datetime'
            });

            laydate.render({
                elem: '#end_time'
                ,lang: 'cn'
                ,type:'datetime'
            });


            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('coupons/edit') }}",
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
            {!! \App\Libs\Upload::getPlupload('coupons') !!}

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
