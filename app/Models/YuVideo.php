<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YuVideo extends Model
{
    protected $table = 'yu_video';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON => '正常'
    ];

    //状态
    const TYPE_YU = 1;
    const TYPE_SUPER = 2;
    const TYPE_PRINCE = 3;

    const TYPE_DESC = [
        self::TYPE_YU => '于文红视频',
        self::TYPE_SUPER => '医美大咖说',
        self::TYPE_SUPER => '小王子',
    ];
}
