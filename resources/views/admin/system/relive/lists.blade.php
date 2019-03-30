@extends('admin.layout')

@section('content')

    <div class="layui-form searchButton">
        <div class="search_input">
            标题：
            <div class="layui-inline">
                <input class="layui-input" name="keyword" autocomplete="off">
            </div>
            用户名：
            <div class="layui-inline">
                <input class="layui-input" name="nick_name" autocomplete="off">
            </div>
            用户id：
            <div class="layui-inline">
                <input class="layui-input" name="m_id" autocomplete="off">
            </div>
            <div class="layui-inline">
                <select name="is_rem">
                    <option value=""></option>
                    <option value="0">不推荐</option>
                    <option value="1">推荐</option>
                </select>
            </div>
            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <div class="layui-btn-group batchButton">
        <button class="layui-btn layui-btn-sm" data-type="is_rem_on">推荐</button>
        <button class="layui-btn layui-btn-sm" data-type="is_rem_off">不推荐</button>
        <button class="layui-btn layui-btn-sm" data-type="del">删除</button>
    </div>

    <table class="layui-table" lay-data="{url:'{{ url('relive/lists_ajax') }}', page:true, id:'row_list'}" lay-filter="row_list">
        <thead>
        <tr>
            <th lay-data="{type:'checkbox'}"></th>
            <th lay-data="{field:'id',width:80, sort: true}">ID</th>
            <th lay-data="{field:'title', width:100,toolbar: '#imgTpl'}">标题</th>
            <th lay-data="{width:100,align:'center', toolbar: '#statusTpl'}">状态</th>
            <th lay-data="{width:100,align:'center', toolbar: '#linkTpl'}">连麦状态</th>
            <th lay-data="{field:'password', width:100,align:'center',edit: 'text'}">密码</th>
            <th lay-data="{width:100,align:'center', toolbar: '#is_remTpl'}">是否推荐</th>
            <th lay-data="{field:'real_time_number',width:100,align:'center'}">实时人数</th>
            <th lay-data="{field:'all_number',width:100,align:'center'}">观看人数</th>
            <th lay-data="{field:'robot_numbet',width:100,align:'center'}">机器人人数</th>
            <th lay-data="{field:'video_length', width:100,align:'center'}">直播时长</th>
            <th lay-data="{field:'created_at', width:200, align:'center', sort: true}">直播时间</th>
            <th lay-data="{field:'end_at', width:200,align:'center', sort: true}">结束时间</th>
            <th lay-data="{field:'position', width:100, align:'center', sort: true, edit: 'text'}">排序</th>
            <th lay-data="{width:150, align:'center', toolbar: '#actionButton',fixed: 'right'}">操作</th>
        </tr>
        </thead>
    </table>
    <script type="text/html" id="actionButton">
        @{{# if(d.file_id != '') { }}
        <a class="layui-btn layui-btn-xs" lay-event="edit">预览</a>
        @{{# } }}
        <a class="layui-btn layui-btn-xs layui-bg-orange" lay-event="link_list">连麦列表</a>
    </script>
    <script type="text/html" id="imgTpl">
        @{{ d.title }}
        @{{# if(d.headimg != ''){ }}
        <a href="@{{ d.headimg }}" target="_blank"><img src="@{{ d.headimg }}" width="30"></a>
        @{{# } }}
    </script>
    <script type="text/html" id="is_remTpl">
        @{{# if(d.file_id != '') { }}
        <input type="checkbox" value="@{{d.id}}" lay-skin="switch" lay-text="{{ \App\Models\Live::IS_REM_DESC[\App\Models\Live::IS_REM_ON] }}|{{ \App\Models\Live::IS_REM_DESC[\App\Models\Live::IS_REM_OFF] }}" lay-filter="is_rem_btn" @{{ d.is_rem == 1 ? 'checked' : '' }}>
        @{{# } }}
    </script>
    <script type="text/html" id="statusTpl">
        @{{# if(d.status == 1){ }}
        <span class="layui-badge layui-bg-green">直播中</span>
        @{{# } else { }}
        <span class="layui-badge">直播结束</span>
        @{{# } }}
    </script>

    <script type="text/html" id="linkTpl">
        @{{# if(d.link == 1){ }}
        <span class="layui-badge layui-bg-green">连麦中</span>
        @{{# } else { }}
        <span class="layui-badge">无连麦</span>
        @{{# } }}
    </script>

    <link href="http://imgcache.qq.com/open/qcloud/video/tcplayer/tcplayer.css" rel="stylesheet">
    <!-- 如需在IE8、9浏览器中初始化播放器，浏览器需支持Flash并在页面中引入 -->
    <!--[if lt IE 9]>
    <script src="http://imgcache.qq.com/open/qcloud/video/tcplayer/ie8/videojs-ie8.js"></script>
    <![endif]-->
    <!-- 如果需要在 Chrome Firefox 等现代浏览器中通过H5播放hls，需要引入 hls.js -->
    <script src="http://imgcache.qq.com/open/qcloud/video/tcplayer/lib/hls.min.0.8.8.js"></script>
    <!-- 引入播放器 js 文件 -->
    <script src="http://imgcache.qq.com/open/qcloud/video/tcplayer/tcplayer.min.js"></script>
    <!-- 示例 CSS 样式可自行删除 -->

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

                //审核
                is_rem_on: function(){
                    var post_data = {_token: csrf_token, is_rem: {{ \App\Models\Live::IS_REM_ON }}};
                    global.footer_ajax('{{ url('relive/isRem') }}', post_data);
                },
                //锁定
                is_rem_off: function(){
                    var post_data = {_token: csrf_token, is_rem: {{ \App\Models\Live::IS_REM_OFF }}};
                    global.footer_ajax('{{ url('relive/isRem') }}', post_data);
                },

            };
            $('.batchButton .layui-btn').on('click', function(){
                var type = $(this).data('type');
                batch_active[type] ? batch_active[type].call(this) : '';
            });
            //底部批量操作按钮end

            //监听工具条操作按钮
            table.on('tool(row_list)', function(obj){
                var data = obj.data;
                if(obj.event === 'edit'){
                    global.layer_show('播放', '{{ url('relive/play_url') }}?id=' + data.id, width='500',height='350');
                }if(obj.event === 'link_list'){
                    global.layer_show('连麦列表', '{{ url('relive/link_lists') }}?live_id=' + data.id, '100%');
                }
            });

            //监听锁定操作
            form.on('switch(is_rem_btn)', function(obj){
                var post_data = {_token: csrf_token, id:this.value, is_rem:obj.elem.checked == true ? 1 : 0};
                global.footer_ajax('{{ url('relive/isRem') }}', post_data, false);
            });

            //监听单元格编辑
            table.on('edit(row_list)', function(obj){
               var post_data = {_token: csrf_token, id: obj.data.id, field: obj.field,value:obj.value};
                if(post_data.field=='password'){
                    if(isNaN(post_data.value)){
                        layer.msg('请输入数字');
                        return false;
                    }
                    if(post_data.value.length!=4){
                        layer.msg('请输入4位数字');
                        return false;
                    }
                    global.footer_ajax('{{ url('relive/password') }}', post_data, false);
                }else{
                    global.footer_ajax('{{ url('relive/position') }}', post_data, false);
                }




               // layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
            });

        });

    </script>
@endsection
