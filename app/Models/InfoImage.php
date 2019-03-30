<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoImage extends Model
{
    protected $table = 'info_image';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON => '正常'
    ];

    public static function getImages($info_id){
        $imgs = self::select('image')->where(['info_id'=>$info_id])->get();
        $imgs_arr = $imgs->isEmpty() ? [] : $imgs->toArray();
        return $imgs_arr;
    }
}
