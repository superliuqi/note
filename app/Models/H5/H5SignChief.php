<?php

    namespace App\Models\H5;

    use Illuminate\Database\Eloquent\Model;

    class H5SignChief extends Model
    {
        protected $table = 'h5_sign_chief';
        protected $guarded = ['id'];

        //状态
        const STATUS_OFF = 0;
        const STATUS_ON = 1;

        const STATUS_DESC = [
            self::STATUS_OFF => '锁定',
            self::STATUS_ON  => '正常',
        ];

        //大区
        const AREA_ZERO = 0;
        const AREA_ONE = 1;
        const AREA_TWO = 2;
        const AREA_THREE = 3;

        const AREA_DESC = [
            self::AREA_ZERO  => '',
            self::AREA_ONE   => '华东大区',
            self::AREA_TWO   => '西北大区',
            self::AREA_THREE => '西南大区',
        ];


    }
