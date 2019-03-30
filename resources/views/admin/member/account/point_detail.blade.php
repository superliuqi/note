@extends('admin.layout')

@section('content')
    <div class="layui-form searchButton" style="margin-top: 8px;">
        <button class="layui-btn layui-btn-sm add_point" data-type="add"><i class="layui-icon">&#xe608;</i>充值积分</button>
        <button class="layui-btn layui-btn-sm del_point" data-type="reduce"><i class="layui-icon">&#xe640;</i>扣除积分</button>
        <div class="search_input">
            <div class="layui-input-inline">
                <select name="event" id="event">
                    <option value="">类型选择</option>
                    @foreach(\App\Models\PointDetail::EVENT_DESC as $ek=>$ev)
                        <option value="{{ $ek }}">{{ $ev }}</option>
                    @endforeach
                </select>
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('account/point_detail_ajax') }}?m_id={{ $m_id }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'amount',toolbar:'#amountTpl'}">金额</th>
            <th lay-data="{field:'event',align:'center'}">类型</th>
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

            //添加 扣除
            $('.add_point,.del_point').on('click', function(index){
                var data_type = $(this).data('type');
                layer.prompt(function(val, index){
                    if(check_money(val)){
                        var post_data = {_token: csrf_token, amount: val,m_id:{{ $m_id }}};
                        if(data_type === 'add'){
                            global.footer_ajax('{{ url('account/point_add') }}', post_data, true);
                        }else if (data_type === 'reduce'){
                            global.footer_ajax('{{ url('account/point_reduce') }}', post_data, true);
                        }
                        layer.close(index);
                    }else{
                        layer.msg('请输入正确的金额');
                    }
                });
            });

            function check_money($point){
                if($point){
                    var reg = /^(0|[1-9][0-9]{0,9})(\.[0-9]{1,2})?$/;
                    if(reg.test($point)){
                        return true;
                    }else{
                        return false;
                    }
                }
                return false;
            }
        });
    </script>
@endsection
