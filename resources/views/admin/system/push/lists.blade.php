@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            <div class="layui-input-inline">
                <select name="type" id="type">
                    <option value="">推送类型</option>
                    <option value="1">所有用户</option>
                    <option value="2">指定用户</option>
                </select>
            </div>

            <div class="layui-inline">
                <input class="layui-input" name="title" autocomplete="off" placeholder="标题">
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="add">添加</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('push/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:70, sort: true}">ID</th>
            <th lay-data="{field:'title', width:170}">标题</th>
            <th lay-data="{field:'content'}">内容</th>
            <th lay-data="{field:'type', width:170,align:'center'}">推送类型</th>
            <th lay-data="{field:'created_at', align:'center', width:200, sort: true}">创建时间</th>
            <th lay-data="{align:'center', toolbar: '#actionButton'}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="push">推送</a>
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
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

            //批量操作按钮start
            var batch_active = {
                //添加按钮
                add: function(){
                    global.layer_show('添加', '{{ url('push/add') }}');
                },
                //删除
                del: function(){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token};
                        global.footer_ajax('{{ url('push/delete') }}', post_data);
                    });
                }
            };
            $('.batchButton .layui-btn').on('click', function(){
                var type = $(this).data('type');
                batch_active[type] ? batch_active[type].call(this) : '';
            });
            //底部批量操作按钮end

            //监听工具条操作按钮
            table.on('tool(row_list)', function(obj){
                var data = obj.data;
                if(obj.event === 'del'){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token, id: data.id};
                        if (global.footer_ajax('{{ url('push/delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                }else if(obj.event === 'edit'){
                    global.layer_show('编辑', '{{ url('push/edit') }}?id=' + data.id);
                }else if(obj.event === 'push'){
                    var post_data = {_token: csrf_token, id: data.id};
                    layer.confirm('确定推送吗', function(index){
                        var post_data = {_token: csrf_token, id: data.id};
                        if (global.footer_ajax('{{ url('push/push') }}', post_data, true)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                }
            });

        });
    </script>
@endsection
