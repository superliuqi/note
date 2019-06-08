<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use EasyWeChat\OfficialAccount\Application;

class UsersController extends Controller
{
    public $wechat;

    public function __construct(Application $wechat)
    {
        $this->wechat = $wechat;
    }

    public function users()
    {
        $config = [
            'app_id' => config('wechat.official_account.default.app_id'),
            'secret' => config('wechat.official_account.default.secret'),
            'response_type'=>'array'
        ];
        $config = config('wechat.official_account.default');
//        unset($config['aes_key']);
        $app = Factory::officialAccount($config);
//        dd($app);
        $users = $app->user->lists();
        dd($users);
    }

}