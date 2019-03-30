@extends('admin.layout')

@section('content')
    <div class="layui-form searchButton" style="margin-top: 8px;">
        <div class="search_input">
            用户名：
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off">
            </div>
            类型：
            <div class="layui-inline">
                <select name="event">
                    <option value=""></option>
                    @foreach(\App\Models\MeibiDetail::EVENT_DESC as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('meibi/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'username', width:200, sort: true}">用户名</th>
            <th lay-data="{field:'amount',width:200,toolbar:'#amountTpl'}">金额</th>
            <th lay-data="{field:'event',width:200,align:'left'}">类型</th>
            <th lay-data="{field:'note'}">备注</th>
            <th lay-data="{field:'created_at', width:200, align:'center'}">创建时间</th>
        </tr>
        </thead>
    </table>

    <script type="text/html" id="amountTpl">
        <span class="layui-badge @{{ d.type == 1 ? 'layui-bg-green' : 'layui-bg-red'}}">@{{ d.type == 1 ? '+' : '-' }} @{{ d.amount }}</span>
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

        });
    </script>
@endsection
