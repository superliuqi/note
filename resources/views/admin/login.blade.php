@extends('admin.layout')

@section('header')
	<link rel="stylesheet" href="{{ URL::asset('admin/css/login.css') }}" media="all">
@endsection

@section('content')
	<div class="layadmin-user-login layadmin-user-display-show" style="display: none;">
		<form class="layui-form" method="post">
			<div class="layadmin-user-login-main">
				<div class="layadmin-user-login-box layadmin-user-login-header">
					<h2>{{ config('app.name') }}管理系统</h2>
				</div>
				<div class="layadmin-user-login-box layadmin-user-login-body layui-form">
					<div class="layui-form-item">
						<label class="layadmin-user-login-icon layui-icon layui-icon-username" for="login-username"></label>
						<input type="text" name="username" id="login-username" lay-verify="required" lay-errormsg="用户名不能为空" placeholder="用户名" class="layui-input">
					</div>
					<div class="layui-form-item">
						<label class="layadmin-user-login-icon layui-icon layui-icon-password" for="login-password"></label>
						<input type="password" name="password" id="login-password" lay-verify="required" lay-errormsg="密码不能为空" placeholder="密码" class="layui-input">
					</div>
					<div class="layui-form-item">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<button class="layui-btn layui-btn-fluid" lay-submit lay-filter="go">登 入</button>
					</div>
				</div>
			</div>
		</form>
		<div class="layui-trans layadmin-user-login-footer">
			<p>© 2018 <a href="{{ config('app.copyright_url') }}" target="_blank">{{ config('app.copyright') }}版权所有</a></p>
		</div>
	</div>
@endsection

@section('footer')
	<script>
        layui.config({
            base: 'admin/js/',
            version: new Date().getTime()
        }).use(['form'], function(){
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer;

            //提交
            form.on('submit(go)', function(data){
                $.ajax({
                    type : "POST",
                    url : "{{ url('login') }}",
                    data : data.field,
                    success : function(result) {
                        if (result.code == 0) {
                            window.location.href='{{ url('index') }}';
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