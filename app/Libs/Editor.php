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
 * 编辑器插件
 * Class Upload
 * @package App\Libs
 */
class  Editor
{
    /**
     * 创建编辑器组件
     * @param string $editor_id  编辑器的id或文本框的名称
     * @param string $max_file_size 上传文件大小
     * @return bool|string
     */
    public static function editorCreate($editor_id = 'desc', $max_file_size = '10') {
        $aliyunoss = new AliyunOss();
        $aliyun_token = $aliyunoss->getWebToken('editer');
        $html = '';
        $html .= <<<eof
                var E = window.wangEditor
                var editor{$editor_id} = new E('#{$editor_id}_id')
                editor{$editor_id}.customConfig.onchange = function (html) {
                    //监控变化，同步更新到 textarea
                    $('[name="{$editor_id}"]').val(html)
                }
                editor{$editor_id}.customConfig.uploadImgServer = '{$aliyun_token['host']}';
                editor{$editor_id}.customConfig.uploadFileName = 'file';
                editor{$editor_id}.customConfig.uploadImgParams = {
                    'domain': '{$aliyun_token['domain']}',
                    'key': '{$aliyun_token['dir']}',
                    'policy': '{$aliyun_token['policy']}',
                    'OSSAccessKeyId': '{$aliyun_token['accessid']}',
                    'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
                    'signature': '{$aliyun_token['signature']}',
                };
                editor{$editor_id}.create();
eof;
        return $html;
    }
}