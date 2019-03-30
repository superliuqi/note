<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>{{ config('app.name') }}管理系统</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<link rel="stylesheet" href="{{ URL::asset('admin/js/layui/css/layui.css') }}"  media="all">
	<link rel="stylesheet" href="{{ URL::asset('admin/css/admin.css') }}"  media="all">
	<link rel="stylesheet" href="//at.alicdn.com/t/font_633552_mw0ynssfzssv2t9.css"  media="all">
	@yield('header')
</head>
<body>
@yield('content')
<script src="{{ URL::asset('admin/js/layui/layui.js') }}" charset="utf-8"></script>
@yield('footer')
</body>
</html>