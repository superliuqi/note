<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 会员资料
 * Class MemberProfile
 * @package App\Models
 */
class MemberProfile extends Model
{
    //性别
    const SEX_UNKNOWN = 0;
    const SEX_BOY = 1;
    const SEX_GIRL = 2;

    const SEX_DESC = [
        self::SEX_UNKNOWN => '未知',
        self::SEX_BOY => '男',
        self::SEX_GIRL => '女',
    ];


    //用户身份
    const USER_SERVER = 1;
    const USER_SHOPER = 2;
    const USER_MEMBER = 3;
    const USER_ORDINARY = 4;

    const USER_TYPE = [
        self::USER_SERVER   => '服务商',
        self::USER_SHOPER   => '店家',
        self::USER_MEMBER   => '员工',
        self::USER_ORDINARY => '普通用户',
    ];

    //达人
    const TALENT_OFF = 0;  //取消达人
    const TALENT_ON = 1;   //达人

    const TALENT_DESC = [
        self::TALENT_ON => '是',
        self::TALENT_OFF => '不是',
    ];

    //直播状态
    const LIVE_OFF = 0;    //直播状态关
    const LIVE_ON = 1;     //直播状态开

    const LIVE_DESC = [
        self::LIVE_ON => '有',
        self::LIVE_OFF => '无',
    ];

    //直播消息状态
    const LIVE_MSG_OFF = 0;    //直播消息状态关
    const LIVE_MSG_ON = 1;     //直播消息状态开

    const LIVE_MSG_DESC = [
        self::LIVE_MSG_ON => '显示',
        self::LIVE_MSG_OFF => '关闭'
    ];

    //机器人
    const ROBOT_OFF = 0;  //不是机器人
    const ROBOT_ON = 1;   //是机器人

    const ROBOT_DESC = [
        self::ROBOT_OFF => '否',
        self::ROBOT_ON => '是'
    ];

    protected $table = 'member_profile';
    protected $guarded = [];

    public $timestamps = false;


}