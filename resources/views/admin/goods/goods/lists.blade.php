@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            ID：
            <div class="layui-inline">
                <input class="layui-input" name="id" autocomplete="off">
            </div>
            名称：
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off">
            </div>
            分类：
            <div class="layui-inline">
                <select name="category_id">
                    <option value=""></option>
                    {!! $category !!}
                </select>
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
            品牌：
            <div class="layui-inline">
                <select name="brand_id">
                    <option value=""></option>
                    @foreach($brand as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            状态：
            <div class="layui-inline">
                <select name="status">
                    <option value=""></option>
                    @foreach(\App\Models\Goods::STATUS_DESC as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
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
        <button class="layui-btn layui-btn-sm" data-type="rem_on">推荐</button>
        <button class="layui-btn layui-btn-sm" data-type="rem_off">取消推荐</button>
        <button class="layui-btn layui-btn-sm" data-type="del">删除</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('goods/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'title', minWidth: 200, toolbar: '#imgTpl'}">商品名称</th>
            <th lay-data="{field:'category', width:150}">分类</th>
            <th lay-data="{field:'sku_code', width:150}">货号</th>
            <th lay-data="{field:'market_price', width:120}">市场价</th>
            <th lay-data="{field:'sell_price', width:120}">销售价</th>
            <th lay-data="{field:'position', width:80, align:'center', sort: true, edit: 'text'}">排序</th>
            <th lay-data="{field:'created_at', width:170, align:'center', sort: true}">创建时间</th>
            <th lay-data="{width:100, align:'center', toolbar: '#statusTpl'}">是否锁定</th>
            <th lay-data="{width:150, align:'center', toolbar: '#actionButton', fixed: 'right'}">操作</th>
        </tr>
        </thead>
    </table>
    <script type="text/html" id="actionButton">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="imgTpl">
        @{{ d.title }}
        @{{# if(d.is_rem == 1){ }}
        <span class="layui-badge">推荐</span>
        @{{# } }}
        @{{# if(d.image != ''){ }}
        <a href="@{{ d.image }}" target="_blank"><img src="@{{ d.image }}" width="30"></a>
        @{{# } }}
    </script>
    <script type="text/html" id="statusTpl">
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\Goods::STATUS_DESC[\App\Models\Goods::STATUS_ON] }}|{{ \App\Models\Goods::STATUS_DESC[\App\Models\Goods::STATUS_OFF] }}" lay-filter="status_btn" @{{ d.status == 1 ? 'checked' : '' }}>
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
                //添加按钮
                add: function(){
                    global.layer_show('添加商品', '{{ url('goods/select_category') }}', '100%');
                },
                //审核
                status_on: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\goods::STATUS_ON }}};
                    global.footer_ajax('{{ url('goods/status') }}', post_data);
                },
                //锁定
                status_off: function(){
                    var post_data = {_token: csrf_token, status: {{ \App\Models\goods::STATUS_OFF }}};
                    global.footer_ajax('{{ url('goods/status') }}', post_data);
                },
                //推荐
                rem_on: function(){
                    var post_data = {_token: csrf_token, rem: {{ \App\Models\goods::REM_ON }}};
                    global.footer_ajax('{{ url('goods/rem') }}', post_data);
                },
                //取消推荐
                rem_off: function(){
                    var post_data = {_token: csrf_token, rem: {{ \App\Models\goods::REM_OFF }}};
                    global.footer_ajax('{{ url('goods/rem') }}', post_data);
                },
                //删除
                del: function(){
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token};
                        global.footer_ajax('{{ url('goods/delete') }}', post_data);
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
                        var post_data = {_token: csrf_token, id: data.id};
                        if (global.footer_ajax('{{ url('goods/delete') }}', post_data, false)) {
                            obj.del();
                            layer.close(index);
                        }
                    });
                } else if(obj.event === 'edit'){
                    global.layer_show('编辑', '{{ url('goods/edit') }}?id=' + data.id, '100%');
                }
            });
            //监听锁定操作
            form.on('switch(status_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, status:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('goods/status') }}', post_data, false);
            });

            //监听单元格编辑
            table.on('edit(row_list)', function(obj){
                var post_data = {_token: csrf_token, id: obj.data.id, position: obj.value};
                global.footer_ajax('{{ url('goods/position') }}', post_data, false);
            });
        });
    </script>
@endsection
