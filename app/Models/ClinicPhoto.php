<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicPhoto extends Model
{
    protected $table = 'clinic_photo';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON => '正常'
    ];
}
