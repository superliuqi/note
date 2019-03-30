<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $table = 'clinic';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    //显示
    const SHOW_OFF = 0;
    const SHOW_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON => '正常'
    ];

    /**
     * 获取图片集合
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function image() {
        return $this->hasMany('App\Models\ClinicPhoto','clinic_id');
    }
}
