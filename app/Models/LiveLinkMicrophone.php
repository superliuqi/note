<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    /**
     * 连麦
     * Class LiveLinkMicro
     * @package App\Models
     */
    class LiveLinkMicrophone extends Model
    {
        const STATUS_WAIT = 0;
        const STATUS_LINKING = 1;
        const STATUS_REFUSE = 2;
        const STATUS_END = 3;
        const STATUS_LIVE_AGREE = 4;

        const STATUS_DESC = [
            self::STATUS_WAIT       => '请求中',
            self::STATUS_LINKING    => '连麦中',
            self::STATUS_REFUSE     => '拒绝连麦',
            self::STATUS_END        => '连麦结束',
            self::STATUS_LIVE_AGREE => '主播同意连麦(需要观众再次确认同意或者拒绝)',
        ];

        protected $table = 'live_link_microphone';
        protected $guarded = ['id'];
    }
