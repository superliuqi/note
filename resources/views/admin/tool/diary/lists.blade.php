@extends('admin.layout')

@section('content')

    <div class="searchTable searchButton layui-form" style="padding: 0 10px;">
        <div class="search_input" style="float: right;">
            <div class="layui-input-inline">
                <select name="is_rem" id="select_pay_status">
                    <option value="">是否推荐</option>
                    <option value="0">否</option>
                    <option value="1">是</option>
                </select>
            </div>

            <div class="layui-inline">
                <input class="layui-input" name="nick_name" autocomplete="off" placeholder="用户昵称">
            </div>

            <div class="layui-inline">
                <input class="layui-input" name="username" autocomplete="off" placeholder="用户账号">
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="cancel_rem">取消推荐</button>
        <button class="layui-btn layui-btn-sm" data-type="add_rem">推荐</button>
        <button class="layui-btn layui-btn-sm" data-type="status_on">审核</button>
        <button class="layui-btn layui-btn-sm" data-type="status_off">锁定</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('diary/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'nick_name',width:200}">用户昵称</th>
            <th lay-data="{field:'username',width:130}">用户账号</th>
            <th lay-data="{field:'image',minWidth: 200, toolbar:'#imgTpl'}">图片</th>
            <th lay-data="{field:'content',minWidth: 250,}">内容</th>
            <th lay-data="{field:'support_num',width:80,align:'center'}">点赞</th>
            <th lay-data="{field:'report_num',width:80,align:'center'}">举报</th>
            <th lay-data="{field:'created_at', align:'center',sort: true}">创建时间</th>
            <th lay-data="{width:100, align:'center', toolbar: '#remTpl'}">推荐</th>
            <th lay-data="{width:100, align:'center', toolbar: '#statusTpl'}">状态</th>
            <th lay-data="{align:'center', toolbar: '#actionButton',width:120}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

    <script type="text/html" id="remTpl">
        <span class="layui-badge @{{ d.is_rem == 1 ? 'layui-bg-green' : 'layui-bg-orange'}}">@{{ d.is_rem == 1 ? '推荐' : '不推荐' }}</span>
    </script>

    <script type="text/html" id="imgTpl">
        @{{# if(d.image){ }}
            @{{# var x;for(x in d.image){ }}
                <a href="@{{ d.image[x] }}" target="_blank"><img src="@{{ d.image[x] }}" width="30"></a>
            @{{# } }}
        @{{# } }}
    </script>

    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\Diary::STATUS_DESC[\App\Models\Diary::STATUS_ON] }}|{{ \App\Models\Diary::STATUS_DESC[\App\Models\Diary::STATUS_OFF] }}" lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
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
            $('.searchTable .layui-btn').on('click', function(){
                var type = $(this).data('type');
                search_active[type] ? search_active[type].call(this) : '';
            });
            //头部按钮类型操作end

            //批量操作按钮start
            var batch_active = {
                //取消推荐
                cancel_rem: function(){
                    var post_data = {_token: csrf_token, is_rem: 0};
                    global.footer_ajax('{{ url('diary/rem') }}', post_data);
                },

                //推荐
                add_rem: function(){
                    var post_data = {_token: csrf_token, is_rem: 1};
                    global.footer_ajax('{{ url('diary/rem') }}', post_data);
                },

                //审核
                status_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Diary::STATUS_ON }}};
                    global.footer_ajax('{{ url('diary/status') }}', post_data);
                },
                //锁定
                status_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Diary::STATUS_OFF }}};
                    global.footer_ajax('{{ url('diary/status') }}', post_data);
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
                if(obj.event === 'del'){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token, id: data.id};
                        if (global.footer_ajax('{{ url('diary/delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                            layer.msg('删除成功');
                        }
                    });
                }
            });
            //监听锁定操作
            form.on('switch(status_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('diary/status') }}', post_data, false);
            });

            //监听单元格编辑
            table.on('edit(row_list)', function(obj){
                var post_data = {_token: csrf_token, id: obj.data.id, position: obj.value};
                global.footer_ajax('{{ url('diary/position') }}', post_data, false);
            });
        });
    </script>
@endsection
