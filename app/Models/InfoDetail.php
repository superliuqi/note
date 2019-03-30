<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoDetail extends Model
{
    protected $table = 'info_detail';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON => '正常'
    ];

}
