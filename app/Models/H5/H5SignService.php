<?php

    namespace App\Models\H5;

    use Illuminate\Database\Eloquent\Model;

    use Illuminate\Database\Eloquent\SoftDeletes;


    class H5SignService extends Model
    {
        protected $table = 'h5_sign_service';
        protected $guarded = ['id'];


        use SoftDeletes;
        protected $dates = ['deleted_at'];

        //状态
        const STATUS_OFF = 0;
        const STATUS_ON = 1;

        const STATUS_DESC = [
            self::STATUS_OFF => '锁定',
            self::STATUS_ON => '正常'
        ];



    }
