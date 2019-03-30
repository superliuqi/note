@extends('admin.layout')

@section('content')
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>系统信息</legend>
    </fieldset>

    <div class="layui-form">
        <table class="layui-table">
            <colgroup>
                <col width="150">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <td>PHP版本</td>
                <td>{{ $system['php_version'] }}</td>
            </tr>
            <tr>
                <td>服务器解析引擎</td>
                <td>{{ $system['server_soft'] }}</td>
            </tr>
            <tr>
                <td>服务器上传限制</td>
                <td>{{ $system['file_size'] }}</td>
            </tr>
            <tr>
                <td>服务器当前时间</td>
                <td>{{ date('Y-m-d H:i:s',time()) }}</td>
            </tr>
            <tr>
                <td>服务器域名</td>
                <td>{{ $system['http_host'] }}</td>
            </tr>
            <tr>
                <td>php路径</td>
                <td>{{ $system['php_path'] }}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection

