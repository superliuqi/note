layui.define(['layer', 'table'], function(exports){
    var $ = layui.$
        ,table = layui.table;
    var obj = {
        /*
        * 重新载入表格
        * table_name 数据表格id
        * */
        search_table: function(table_name = 'row_list'){
            var where_search = new Array();
            //搜索条件
            $('.searchButton .search_input input').each(function () {
                where_search[$(this).attr('name')] = $(this).val();
            })
            $('.searchButton .search_input select').each(function () {
                where_search[$(this).attr('name')] = $(this).val();
            })
            table.reload(table_name, {
                page: {
                    curr: 1 //重新从第 1 页开始
                },
                where: where_search
            });
        },
        /*
        * 底部批量操作
        * ajax_url 请求地址
        * post_data 发送数据
        * reload 是否需要从新载入数据，默认需要
        * table_name 数据表格id
        * */
        footer_ajax: function(ajax_url, post_data, reload = true, table_name = 'row_list'){
            //存在id时不再重新获取
            if (!post_data.id) {
                var data_id = new Array();
                var checkStatus = table.checkStatus(table_name),
                    data = checkStatus.data;
                for(var i=0;i< data.length;i++){
                    data_id.push(data[i].id);
                }
                post_data['id'] = data_id;
            }
            var return_data = '';//返回数据
            $.ajax({
                type : "POST",
                url : ajax_url,
                data : post_data,
                async: false,
                success : function(result) {
                    if (result.code == 0) {
                        if (reload) {
                            layer.msg('操作成功', {time:1000}, function () {
                                try {
                                    $(".layui-laypage-btn")[0].click();//刷新数据
                                } catch (e) {
                                    window.location.reload();//没有分页刷新页面
                                }
                            })
                        } else {
                            return_data = true;
                        }
                    } else if(result.msg) {
                        layer.msg(result.msg);
                    } else {
                        layer.msg('操作失败');
                    }
                },
                error : function () {
                    layer.msg('操作失败，请刷新页面重试！');
                }
            });

            if (return_data) {
                return return_data;
            }
        },
        /*
        * 弹出层
        * title 标题
        * url 打开地址
        * w 弹出框宽度
        * h 弹出框高度
        * */
        layer_show: function(title,url,w,h){
            var w = arguments[2] ? arguments[2] :600;
            var h = arguments[3] ? arguments[3] :500;
            var index = layer.open({
                type: 2,
                title: title,
                content: url,
                fix: false, //不固定
                maxmin: true,
                area: [w+'px', h+'px'], //宽高
            });
            if(w=='100%')
            {
                layer.full(index);
            }
        },
        //关闭弹出框口
        layer_close: function(){
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
            //刷新数据
            try {
                //分页刷新当前页数据
                var parent$ = window.parent.layui.jquery;
                parent$(".layui-laypage-btn")[0].click();
            } catch (e) {
                window.parent.location.reload();//没有分页刷新页面
            }
        }
    };
    exports('global', obj);
});