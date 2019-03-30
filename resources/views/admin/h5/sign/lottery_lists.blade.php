@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off" placeholder="姓名">
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-form gorupButton">
        <div class="gorup_input">
            <div class="layui-btn-group batchButton">
                {{--<button class="layui-btn layui-btn-sm" data-type="add">添加</button>--}}
                {{--<button class="layui-btn layui-btn-sm" data-type="status_on">审核</button>--}}
                {{--<button class="layui-btn layui-btn-sm" data-type="status_off">锁定</button>--}}
            </div>
        </div>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('sign/lottery_lists_ajax') }}', page:true, id:'row_list'}"
           lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'user_name'}">姓名</th>
            <th lay-data="{field:'nick_name'}">昵称</th>
            <th lay-data="{align:'center',toolbar: '#imgTpl'}">头像</th>
            <th lay-data="{align:'center',toolbar: '#typeTpl'}">类型</th>
            <th lay-data="{field:'prize'}">奖品</th>
            <th lay-data="{field:'created_at', align:'center', width:170, sort: true}">创建时间</th>
            {{--<th lay-data="{align:'center', toolbar: '#actionButton',width:100}">操作</th>--}}
        </tr>
        </thead>
    </table>

    <script type="text/html" id="typeTpl">
        @{{# if(d.type == 1){ }}
       <laber>店家</laber>
        @{{# } else if(d.type==2){ }}
        <laber>陪同</laber>
        @{{# } else{ }}
        <laber>服务商</laber>
        @{{# } }}
    </script>

    <script type="text/html" id="imgTpl">
        @{{# if(d.head_img != ''){ }}
        <a href="@{{ d.head_img }}" target="_blank"><img src="@{{ d.head_img }}" width="30"></a>
        @{{# } }}
    </script>

    <script type="text/html" id="actionButton">
        {{--<a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>--}}
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch"
               lay-text="{{ \App\Models\H5\H5SignShoper::STATUS_DESC[\App\Models\H5\H5SignShoper::STATUS_ON] }}|{{ \App\Models\H5\H5SignShoper::STATUS_DESC[\App\Models\H5\H5SignShoper::STATUS_OFF] }}"
               lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
    </script>
@endsection

@section('footer')
    <script>
        layui.config({
            base: '/admin/js/',
            version: new Date().getTime()
        }).use(['table', 'global'], function () {
            var table = layui.table,
                    $ = layui.$,
                    form = layui.form,
                    global = layui.global,
                    csrf_token = '{{ csrf_token() }}';

            //头部按钮类型操作start
            var search_active = {
                refresh: function () {
                    window.location.reload();
                },//刷新
                search: function () {
                    global.search_table();
                },//搜索
            };
            $('.searchButton .layui-btn').on('click', function () {
                var type = $(this).data('type');
                search_active[type] ? search_active[type].call(this) : '';
            });
            //头部按钮类型操作end

            //批量操作按钮start
            var batch_active = {
                //添加按钮
                add: function () {
                    global.layer_show('添加', '{{ url('sign/lottery_add') }}', '100%');
                },
                //审核
                status_on: function () {
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Gift::STATUS_ON }}};
                    global.footer_ajax('{{ url('sign/lottery_status') }}', post_data);
                },
                //锁定
                status_off: function () {
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Gift::STATUS_OFF }}};
                    global.footer_ajax('{{ url('sign/lottery_status') }}', post_data);
                },
            };
            $('.gorupButton .layui-btn').on('click', function () {
                var type = $(this).data('type');
                batch_active[type] ? batch_active[type].call(this) : '';
            });
            //底部批量操作按钮end

            //监听工具条操作按钮
            table.on('tool(row_list)', function (obj) {
                var data = obj.data;
                if (obj.event === 'del') {
                    layer.confirm('确定删除吗', function (index) {
                        var post_data = {_token: csrf_token, id: data.id};
                        if (global.footer_ajax('{{ url('sign/lottery_delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                            layer.msg('删除成功');
                        }
                    });
                }
                else if (obj.event === 'edit') {
                    global.layer_show('编辑', '{{ url('sign/lottery_edit') }}?id=' + data.id, '100%');
                }
            });
            //监听锁定操作
            form.on('switch(status_btn)', function (obj) {
                var post_data = {_token: csrf_token, id: this.value, status: obj.elem.checked == true ? 1 : 2};
                global.footer_ajax('{{ url('sign/lottery_status') }}', post_data, false);
            });

        });
    </script>
@endsection
