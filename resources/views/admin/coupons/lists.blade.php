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

    <table class="layui-table" lay-data="{url:'{{ url('coupons/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id',width:80, sort: true}">ID</th>
            <th lay-data="{field:'name',width:300, toolbar: '#imgTpl'}">活动名称</th>
            <th lay-data="{field:'amount', width:150, sort: true}">优惠券金额</th>
            <th lay-data="{field:'use_price', width:150, sort: true}">起用金额</th>
            <th lay-data="{field:'start_time', width:200, sort: true}">开始时间</th>
            <th lay-data="{field:'end_time', sort: true}">结束时间</th>
            <th lay-data="{width:90, align:'center', toolbar: '#statusTpl'}">是否锁定</th>
            <th lay-data="{width:300, align:'center', toolbar: '#actionButton'}">操作</th>
        </tr>
        </thead>
    </table>


    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="generate">生成券</a>
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="watch">查看券</a>
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="imgTpl">
        @{{ d.name }}
        @{{# if(d.image != ''){ }}
        <a href="@{{ d.image }}" target="_blank"><img src="@{{ d.image }}" width="30"></a>
        @{{# } }}
    </script>

    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\Coupons::STATUS_DESC[\App\Models\Coupons::STATUS_ON] }}|{{ \App\Models\Coupons::STATUS_DESC[\App\Models\Coupons::STATUS_OFF] }}" lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
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
                //添加
                add: function(){
                    global.layer_show('添加', '{{ url('coupons/add') }}','100%');
                },

                //删除
                del: function(){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token};
                        global.footer_ajax('{{ url('coupons/delete') }}', post_data);
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
                        if (global.footer_ajax('{{ url('coupons/delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                } else if(obj.event === 'edit'){
                    global.layer_show('编辑', '{{ url('coupons/edit') }}?id=' + data.id, '100%');
                } else if(obj.event === 'watch'){
                    global.layer_show('查看券', '{{ url('coupons/generate_lists') }}?id=' + data.id, '100%');
                } else if(obj.event === 'generate'){
                    //prompt层
                    layer.prompt({title: '生成张数，每次最多1000张', formType: 0}, function(pass, index){
                        layer.close(index);
                        var post_data = {_token: csrf_token, id: data.id,generate_num:pass};

                        //提交请求
                        $.ajax({
                            type:"POST",
                            url: "coupons/generate",
                            data: post_data,
                            dataType:"json",
                            success: function(data){
                                if (data.code=='0') {
                                    layer.msg('生成成功');
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
                global.footer_ajax('{{ url('coupons/status') }}', post_data, false);
            });

        });
    </script>
@endsection
