@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            名称：
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off">
            </div>
            分类：
            <div class="layui-inline">
                <select name="category_id">
                    <option value=""></option>
                    {!! $category !!}
                </select>
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="add">添加</button>
        <button class="layui-btn layui-btn-sm" data-type="del">删除</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('attribute/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'title',width:200}">名称</th>
            <th lay-data="{field:'category_name', width:170}">所属分类</th>
            <th lay-data="{field:'note'}">备注</th>
            <th lay-data="{field:'position', width:80, align:'center', sort: true, edit: 'text'}">排序</th>
            <th lay-data="{field:'created_at', width:170, align:'center', sort: true}">创建时间</th>
            <th lay-data="{width:200, align:'center', toolbar: '#actionButton'}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="add_value">属性值列表</a>
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
                //添加
                add: function(){
                    global.layer_show('添加', '{{ url('attribute/add') }}');
                },
                //删除
                del: function(){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token};
                        global.footer_ajax('{{ url('attribute/delete') }}', post_data);
                    });
                }
            };
            $('.batchButton .layui-btn').on('click', function(){
                var type = $(this).data('type');
                batch_active[type] ? batch_active[type].call(this) : '';
            });
            //批量操作按钮end

            //监听工具条操作按钮
            table.on('tool(row_list)', function(obj){
                var data = obj.data;
                if(obj.event === 'del'){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token, id: data.id};
                        if (global.footer_ajax('{{ url('attribute/delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                } else if(obj.event === 'edit'){
                    global.layer_show('编辑', '{{ url('attribute/edit') }}?id=' + data.id);
                } else if(obj.event === 'add_value'){
                    global.layer_show('属性值列表', '{{ url('attribute_value') }}?attribute_id=' + data.id, '100%');
                }
            });

            //监听单元格编辑
            table.on('edit(row_list)', function(obj){
                var post_data = {_token: csrf_token, id: obj.data.id, position: obj.value};
                global.footer_ajax('{{ url('attribute/position') }}', post_data, false);
            });
        });
    </script>
@endsection
