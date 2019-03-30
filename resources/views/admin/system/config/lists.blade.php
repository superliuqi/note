@extends('admin.layout')

@section('content')
    <div class="searchButton">
        <button class="layui-btn layui-btn-sm" data-type="add"><i class="layui-icon">&#xe608;</i> 添加</button>
        <div class="search_input">
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>
    <form class="layui-form" method="post">
        <div class="layui-tab layui-tab-brief">
            <ul class="layui-tab-title">
                @foreach($tab_name as $key => $val)
                <li @if ($key == 0) class="layui-this" @endif>{{ $val }}</li>
                @endforeach
            </ul>
            <div class="layui-tab-content">
                @foreach($config as $key => $val)
                <div class="layui-tab-item @if ($key == 0) layui-show @endif ">
                    @foreach($val as $v)
                        @if ($v['input_type'] == 'text')
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ $v['title'] }}</label>
                                <div class="layui-input-block">
                                    <input type="text" name="config[{{ $v['id'] }}]" value="{{ $v['value'] }}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                        @elseif ($v['input_type'] == 'textarea')
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ $v['title'] }}</label>
                                <div class="layui-input-block">
                                    <textarea name="config[{{ $v['id'] }}]" class="layui-textarea">{{ $v['value'] }}</textarea>
                                </div>
                            </div>
                        @elseif ($v['input_type'] == 'radio')
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ $v['title'] }}</label>
                                <div class="layui-input-block">
                                    @foreach(explode(',', $v['select_value']) as $k => $value)
                                    <input type="radio" name="config[{{ $v['id'] }}]" value="{{ $k }}" title="{{ $value }}" @if ($k == $v['value']) checked @endif>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($v['input_type'] == 'select')
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ $v['title'] }}</label>
                                <div class="layui-input-block">
                                    <select name="config[{{ $v['id'] }}]">
                                        @foreach(explode(',', $v['select_value']) as $k => $value)
                                            <option value="{{ $k }}" @if ($k == $v['value']) selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <button class="layui-btn" lay-submit="" lay-filter="go">保存</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
@endsection

@section('footer')
    <script>
        layui.config({
            base: '/admin/js/',
            version: new Date().getTime()
        }).use(['table', 'global', 'element'], function(){
            var table = layui.table,
                $ = layui.$,
                form = layui.form,
                global = layui.global,
                element = layui.element;

            //头部按钮类型操作start
            var search_active = {
                refresh: function(){window.location.reload();},//刷新
                //添加按钮
                add: function(){
                    global.layer_show('添加', '{{ url('config/add') }}');
                }
            };
            $('.searchButton .layui-btn').on('click', function(){
                var type = $(this).data('type');
                search_active[type] ? search_active[type].call(this) : '';
            });
            //头部按钮类型操作end

            //监听提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('config') }}",
                    data : data.field,
                    success : function(result) {
                        if ( result.code == 0 ) {
                            layer.msg('保存成功', {time: 1000});
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
        });
    </script>
@endsection
