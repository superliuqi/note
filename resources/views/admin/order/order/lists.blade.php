@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            ID：
            <div class="layui-inline">
                <input class="layui-input" name="id" autocomplete="off">
            </div>
            订单号：
            <div class="layui-inline">
                <input class="layui-input" name="order_no" autocomplete="off">
            </div>
            收货人姓名：
            <div class="layui-inline">
                <input class="layui-input" name="full_name" autocomplete="off">
            </div>
            收货人电话：
            <div class="layui-inline">
                <input class="layui-input" name="tel" autocomplete="off">
            </div>
            用户名：
            <div class="layui-inline">
                <input class="layui-input" name="username" autocomplete="off">
            </div>
            商家：
            <div class="layui-inline">
                <select name="seller_id">
                    <option value=""></option>
                    @foreach($seller as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            状态：
            <div class="layui-inline">
                <select name="status">
                    <option value=""></option>
                    @foreach(\App\Models\Order::STATUS_DESC as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('order/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'order_no', width: 250}">订单号</th>
            <th lay-data="{field:'status', width: 100, align:'center'}">状态</th>
            <th lay-data="{field:'full_name', width:120}">收货人</th>
            <th lay-data="{field:'tel', width:150}">电话</th>
            <th lay-data="{field:'payment', width:100, align:'center'}">支付方式</th>
            <th lay-data="{field:'username', width:180}">用户名</th>
            <th lay-data="{field:'pay_at', width:170, align:'center', sort: true}">支付时间</th>
            <th lay-data="{field:'created_at', width:170, align:'center', sort: true}">下单时间</th>
            <th lay-data="{width:150, align:'center', toolbar: '#actionButton', fixed: 'right'}">操作</th>
        </tr>
        </thead>
    </table>
    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs" lay-event="detail">订单详情</a>
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
                if(obj.event === 'detail'){
                    global.layer_show('订单详情', '{{ url('order/detail') }}?id=' + data.id, '100%');
                }
            });
        });
    </script>
@endsection
