@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            <div class="layui-input-inline">
                <select name="type" id="type">
                    <option value="">礼物类型</option>
                    <option value="2">大礼物</option>
                    <option value="1">小礼物</option>
                </select>
            </div>

            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off" placeholder="礼物名称">
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

    <table class="layui-table" lay-data="{url:'{{ url('gift/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'name', width:200, toolbar: '#imgTpl'}">礼物名称</th>
            <th lay-data="{field:'unit',align:'center'}">单位</th>
            <th lay-data="{field:'amount',width:100,align:'center'}">美币</th>
            <th lay-data="{field:'position', width:80, align:'center', sort: true, edit: 'text'}">排序</th>
            <th lay-data="{field:'type', width:100, align: 'center', toolbar: '#typeTpl'}">类型</th>
            <th lay-data="{field:'is_gif_additional',width:150,align: 'center'}">触发动画效果个数</th>
            <th lay-data="{field:'created_at', align:'center', width:200, sort: true}">创建时间</th>
            <th lay-data="{width:100, align:'center', toolbar: '#statusTpl'}">状态</th>
            <th lay-data="{align:'center', width:100,toolbar: '#actionButton'}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    </script>
    <script type="text/html" id="imgTpl">
        @{{ d.name }}
        @{{# if(d.image != ''){ }}
        <a href="@{{ d.image }}" target="_blank"><img src="@{{ d.image }}" width="30"></a>
        @{{# } }}
    </script>
    <script type="text/html" id="typeTpl">
        <span class="layui-badge @{{ d.type == 1 ? 'layui-bg-blue' : 'layui-bg-green'}}">@{{ d.type == 1 ? '小礼物' : '大礼物' }}</span>
    </script>

    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\Gift::STATUS_DESC[\App\Models\Gift::STATUS_ON] }}|{{ \App\Models\Gift::STATUS_DESC[\App\Models\Gift::STATUS_OFF] }}" lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
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
                    global.layer_show('添加', '{{ url('gift/add') }}');
                },
                //审核
                status_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Gift::STATUS_ON }}};
                    global.footer_ajax('{{ url('gift/status') }}', post_data);
                },
                //锁定
                status_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Gift::STATUS_OFF }}};
                    global.footer_ajax('{{ url('gift/status') }}', post_data);
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
                    global.layer_show('编辑', '{{ url('gift/edit') }}?id=' + data.id);
                }
            });
            //监听锁定操作
            form.on('switch(status_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 2};
                global.footer_ajax('{{ url('gift/status') }}', post_data, false);
            });

            //监听单元格编辑
            table.on('edit(row_list)', function(obj){
                var post_data = {_token: csrf_token, id: obj.data.id, position: obj.value};
                global.footer_ajax('{{ url('gift/position') }}', post_data, false);
            });
        });
    </script>
@endsection
