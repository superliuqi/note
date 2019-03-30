<?php

    namespace App\Models\H5;

    use Illuminate\Database\Eloquent\Model;

    class H5SignAuth extends Model
    {
        protected $table = 'h5_sign_auth';
        protected $guarded = ['id'];

        //状态
        const STATUS_OFF = 0;
        const STATUS_ON = 1;
    
        //抽取未中奖人数的头像个数
        const NO_LUCKY_RAND_NUM = 100;

        const STATUS_DESC = [
            self::STATUS_OFF => '锁定',
            self::STATUS_ON => '正常'
        ];
    }
