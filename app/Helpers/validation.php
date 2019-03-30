<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/7/3
 * Time: 上午9:39
 */

/**
 * 验证手机号码格式
 * @param $mobile
 * @return bool
 */
function check_mobile($mobile) {
    if (preg_match("/^1[3456789]{1}\d{9}$/", $mobile)) {
        return true;
    }
    return false;
}