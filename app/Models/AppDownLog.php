<?php
    /**
     * Created by PhpStorm.
     * User: wanghui
     * Date: 2018/5/8
     * Time: 下午5:11
     */

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    /**
     * app下载记录
     * Class Article
     * @package App\Models
     */
    class AppDownLog extends Model
    {
        protected $table = 'app_down_log';
        protected $guarded = ['id'];

        //系统
        const SYSTEM_IOS = 1;
        const SYSTEM_AN = 2;

        const SYSTEM_DESC = [
            self::SYSTEM_IOS => 'IOS',
            self::SYSTEM_AN  => '安卓',
        ];

    }