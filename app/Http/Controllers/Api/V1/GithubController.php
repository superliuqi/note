<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Test;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class GithubController extends Controller
{
    /**
     * 将用户重定向到Github认证页面
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * 从Github获取用户信息.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('github')->user();
        $data = $user->user;
        Test::create(['param'=>json_encode($data)]);
        echo 'ok';
    }
}
