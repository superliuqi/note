﻿@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off" placeholder="陪同姓名">
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">服务商</label>
                <div class="layui-input-block">
                    <select name="service_id" id="" class="radio-box">
                        <option value=""></option>
                        @foreach($service as $lk=>$lv)
                            <option value="{{ $lk }}">{{ $lv }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-form gorupButton">
        <div class="gorup_input">

            <div class="layui-btn-group batchButton">
                <button class="layui-btn layui-btn-sm" data-type="add">添加</button>
                <button class="layui-btn layui-btn-sm" data-type="status_on">审核</button>
                <button class="layui-btn layui-btn-sm" data-type="status_off">锁定</button>
            </div>

            分组：
            <div class="layui-inline">
                <select name="group">
                    <option value=""></option>
                    @foreach(\App\Models\H5\H5SignRecord::GROUP as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <button class="layui-btn layui-btn-sm" data-type="sure">确定</button>
        </div>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('sign/accompany_lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'name'}">姓名</th>
            <th lay-data="{field:'identity_name'}">类型</th>
            <th lay-data="{field:'service_name', align:'center', edit: 'text'}">服务商姓名</th>
            <th lay-data="{field:'group_name'}">分组</th>
            <th lay-data="{field:'area'}">大区</th>
            <th lay-data="{align:'center', toolbar: '#statusTpl'}">状态</th>
            <th lay-data="{field:'created_at', align:'center', width:170, sort: true}">创建时间</th>
            <th lay-data="{align:'center', toolbar: '#actionButton',width:100}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\H5\H5SignShoper::STATUS_DESC[\App\Models\H5\H5SignShoper::STATUS_ON] }}|{{ \App\Models\H5\H5SignShoper::STATUS_DESC[\App\Models\H5\H5SignShoper::STATUS_OFF] }}" lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
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
                    global.layer_show('添加', '{{ url('sign/accompany_add') }}','100%');
                },
                //审核
                status_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Gift::STATUS_ON }}};
                    global.footer_ajax('{{ url('sign/accompany_status') }}', post_data);
                },
                //锁定
                status_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Gift::STATUS_OFF }}};
                    global.footer_ajax('{{ url('sign/accompany_status') }}', post_data);
                },
                //分组
                sure: function () {
                    var choose_search = new Array();
                    $('.gorupButton .gorup_input select').each(function () {
                        choose_search[$(this).attr('name')] = $(this).val();
                    });
                    var post_data = {_token: csrf_token,group:choose_search['group']};
                    global.footer_ajax('{{ url('sign/shoper_group') }}', post_data);
                },
            };
            $('.gorupButton .layui-btn').on('click', function(){
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
                        if (global.footer_ajax('{{ url('sign/accompany_delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                            layer.msg('删除成功');
                        }
                    });
                }
                else if(obj.event === 'edit'){
                    global.layer_show('编辑', '{{ url('sign/accompany_edit') }}?id=' + data.id,'100%');
                }
            });
            //监听锁定操作
            form.on('switch(status_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 2};
                global.footer_ajax('{{ url('sign/accompany_status') }}', post_data, false);
            });

        });
    </script>
@endsection
