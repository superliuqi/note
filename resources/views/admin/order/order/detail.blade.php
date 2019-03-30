@extends('admin.layout')

@section('content')
    <div class="layui-tab layui-tab-brief">
        <ul class="layui-tab-title">
            <li class="layui-this">基本信息</li>
            <li>发货记录</li>
            <li>操作日志</li>
        </ul>
        <div class="layui-tab-content">
            <!--基本信息-->
            <div class="layui-tab-item layui-show">
                <form class="layui-form" method="post">
                    <table>
                        <tr>
                            <td valign="top">
                                <table class="layui-table" style="width: 300px;">
                                    <tbody>
                                    <tr>
                                        <td colspan="2">订单号：{{ $order['order_no'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">商品总金额：</td>
                                        <td>{{ $order['sell_price_total'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">邮费金额金额：</td>
                                        <td>{{ $order['delivery_price_real'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">优惠金额：</td>
                                        <td>{{ $order['promotion_price'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">改价金额：</td>
                                        <td>{{ $order['discount_price'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">支付金额：</td>
                                        <td>{{ $order['subtotal'] }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td valign="top">
                                <table class="layui-table" style="width: 400px; margin-left: 10px;">
                                    <tbody>
                                    <tr>
                                        <td width="100">姓名：</td>
                                        <td>{{ $order['full_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">电话：</td>
                                        <td>{{ $order['tel'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">地址：</td>
                                        <td>{{ $order['prov'] . $order['city'] . $order['area']. $order['address'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">送货时间：</td>
                                        <td>{{ $order['delivery_time'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">配送方式：</td>
                                        <td>{{ $order['delivery_type'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">备注：</td>
                                        <td>{{ $order['note'] }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td valign="top">
                                <table class="layui-table" style="width: 400px; margin-left: 10px;">
                                    <tbody>
                                    <tr>
                                        <td width="100">状态：</td>
                                        <td><span class="layui-badge">{{ $order['status_label'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td width="100">下单用户：</td>
                                        <td>{{ $order['username'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">支付方式：</td>
                                        <td>{{ $order['payment_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">第三方单号：</td>
                                        <td>{{ $order['payment_no'] }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">所属店铺：</td>
                                        <td>{{ $order['seller'] }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th style="min-width: 300px;">商品名称</th>
                            <th width="300">规格</th>
                            <th width="100">商品价格</th>
                            <th width="100">商品数量</th>
                            <th width="100" style="text-align: center;">售后状态</th>
                            <th width="100" style="text-align: center;">发货状态</th>
                            <th width="80" style="text-align: center;">发货</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order_goods as $value)
                            <tr>
                                <td>{{ $value['goods_title'] }}</td>
                                <td>{{ $value['spec_value'] }}</td>
                                <td>{{ $value['sell_price'] }}</td>
                                <td>{{ $value['buy_qty'] }}</td>
                                <td style="text-align: center;">{{ $value['refund'] }}</td>
                                <td style="text-align: center;">{{ $value['delivery_text'] }}</td>
                                <td style="text-align: center;">
                                    @if ($value['delivery'] == \App\Models\OrderGoods::DELIVERY_OFF)
                                    <input type="checkbox" name="order_goods_id[]" value="{{ $value['id'] }}" lay-skin="primary" title="" checked>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if($order['status'] == \App\Models\Order::STATUS_WAIT_PAY)
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit="" lay-filter="cancel">取消订单</button>
                            </div>
                        </div>
                    @elseif($order['status'] == \App\Models\Order::STATUS_PAID || $order['status'] == \App\Models\Order::STATUS_SHIPMENT)
                        <div class="layui-form-item">
                            <label class="layui-form-label">物流公司</label>
                            <div class="layui-input-inline">
                                <select name="company_id" lay-verify="required" lay-errormsg="请选择物流公司">
                                    <option value=""></option>
                                    @foreach($express_company as $id => $title)
                                        <option value="{{ $id }}">{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">快递单号</label>
                            <div class="layui-input-inline">
                                <input type="text" name="code" lay-verify="required" lay-errormsg="快递单号不能为空" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">备注</label>
                            <div class="layui-input-block" style="width: 400px;">
                                <textarea name="note" class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit="" lay-filter="delivery">发货</button>
                            </div>
                        </div>
                    @endif
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="id" value="{{ $order['id'] }}" />
                </form>
            </div>
            <!--发货记录-->
            <div class="layui-tab-item ">
                <table class="layui-table">
                    <thead>
                    <tr>
                        <th width="170" style="text-align: center;">时间</th>
                        <th width="120">物流公司</th>
                        <th width="80">物流单号</th>
                        <th width="80">物流状态</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($delivery as $value)
                        <tr>
                            <td style="text-align: center;">{{ $value['created_at'] }}</td>
                            <td>{{ $value['company_name'] }}</td>
                            <td>{{ $value['code'] }}</td>
                            <td><a href="https://www.kuaidi100.com/chaxun?com={{ $value['company_name'] }}&nu={{ $value['code'] }}" target="_blank"><span class="layui-badge">查看状态</span></a></td>
                            <td>{{ $value['note'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!--操作日志-->
            <div class="layui-tab-item ">
                <table class="layui-table">
                    <thead>
                    <tr>
                        <th width="170" style="text-align: center;">时间</th>
                        <th width="120">操作用户</th>
                        <th width="80">操作动作</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($log as $value)
                        <tr>
                            <td style="text-align: center;">{{ $value['created_at'] }}</td>
                            <td>{{ $value['username'] }}</td>
                            <td>{{ $value['action'] }}</td>
                            <td>{{ $value['note'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        layui.config({
            base: '/admin/js/',
            version: new Date().getTime()
        }).use(['table', 'global', 'element'], function(){
            var table = layui.table,
                $ = layui.$,
                form = layui.form,
                global = layui.global,
                element = layui.element;

            form.render('checkbox');//初始化状态按钮

            //监听发货提交
            form.on('submit(delivery)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('order/delivery') }}",
                    data : data.field,
                    success : function(result) {
                        if ( result.code == 0 ) {
                            layer.msg('操作成功', {time:1000}, function () {
                                window.location.reload();
                            })
                        } else {
                            layer.msg(result.msg);
                        }
                    },
                    error : function () {
                        layer.msg('操作失败，请刷新页面重试！');
                    }
                });
                return false;
            });
            //监听取消提交
            form.on('submit(cancel)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('order/cancel') }}",
                    data : data.field,
                    success : function(result) {
                        if ( result.code == 0 ) {
                            layer.msg('操作成功', {time:1000}, function () {
                                window.location.reload();
                            })
                        } else {
                            layer.msg(result.msg);
                        }
                    },
                    error : function () {
                        layer.msg('操作失败，请刷新页面重试！');
                    }
                });
                return false;
            });
        });
    </script>
@endsection
