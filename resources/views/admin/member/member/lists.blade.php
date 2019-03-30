@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">

            手机号：
            <div class="layui-inline">
                <input class="layui-input" name="tel" autocomplete="off">
            </div>
            真实姓名：
            <div class="layui-inline">
                <input class="layui-input" name="full_name" autocomplete="off">
            </div>
            用户id：
            <div class="layui-inline">
                <input class="layui-input" name="member_id" autocomplete="off">
            </div>
            用户组：
            <div class="layui-inline">
            <select name="group_id">
                <option value=""></option>
                @foreach($membergroup as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            </div>
            用户类型：
            <div class="layui-inline">
            <select lay-filter="member_type" id="member_type" name="member_type">
                <option value=""></option>
                <option value="1">服务商</option>
                <option value="2">店家</option>
                <option value="3">员工</option>
                <option value="4">普通用户</option>
            </select>
            </div>
            <div id="t_depart_id" class="layui-inline">
                用户部门：
                <div class="layui-inline">
                    <select name="depart_id">
                        <option value=""></option>
                        {!! $department !!}
                    </select>
                </div>
            </div>

            直播权限：
            <div class="layui-inline">
                <select name="is_live">
                    <option value=""></option>
                    <option value="0">无</option>
                    <option value="1">有</option>
                </select>
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

        {{--<button class="layui-btn layui-btn-sm" data-type="talent_on">达人</button>--}}
        {{--<button class="layui-btn layui-btn-sm" data-type="talent_off">取消达人</button>--}}

        {{--<button class="layui-btn layui-btn-sm" data-type="live_on">开直播权限</button>--}}
        {{--<button class="layui-btn layui-btn-sm" data-type="live_off">关直播权限</button>--}}

        {{--<button class="layui-btn layui-btn-sm" data-type="live_msg_on">开直播消息</button>--}}
        {{--<button class="layui-btn layui-btn-sm" data-type="live_msg_off">关直播消息</button>--}}

        <button class="layui-btn layui-btn-sm" data-type="del">删除</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('member/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'member_id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'username', width:180, toolbar: '#imgTpl'}">用户名</th>
            <th lay-data="{field:'nick_name', width:150}">昵称</th>
            <th lay-data="{field:'created_at', width:170, align:'center', sort: true}">创建时间</th>
            <th lay-data="{width:100, align:'center', toolbar: '#statusTpl'}">是否锁定</th>
            <th lay-data="{width:100, align:'center', toolbar: '#liveTpl'}">直播权限</th>
            <th lay-data="{width:100, align:'center', toolbar: '#talentTpl'}">是否达人</th>
            <th lay-data="{width:150, align:'center', toolbar: '#livemsgTpl'}">直播消息状态</th>
            <th lay-data="{width:150, align:'center', toolbar: '#actionButton', fixed: 'right'}">操作</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="simple">简介</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="imgTpl">
        @{{ d.username }}
        @{{# if(d.headimg != ''){ }}
        <a href="@{{ d.headimg }}" target="_blank"><img src="@{{ d.headimg }}" width="30"></a>
        @{{# } }}
    </script>
    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.member_id}}" lay-skin="switch" lay-text="{{ \App\Models\Member::STATUS_DESC[\App\Models\Member::STATUS_ON] }}|{{ \App\Models\Member::STATUS_DESC[\App\Models\Member::STATUS_OFF] }}" lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
    </script>
    <script type="text/html" id="talentTpl">
        <input type="checkbox" value="@{{d.member_id}}" lay-skin="switch" lay-text="{{ \App\Models\MemberProfile::TALENT_DESC[\App\Models\MemberProfile::TALENT_ON] }}|{{ \App\Models\MemberProfile::TALENT_DESC[\App\Models\MemberProfile::TALENT_OFF] }}" lay-filter="status_btn_talent" @{{ d.talent_show == 1 ? 'checked' : '' }}>
    </script>
    <script type="text/html" id="liveTpl">
        <input type="checkbox" value="@{{d.member_id}}" lay-skin="switch" lay-text="{{ \App\Models\MemberProfile::LIVE_DESC[\App\Models\MemberProfile::LIVE_ON] }}|{{ \App\Models\MemberProfile::LIVE_DESC[\App\Models\MemberProfile::LIVE_OFF] }}" lay-filter="status_btn_live" @{{ d.is_live == 1 ? 'checked' : '' }}>
    </script>
    <script type="text/html" id="livemsgTpl">
        <input type="checkbox" value="@{{d.member_id}}" lay-skin="switch" lay-text="{{ \App\Models\MemberProfile::LIVE_MSG_DESC[\App\Models\MemberProfile::LIVE_MSG_ON] }}|{{ \App\Models\MemberProfile::LIVE_MSG_DESC[\App\Models\MemberProfile::LIVE_MSG_OFF] }}" lay-filter="status_btn_live_msg" @{{ d.show_live_msg == 1 ? 'checked' : '' }}>
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
