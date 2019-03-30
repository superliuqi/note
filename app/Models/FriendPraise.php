<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FriendPraise extends Model
{
    protected $table = 'friend_praise';
    protected $guarded = ['id'];
    
    const CANCEL_PRAISE = 1;//取消点赞
    const ADD_PRAISE = 2;//点赞
}