@extends('admin.layout')

@section('content')


    <div class="layui-form searchButton">


        <div class="search_input">

            用户名：
            <div class="layui-inline">
                <input class="layui-input" name="username">
            </div>


            开始日期:
            <div class="layui-inline">
                <input class="layui-input" id="start_time" name="start_time" autocomplete="off">
            </div>


            结束日期:
            <div class="layui-inline">
                <input class="layui-input" id="end_time" name="end_time" autocomplete="off">
            </div>


            <button class="layui-btn layui-btn-sm" data-type="search">搜索</button>
            <button class="layui-btn layui-btn-sm" data-type="refresh"><i class="iconfont icon-refresh"></i></button>
        </div>
        <div class="layui-clear"></div>
    </div>

    <table class="layui-table" id="row_list" lay-filter="row_list">

        <div id="container" style="min-width:400px;height:400px"></div>
        <div class="message"></div>
    </table>


@endsection

@section('footer')

    <script src="{{ URL::asset('admin/js/jquery-3.1.1.min.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/js/highcharts/highcharts.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/js/highcharts/modules/data.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/js/highcharts/modules/series-label.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/js/highcharts/modules/exporting.js') }}" charset="utf-8"></script>
    <script src="{{ URL::asset('admin/js/highcharts/modules/export-data.js') }}" charset="utf-8"></script>

    <script>
        layui.config({
            base: '/admin/js/',
            version: new Date().getTime()
        }).use(['table', 'global', 'laydate'], function () {
            var table = layui.table,
                    $ = layui.$,
                    form = layui.form,
                    global = layui.global,
                    csrf_token = '{{ csrf_token() }}';

            var laydate = layui.laydate;
            var nowdate = new Date();
            var endDate = laydate.render({
                elem: '#end_time',//选择器结束时间
                type: 'datetime',
                min: "1970-1-1",//设置min默认最小值
                max: 'nowdate',
                done: function (value, date) {
                    startDate.config.max = {
                        year: date.year,
                        month: date.month - 1,//关键
                        date: date.date,
                        hours: 0,
                        minutes: 0,
                        seconds: 0
                    }
                }
            });
            //日期范围
            var startDate = laydate.render({
                elem: '#start_time',
                type: 'datetime',
                //max: "2099-12-31",//设置一个默认最大值
                max: 'nowdate',
                done: function (value, date) {
                    endDate.config.min = {
                        year: date.year,
                        month: date.month - 1, //关键
                        date: date.date,
                        hours: 0,
                        minutes: 0,
                        seconds: 0
                    };
                }
            });

            //头部按钮类型操作start
            var search_active = {
                refresh: function () {
                    window.location.reload();
                },//刷新
                search: function () {
                    var where_search = new Array();
                    //搜索条件
                    $('.searchButton .search_input input').each(function () {
                        where_search[$(this).attr('name')] = $(this).val();
                    });

                    var start_time_data = new Date(where_search['start_time'].replace(/-/g, "/"));
                    var end_time_data = new Date(where_search['end_time'].replace(/-/g, "/"));

                    var days = end_time_data.getTime() - start_time_data.getTime();
                    var gettime = parseInt(days / (1000 * 60 * 60 * 24));

                    if (gettime >= 31) {

                        layer.msg('数值不能超过一个月', {time: 1000}, function () {
                            window.location.reload();
                        });
                        return false;
                    }
                    container(where_search['username'], where_search['start_time'], where_search['end_time'])
                }
            };
            $('.searchButton .layui-btn').on('click', function () {
                var type = $(this).data('type');
                search_active[type] ? search_active[type].call(this) : '';
            });
            //头部按钮类型操作end

        });


        function container(username, start_time, end_time) {
            var chart = $("#container").highcharts();
            // 获取 CSV 数据并初始化图表
            $.getJSON("{{ url('statistics/balance_lists_ajax') }}" + "?username=" + username + "&start_time=" + start_time + "&end_time=" + end_time, function (csv) {
                chart = Highcharts.chart('container', {
                    //去水印
                    credits: {
                        text: '',
                        href: ''
                    },
                    data: {
                        csv: csv
                    },
                    title: {
                        text: '余额使用情况'
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        tickInterval: 1 * 24 * 3600 * 1000, // 坐标轴刻度间隔为一星期
                        tickWidth: 0,
                        gridLineWidth: 1,
                        labels: {
                            align: 'left',
                            x: 3,
                            y: -3
                        },
                        // 时间格式化字符
                        // 默认会根据当前的刻度间隔取对应的值，即当刻度间隔为一周时，取 week 值
                        dateTimeLabelFormats: {
                            day: '%Y-%m-%d'
                        }
                    },
                    yAxis: [{ // 第一个 Y 轴，放置在左边（默认在坐标）
                        title: {
                            text: '金额'
                        },
                        labels: {
                            align: 'left',
                            x: 3,
                            y: 16,
                            format: '{value:.,0f}'
                        },
                        allowDecimals: false,
                        showFirstLabel: false
                    }, {    // 第二个坐标轴，放置在右边
                        linkedTo: 0,
                        gridLineWidth: 0,
                        opposite: true,  // 通过此参数设置坐标轴显示在对立面
                        title: {
                            text: null
                        },
                        labels: {
                            align: 'right',
                            x: -3,
                            y: 16,
                            format: '{value:.,0f}'
                        },
                        showFirstLabel: false
                    }],
                    legend: {
                        align: 'left',
                        verticalAlign: 'top',
                        y: 20,
                        floating: true,
                        borderWidth: 0
                    },
                    tooltip: {
                        shared: true,
                        crosshairs: true,
                        // 时间格式化字符
                        // 默认会根据当前的数据点间隔取对应的值
                        // 当前图表中数据点间隔为 1天，所以配置 day 值即可
                        dateTimeLabelFormats: {
                            day: '%Y-%m-%d'
                        }
                    },
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            point: {
                                events: {}
                            },
                            marker: {
                                lineWidth: 1
                            }
                        }
                    },
                });
            });

            Highcharts.setOptions({
                lang: {
                    downloadJPEG: "下载 JPEG 图片",
                    downloadPDF: "下载 PDF 文件",
                    downloadPNG: "下载 PNG 文件",
                    downloadSVG: "下载 SVG 文件",
                    printChart: "打印 图表",
                    downloadCSV: "下载 CSV 文件",
                    downloadXLS: "下载 XLS 文件",
                    viewData: "视图 表格",
                    openInCloud: "Highcharts Cloud 中打开"
                }
            });
        }

        $(function () {
            container('', '', '');
        })


    </script>
@endsection
