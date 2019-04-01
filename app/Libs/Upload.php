<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/10
 * Time: 下午1:42
 */

namespace App\Libs;

use App\Libs\Aliyun\AliyunOss;

/**
 * 文件上传插件
 * Class Upload
 * @package App\Libs
 */
class  Upload
{
    /**
     * 创建plupload上传组件
     * @param array $button_id  上传文件按钮，多个时传入数组
     * @param string $quality   是否压缩1-100的数字
     * @param string $max_file_size 上传文件大小
     * @return bool|string
     */
    public static function getPlupload($model = 'images', $quality = '', $max_file_size = '1') {
        $aliyunoss = new AliyunOss();
        $aliyun_token = $aliyunoss->getWebToken($model);
        $max_file_size = (int)$max_file_size;//上传文件大小
        $quality = (int)$quality;
        $quality_value = $quality ? 'quality:' . $quality . ',' : '';//是否压缩
        $html = '';
        $html .= <<<eof
                /**按钮上参数说明
                 * plupload_btn 每个上传按钮都可以加上，加上后会自动出发上传操作
                 * is_callback 如果不调用默认的回调方法，加上此class会调用plupload_callback函数，然后自行在处理后续操作
                 * id 每个按钮都需要给定一个id名词，方便回调使用
                */
                //自动组装按钮id
                var plupload_button_ids = new Array();
                $(".plupload_btn").each(function(){
                    plupload_button_ids.push($(this).attr('id'))
                })
                //监控上传按钮的点击事件
                var plupload_btn_id = '';
                $(".plupload_btn").on('click', function () {
                    plupload_btn_id = $(this).attr('id');
                    
                })
                
                var plupload_multipart_params = '';
                var plupload_load_index = '';
                var uploader = new plupload.Uploader({
                    browse_button: plupload_button_ids,
                    url: '{$aliyun_token['host']}',
                    chunk_size: '0',
                    unique_names: true,
                    flash_swf_url: '/admin/js/plupload/Moxie.swf',
                    silverlight_xap_url : '/admin/js/plupload/Moxie.xap',
                    filters: {
                        max_file_size : '{$max_file_size}mb',
                        mime_types: [
                            {title : "Image files", extensions : "jpg,gif,png,jpeg"},
                        ],
                        prevent_duplicates : true //不允许选取重复文件
                    },
                    resize: {{$quality_value}preserve_headers: false},
                    init: {
                        //选择文件后执行
                        FilesAdded: function(up,files){
                            var next = true;
                            if ($('#' + plupload_btn_id).hasClass('is_callback')) {
                                next = false;
                                try{
                                    //调用自定义函数处理回调,比如控制上传的数量(plupload_btn_id 上传图片的按钮id)
                                    if (plupload_file_add_callback(plupload_btn_id)) {
                                        next = true;
                                    }
                                }catch(e){
                                }
                            }
                            //弹出正在上传的提示
                            if (next) {
                                plupload_load_index = layer.msg('正在上传，请稍后', {
                                    icon: 16, shade: 0.01, time: 1000000000
                                });
                                uploader.start();
                            }
                        },
                        BeforeUpload: function (up,file) {
                            plupload_multipart_params = {
                                'key': '{$aliyun_token['dir']}' + file.target_name,
                                'policy': '{$aliyun_token['policy']}',
                                'OSSAccessKeyId': '{$aliyun_token['accessid']}',
                                'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
                                'signature': '{$aliyun_token['signature']}',
                            };
                            up.setOption({
                                'multipart_params': plupload_multipart_params
                            });
                        },
                        //单个文件上传完成
                        FileUploaded: function(up,file,result){
                            var res_url = '{$aliyun_token['domain']}/{$aliyun_token['dir']}' + file.target_name;
                            if(plupload_btn_id) {
                                //当图片按钮上存在is_callback class属性的时候说明需要回调自定义的函数
                                if ($('#' + plupload_btn_id).hasClass('is_callback')) {
                                    try{
                                        //调用自定义函数处理回调(plupload_btn_id 上传图片的按钮id,res_url图片地址)
                                        plupload_callback(plupload_btn_id, res_url);
                                    }catch(e){
                                    }
                                } else {
                                    $("#"+plupload_btn_id).parent().find('img').attr('src',res_url).show();
                                    $("#"+plupload_btn_id).parent().find('a').attr('href',res_url);
                                    $("#"+plupload_btn_id).parent().find('[type="hidden"]').val(res_url);
                                }
                            }
                        },
                        //全部文件上传完成
                        UploadComplete: function(up,files){
                            layer.close(plupload_load_index);
                        },
                        //上传进度
                        UploadProgress: function(up,files){
                            
                        },
                        //返回错误
                        Error: function(up,err){
                            if (err.message) {
                                layer.msg(err.message);
                            } else {
                                layer.msg('上传失败');
                            }
                        }
                    }
                });
                uploader.init();
eof;
        return $html;
    }

}