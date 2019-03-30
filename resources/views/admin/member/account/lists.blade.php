@extends('admin.layout')

@section('content')
    <div class="layui-form searchButton">
        <div class="search_input">
            用户名：
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off">
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('account/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'username',toolbar: '#imgTpl'}">用户名</th>
            <th lay-data="{field:'nick_name', width:200}">昵称</th>
            <th lay-data="{field:'balance_amount', width:200, toolbar:'#balanceTpl',align:'center'}" style="position: relative">现金总额</th>
            <th lay-data="{field:'meibi_amount', width:200, toolbar:'#meibiTpl',align:'center'}" style="position: relative">美币总额</th>
            <th lay-data="{field:'point_amount', width:200, toolbar:'#pointTpl',align:'center'}" style="position: relative">积分总额</th>
        </tr>
        </thead>
    </table>


    <script type="text/html" id="balanceTpl">
        <a class="layui-btn-xs @{{ d.balance_amount > 0 ? 'layui-btn layui-bg-green' : '' }}" style="position: absolute;left:10px;top:50%;margin-top: -10px;">@{{ d.balance_amount }}</a>
        <a class="layui-btn layui-btn-xs" lay-event="balance_edit" style="position: absolute;right:10px;top:50%;margin-top: -11px;">查看明细</a>
    </script>

    <script type="text/html" id="meibiTpl">
        <a class="layui-btn-xs @{{ d.meibi_amount > 0 ? 'layui-btn layui-bg-red' : '' }}" style="position: absolute;left:10px;top:50%;margin-top: -10px;">@{{ d.meibi_amount }}</a>
        <a class="layui-btn layui-btn-xs" lay-event="meibi_edit" style="position: absolute;right:10px;top:50%;margin-top: -11px;">查看明细</a>
    </script>

    <script type="text/html" id="pointTpl">
        <a class="layui-btn-xs @{{ d.point_amount > 0 ? 'layui-btn layui-bg-orange' : '' }}" style="position: absolute;left:10px;top:50%;margin-top: -10px;">@{{ d.point_amount }}</a>
        <a class="layui-btn layui-btn-xs" lay-event="point_edit" style="position: absolute;right:10px;top:50%;margin-top: -11px;">查看明细</a>
    </script>

    <script type="text/html" id="imgTpl">
        @{{ d.username }}
        @{{# if(d.headimg){ }}
        <a href="@{{ d.headimg }}" target="_blank"><img src="@{{ d.headimg }}" width="30"></a>
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


            //监听工具条操作按钮
            table.on('tool(row_list)', function(obj){
                var data = obj.data;
                if(obj.event === 'balance_edit'){
                    global.layer_show('现金明细', '{{ url('account/balance_detail') }}?m_id=' + data.id, '100%');
                }else if(obj.event === 'meibi_edit'){
                    global.layer_show('美币明细', '{{ url('account/meibi_detail') }}?m_id=' + data.id, '100%');
                }else if(obj.event === 'point_edit'){
                    global.layer_show('积分明细', '{{ url('account/point_detail') }}?m_id=' + data.id, '100%');
                }
            });
        });
    </script>
@endsection
