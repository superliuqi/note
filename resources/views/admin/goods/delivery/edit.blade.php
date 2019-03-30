@extends('admin.layout')

@section('content')
    <div style="margin: 15px;">
        <form class="layui-form" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">名称</label>
                <div class="layui-input-block">
                    <input type="text" name="title" lay-verify="required" lay-errormsg="名称不能为空" autocomplete="off" class="layui-input">
                </div>
            </div>
            <fieldset class="layui-elem-field">
                <legend>默认</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-inline">
                            @foreach(\App\Models\Delivery::TYPE_DESC as $key => $value)
                                <input type="radio" name="type" value="{{ $key }}" title="{{ $value }}" @if ($key == \App\Models\Delivery::TYPE_WEIGHT) checked @endif>
                            @endforeach
                        </div>
                        <label class="layui-form-label">包邮类型</label>
                        <div class="layui-input-inline">
                            @foreach(\App\Models\Delivery::FREE_TYPE_DESC as $key => $value)
                                <input type="radio" name="free_type" value="{{ $key }}" title="{{ $value }}" @if ($key == \App\Models\Delivery::FREE_TYPE_MONEY) checked @endif>
                            @endforeach
                        </div>
                        <label class="layui-form-label">包邮金额/件</label>
                        <div class="layui-input-inline">
                            <input type="text" value="0" name="free_price" lay-verify="price" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">默认金额0表示不包邮</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">首重/件数</label>
                        <div class="layui-input-inline">
                            <input type="text" name="first_weight" lay-verify="number" autocomplete="off" class="layui-input" placeholder="重量单位克">
                        </div>
                        <label class="layui-form-label">费用</label>
                        <div class="layui-input-inline">
                            <input type="text" name="first_price" lay-verify="price" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">续重/件数</label>
                        <div class="layui-input-inline">
                            <input type="text" name="second_weight" lay-verify="number" autocomplete="off" class="layui-input" placeholder="重量单位克">
                        </div>
                        <label class="layui-form-label">费用</label>
                        <div class="layui-input-inline">
                            <input type="text" name="second_price" lay-verify="price" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label">费用类型</label>
                <div class="layui-input-inline">
                    @foreach(\App\Models\Delivery::PRICE_TYPE_DESC as $key => $value)
                        <input type="radio" name="price_type" value="{{ $key }}" lay-filter="price_type" title="{{ $value }}" @if ($key == \App\Models\Delivery::PRICE_TYPE_UNIFIED) checked @endif>
                    @endforeach
                </div>
            </div>
            <div class="group_delivery" style="display: none;">
                <div class="layui-form-item">
                    <label class="layui-form-label">默认地区配送</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="open_default" lay-skin="primary" value="{{ \App\Models\Delivery::OPEN_DEFAULT_ON }}" title="其他地区默认运费  注意：如果不开启此项，那么未设置的地区将无法送达！">
                    </div>
                </div>
                <div id="group_delivery_prov">

                </div>
                <div class="layui-form-item">
                    <button type="button" class="layui-btn" lay-filter="add">添加地区</button>
                </div>
            </div>

            <script id="group_delivery_tpl" type="text/html">
                @{{# for(var i = 0; i < d.length; i++) { }}
                <fieldset class="layui-elem-field">
                    <legend>指定地区</legend>
                    <div class="layui-field-box">
                        <div class="layui-form-item">
                            <label class="layui-form-label">地区</label>
                            <div class="layui-input-block">
                                @foreach($prov_list as $key => $value)
                                    <input type="checkbox" name="group_area_id[@{{ d[i].list_id }}][]" lay-filter="group_area_id" lay-skin="primary" value="{{ $key }}" title="{{ $value }}">
                                @endforeach
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">类型</label>
                            <div class="layui-input-inline">
                                @foreach(\App\Models\Delivery::TYPE_DESC as $key => $value)
                                    <input type="radio" name="group_type[@{{ d[i].list_id }}]" value="{{ $key }}" title="{{ $value }}" @if ($key == \App\Models\Delivery::TYPE_WEIGHT) checked @endif>
                                @endforeach
                            </div>
                            <label class="layui-form-label">包邮类型</label>
                            <div class="layui-input-inline">
                                @foreach(\App\Models\Delivery::FREE_TYPE_DESC as $key => $value)
                                    <input type="radio" name="group_free_type[@{{ d[i].list_id }}]" value="{{ $key }}" title="{{ $value }}" @if ($key == \App\Models\Delivery::FREE_TYPE_MONEY) checked @endif @{{# if(d[i].free_type_checked == 1){ }} checked @{{# } }}>
                                @endforeach
                            </div>
                            <label class="layui-form-label">包邮金额/件</label>
                            <div class="layui-input-inline">
                                <input type="text" name="group_free_price[@{{ d[i].list_id }}]" value="@{{ d[i].free_price }}" lay-verify="price" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid layui-word-aux">默认金额0表示不包邮</div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">续重/件数</label>
                            <div class="layui-input-inline">
                                <input type="text" name="group_first_weight[@{{ d[i].list_id }}]" value="@{{ d[i].first_weight }}" lay-verify="number" autocomplete="off" class="layui-input" placeholder="重量单位克">
                            </div>
                            <label class="layui-form-label">费用</label>
                            <div class="layui-input-inline">
                                <input type="text" name="group_first_price[@{{ d[i].list_id }}]" value="@{{ d[i].first_price }}" lay-verify="price" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">续重/件数</label>
                            <div class="layui-input-inline">
                                <input type="text" name="group_second_weight[@{{ d[i].list_id }}]" value="@{{ d[i].second_weight }}" lay-verify="number" autocomplete="off" class="layui-input" placeholder="重量单位克">
                            </div>
                            <label class="layui-form-label">费用</label>
                            <div class="layui-input-inline">
                                <input type="text" name="group_second_price[@{{ d[i].list_id }}]" value="@{{ d[i].second_price }}" lay-verify="price" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <button type="button" class="layui-btn layui-btn-xs" lay-filter="del">删除地区</button>
                        </div>
                    </div>
                </fieldset>
                @{{# } }}
            </script>
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
        }).use(['form', 'global', 'laytpl'], function(){
            var form = layui.form,
                layer = layui.layer,
                global = layui.global,
                laytpl = layui.laytpl,
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
                    url : "{{ url('delivery/edit') }}",
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

            //显示指定地区信息
            form.on('radio(price_type)', function (data) {
                show_set_other_prov(data.value);
            })

            //显示指定地区信息
            function show_set_other_prov(price_type) {
                if (price_type == {{ \App\Models\Delivery::PRICE_TYPE_SPECIFY_AREA }}) {
                    $('.group_delivery').show();
                } else {
                    $('.group_delivery').hide();
                }
            }

            //监听删除和添加按钮
            $('.group_delivery').on('click', '[type="button"]', function () {
                var filter = $(this).attr('lay-filter');
                if (filter == 'add') {
                    add_delivery_prov([{list_id:prov_list_id, free_price: 0, first_weight: 0, first_price: 0, second_weight: 0, second_price: 0}]);
                } else if (filter == 'del') {
                    $(this).closest('.layui-elem-field').remove();
                }
            })

            //添加一个地区
            var prov_list_id = 0;
            function add_delivery_prov(data) {
                laytpl($('#group_delivery_tpl').html()).render(data, function (html) {
                    $('#group_delivery_prov').append(html);
                })
                prov_list_id = prov_list_id+data.length;
                check_prov();
            }

            //监听地区选择选择的加入数组，方便后面按钮状态判断
            var group_area_id = Array();
            form.on('checkbox(group_area_id)', function (data) {
                if (data.elem.checked) {
                    group_area_id.push(data.value);
                } else {
                    group_area_id.splice($.inArray(data.value,group_area_id),1);
                }
                check_prov();
            })
            //检查省份是否已经存在
            function check_prov() {
                $('#group_delivery_prov [type="checkbox"]').each(function () {
                    //已经选择的地区除了已经勾选的其他否不能勾选
                    if ($.inArray($(this).val(), group_area_id) != -1) {
                        if (this.checked == true) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    } else {
                        //没有选择的地区全部可以选择
                        $(this).prop('disabled', false);
                    }
                })
                form.render();
            }

            //数据回填
            @if ($item)
                //控制其他地区是否显示
                show_set_other_prov({{ $item['price_type'] }});
                //初始化其他地区
                add_delivery_prov(@json($item['group_data']));
                //表单回填
                var formObj = new Form();
                formObj.init(@json($item));
                //初始化地区选择按钮
                group_area_id = @json($item['select_area_id']);
                check_prov();
            @endif
        });
    </script>
@endsection
