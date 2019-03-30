<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Red extends Model
{
    //类型
    const STATUS_OFF = 0;//未处理
    const STATUS_ON = 1;//已处理

    protected $table = 'red';
    protected $guarded = ['id'];
}
