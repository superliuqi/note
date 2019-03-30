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
                <label class="layui-form-label">h5链接</label>
                <div class="layui-input-block">
                    <input type="text" name="url" lay-verify="url" lay-errormsg="h5链接不正确" autocomplete="off" class="layui-input">
                </div>
            </div>

            {{--<div class="layui-form-item">--}}
                {{--<label class="layui-form-label">缩略图</label>--}}
                {{--<button type="button" class="layui-btn layui-btn-sm plupload_btn" id="up_image">选择图片</button>--}}
                {{--<a href="{{ isset($item['image']) ? $item['image'] : '' }}" target="_blank"><img src="{{ isset($item['image']) ? $item['image'] : '' }}" width="50" style="display: {{ isset($item['image']) ? : 'none' }};"></a>--}}
                {{--<input type="hidden" value="" name="image" lay-verify="required" lay-errormsg="请上传图片">--}}
            {{--</div>--}}

            <div class="layui-form-item">
                <label class="layui-form-label">缩略图</label>
                <div class="layui-input-inline">
                    <button type="button" class="layui-btn layui-btn-sm plupload_btn is_callback" id="goods_image">选择图片</button>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block goods_image_tpl"></div>
                <script id="goods_image_tpl" type="text/html">
                    <div class="goods_image">
                        <li><a href="@{{ d.url }}" target="_blank"><img src="@{{ d.url }}"></a></li>
                        <li><i class="iconfont icon-jiantouarrow506 image_move_left"></i>&nbsp;&nbsp;<i class="iconfont icon-jiantouarrow484 image_move_right"></i>&nbsp;&nbsp;<i class="iconfont icon-dustbin_icon image_delete"></i></li>
                        <input type="hidden" name="image[]" value="@{{ d.url }}">
                    </div>
                </script>
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
        }).use(['form', 'global','laytpl'], function(){
            var form = layui.form,
                layer = layui.layer,
                global = layui.global,
                    laytpl = layui.laytpl,
                $ = layui.$;

            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('info/edit') }}",
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

            //图片上传数量判断
            function plupload_file_add_callback(plupload_btn_id) {
                //商品主图
                if (plupload_btn_id == 'goods_image') {
                    //判断数量是否超过5个
                    var image_num = $('.goods_image_tpl').children('.goods_image').length;
                    if (image_num >= 5) {
                        layer.msg('最多只能上传5张图片');
                        return false;
                    }
                }
                return true;
            }
            //主图片上传完成处理
            function plupload_callback(plupload_btn_id, url) {
                if (!plupload_file_add_callback(plupload_btn_id)) {
                    return false;
                }
                //商品主图
                if (plupload_btn_id == 'goods_image') {
                    var data = {url:url}
                    laytpl($('#goods_image_tpl').html()).render(data, function (html) {
                        $('.goods_image_tpl').append(html);
                    })
                } else {
                    //规格图片
                    $("#"+plupload_btn_id).parent().find('img').attr('src',url).show();
                    $("#"+plupload_btn_id).parent().find('a').attr('href',url);
                    $("#"+plupload_btn_id).parent().find('[type="hidden"]').val(url);
                    goods_sku.Creat_Table();//更新图片后需要更新sku
                }
            }

            //图片删除或位置移动
            $('.goods_image_tpl').on('click', '.iconfont', function () {
                var obj = $(this).closest('.goods_image');
                if ($(this).hasClass('image_move_left')) {
                    //左移
                    var to_index = obj.prev().index();
                    $('.goods_image_tpl .goods_image:eq('+to_index+')').before(obj);
                } else if ($(this).hasClass('image_move_right')) {
                    //右移
                    var to_index = obj.next().index();
                    $('.goods_image_tpl .goods_image:eq('+to_index+')').after(obj);
                } else if ($(this).hasClass('image_delete')) {
                    //删除
                    obj.remove();
                }
            })

            //回填图片
            var item_goods_image = @json($item['goods_image']);
            for (j = 0,len=item_goods_image.length; j < len; j++) {
                plupload_callback('goods_image', item_goods_image[j]);
            }

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
