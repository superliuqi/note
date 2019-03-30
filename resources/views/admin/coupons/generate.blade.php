@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">

            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="close_on">禁用</button>
        <button class="layui-btn layui-btn-sm" data-type="close_off">启用</button>
        <button class="layui-btn layui-btn-sm" data-type="del">删除</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('coupons/generate_ajax') }}?cou_id={{ $cou_id }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id',width:80,  sort: true}">ID</th>
            <th lay-data="{field:'name'，width:200}">活动名称</th>
            <th lay-data="{field:'password', width:250}">密码</th>
            <th lay-data="{field:'amount', width:150, sort: true}">优惠券金额</th>
            <th lay-data="{field:'m_id', toolbar: '#userTpl'}">绑定用户</th>
            <th lay-data="{field:'status', width:90,toolbar: '#statusTpl'}">是否使用</th>
            <th lay-data="{field:'is_close',width:150, align:'center', toolbar: '#closeTpl'}">是否禁用</th>
            <th lay-data="{width:100, align:'center', toolbar: '#actionButton'}">操作</th>
        </tr>
        </thead>
    </table>


    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

    <script type="text/html" id="userTpl">
        @{{# if(d.m_id > 0){ }}
        <span class="layui-badge">已绑定</span>
        @{{# } }}
        @{{# if(d.m_id == 0){ }}
        <a class="layui-btn layui-btn-xs" lay-event="detail_set_user">立即绑定</a>
        @{{# } }}
    </script>


    <script type="text/html" id="statusTpl">
        @{{# if(d.status == 1){ }}
        <span class="layui-badge">已使用</span>
        @{{# } }}
        @{{# if(d.status == 0){ }}
        <span class="layui-btn layui-btn-xs">未使用</span>
        @{{# } }}
    </script>

    <script type="text/html" id="closeTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\CouponsDetail::TYPE_DESC[\App\Models\CouponsDetail::CLOSED_ON] }}|{{ \App\Models\CouponsDetail::TYPE_DESC[\App\Models\CouponsDetail::CLOSED_OFF] }}" lay-filter="status_btn" @{{ d.is_close == 1 ? 'checked' : '' }}>
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
                //禁用
                close_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\CouponsDetail::CLOSED_ON }}};
                    global.footer_ajax('{{ url('coupons/is_close') }}', post_data);
                },
                //启用
                close_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\CouponsDetail::CLOSED_OFF }}};
                    global.footer_ajax('{{ url('coupons/is_close') }}', post_data);
                },

                //删除
                del: function(){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token};
                        global.footer_ajax('{{ url('coupons/coupons_detail_delete') }}', post_data);
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
                        if (global.footer_ajax('{{ url('coupons/coupons_detail_delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                } else if(obj.event === 'detail_set_user'){
                    //prompt层
                    layer.prompt({title: '输入用户名', formType: 0}, function(pass, index){
                        layer.close(index);
                        var post_data = {_token: csrf_token, id: data.id,username:pass};

                        //提交请求
                        $.ajax({
                            type:"POST",
                            url: "detail_set_user",
                            data: post_data,
                            dataType:"json",
                            success: function(data){
                                if (data.status=='y') {
                                    layer.msg('绑定完成');
                                    setTimeout(function(){
                                        window.location.reload();
                                    },1000)
                                } else {
                                    layer.msg(data.msg);
                                }
                            }
                        });

                    });
                }
            });


            //监听锁定操作
            form.on('switch(status_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('coupons/is_close') }}', post_data, false);
            });

        });
    </script>
@endsection
