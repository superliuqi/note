@extends('admin.layout')

@section('content')

    <div class="searchButton">
        <button class="layui-btn layui-btn-sm" data-type="add"><i class="layui-icon">&#xe608;</i> 添加</button>
        <div class="search_input">
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>
    <form class="layui-form" method="post">
        <table class="layui-table" lay-filter="row_list">
            <thead>
                <tr>
                    <th>部门名称</th>
                    <th width="400">操作</th>
                </tr>
            </thead>
            <tbody>
            {!! $html !!}
            </tbody>
        </table>
    </form>
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
                //添加按钮
                add: function(){
                    global.layer_show('添加', '{{ url('member_department/add') }}');
                }
            };
            $('.searchButton .layui-btn').on('click', function(){
                var type = $(this).data('type');
                search_active[type] ? search_active[type].call(this) : '';
            });
            //头部按钮类型操作end

            form.render('checkbox');//初始化状态按钮

            //隐藏展开下级
            $('.layui-table .iconfont').on('click', function (){
                $('.category_id' + $(this).attr('data-id')).toggle();
                //动态更换图标
                $(this).toggleClass(function () {
                    if($(this).hasClass('icon-jian')){
                        $(this).removeClass('icon-jian');
                        return 'icon-jia';
                    }else{
                        $(this).removeClass('icon-jia');
                        return 'icon-jian';
                    }
                });
                //循环隐藏或展开下级
                var forOpen = function(id){
                    $('.category_id' + id).each(function (i) {
                        if ($(this).css('display') == 'none') {
                            $(this).find('i').removeClass('icon-jian').addClass('icon-jia');
                            $('.category_id' + $(this).attr('data-id')).hide();
                        } else {
                            $(this).find('i').removeClass('icon-jia').addClass('icon-jian');
                            $('.category_id' + $(this).attr('data-id')).show();
                        }
                        forOpen($(this).attr('data-id'));
                    })
                };
                forOpen($(this).attr('data-id'));
            })


            //监听工具条操作按钮
            $('.layui-table .layui-btn').on('click', function (){
                var event = $(this).attr('lay-event');
                var id = $(this).attr('data-id');
                if (event == 'add_category'){
                    global.layer_show('添加', '{{ url('member_department/add') }}?parent_id=' + id);
                } else if(event == 'edit') {
                    global.layer_show('编辑', '{{ url('member_department/edit') }}?id=' + id);
                } else if(event == 'del') {
                    layer.confirm('确定删除吗', function(index){
                        var post_data = {_token: csrf_token, id: id};
                        if (global.footer_ajax('{{ url('member_department/delete') }}', post_data, false)) {
                            $('#row_category_id' + id).remove();
                            layer.close(index);
                        }
                    });
                }
            })
        });
    </script>
@endsection
