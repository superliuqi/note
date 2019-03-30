<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '禁用',
        self::STATUS_ON => '正常'
    ];

    //类型
    const TYPE_DIARY = 1;
    const TYPE_CIRCLE = 2;

    const TYPE_DESC = [
        self::TYPE_DIARY => '医院机构，医生，日记',
        self::TYPE_CIRCLE => '圈子'
    ];
}
