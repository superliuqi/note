<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicCity extends Model
{
    protected $table = 'clinic_city';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON => '正常'
    ];

    public static function getCityName($id){
        $city = self::select('name')->orderBy('position','asc')->find($id);
        return $city['name'];
    }
}
