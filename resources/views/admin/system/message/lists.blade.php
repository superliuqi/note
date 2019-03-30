@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            内容：
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off">
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    {{--<div class="layui-btn-group batchButton">--}}
        {{--<button class="layui-btn layui-btn-sm" data-type="add">添加</button>--}}
        {{--<button class="layui-btn layui-btn-sm" data-type="status_on">审核</button>--}}
        {{--<button class="layui-btn layui-btn-sm" data-type="status_off">锁定</button>--}}
    {{--</div>--}}

    <table class="layui-table" lay-data="{url:'{{ url('message/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'desc'}">内容</th>
            <th lay-data="{field:'created_at', align:'center', width:200, sort: true}">创建时间</th>
            <th lay-data="{align:'center', toolbar: '#actionButton',width:200}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</a>
    </script>

@endsection

@section('footer')
    <script>
        layui.config({
            base: '/admin/js/',
            version: new Date().getTime()
        }).use(['table', 'global'], function(){
            var table = layui.table,
                $ = layui.$,
                form = layui.form,
                global = layui.global,
                csrf_token = '{{ csrf_token() }}';

            //头部按钮类型操作start
            var search_active = {
                refresh: function(){window.location.reload();},//刷新
                search: function(){global.search_table();},//搜索
            };
            $('.searchButton .layui-btn').on('click', function(){
                var type = $(this).data('type');
                search_active[type] ? search_active[type].call(this) : '';
            });
            //头部按钮类型操作end

            //监听工具条操作按钮
            table.on('tool(row_list)', function(obj){
                var data = obj.data;
                if(obj.event === 'del'){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token, id: data.id};
                        if (global.footer_ajax('{{ url('message/delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                }
            });
        });
    </script>
@endsection
