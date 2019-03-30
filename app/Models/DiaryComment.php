<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaryComment extends Model
{
    protected $table = 'diary_comment';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '待审核',
        self::STATUS_ON => '正常'
    ];
}
