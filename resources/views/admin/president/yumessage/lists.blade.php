@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            名称：
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off">
            </div>

            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="status_off">待处理</button>
        <button class="layui-btn layui-btn-sm" data-type="status_on">处理</button>
        <button class="layui-btn layui-btn-sm" data-type="del">删除</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('yumessage/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'m_id', toolbar: '#imgTpl'}">图片</th>
            <th lay-data="{field:'name'}">联系人</th>
            <th lay-data="{field:'tel'}">电话</th>
            <th lay-data="{field:'desc'}">内容</th>
            <th lay-data="{field:'created_at', width:170, align:'center', sort: true}">创建时间</th>
            <th lay-data="{width:100, align:'center', toolbar: '#statusTpl'}">状态</th>
            <th lay-data="{width:150, align:'center', toolbar: '#actionButton'}">操作</th>
        </tr>
        </thead>
    </table>


    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="imgTpl">
        @{{# if(d.positive_pic != ''){ }}
        <a href="@{{ d.positive_pic }}" target="_blank"><img src="@{{ d.positive_pic }}" width="30"></a>
        @{{# } }}

        @{{# if(d.left_pic != ''){ }}
        <a href="@{{ d.left_pic }}" target="_blank"><img src="@{{ d.left_pic }}" width="30"></a>
        @{{# } }}

        @{{# if(d.right_pic != ''){ }}
        <a href="@{{ d.right_pic }}" target="_blank"><img src="@{{ d.right_pic }}" width="30"></a>
        @{{# } }}
    </script>
    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\YuMessage::STATUS_DESC[\App\Models\YuMessage::STATUS_ON] }}|{{ \App\Models\YuMessage::STATUS_DESC[\App\Models\YuMessage::STATUS_OFF] }}" lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
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
                //审核
                status_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\YuMessage::STATUS_ON }}};
                    global.footer_ajax('{{ url('yumessage/status') }}', post_data);
                },
                //锁定
                status_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\YuMessage::STATUS_OFF }}};
                    global.footer_ajax('{{ url('yumessage/status') }}', post_data);
                },
                //删除
                del: function(){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token};
                        global.footer_ajax('{{ url('yumessage/delete') }}', post_data);
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
                        if (global.footer_ajax('{{ url('yumessage/delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                }
            });
            //监听锁定操作
            form.on('switch(status_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('yumessage/status') }}', post_data, false);
            });

        });
    </script>
@endsection
