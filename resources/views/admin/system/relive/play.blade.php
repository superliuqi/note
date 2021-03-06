@extends('admin.layout')

@section('content')

    <!-- 引入播放器 css 文件 -->
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
<video id="player-container-id" preload="auto" width="640" height="360" playsinline webkit-playsinline>
</video>

@endsection

@section('footer')
<!--
注意事项：
* 播放器容器必须为 video 标签
* player-container-id 为播放器容器的ID，可自行设置
* 播放器区域的尺寸请按需设置，建议通过 css 进行设置，通过css可实现容器自适应等效果
* playsinline webkit-playsinline 这几个属性是为了在标准移动端浏览器不劫持视频播放的情况下实现行内播放，此处仅作示例，请按需使用
* 设置 x5-playsinline 属性会使用 X5 UI 的播放器
-->
<script>
    var player = TCPlayer('player-container-id', { // player-container-id 为播放器容器ID，必须与html中一致
        fileID: "{{ isset($item['file_id']) ? $item['file_id'] : '' }}", // 请传入需要播放的视频filID 必须
        appID: '1253258200' // 请传入点播账号的appID 必须
        //其他参数请在开发文档中查看
    });
</script>
@endsection
