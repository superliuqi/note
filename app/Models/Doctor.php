<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'doctor';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    //类型
    const DOCTOR = 1;
    const DESIGNER = 2;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON => '正常'
    ];
    const TYPE_DESC = [
        self::DOCTOR => '医生',
        self::DESIGNER => '设计师'
    ];
}
