@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
            <fieldset class="layui-elem-field">
                <legend>基础信息</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <label class="layui-form-label">商品名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="title" lay-verify="required" lay-errormsg="名称不能为空" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">副标题</label>
                        <div class="layui-input-block">
                            <input type="text" name="sub_title" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">店铺</label>
                        <div class="layui-input-inline">
                            <select name="seller_id" lay-filter="seller" lay-verify="required" lay-errormsg="请选择店铺">
                                <option value=""></option>
                                @foreach($seller as $value)
                                    <option value="{{ $value['id'] }}">{{ $value['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="layui-form-label">运费模板</label>
                        <div class="layui-input-inline">
                            <select name="delivery_id" lay-verify="required" lay-errormsg="请选择运费模板">
                                <option value=""></option>
                            </select>
                        </div>
                        <label class="layui-form-label">品牌</label>
                        <div class="layui-input-inline">
                            <select name="brand_id" lay-verify="required" lay-errormsg="请选择品牌">
                                <option value=""></option>
                                @foreach($brand as $value)
                                    <option value="{{ $value['id'] }}">{{ $value['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">推荐图片</label>
                        <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="flag_image">选择图片</button>
                        <a href="{{ isset($item['flag_image']) ? $item['flag_image'] : '' }}" target="_blank"><img src="{{ isset($item['flag_image']) ? $item['flag_image'] : '' }}" width="50" style="display: {{ isset($item['flag_image']) ? : 'none' }};"></a>
                        <input type="hidden" value="" name="flag_image">
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">列表图片</label>
                        <button type="button" class="layui-btn layui-btn-sm plupload_btn" id="list_image">选择图片</button>
                        <a href="{{ isset($item['list_image']) ? $item['list_image'] : '' }}" target="_blank"><img src="{{ isset($item['list_image']) ? $item['list_image'] : '' }}" width="50" style="display: {{ isset($item['list_image']) ? : 'none' }};"></a>
                        <input type="hidden" value="" name="list_image">
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">商品图片</label>
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
                        <label class="layui-form-label">商品类型</label>
                        <div class="layui-input-inline">
                            <input type="radio" name="type" value="1" title="普通" checked>
                            <input type="radio" name="type" value="2" title="积分">
                        </div>
                        <label class="layui-form-label">货号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="sku_code" lay-verify="required" lay-errormsg="货号不能为空" autocomplete="off" class="layui-input" value="999">
                        </div>
                        <label class="layui-form-label">排序</label>
                        <div class="layui-input-inline">
                            <input type="text" name="position" lay-verify="number" lay-errormsg="排序只能是数字" autocomplete="off" class="layui-input" value="999">
                        </div>
                    </div>
                </div>
            </fieldset>
            @if ($attribute)
            <fieldset class="layui-elem-field">
                <legend>属性</legend>
                <div class="layui-field-box">
                    @foreach($attribute as $attr)
                        @if ($attr['input_type'] == 'checkbox')
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ $attr['title'] }}</label>
                                <div class="layui-input-block">
                                    @foreach($attr['value'] as $value)
                                        <input type="checkbox" name="attribute[{{ $attr['id'] }}][]" lay-skin="primary" value="{{ $value['value'] }}" title="{{ $value['value'] }}" @if ($value['is_checked']) checked @endif>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($attr['input_type'] == 'radio')
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ $attr['title'] }}</label>
                                <div class="layui-input-block">
                                    @foreach($attr['value'] as $value)
                                        <input type="radio" name="attribute[{{ $attr['id'] }}]" value="{{ $value['value'] }}" title="{{ $value['value'] }}" @if ($value['is_checked']) checked @endif>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($attr['input_type'] == 'select')
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ $attr['title'] }}</label>
                                <div class="layui-input-inline">
                                    <select name="attribute[{{ $attr['id'] }}]">
                                        <option value="">请选择</option>
                                        @foreach($attr['value'] as $value)
                                            <option value="{{ $value['value'] }}" @if ($value['is_checked']) selected @endif>{{ $value['value'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @elseif ($attr['input_type'] == 'text')
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ $attr['title'] }}</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="attribute[{{ $attr['id'] }}]" value="{{ isset($attr['value']) ? $attr['value'] : '' }}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </fieldset>
            @endif
            <fieldset class="layui-elem-field">
                <legend>规格</legend>
                <div class="layui-field-box goods_spec">
                    @foreach($spec as $s)
                        <div class="layui-form-item">
                            <label class="layui-form-label">{{ $s['title'] }}</label>
                            <div class="input_list" id="spec_{{ $s['id'] }}">
                                @foreach($s['value'] as $value)
                                    <div class="layui-input-inline">
                                        <input type="checkbox" lay-filter="goods_spec" lay-skin="primary" value="" title="{{ $value['value'] }}" data-id="{{ $value['id'] }}" data-name="{{ $s['title'] }}" @if ($value['is_checked']) checked @endif>
                                        <input type="text" class="layui-input spec_alias" autocomplete="off" value="{{ $value['alias'] }}">
                                        @if ($s['type'] == \App\Models\Spec::TYPE_IMAGE_ON)
                                            <br>
                                            <button type="button" class="layui-btn layui-btn-xs plupload_btn is_callback" id="spec_{{ $value['id'] }}">选择图片</button>
                                        @endif
                                        <a href="{{ $value['image'] }}" target="_blank"><img src="{{ $value['image'] }}" @if (!$value['image']) class="img_none" @endif></a>
                                        <input type="hidden" value="{{ $value['image'] }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                        <div id="goods_spec_table">
                        </div>
                </div>
            </fieldset>
            <fieldset class="layui-elem-field">
                <legend>描述</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <div id="desc_id">
                            {!! isset($item['desc']) ? $item['desc'] : '' !!}
                        </div>
                        <textarea name="desc" id="desc" style="display: none"></textarea>
                    </div>
                </div>
            </fieldset>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="category_id" value="" />
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
        }).use(['form', 'global', 'laytpl', 'goods_sku'], function(){
            var form = layui.form,
                layer = layui.layer,
                global = layui.global,
                laytpl = layui.laytpl,
                goods_sku = layui.goods_sku,
                $ = layui.$;

            //自定义验证规则
            form.verify({
                price: [
                    /(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/,
                    '请输入正确的金额,且最多两位小数!'
                ]
            });

            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('goods/edit') }}",
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
            {!! \App\Libs\Upload::getPlupload('goods') !!}

            //加载编辑器
            {!! \App\Libs\Editor::editorCreate() !!}

            //监听店铺选择
            form.on('select(seller)', function(data){
                select_seller(data.value);
            });
            //根据店铺刷新配送方式
            function select_seller(seller_id) {
                $.ajax({
                    type : "GET",
                    url : "{{ url('goods/get_delivery') }}",
                    data : {seller_id:seller_id},
                    success : function(result) {
                        if (result.code==0) {
                            var html = '<option value="">请选择</option>';
                            $.each(result.data, function (index, value) {
                                html += '<option value="' + value.id + '"';
                                if (value.id == '{{ isset($item['delivery_id']) ? $item['delivery_id'] : '' }}') {
                                    html += 'selected';
                                }
                                html += '>' + value.title + '</option>';
                            })
                            $('[name="delivery_id"]').html(html);
                            form.render('select');
                        }
                    },
                    error : function () {
                        layer.msg('操作失败，请刷新页面重试！');
                    }
                });
            }

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

            @if (isset($item['seller_id']))
                select_seller('{{ $item['seller_id'] }}');//回填品牌
                //回填图片
                var item_goods_image = @json($item['goods_image']);
                for (j = 0,len=item_goods_image.length; j < len; j++) {
                    plupload_callback('goods_image', item_goods_image[j]);
                }

                //回填默认sku数据
                goods_sku.saveLastTableData(@json($item['goods_sku']));
                goods_sku.Creat_Table();

            @endif
        });

        //表单回填
        @if ($item)
        var formObj = new Form();
        formObj.init(@json($item));
        @endif
    </script>
@endsection
