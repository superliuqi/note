﻿@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            礼物名称：
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off">
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="add">添加</button>
        <button class="layui-btn layui-btn-sm" data-type="status_on">审核</button>
        <button class="layui-btn layui-btn-sm" data-type="status_off">锁定</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('tag/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'name',width:200}">标签名称</th>
            <th lay-data="{field:'position', width:80, align:'center', sort: true, edit: 'text'}">排序</th>
            <th lay-data="{field:'type', width:170, toolbar: '#typeTpl'}">类型</th>
            <th lay-data="{width:100, align:'center', toolbar: '#statusTpl'}">状态</th>
            <th lay-data="{field:'created_at', align:'center',  sort: true}">创建时间</th>
            <th lay-data="{align:'center', toolbar: '#actionButton',width:120}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    </script>

    <script type="text/html" id="typeTpl">
        <span class="layui-badge layui-bg-green">@{{ d.type }}</span>
    </script>

    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\Tag::STATUS_DESC[\App\Models\Tag::STATUS_ON] }}|{{ \App\Models\Tag::STATUS_DESC[\App\Models\Tag::STATUS_OFF] }}" lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
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
                    global.layer_show('添加', '{{ url('tag/add') }}');
                },
                //审核
                status_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Tag::STATUS_ON }}};
                    global.footer_ajax('{{ url('tag/status') }}', post_data);
                },
                //锁定
                status_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Tag::STATUS_OFF }}};
                    global.footer_ajax('{{ url('tag/status') }}', post_data);
                },

            };
            $('.batchButton .layui-btn').on('click', function(){
                var type = $(this).data('type');
                batch_active[type] ? batch_active[type].call(this) : '';
            });
            //底部批量操作按钮end

            //监听工具条操作按钮
            table.on('tool(row_list)', function(obj){
                var data = obj.data;
                if(obj.event === 'edit'){
                    global.layer_show('编辑', '{{ url('tag/edit') }}?id=' + data.id);
                }
            });
            //监听锁定操作
            form.on('switch(status_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('tag/status') }}', post_data, false);
            });

            //监听单元格编辑
            table.on('edit(row_list)', function(obj){
                var post_data = {_token: csrf_token, id: obj.data.id, position: obj.value};
                global.footer_ajax('{{ url('tag/position') }}', post_data, false);
            });
        });
    </script>
@endsection
