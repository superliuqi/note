<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use EasyWeChat\BasicService\Application;

class UsersController extends Controller
{
    public $wechat;

    public function __construct(Application $wechat)
    {
        $this->wechat = $wechat;
    }

    public function users()
    {
        $users = $this->wechat->user->list();
        return $users;
    }

}