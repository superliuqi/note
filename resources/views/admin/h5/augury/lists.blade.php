@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">

            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="add">添加</button>
        <button class="layui-btn layui-btn-sm" data-type="del">删除</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('augury/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'height', width:100}">身高</th>
            <th lay-data="{field:'weight', width:100}">体重</th>
            <th lay-data="{field:'sex', align:'center', toolbar: '#sexTpl'}">性别</th>
            <th lay-data="{field:'constellation', width:150}">星座</th>
            <th lay-data="{field:'bmi', width:200}">结果</th>
            <th lay-data="{field:'character', width:250}">星座结果</th>
            <th lay-data="{field:'proposal', width:250}">内部结果</th>
            <th lay-data="{field:'created_at', width:200}">创建时间</th>
            <th lay-data="{width:150, align:'center', toolbar: '#actionButton', fixed: 'right'}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="sexTpl">
        @{{# if(d.sex ==1){ }}
        <span>男</span>
        @{{# }else { }}
        <span>男</span>
        @{{# }  }}
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

            $("#t_depart_id").hide();//隐藏部门

            form.on('select(member_type)', function(data){

                var t_val=data.value;
                if(t_val=='3'){
                   $("#t_depart_id").show();
                }else{
                    $("#t_depart_id").hide();
                }

            });

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
                    global.layer_show('添加', '{{ url('member/add') }}', '100%');
                },
                //审核
                status_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Member::STATUS_ON }}};
                    global.footer_ajax('{{ url('member/status') }}', post_data);
                },
                //锁定
                status_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\Member::STATUS_OFF }}};
                    global.footer_ajax('{{ url('member/status') }}', post_data);
                },
                //达人
                talent_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\MemberProfile::TALENT_ON }}};
                    global.footer_ajax('{{ url('member/talent') }}', post_data);
                },
                //取消达人
                talent_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\MemberProfile::TALENT_OFF }}};
                    global.footer_ajax('{{ url('member/talent') }}', post_data);
                },
                //直播权限开
                live_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\MemberProfile::LIVE_ON }}};
                    global.footer_ajax('{{ url('member/live') }}', post_data);
                },
                //直播权限关
                live_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\MemberProfile::LIVE_OFF }}};
                    global.footer_ajax('{{ url('member/live') }}', post_data);
                },
                //直播消息状态开
                live_msg_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\MemberProfile::LIVE_MSG_ON }}};
                    global.footer_ajax('{{ url('member/live_msg') }}', post_data);
                },
                //直播消息状态关
                live_msg_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\MemberProfile::LIVE_MSG_OFF }}};
                    global.footer_ajax('{{ url('member/live_msg') }}', post_data);
                },
                //删除
                del: function(){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token};
                        global.footer_ajax('{{ url('member/delete') }}', post_data);
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
                        var post_data = {_token: csrf_token, id: data.member_id};
                        if (global.footer_ajax('{{ url('member/delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                } else if(obj.event === 'simple'){
                    global.layer_show('简介', '{{ url('member/simple_add') }}?member_id=' + data.member_id, '100%');
                }else if(obj.event === 'edit'){
                    global.layer_show('编辑', '{{ url('member/edit') }}?id=' + data.member_id, '100%');
                }
            });
            //监听锁定操作
            form.on('switch(status_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('member/status') }}', post_data, false);
            });
            //监听达人操作
            form.on('switch(status_btn_talent)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('member/talent') }}', post_data, false);
            });
            //监听直播权限操作
            form.on('switch(status_btn_live)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('member/live') }}', post_data, false);
            });
            //监听直播消息操作
            form.on('switch(status_btn_live_msg)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('member/live_msg') }}', post_data, false);
            });
        });

    </script>
@endsection
