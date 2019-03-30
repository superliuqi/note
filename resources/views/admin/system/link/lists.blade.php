@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="del">删除</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('relive/link_lists_ajax') }}?live_id={{ $live_id }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id',width:80, sort: true}">ID</th>
            <th lay-data="{field:'title', width:300,toolbar: '#imgTpl'}">直播标题</th>
            <th lay-data="{field:'nick_name', width:200, align:'center'}">主播昵称</th>
            <th lay-data="{field:'link_nick_name',width:200,align:'center'}">连麦用户昵称</th>
            <th lay-data="{width:150,align:'center', toolbar: '#linkTpl'}">连麦状态</th>
            <th lay-data="{field:'created_at', width:200, align:'center', sort: true}">创建时间</th>
            <th lay-data="{field:'end_at',align:'center', sort: true}">结束时间</th>
        </tr>
        </thead>
    </table>
    <script type="text/html" id="imgTpl">
        @{{ d.title }}
        @{{# if(d.headimg != ''){ }}
        <a href="@{{ d.headimg }}" target="_blank"><img src="@{{ d.headimg }}" width="30"></a>
        @{{# } }}
    </script>
    <script type="text/html" id="is_remTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\Live::IS_REM_DESC[\App\Models\Live::IS_REM_ON] }}|{{ \App\Models\Live::IS_REM_DESC[\App\Models\Live::IS_REM_OFF] }}" lay-filter="is_rem_btn" @{{ d.is_rem == 1 ? 'checked' : '' }}>
    </script>

    <script type="text/html" id="linkTpl">
        @{{# if(d.status == 0){ }}
        <span class="layui-badge layui-bg-green">请求中</span>
        @{{# } if(d.status == 1) { }}
        <span class="layui-badge">连麦中</span>
        @{{# } if(d.status == 2) { }}
        <span class="layui-badge">拒绝</span>
        @{{# } if(d.status == 3) { }}
        <span class="layui-badge">已完成</span>
        @{{# } }}
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
                is_rem_on: function(){
                    var post_data = {_token: csrf_token, is_rem: {{ \App\Models\Live::IS_REM_ON }}};
                    global.footer_ajax('{{ url('relive/isRem') }}', post_data);
                },
                //锁定
                is_rem_off: function(){
                    var post_data = {_token: csrf_token, is_rem: {{ \App\Models\Live::IS_REM_OFF }}};
                    global.footer_ajax('{{ url('relive/isRem') }}', post_data);
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
                    global.layer_show('播放', '{{ url('relive/play_url') }}?id=' + data.id, width='500',height='350');
                }
            });

            //监听锁定操作
            form.on('switch(is_rem_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, is_rem:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('relive/isRem') }}', post_data, false);
            });

            //监听单元格编辑
            table.on('edit(row_list)', function(obj){
                var post_data = {_token: csrf_token, id: obj.data.id, position: obj.value};
                global.footer_ajax('{{ url('relive/position') }}', post_data, false);
            });

        });

    </script>
@endsection
