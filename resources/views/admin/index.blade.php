@extends('admin.layout')

@section('header')
	<link rel="stylesheet" href="{{ URL::asset('admin/css/global.css') }}" media="all">
@endsection

@section('content')
	<div class="layui-layout layui-layout-admin" style="border-bottom: solid 5px #1aa094;">
		<div class="layui-header header header-demo">
			<div class="layui-main">
				<div class="admin-login-box admin-header-item">
					<a class="logo" style="left: 0;" href="javascript:;">
						<span style="font-size: 22px;">{{ config('app.name') }}管理系统</span>
					</a>
					<div class="admin-side-toggle">
						<i class="iconfont icon-weibiaoti26"></i>
					</div>
				</div>
				<ul class="layui-nav admin-header-item">
					@foreach($menu as $val)
						<li class="layui-nav-item header_menu">
							<a href="javascript:;"><i class="iconfont {!! $val['icon'] !!}"></i>{{ $val['title'] }}</a>
						</li>
					@endforeach
					<li class="layui-nav-item">
						<a href="javascript:;" class="admin-header-user">
							<img src="{{ URL::asset('admin/images/head.jpeg') }}" />
							<span>{{ $user_data['username'] }}</span>
						</a>
						<dl class="layui-nav-child">
							<dd>
								<a href="{{ url('loginout') }}"><i class="iconfont  icon-close"></i> 注销</a>
							</dd>
						</dl>
					</li>
				</ul>
				<ul class="layui-nav admin-header-item-mobile">
					@foreach($menu as $val)
						<li class="layui-nav-item header_menu">
							<a href="javascript:;"><i class="iconfont {!! $val['icon'] !!}"></i>{{ $val['title'] }}5345</a>
						</li>
					@endforeach
					<li class="layui-nav-item">
						<a href="{{ url('loginout') }}"><i class="iconfont icon-close"></i> 注销</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="layui-side layui-bg-black" id="admin-side">
			<div class="layui-side-scroll" id="admin-navbar-side" lay-filter="side"></div>
		</div>
		<div class="layui-body" style="bottom: 0;border-left: solid 2px #1AA094;" id="admin-body">
			<div class="layui-tab admin-nav-card layui-tab-brief" lay-filter="admin-tab">
				<ul class="layui-tab-title">
					<li class="layui-this">
						<i class="fa fa-dashboard" aria-hidden="true"></i>
						<cite>系统信息</cite>
					</li>
				</ul>
				<div class="layui-tab-content" style="min-height: 150px; padding: 5px 0 0 0;">
					<div class="layui-tab-item layui-show">
						<iframe src="{{ url('main') }}"></iframe>
					</div>
				</div>
			</div>
		</div>
		<div class="layui-footer footer footer-demo" id="admin-footer">
			<div class="layui-main">
				<p>© 2018
					<a href="{{ config('app.copyright_url') }}" target="_blank">{{ config('app.copyright') }}版权所有</a>
				</p>
			</div>
		</div>
		<div class="site-tree-mobile layui-hide">
			<i class="layui-icon">&#xe602;</i>
		</div>
		<div class="site-mobile-shade"></div>
	</div>
@endsection

@section('footer')
	<script src="{{ URL::asset('admin/js/index.js') }}"></script>
	<script>
        //左侧菜单数组
        var navs = @json($menu);
	</script>
@endsection