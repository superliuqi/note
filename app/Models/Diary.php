<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diary extends Model
{
    protected $table = 'diary';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '待审核',
        self::STATUS_ON => '正常'
    ];

    /**
     * 获取日记详情
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function content() {
        return $this->hasMany('App\Models\DiaryContent');
    }
}
