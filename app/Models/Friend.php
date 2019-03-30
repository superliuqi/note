<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Friend extends Model
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    
    const FRIEND_WORD = 1;
    const FRIEND_IMG = 2;
    const FRIEND_VIDEO = 3;
    const FRIEND_IMG_LIMIT = 9;//图片上限
    const FRIEND_VIDEO_LIMIT = 1;//视频上限

    const STATUS_DESC = [
        self::STATUS_ON => '审核',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'friend';
    protected $guarded = ['id'];
    
    protected $dates = ['deleted_at'];
}