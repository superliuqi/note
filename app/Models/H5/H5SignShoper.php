<?php


namespace App\Models\H5;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class H5SignShoper extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $table = 'h5_sign_shoper';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const IDENTITY_ONE = 1;
    const IDENTITY_TWO = 2;
    const IDENTITY_THREE = 3;
    const IDENTITY_FOUR = 4;

    const IDENTITY_DESC = [
        self::IDENTITY_ONE   => '合作伙伴',
        self::IDENTITY_TWO   => '员工',
        self::IDENTITY_THREE => '亲属',
        self::IDENTITY_FOUR  => '其他'
    ];

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON  => '正常'
    ];


}

