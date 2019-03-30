@extends('admin.layout')

@section('content')

    <!-- 引入播放器 css 文件 -->
    <link href="http://imgcache.qq.com/open/qcloud/video/tcplayer/tcplayer.css" rel="stylesheet">
    <!-- 如需在IE8、9浏览器中初始化播放器，浏览器需支持Flash并在页面中引入 -->

    <!-- 示例 CSS 样式可自行删除 -->
    <style>
        html,body{
            margin: 0;
            padding: 0;
        }
        .tcplayer {
            margin: 0 auto;
        }
        @media screen and (max-width: 640px) {
            #player-container-id {
                width: 100%;
                height: 270px;
            }
        }
        /* 设置logo在高分屏的显示样式 */
        @media only screen and (min-device-pixel-ratio: 2), only screen and (-webkit-min-device-pixel-ratio: 2) {
            .tcp-logo-img {
                width: 50%;
            }
        }
    </style>

<!-- 设置播放器容器 -->
<video src="{{$item['video']}}" width="100%" height="300" controls="controls">
</video>

@endsection

@section('footer')

<script>
    //表单回填
            @if ($item)
    var formObj = new Form();
    formObj.init(@json($item));
    @endif
</script>
@endsection
